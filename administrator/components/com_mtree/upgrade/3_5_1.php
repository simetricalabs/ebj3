<?php
/**
 * @version	$Id: 3_5_1.php 2105 2013-10-11 09:53:37Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mUpgrade_3_5_1 extends mUpgrade
{
	function upgrade() {

		updateVersion(3,5,1);
		$this->updated = true;
		return true;
	}
}
?>