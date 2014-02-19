<?php defined('_JEXEC') or die('Restricted access'); 

$htmls = array();
foreach( $list AS $key => $item )
{
	$chr = chr($key);
	$html = '<a href="' . $item->link . '"><span>' . $item->text . '</span>';
	if ($display_total_links) {
		$html .= " <small>(".$item->total.")</small>";
	}
	$html .= '</a>';
	$htmls[] = $html;
}
echo implode( $seperator, $htmls );
?>