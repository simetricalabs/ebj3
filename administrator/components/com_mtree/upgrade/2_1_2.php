<?php
/**
 * @version		$Id: 2_1_2.php 773 2009-08-26 11:26:25Z CY $
 * @package		Mosets Tree
 * @copyright	(C) 2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_2 extends mUpgrade {
	function upgrade() {
		updateVersion(2,1,2);
		$this->updated = true;
		return true;
	}
}
?>