<?php
/**
 * Modified and translated to PHP by Lee Cher Yeong
 * Copyright (C) 2007 Mosets Consulting
 * http://www.mosets.com/
 * License GNU LGPL http://www.gnu.org/licenses/lgpl.txt
 */

defined('_JEXEC') or die('Restricted access');

// Diff_Match_Patch v1.4
// Computes the difference between two texts to create a patch.
// Applies the patch onto another text, allowing for errors.
// Copyright (C) 2006 Neil Fraser
// http://neil.fraser.name/software/diff_match_patch/

// This program is free software you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License (www.gnu.org) for more details.
// Number of seconds to map a diff before giving up.  (0 for infinity)

DEFINE( 'DIFF_TIMEOUT', '1.0');
// Cost of an empty edit operation in terms of edit characters.
DEFINE( 'DIFF_EDIT_COST', '4');
// Tweak the relative importance (0.0 = accuracy, 1.0 = proximity)
DEFINE( 'MATCH_BALANCE', '0.5');
// At what point is no match declared (0.0 = perfection, 1.0 = very loose)
DEFINE( 'MATCH_THRESHOLD', '0.5');
// The min and max cutoffs used when computing text lengths.
DEFINE( 'MATCH_MINLENGTH', '100');
DEFINE( 'MATCH_MAXLENGTH', '1000');
// Chunk size for context length.
DEFINE( 'PATCH_MARGIN', '4');

DEFINE( 'DIFF_DELETE', '-1');
DEFINE( 'DIFF_INSERT', '1');
DEFINE( 'DIFF_EQUAL', '0');

function diff_main($text1, $text2, $checklines=true) {
	// Find the differences between two texts.  Return an array of changes.
	// If checklines is present and false, then don't run a line-level diff first to identify the changed areas.
	// Check for equality (speedup)
	if ($text1 == $text2)
		return array( array(DIFF_EQUAL, $text1) );

	if ( !isset($checklines ) )
		$checklines = true;

	// Trim off common prefix (speedup)
	$a = diff_prefix($text1, $text2);
	$text1 = $a[0];
	$text2 = $a[1];
	$commonprefix = $a[2];

	// Trim off common suffix (speedup)
	$a = diff_suffix($text1, $text2);
	$text1 = $a[0];
	$text2 = $a[1];
	$commonsuffix = $a[2];

	$longtext = strlen($text1) > strlen($text2) ? $text1 : $text2;
	$shorttext = strlen($text1) > strlen($text2) ? $text2 : $text1;

	if (!$text1) {  // Just add some text (speedup)
		$diff = array( array(DIFF_INSERT, $text2) );
	} else if (!$text2) { // Just delete some text (speedup)
		$diff = array( array(DIFF_DELETE, $text1) );
	} else if (($i = strpos($longtext,$shorttext)) != false) {
		// Shorter text is inside the longer text (speedup)
		$diff = array( array(DIFF_INSERT, substr($longtext,0,$i)), array(DIFF_EQUAL, $shorttext), array(DIFF_INSERT, substr($longtext,$i+strlen($shorttext)) ));
		// Swap insertions for deletions if diff is reversed.
		if (strlen($text1) > strlen($text2)) {
			$diff[0][0] = DIFF_DELETE;
			$diff[2][0] = DIFF_DELETE;
		}
	} else {
		$longtext = null;
		$shorttext = null; // Garbage collect
		// Check to see if the problem can be split in two.
		$hm = diff_halfmatch($text1, $text2);
		if ($hm) {
			// A half-match was found, sort out the return data.
			$text1_a = $hm[0];
			$text1_b = $hm[1];
			$text2_a = $hm[2];
			$text2_b = $hm[3];
			$mid_common = $hm[4];
			// Send both pairs off for separate processing.
			$diff_a = diff_main($text1_a, $text2_a, $checklines);
			$diff_b = diff_main($text1_b, $text2_b, $checklines);
			// Merge the results.
			$diff = array_merge($diff_a,array(array(DIFF_EQUAL, $mid_common)),$diff_b);
		} else {

			// Perform a real diff.
			if ($checklines && strlen($text1) + strlen($text2) < 250)
				$checklines = false; // Too trivial for the overhead.
			if ($checklines) {
				// Scan the text on a line-by-line basis first.
				$a = diff_lines2chars($text1, $text2);
				$text1 = $a[0];
				$text2 = $a[1];
				$linearray = $a[2];
			}
			$diff = diff_map($text1, $text2);

			if (!$diff) // No acceptable result.
				$diff = array(array(DIFF_DELETE, $text1), array(DIFF_INSERT, $text2));
			if ($checklines) {
			  diff_chars2lines($diff, $linearray); // Convert the diff back to original text.
			  diff_cleanup_semantic($diff); // Eliminate freak matches (e.g. blank lines)

			  // Rediff any replacement blocks, this time on character-by-character basis.
			  array_push($diff,array(DIFF_EQUAL,''));  // Add a dummy entry at the end.
			  $pointer = 0;
			  $count_delete = 0;
			  $count_insert = 0;
			  $text_delete = '';
			  $text_insert = '';
			  while($pointer < count($diff)) {
				 if ($diff[$pointer][0] == DIFF_INSERT) {
					$count_insert++;
					$text_insert .= $diff[$pointer][1];
				 } else if ($diff[$pointer][0] == DIFF_DELETE) {
					$count_delete++;
					$text_delete .= $diff[$pointer][1];
				 } else {  // Upon reaching an equality, check for prior redundancies.
					if ($count_delete >= 1 && $count_insert >= 1) {
						// Delete the offending records and add the merged ones.
						$a = diff_main($text_delete, $text_insert, false);
						array_splice($diff,$pointer - $count_delete - $count_insert, $count_delete + $count_insert);
						$pointer = $pointer - $count_delete - $count_insert;
						for ($i=count($a)-1; $i>=0; $i--)
							array_splice($diff,$pointer,0,array($a[$i]));
						$pointer = $pointer + count($a);
					}
					$count_insert = 0;
					$count_delete = 0;
					$text_delete = '';
					$text_insert = '';
				 }
				 $pointer++;
			  }
			  array_pop($diff); // Remove the dummy entry at the end.
			}
		}
	}

	if ($commonprefix)
		array_unshift($diff,array(DIFF_EQUAL, $commonprefix));
	if ($commonsuffix)
		array_push($diff,array(DIFF_EQUAL, $commonsuffix));
	
	diff_cleanup_merge($diff);
	return $diff;
}

