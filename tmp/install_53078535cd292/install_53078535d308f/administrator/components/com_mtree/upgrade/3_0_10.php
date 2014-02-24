<?php
/**
 * @version	$Id: 3_0_10.php 1949 2013-07-08 09:49:48Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_0_10 extends mUpgrade {

	function upgrade() {

		updateVersion(3,0,10);
		$this->updated = true;
		return true;
	}
}
?>
