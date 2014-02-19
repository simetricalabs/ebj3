<?php
/**
 * @version		$Id: 2_1_9.php 1005 2011-01-19 08:22:55Z cy $
 * @package		Mosets Tree
 * @copyright		(C) 2011 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_9 extends mUpgrade {
	function upgrade() {
		updateVersion(2,1,9);
		$this->updated = true;
		return true;
	}
}
?>