function diff_lines2chars($text1, $text2) {
	// Split text into an array of strings.
	// Reduce the texts to a string of hashes where each character represents one line.
	global $linearray, $linehash;
	$linearray = array();  // linearray[4] == "Hello\n"
	$linehash = array();  // linehash["Hello\n"] == 4

	// "\x00" is a valid JavaScript character, but the Venkman debugger doesn't like it (bug 335098)
	// So we'll insert a junk entry to avoid generating a null character.
	// This has nothing to do with PHP, but is kept for cross-language maintainablility.
	array_push($linearray,'');

	$chars1 = diff_lines2chars_munge($text1);
	$chars2 = diff_lines2chars_munge($text2);
	return array($chars1, $chars2, $linearray);
}

function diff_lines2chars_munge($text) {
	global $linearray, $linehash;
	// My first ever closure!
	$chars = '';
	while ($text) {
		$i = strpos($text,"\n");
		if ($i === false)
			$i = strlen($text);
		$line = substr($text,0,$i+1);
		$text = substr($text,$i+1);
		if(array_key_exists($line,$linehash)) {
			$chars .= chr($linehash[$line]);
		} else {
			$linearray[] = $line;
			$linehash[$line] = count($linearray) - 1;
			$chars .= chr(count($linearray) - 1);
		}
	}
	return $chars;
}

function diff_chars2lines(&$diff, $linearray) {
	// Rehydrate the text in a diff from a string of line hashes to real lines of text.
 	for ($x=0; $x<count($diff); $x++) {
		$chars = $diff[$x][1];
		$text = '';
		for ($y=0; $y<strlen($chars); $y++)
			$text .= $linearray[ord($chars{$y})];
		$diff[$x][1] = $text;
	}
}

