<?php
/**
 * @package		Mosets Tree
 * @copyright		(C) 2010 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_2_1_7 extends mUpgrade {
	function upgrade() {
		updateVersion(2,1,7);
		$this->updated = true;
		return true;
	}
}
?>