<?php 
$content = ob_get_contents();
ob_clean();

include_once('class_css_and_js_compression.php');

$excludeFilesArray  = $excludeFilesArrayTop;

// js regax
$regex = '/src=\"([^\"]*)\"/i';
$jsfiles = getArray($content,$regex);

foreach($jsfiles as $k=>$jsfile){
	foreach($excludeFilesArray as $excludeFiles){
		if( stristr( $jsfile , $excludeFiles )){
			 $excludejsFilesArray[] = $jsfiles[$k];
			 unset($jsfiles[$k]);
			 break;
		}
	}
}


$regex = '/href=\"([^\"]*)\"/i';
$cssfiles = getArray($content,$regex);

foreach($cssfiles as $k=>$cssfile){
	foreach($excludeFilesArray as $excludeFiles){
		if( stristr( $cssfile , $excludeFiles )){
			 $excludecssFilesArray[] = $cssfiles[$k];
			 unset($cssfiles[$k]);
			 break;
		}
	}
}




$arrayTemp = array();
if(isset($usejscompression) && ($usejscompression=="yes")){
	foreach($jsfiles as $jsfile){
		if(!in_array($jsfile,$arrayTemp)) {
			$content = str_replace($jsfile,'',$content);
			$arrayTemp[]  = $jsfile;
		}
	}
	if(isset($excludejsFilesArray)){
		if(is_array($excludejsFilesArray)){
			foreach($excludejsFilesArray as $excludejsFiles){
				if(!in_array($excludejsFiles,$arrayTemp)) {
					$content = str_replace($excludejsFiles,'',$content);
					$arrayTemp[]  = $excludejsFiles;
				}
			}
		}
	}
	
}

if(isset($usecsscompression) && ($usecsscompression=="yes")){
	foreach($cssfiles as $cssfil){
		if(!in_array($cssfil,$arrayTemp)) {
			$content = str_replace($cssfil,'',$content);
			$arrayTemp[]  = $cssfil;
		}
	}
	if(isset($excludecssFilesArray)&&is_array($excludecssFilesArray)){
		foreach($excludecssFilesArray as $excludecssFiles){
			if(!in_array($excludecssFiles,$arrayTemp)) {
				$content = str_replace($excludecssFiles,'',$content);
				$arrayTemp[]  = $excludecssFiles;
			}	
		}
	}
}
echo $content = removeblankLinks($content);
/*echo "<pre>";
print_r($jsfiles);
print_r($cssfiles);
exit;*/


$HTTP_HOST =  $_SERVER['HTTP_HOST'];
$SCRIPT_NAME =  $_SERVER['SCRIPT_NAME'];
$scripnameArray = explode("/",$SCRIPT_NAME);
$scripname="";
for($i=0;$i<(count($scripnameArray)-1);$i++){
	if($scripnameArray[$i]==''){continue;}
	$scripname = $scripname."/".$scripnameArray[$i];
}
$serverpath =  "http://".$HTTP_HOST.$scripname."/".$filepath;
$scriptpath = $scripname."/".$filepath;
//echo "</pre>";
$writabledir=$filepath;
$mybrowser = getBrowser();
$name = 'top_compression_js.php';

if(isset($usejscompression) && ($usejscompression=="yes")){
/* 	if(file_exists($writabledir.$name)){
			delete_old_md5s($writabledir,$name,$cachetime);
		 
	
	}else{*/
			$js = '';
			foreach($jsfiles as $jsfile){
				$js .= JSMin::minify(file_get_contents($jsfile));
			}
			if(delete_old_md5s($writabledir,$name,$cachetime)){
				$fh = fopen($writabledir.$name, 'w');
				$js = '<?php header("Content-type: text/javascript"); ?>' . $js . '<?php exit();?>' ;
				fwrite($fh, $js);
				fclose($fh);
			}
			//file_put_contents($writabledir.$name,$js);
			
			
	//}
	echo "\n
			<script type='text/javascript' src='$serverpath"."$name' > </script>" ;
}

$name = 'top_compression_css.php';
if(isset($usecsscompression) && ($usecsscompression=="yes")){ 
/*	if(file_exists($writabledir.$name)){
	delete_old_md5s($writabledir,$name,$cachetime);
		 
		
	}else{*/
		$css = '';
		foreach($cssfiles as $cssfile){
			$css .= CssMin::minify(file_get_contents($cssfile));
		}
		$css = removeblankLinks($css);
		if(delete_old_md5s($writabledir,$name,$cachetime)){
			$fh = fopen($writabledir.$name, 'w');
			$css = '<?php header("Content-type: text/css"); ?>' . $css . '<?php exit();?>' ;
			$css = removepath($css);
			fwrite($fh, $css);
			fclose($fh);
		}
		//file_put_contents($writabledir.$name,$css);
		
	
	//}
	
		echo "\n
		<link rel='stylesheet' href='$serverpath"."$name' type='text/css' />
		" ;
}

if(isset($excludejsFilesArray)){
	if(is_array($excludejsFilesArray)){
		foreach($excludejsFilesArray as $excludejsFiles){
				echo "\n<script type='text/javascript' src='$excludejsFiles' ></script>" ;
		}
	}
}
if(isset($excludecssFilesArray)){
	if(is_array($excludecssFilesArray)){
		foreach($excludecssFilesArray as $excludecssFiles){
				echo "\n<link rel='stylesheet' href='$excludecssFiles' type='text/css' />" ;
		}
	}
}
 
?> 