function diff_map($text1, $text2) {
	$ms_end		= round(array_sum(explode(" ",microtime())),3) + DIFF_TIMEOUT;
	$max			= (strlen($text1) + strlen($text2)) / 2;
	$v_map1		= array();
	$v_map2		= array();
	$done			= false;
	$footsteps	= array();
	$v1			= array();
	$v2			= array();
	$v1[1]		= 0;
	$v2[1]		= 0;
	$front		= (strlen($text1) + strlen($text2)) % 2;
	
	for( $d=0; $d<$max; $d++ ) {
		if( DIFF_TIMEOUT > 0 && round(array_sum(explode(" ",microtime())),3) > $ms_end ) {
			return null;
		}
		// Walk the front path one step.
		for ($k= -$d; $k<=$d; $k+=2) {
			if ($k == -$d || $k != $d && $v1[$k-1] < $v1[$k+1]) {
				$x = $v1[$k+1];
			} else {
				$x = $v1[$k-1]+1;
			}
			$y = $x - $k;
			$footstep = $x.','.$y;
			if ($front && array_key_exists($footstep, $footsteps)) {
				$done = true;
			}
			if (!$front) {
				$footsteps[$footstep] = $d;
			}
			while(!$done && $x < strlen($text1) && $y < strlen($text2) && $text1{$x} == $text2{$y}) {
				$x++; $y++;
				$footstep = $x.','.$y;
				if ($front && array_key_exists($footstep, $footsteps)) {
					$done = true;
				}
				if(!$front) {
					$footsteps[$footstep] = $d;
				}
			}
			$v1[$k] = $x;
			$v_map1[$d][($x.','.$y)] = true;
			if ($done) {
				// Front path ran over reverse path.
				$v_map2 = array_slice($v_map2, 0, $footsteps[$footstep]+1);
				$a = diff_path1($v_map1, substr($text1,0,$x), substr($text2,0,$y));
				return array_merge($a, diff_path2($v_map2, substr($text1,$x), substr($text2,$y) ) );
			}
		} // End for

		// Walk the reverse path one step.
		$v_map2[$d] = array();
		for ($k= -$d; $k<=$d; $k+=2) {
			if ($k == -$d || $k != $d && $v2[$k-1] < $v2[$k+1]) {
				$x = $v2[$k+1];
			} else {
				$x = $v2[$k-1]+1;
			}
			$y = $x - $k;
			$footstep = (strlen($text1)-$x).','.(strlen($text2)-$y);
			if (!$front && array_key_exists($footstep, $footsteps)) {
			  $done = true;
			}
			if ($front) {
			  $footsteps[$footstep] = $d;
			}
			while (!$done && $x < strlen($text1) && $y < strlen($text2) && $text1{strlen($text1)-$x-1} == $text2{strlen($text2)-$y-1} ) {
				$x++; $y++;
				$footstep = (strlen($text1)-$x).','.(strlen($text2)-$y);
				if (!$front && array_key_exists($footstep, $footsteps)) {
					$done = true;
				}
				if ($front) {
					$footsteps[$footstep] = $d;
				}
			}
			$v2[$k] = $x;
			$v_map2[$d][$x.','.$y] = true;
			if ($done) {
				// Reverse path ran over front path.
				$v_map1 = array_slice($v_map1, 0, $footsteps[$footstep]+1);
				$a = diff_path1($v_map1, substr($text1,0,strlen($text1)-$x), substr($text2,0,strlen($text2)-$y) );
				return array_merge($a, diff_path2($v_map2, substr($text1,strlen($text1)-$x), substr($text2,strlen($text2)-$y) ));
			}
		}
	}
	// Number of diffs equals number of characters, no commonality at all.
	return null;
}

function diff_path1($v_map, $text1, $text2) {
	$path = array();
	$x = strlen($text1);
	$y = strlen($text2);
	$last_op = null;
	for ($d= count($v_map)-2; $d>=0; $d--) {
		while(true) {
			if( array_key_exists((($x-1).','.$y), $v_map[$d]) ) {
				$x--;
				if ($last_op == DIFF_DELETE) {
					$path[0][1] = $text1{$x} . $path[0][1];
				} else {
					array_unshift($path, array(DIFF_DELETE, $text1{$x}));
				}
				$last_op = DIFF_DELETE;
				break;

			} elseif ( array_key_exists(($x.','.($y-1)), $v_map[$d]) ) {
				$y--;
				if ($last_op == DIFF_INSERT) {
					$path[0][1] = $text2{$y} . $path[0][1];
				} else {
					array_unshift($path, array(DIFF_INSERT, $text2{$y}));
				}
				$last_op = DIFF_INSERT;
				break;
			} else {
				$x--;
				$y--;
				if ($last_op == DIFF_EQUAL) {
					$path[0][1] = $text1{$x} . $path[0][1];
				} else {
					array_unshift($path, array(DIFF_EQUAL, $text1{$x}));
				}
				$last_op = DIFF_EQUAL;
			
			}
		}
	}
	return $path;
}

