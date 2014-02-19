<?php
/**
 * @version		$Id: admin.mtimporter.html.php 2098 2013-10-09 10:52:01Z cy $
 * @package		MT Importer
 * @copyright	(C) 2004-2013 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class HTML_mtimporter {
	
	function csv_import_dryrun_report($fields, $create_cfs, $create_cf_captions, $index_linkname, $index_catid, $total_listings, $sample_imports) 
	{
		$language = JFactory::getLanguage();
		$loaded = $language->load('com_mtimporter.sys', JPATH_ADMINISTRATOR);

		JToolBarHelper::title(  JText::_( 'COM_MTIMPORTER_TOOLBAR_TITLE_DRYRUN_REPORT' ), 'article-add' );

		$bar = & JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', 'Back', 'index.php?option=com_mtimporter&amp;task=check_csv' );

		if( $index_linkname < 0 ) {
			self::print_csv_dryrun_system_message( JText::_('COM_MTIMPORTER_DRYRUN_REPORT_NO_LINK_NAME'), 'warning');
		} 
		elseif( $total_listings < 1 ) 
		{
			self::print_csv_dryrun_system_message( JText::_('COM_MTIMPORTER_DRYRUN_REPORT_NO_LISTINGS'), 'warning');
		}
		else
		{
			self::print_csv_dryrun_system_message( JText::_('COM_MTIMPORTER_DRYRUN_REPORT_NO_PROBLEM') );
		}
		
		?>
		<h1><?php echo JText::_( 'COM_MTIMPORTER_DRYRUN_REPORT_REPORT_DETAILS' ); ?></h1>
		<table class="table table-striped">
			<tr>
				<td width="300">All fields</td>
				<td><?php echo implode(', ',$fields); ?></td>
			</tr>
			<tr>
				<td>List of new custom fields to be created</td>
				<td><?php 
				if( !empty($create_cf_captions) )
				{
					$tmp = array();
					foreach($create_cf_captions AS $key => $value )
					{
						$tmp[] = $value;
					}
					echo implode(', ',$tmp); 
				}
				else
				{
					echo '<em>none</em>';
				}
				?></td>
			</tr>
			<tr>
				<td width="200">Has name (link_name) column?</td>
				<td><?php echo ($index_linkname>=0) ? JText::_('JYES') : JText::_('JNO') ; ?></td>
			</tr>
			<tr>
				<td width="200">Has category (cat_id) column?</td>
				<td><?php echo ($index_catid>=0) ? JText::_('JYES') : JText::_('JNO') ; ?></td>
			</tr>
			<tr>
				<td width="200">Number of listings</td>
				<td><?php echo $total_listings; ?></td>
			</tr>
		</table>

		<?php
		if( !empty($sample_imports) )
		{
		// if( $index_linkname >= 0 ) {
		// 	if( $total_listings >= 1 ) {
				
				echo '<h1>'.JText::_('COM_MTIMPORTER_DRYRUN_REPORT_SAMPLE_LISTINGS').'</h1>';
				
				// Print sample imports
				echo '<div style="width:100%;max-height:400px;overflow:auto">';
				echo '<table class="table table-striped">';

				// Print column name
				echo '<tr>';
				echo '<th style="text-align:center">#</th>';
				foreach( $fields AS $field ) {
					echo '<th style="text-align:center">';
					echo $field;
					echo '</th>';
				}
				echo '</tr>';
				
				$count=0;
				foreach( $sample_imports AS $row ) {
					echo '<tr>';
					echo '<td><strong>'.++$count.'</strong></td>';
					foreach( $fields AS $field ) {
						echo '<td>';
						if( isset($row[$field]) ) {
							echo $row[$field];
						} else {
							echo '&nbsp;';
						}
						echo '</td>';
					}
					echo '</tr>';
				}
				echo '</table>';
				echo '</div>';

		// 	}
		// }

		}
	}

	function print_csv_dryrun_system_message( $message, $type='message' ) {
		?>
		<div class="alert alert-success">
		<h4 class="alert-heading">Message</h4>
				<p><?php echo $message; ?></p>
		</div>
		<?php
	}

	function check_jcontent( &$top_level_categories ) {
		JToolBarHelper::title(  JText::_( 'COM_MTIMPORTER_MENU_NAME_JCONTENT_WEBLINKS' ), 'article-add' );
	?>
	<form action="index.php" method="post" name="adminForm">
	<table class="adminform">
		<tr valign="top">
			<td align="left">
				<p /><b>Select the top level categories you wish to import to Mosets Tree</b> and then <b>press the Import button</b	>.<p />These sections and its categories and content will be imported to the root directory. Please note that most mambot calls (eg: {mosimage}, {mospagebreak} etc.) will not work in a Mosets Tree listing.<p />Since you're importing data from another component, you need to perform "<b>Recount Cats/Listings</b>" after the import process is completed. This function will recount the number of categories and listings you have in Mosets Tree.
			</td>
		</tr>
	</table>
	<p align="left" />
	<table class="adminlist">
		<thead>
			<th width="20">
			#
			</th>
			<th width="20">
			<input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
			</th>
			<th class="title" width="64%">
			Top Level Categories
			</th>
			<th width="12%" nowrap="nowrap">
			# Categories
			</th>
			<th width="12%" nowrap="nowrap">
			# Items
			</th>
			<th width="12%" nowrap="nowrap">
			Extension
			</th>
		</thead><?php
		$k = 0;
		for ( $i=0, $n=count( $top_level_categories ); $i < $n; $i++ ) 
		{
			$row = &$top_level_categories[$i];
			JFilterOutput::objectHTMLSafe($row);
			$checked = JHtml::_('grid.checkedout',  $row, $i);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="20" align="right"><?php echo $i+1; ?></td>
				<td width="20"><?php echo $checked; ?></td>
				<td width="35%"><?php echo $row->title; ?></td>
				<td align="center"><?php echo $row->categories; ?></td>
				<td align="center"><?php echo ($row->contentitems) ? $row->contentitems : 0; ?></td>
				<td align="center"><?php echo $row->extension; ?></td>
				<?php
				$k = 1 - $k;
				?>
			</tr>
			<?php
		}
		?>
		<tfoot><th colspan="6" align="left"></th></tfoot>
		</table>
	<p align="left">
	<input type="submit" value="Import" />
	</p>
	<input type="hidden" name="task" value="import_jcontent" />
	<input type="hidden" name="option" value="com_mtimporter" />
	</form>
	
	<?php
	}
	
	function check_hotproperty( $results_count ) 
	{
		JToolBarHelper::title(  JText::_( 'COM_MTIMPORTER_TITLE_HOTPROPERTY' ), 'article-add' );
		$app = JFactory::getApplication();
		$db_prefix = $app->getCfg('dbprefix');
		
		?>
		<form action="index.php" method="post" name="adminForm">

		<div class="row-fluid">
			<div class="span6">
				<h1>Step 1: Pre-check and introduction</h1>

				<table class="adminform">
					<tr><td>
						<font color="Blue"><b>Introduction</b></font>: MT Importer will import all types, companies, agents and properties from Hot Property 1.0 to Mosets Tree version 3.5. During the import, 3 new top level categories will be created, namely 'Hot Property Properties', 'Hot Property Agents' and 'Hot Property Companies' to store the imported properties, agents and companies respectively.<br /><br />No data will be removed during or after the import process. Any data that you currently have in Mosets Tree will be retained.
				<br /><br />
				<font color="Green"><b>Requirement</b></font>: This importer requires Hot Property data from version 1.0 and an installed copy of Mosets Tree 3.5.<br /><br />
				You need to make sure the following database tables and image paths exists in order for the import to proceed:
				<ul>
					<li>Database tables: <em><?php
					$hotproperty_tables = array('agents', 'companies', 'photos', 'properties', 'properties2', 'prop_ef', 'prop_types');
					echo $db_prefix.'hp_'.implode(', '.$db_prefix.'hp_', $hotproperty_tables);
					?></em></li>
					<li>Image paths: <em><?php
					$hotproperty_path = array('std', 'thb', 'ori', 'agent', 'company');
					echo JPATH_ROOT.'/media/com_hotproperty/images/'.implode(', '.JPATH_ROOT.'/media/com_hotproperty/images/', $hotproperty_path);
					?></em></li>
				</ul>

					</td></tr>



					<tr><td><u><b>Detected data from Hot Property</b></u></td></tr>
					<tr><td>Number of Types: <b><?php echo (($results_count['types'] >= 0) ? $results_count['types'] : "<font color=\"Red\">No table found</font>") ?></b></td></tr>
					<tr><td>Number of Companies: <b><?php echo (($results_count['companies'] >= 0) ? $results_count['companies'] : "<font color=\"Red\">No table found</font>") ?></b></td></tr>
					<tr><td>Number of Agents: <b><?php echo (($results_count['agents'] >= 0) ? $results_count['agents'] : "<font color=\"Red\">No table found</font>") ?></b></td></tr>
					<tr><td>Number of Properties: <b><?php echo (($results_count['properties'] >= 0) ? $results_count['properties'] : "<font color=\"Red\">No table found</font>") ?></b></td></tr>
				</table>
			</div>
			<div class="span6">
				<h1>Step 2: Import</h1>
				<table class="adminform">
					<tr><td>
						<p>Click the "Import" button below to start the import process. You will be notified and redirected to Mosets Tree main page once the import is complete.</p><p>Since you're importing data from another component, you need to perform "<b>Recount Cats/Listings</b>" after the import process is complete to recount the number of categories and listings you have in Mosets Tree. This function is available under the "<strong>Tools</strong>" section in Mosets Tree back-end.</p>
					</td></tr>

					<tr><td>
						<p><button type="submit" class="btn btn-primary" <?php 
					if( $results_count['types'] <= 0 || $results_count['properties'] <= 0 ) {
						echo 'disabled ';
					}
					?>>Import</button>
						</p>
					</td></tr>
				</table>
			</div>
		</div>

		<input type="hidden" name="task" value="import_hotproperty" />
		<input type="hidden" name="option" value="com_mtimporter" />

		</form>
	<?php
	}

	function check_sobi2( &$pt_count, &$mt_count ) 
	{
				JToolBarHelper::title(  JText::_( 'COM_MTIMPORTER_TITLE_SOBI2' ), 'article-add' );
			?>
		<form action="index.php" method="post" name="adminForm">
		<table>
			<tr valign="top">
				<td width="33%" align="left">
				<h1>Step 1: Pre-check and warning</h1>

				<table class="adminform">
					<tr><td>
						<font color="Blue"><b>Introduction</b></font>: MT Importer will import all entries and categories from SOBI2 to Mosets Tree version 2.1.x.<br /><br />			

						<font color="Green"><b>Requirement</b></font>: You must have the <b>correct version</b> of SOBI2 2.9.x and Mosets Tree installed before you can use this Importer.<br /><br />

						<font color="Red"><b>WARNING</b></font>: This importer will delete all your current listings in Mosets Tree before importing any data from SOBI2. Please backup your database if you do not wish to delete these information.<br /><br />
					</td></tr>



					<tr><td><u><b>SOBI2</b></u></td></tr>
					<tr><td>Number of Categories: <b><?php echo (($pt_count['cats'] >= 0) ? $pt_count['cats'] : "<font color=\"Red\">No table found</font>") ?></b></td></tr>
					<tr><td>Number of Entries: <b><?php echo (($pt_count['listings'] >= 0) ? $pt_count['listings'] : "<font color=\"Red\">No table found</font>") ?></b></td></tr>

					<tr><td>&nbsp;</td></tr>

					<tr><td><u><b>Mosets Tree</b></u></td></tr>
					<tr><td>Number of Categories: <b><?php echo $mt_count['cats'] ?></b></td></tr>
					<tr><td>Number of Listings: <b><?php echo $mt_count['listings'] ?></b></td></tr>

				</table>
				</td>	

				<td width="33%" align="left">
				<h1>Step 2: Import</h1>
				<table class="adminform">
					<tr><td>
						<p>Click the "Import" button below to start the import process. You will be notified and redirected to Mosets Tree main page when the import is complete.</p><p>Since you're importing data from another component, you need to perform "<b>Recount Cats/Listings</b>" after the import process is complete. This function will recount the number of categories and listings you have in Mosets Tree.</p>
					</td></tr>

					<tr><td><p><input type="submit" value="Import" <?php 
					if( $pt_count['cats'] <= 0 && $pt_count['listings'] <= 0 ) {
						echo 'disabled ';
					}
					?>/></p></td></tr>
				</table>
				</td>

			</tr>
		</table>

		<input type="hidden" name="task" value="import_sobi2" />
		<input type="hidden" name="option" value="com_mtimporter" />

		</form>
	<?php
	} 	
	

	function check_csv() 
	{
		$app = JFactory::getApplication();
		
		$my = JFactory::getuser();
		
		JToolBarHelper::title(  JText::_( 'COM_MTIMPORTER_TITLE_CSV' ), 'article-add' );
		?>
			<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

			<div class="row-fluid">
				<div class="span8">
					<h1>Step 1: Introduction</h1>
					This Importer will import all listings from a <i>.csv</i> files. <a href="components/com_mtimporter/sample.csv">Download a sample</a> and start by adding your listings to the file. Please bear in mind the following when adding listings:
					<ul>
						<li>The first line of <i>sample.csv</i> contains the list of column names that map to Moset Tree's database. Only the first column - <b>link_name</b> is compulsory. Other columns are optional and can be safely removed. If you're removing a column, make sure you remove the corresponding values for the listings.</li>
						<li>Second line and onwards is where you insert your data. One line for each listing. In <i>sample.csv</i>, the second line is filled with one sample listing.</li>
						<li>You may use Microsoft Excel or any other word processor to edit the file. Make sure you do not save the formatting when prompted.</li>
						<li>Enter Category ID to the <b>cat_id</b> field. This information can be found when you're browsing the category. If no cat_id is specified, Importer will import the listing to Root category (0). To import a listing to more than one category, specify the category IDs separated by command. ie: "2,6,17"</li>
						<li>Enter User ID to the <b>user_id</b> field. This information can be found from your database table called <i><?php echo $app->getCfg('dbprefix'); ?>users</i>. If no user_id column is specified, the listing will be owned by you (username: <b><?php echo $my->username; ?></b>) by default.</li>
						<li>If you want a particular listing to be featured, set <b>link_featured</b> field to <i>1</i>, otherwise set it to <i>0</i>.</li>
						<li>There is no need to enter <b>link_published</b> or <b>link_approved</b>'s value. All imported listings will be published and approved automatically. </li>
						<li>If you want to import a particular column to an existing custom field, use the ID of the custom field as the column name. In the sample, the last 2 columns are mapped to custom fields with ID 25 and 26. You can locate these IDs at <a href="<?php echo JUri::base(); ?>index.php?option=com_mtree&amp;task=customfields">Custom Fields</a> page.</li>
						<li>Any column names that are not mapped to <i><?php echo $app->getCfg('dbprefix'); ?>_mt_links</i> table or a custom field ID will assigned to a new text-based custom field.</li>
						<li>If you have multiple values in a column (Checkbox or Multiple select box), separate each values with the bar character. For example - value1|value2|value3</li>
						<li>The field separator is comma (,) and the fields should be enclosed by double quote if the values contains comma.</li>
						<li>Use the Dry Run option to check if your CSV file is properly formatted. Dry run will scan your CSV file and report any errors if it finds any. Importing using the dry-run option does not write any data to your database. </li>
					</ul>

					<p />
					What this Importer <i>doesn't</i> do:
					<ul>
						<li>It does not support importing files or binary based data.</li>
						<li>It does not create categories. You have to create the categories first before starting the import.</li>
					</ul>

					<p />

					<font color="Red"><b>WARNING</b></font>: <b>PLEASE BACKUP YOUR DATABASE BEFORE PROCEEDING TO THE NEXT STEP.</b> Although we have done everything possible to minimize the risk of database corruption, accident do happens once a while. Backing up your database is the best protection to this.<br /><br />		
				</div>
				<div class="span4">
					<h1>Step 2: Import</h1>
	
					<p>Select your <i>.csv</i> file and click "Import" button below to start the import process. You will be notified and redirected to Mosets Tree main page when the import is completed.</p><p>Since you're importing data from another source, you need to perform "<b>Recount Cats/Listings</b>" after the import process is complete. This function will recount the number of categories and listings you have in Mosets Tree.</p>
		
					<hr />
					CSV File: <input type="file" name="file_csv" />
					</p>
		
					<p class="form-inline">
					<input type="checkbox" value="1" name="dryrun" id="dryrun" /> <label for="dryrun" style="position:relative;top:3px">Dry-run</label>
					</p>
					<p><button type="submit" class="btn btn-primary">Import</button></p>
		
				</div>
			</div>

			<input type="hidden" name="task" value="import_csv" />
			<input type="hidden" name="option" value="com_mtimporter" />

			</form>
	<?php
	}		

}
?>

