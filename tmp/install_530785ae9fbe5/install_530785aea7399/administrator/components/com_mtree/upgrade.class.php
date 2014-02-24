<?php
if ( !class_exists('mUpgrade') )
{
	class mUpgrade {
		var $updated = false;
		var $db = null;
	
		function __construct()
		{
			$this->db = JFactory::getDBO();
		}
	
		function preflight()
		{
			return false;
		}
	
		function updated() {
			return $this->updated;
		}
		function addColumn($table, $column_name, $column_info='', $after='') {
			if(addColumn($table, $column_name, $column_info, $after)) {
				$this->updated = true;
			}
		}
		function addRows($table, $rows) {
			if(addRows($table, $rows)) {
				$this->updated = true;
			}	
		}
		function printStatus( $msg, $status=1 ) {
			if( $status == -1 ) {
				echo '<tr><td><b><span style="color:red">Error</span></b> - '.$msg.'</td></tr>';
			} elseif( $status == 1 OR $status == 0 ) {
				echo '<tr><td><b>'.(($status)?'<span style="color:green">OK</span>':'Skipped').'</b> - '.$msg.'</td></tr>';
			} elseif( $status == 2 ) {
				echo '<tr><td><strong>'.$msg.'</strong></td></tr>';
			}
		}
	}
}
?>