function diff_path2($v_map, $text1, $text2) {
	// Work from the middle back to the end to determine the path.
	$path = array();
	$x = strlen($text1);
	$y = strlen($text2);
	$last_op = null;

	for ($d=count($v_map)-2; $d>=0; $d--) {
		while(true) {
			if( array_key_exists((($x-1).','.$y), $v_map[$d]) ) {
				$x--;
				if ($last_op == DIFF_DELETE) {
					$path[count($path)-1][1] .= $text1{strlen($text1)-$x-1};
				} else {
					array_push($path, array(DIFF_DELETE,$text1{strlen($text1)-$x-1}));
				}
				$last_op = DIFF_DELETE;
				break;
			} elseif( array_key_exists(($x.','.($y-1)), $v_map[$d]) ) {
				$y--;
				if ($last_op == DIFF_INSERT) {
					$path[count($path)-1][1] .= $text2{strlen($text2)-$y-1};
				} else {
					array_push( $path, array(DIFF_INSERT, $text2{strlen($text2)-$y-1}) );
				}
				$last_op = DIFF_INSERT;
				break;
			} else {
				$x--;
				$y--;
				if ($last_op == DIFF_EQUAL) {
					$path[count($path)-1][1] .= $text1{strlen($text1)-$x-1};
				} else {
					array_push( $path, array(DIFF_EQUAL, $text1{strlen($text1)-$x-1}) );
				}
				$last_op = DIFF_EQUAL;

			}
		}
	}
	return $path;
}

function diff_prefix($text1, $text2) {
	// Trim off common prefix
	$pointermin = 0;
	$pointermax = min(strlen($text1), strlen($text2));
	$pointermid = $pointermax;
	while($pointermin < $pointermid) {
		if ( substr($text1,0,$pointermid) == substr($text2,0,$pointermid)) {
			$pointermin = $pointermid;
		} else {
			$pointermax = $pointermid;
		}
		$pointermid = floor(($pointermax - $pointermin) / 2 + $pointermin);
	}
	$commonprefix = substr($text1,0,$pointermid);
	$text1 = substr($text1,$pointermid);
	$text2 = substr($text2,$pointermid);
	return array($text1,$text2,$commonprefix);
}

function diff_suffix($text1, $text2) {
  // Trim off common suffix
  $pointermin = 0;
  $pointermax = min(strlen($text1), strlen($text2));
  $pointermid = $pointermax;
  while($pointermin < $pointermid) {
    if ( substr($text1,strlen($text1)-$pointermid) == substr($text2,strlen($text2)-$pointermid) ) {
      $pointermin = $pointermid;
    } else {
      $pointermax = $pointermid;
	 }
    $pointermid = floor(($pointermax - $pointermin) / 2 + $pointermin);
  }
  $commonsuffix = substr($text1,strlen($text1)-$pointermid);
  $text1 = substr($text1,0,strlen($text1)-$pointermid);
  $text2 = substr($text2,0,strlen($text2)-$pointermid);
  return array($text1, $text2, $commonsuffix);
}

function diff_halfmatch($text1, $text2) {
	// Do the two texts share a substring which is at least half the length of the longer text?
	$longtext = strlen($text1) > strlen($text2) ? $text1 : $text2;
	$shorttext = strlen($text1) > strlen($text2) ? $text2 : $text1;
	if (strlen($longtext) < 10 || strlen($shorttext) < 1) {
		return null; // Pointless.
	}

	// First check if the second quarter is the seed for a half-match.
	$hm1 = diff_halfmatch_i($longtext, $shorttext, ceil(strlen($longtext)/4));
	// Check again based on the third quarter.
	$hm2 = diff_halfmatch_i($longtext, $shorttext, ceil(strlen($longtext)/2));
	$hm;
	if (!$hm1 && !$hm2)
		return null;
	else if (!$hm2)
		$hm = $hm1;
	else if (!$hm1)
	 $hm = $hm2;
	else // Both matched.  Select the longest.
	 $hm = strlen($hm1[4]) > strlen($hm2[4]) ? $hm1 : $hm2;

	// A half-match was found, sort out the return data.
	if (strlen($text1) > strlen($text2)) {
	 $text1_a = $hm[0];
	 $text1_b = $hm[1];
	 $text2_a = $hm[2];
	 $text2_b = $hm[3];
	} else {
	 $text2_a = $hm[0];
	 $text2_b = $hm[1];
	 $text1_a = $hm[2];
	 $text1_b = $hm[3];
	}
	$mid_common = $hm[4];
	return array($text1_a, $text1_b, $text2_a, $text2_b, $mid_common);

}

function diff_halfmatch_i($longtext, $shorttext, $i) {
	// Start with a 1/4 length substring at position i as a seed.
	$seed = substr($longtext,$i,floor(strlen($longtext)/4));
	$j = -1;
	$best_common = '';
	$best_longtext_a = '';
	$best_longtext_b = '';
	$best_shorttext_a = '';
	$best_shorttext_b = '';
	$j = strpos($shorttext,$seed,$j+1);
	while ($j != false) {
		$my_prefix = diff_prefix(substr($longtext,$i), substr($shorttext,$j));
		$my_suffix = diff_suffix(substr($longtext,0,$i), substr($shorttext,0,$j));
		if (strlen($best_common) < strlen($my_suffix[2] . $my_prefix[2])) {
			$best_common = $my_suffix[2] . $my_prefix[2];
			$best_longtext_a = $my_suffix[0];
			$best_longtext_b = $my_prefix[0];
			$best_shorttext_a = $my_suffix[1];
			$best_shorttext_b = $my_prefix[1];
		}
		$j = strpos($shorttext,$seed,$j+1);
	}
	if (strlen($best_common) >= strlen($longtext)/2)
		return array($best_longtext_a, $best_longtext_b, $best_shorttext_a, $best_shorttext_b, $best_common);
	else
		return null;
}

function diff_cleanup_semantic(&$diff) {
	// Reduce the number of edits by eliminating semantically trivial equalities.
	$changes = false;
	$equalities = array(); // Stack of indices where equalities are found.
	$lastequality = null; // Always equal to equalities[equalities.length-1][1]
	$pointer = 0; // Index of current position.
	$length_changes1 = 0; // Number of characters that changed prior to the equality.
	$length_changes2 = 0; // Number of characters that changed after the equality.
	while ($pointer < count($diff)) {
		if ($diff[$pointer][0] == DIFF_EQUAL) { // equality found
			array_push($equalities,$pointer);
			$length_changes1 = $length_changes2;
			$length_changes2 = 0;
			$lastequality = $diff[$pointer][1];
		} else { // an insertion or deletion
			$length_changes2 += strlen($diff[$pointer][1]);
			if ($lastequality != null && (strlen($lastequality) <= $length_changes1) && (strlen($lastequality) <= $length_changes2)) {
				//alert("Splitting: '"+lastequality+"'");
				array_splice($diff,$equalities[count($equalities)-1],0,array(array(DIFF_DELETE, $lastequality)));
				$diff[$equalities[count($equalities)-1]+1][0] = DIFF_INSERT; // Change second copy to insert.
				array_pop($equalities); // Throw away the equality we just deleted;
				array_pop($equalities); // Throw away the previous equality;
				$pointer = count($equalities) ? $equalities[count($equalities)-1] : -1;
				$length_changes1 = 0; // Reset the counters.
				$length_changes2 = 0;
				$lastequality = null;
				$changes = true;
			}
		}
		$pointer++;
	}

	if ($changes)
		diff_cleanup_merge($diff);
}

function diff_cleanup_merge(&$diff) {
	// Reorder and merge like edit sections.  Merge equalities.
	// Any edit section can move as long as it doesn't cross an equality.
	array_push($diff, array(DIFF_EQUAL, '')); // Add a dummy entry at the end.
	$pointer = 0;
	$count_delete = 0;
	$count_insert = 0;
	$text_delete = '';
	$text_insert = '';

	//while($pointer < strlen($diff)) {
	while($pointer < count($diff)) {
		if ($diff[$pointer][0] == DIFF_INSERT) {
			$count_insert++;
			$text_insert .= $diff[$pointer][1];
			$pointer++;
		} elseif ($diff[$pointer][0] == DIFF_DELETE) {
			$count_delete++;
			$text_delete .= $diff[$pointer][1];
			$pointer++;
		} else {  // Upon reaching an equality, check for prior redundancies.
			if ($count_delete != 0 || $count_insert != 0) {
				if ($count_delete != 0 && $count_insert != 0) {
					// Factor out any common prefixies.
					$my_xfix = diff_prefix($text_insert, $text_delete);
					if ($my_xfix[2] != '') {
						if (($pointer - $count_delete - $count_insert) > 0 && $diff[$pointer - $count_delete - $count_insert - 1][0] == DIFF_EQUAL) {
						  $diff[$pointer - $count_delete - $count_insert - 1][1] .= $my_xfix[2];
						} else {
						  array_unshift($diff,array(DIFF_EQUAL, $my_xfix[2]));
						  $pointer++;
						}
						$text_insert = $my_xfix[0];
						$text_delete = $my_xfix[1];
					}
					// Factor out any common suffixies.
					$my_xfix = diff_suffix($text_insert, $text_delete);
					if ($my_xfix[2] != '') {
						$text_insert = $my_xfix[0];
						$text_delete = $my_xfix[1];
						$diff[$pointer][1] = $my_xfix[2] . $diff[$pointer][1];
					}
				}
				// Delete the offending records and add the merged ones.
				if ($count_delete == 0) {
					array_splice($diff,$pointer - $count_delete - $count_insert, $count_delete + $count_insert, array(array(DIFF_INSERT, $text_insert))); 
				} elseif ($count_insert == 0) {
					array_splice($diff,$pointer - $count_delete - $count_insert, $count_delete + $count_insert, array(array(DIFF_DELETE, $text_delete)));
				} else {
					array_splice($diff,$pointer - $count_delete - $count_insert, $count_delete + $count_insert, array(array(DIFF_DELETE, $text_delete),array(DIFF_INSERT, $text_insert)));
				}
				$pointer = $pointer - $count_delete - $count_insert + ($count_delete ? 1 : 0) + ($count_insert ? 1 : 0) + 1;
			} elseif ($pointer != 0 && $diff[$pointer-1][0] == DIFF_EQUAL) {
			  // Merge this equality with the previous one.
			  $diff[$pointer-1][1] .= $diff[$pointer][1];
			  array_splice($diff,$pointer,1);
			} else {
			  $pointer++;
			}
			$count_insert = 0;
			$count_delete = 0;
			$text_delete = '';
			$text_insert = '';
		}
	}
	if ($diff[count($diff)-1][1] == '')
		array_pop($diff);
}

function diff_addindex(&$diff) {
	// Add an index to each tuple, represents where the tuple is located in text2.
	// e.g. [[DIFF_DELETE, 'h', 0], [DIFF_INSERT, 'c', 0], [DIFF_EQUAL, 'at', 1]]
	$i = 0;
	for ($x=0; $x<count($diff); $x++) {
		$diff[$x] = array($diff[$x][0], $diff[$x][1], $i);
		if ($diff[$x][0] != DIFF_DELETE)
			$i += strlen($diff[$x][1]);
	}
}

function diff_prettyhtml($diff) {
	// Convert a diff array into a pretty HTML report.
	diff_addindex($diff);

	$html = '';

	for ($x=0; $x<count($diff); $x++) {
		$m = $diff[$x][0]; // Mode (delete, equal, insert)
		$t = $diff[$x][1]; // Text of change.
		$i = $diff[$x][2]; // Index of change.
		$t = htmlspecialchars($t);
		//$t = nl2br($t);
		if ($m == DIFF_DELETE) {
			//$html .= "<del title='i=".$i."'>".$t."</del>";
			$html .= "<del>".$t."</del>";
		} elseif ($m == DIFF_INSERT) {
			$html .= "<ins>".$t."</ins>";
		} else {
			$html .= "<span>".$t."</span>";
		}
	}
	return $html;
}
?>