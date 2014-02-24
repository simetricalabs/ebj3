<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.3.0.1671
 * @date		2014-01-23
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

jimport('joomla.html.html.bootstrap');
JHtml::_('formbehavior.chosen', 'select');

$sticky = Sh404sefHelperHtml::setFixedTemplate();

if($sticky) :?>
<div class="shl-fixed-top-hidden">&nbsp;</div>
<?php endif; ?>

<div class="shl-main-content">

<form method="post" name="adminForm" id="adminForm" class="shl-no-margin">
<?php

if($sticky) : ?>
<div class="shl-fixed span12 shl-main-searchbar-wrapper">
	<div class="span2 shl-left-separator shl-hidden-low-width">&nbsp;</div>
	<div id="shl-main-searchbar-right-block" class="span10">
	<?php
	echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);
	echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_shurl', $this->options);
	echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $this->pagination);
	echo '<div id="shl-top-pagination-container" class="pull-right"></div>';
	?>
	</div>
</div>
<?php endif; ?>

<div id="shl-sidebar-container" class="<?php echo $sticky ? 'shl-fixed' : ''; ?> span2 shl-no-margin">
<?php echo $this->sidebar; ?>
</div>

<?php if(!$sticky): ?>
<div class="span10">
<?php endif; ?>

<?php if(!$sticky): ?>
<div class="span12 shl-main-searchbar-wrapper">
	<?php
	echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_all', $this->options);

	if (!$this->slowServer)
	{
	echo ShlMvcLayout_Helper::render('com_sh404sef.filters.search_shurl', $this->options);
	}

	echo ShlMvcLayout_Helper::render('com_sh404sef.filters.limit_box', $this->pagination);

	if ($this->slowServer) : ?>
    <input type="hidden" value="" name="search_pageid" />
    <input type="hidden" value="0" name="filter_duplicate" />
    <input type="hidden" value="0" name="filter_aliases" />
	<?php endif; ?>
</div>
<?php endif; ?>

<div class="shl-main-list-wrapper span12 <?php if($sticky) echo ' shl-main-list-wrapper-padding'; ?>">

	<?php if($sticky):?>
	<div class="span2 shl-hidden-low-width"></div>
	<div class="span10">
	<?php
		endif;
		echo ShlHtmlBs_Helper::alert($this->helpMessage, $type = 'info', $dismiss = true);
	?>

	<div id="sh-message-box"></div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th class="shl-list-id">&nbsp;
          </th>

          <th class="shl-list-check">
            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
          </th>

          <th class="shl-list-shurl">
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_PAGE_ID'), 'pageid', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>

          <th>
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_URL'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>

          <th class="shl-list-icon">
            <?php echo JText::_( 'COM_SH404SEF_IS_CUSTOM'); ?>
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <?php echo '<div id="shl-bottom-pagination-container">' . $this->pagination->getListFooter() . '</div>'; ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <?php
          $k = 0;
          if( $this->itemCount > 0 ) {
            for ($i=0; $i < $this->itemCount; $i++) {

              $url = &$this->items[$i];
              $checked = JHtml::_( 'grid.id', $i, $url->pageidid);
              $custom = !empty($url->newurl) && $url->dateadd != '0000-00-00' ? ShlHtmlBs_Helper::iconglyph('', 'wrench', JText::_('COM_SH404SEF_CUSTOM_URL_LINK_TITLE')) : '&nbsp;';
        ?>

        <tr>

          <td class="shl-list-id" >
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>

          <td class="shl-list-check" >
            <?php echo $checked; ?>
          </td>

          <td class="shl-list-shurl">
            <?php echo empty($url->pageid) ? '' : ShlHtmlBs_Helper::badge($this->escape($url->pageid), 'info'); ?>
          </td>

          <td class="shl-list-sef">
            <?php
              echo '<input type="hidden" name="metaid['.$url->id.']" value="'.(empty($url->metaid) ? 0 : $url->metaid).'" />';
              echo '<input type="hidden" name="newurls['.$url->id.']" value="'.(empty($url->nonsefurl) ? '' : $this->escape( $url->nonsefurl)).'" />';
              // link to full meta edit
              $anchor = empty($url->oldurl) ? '(-)' : $this->escape( $url->oldurl);
              $anchor .= '<br/><i>(' . $this->escape( $url->nonsefurl) . ')</i>';
              $params = array();
              $linkData = array( 'c' => 'editurl', 'task' => 'edit', 'view' => 'editurl', 'startOffset' => '1','cid[]' => $url->id, 'tmpl' => 'component');
              $targetUrl = Sh404sefHelperGeneral::buildUrl($linkData);
              $displayedUrl = empty($url->oldurl) ? $url->nonsefurl : $url->oldurl;
              $params['linkTitle'] = JText::_('COM_SH404SEF_MODIFY_META_TITLE') . ' ' . $this->escape($displayedUrl);
              $modalTitle = '';
              $params['linkClass'] = 'shl-list-sef';
              $params['linkType'] = 'a';
              $name = '-editurl-' . $url->id;
              echo ShlHtmlModal_helper::modalLink($name, $anchor, $targetUrl, Sh404sefFactory::getPConfig()->windowSizes['editurl']['x'], Sh404sefFactory::getPConfig()->windowSizes['editurl']['y'], $top = 0, $left = 0, $onClose = '', $modalTitle, $params);

              // small preview icon
              $sefConfig = & Sh404sefFactory::getConfig();
              $link = JURI::root() . ltrim( $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . (empty($url->oldurl) ? $url->nonsefurl : $url->oldurl);
              echo '&nbsp;<a href="' . $this->escape($link) . '" target="_blank" title="' . JText::_('COM_SH404SEF_PREVIEW') . ' ' . $this->escape($url->oldurl) . '">';
              echo '<img src=\'components/com_sh404sef/assets/images/external-black.png\' border=\'0\' alt=\''.JText::_('COM_SH404SEF_PREVIEW').'\' />';
              echo '</a>';
            ?>
          </td>

          <td class="shl-list-icon">
            <?php echo $custom;?>
          </td>

        </tr>
        <?php
        $k = 1 - $k;
      }
    } else {
      ?>
        <tr>
          <td class="center shl-middle" colspan="5">
            <?php echo JText::_( 'COM_SH404SEF_NO_URL' ); ?>
          </td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
    <?php if($sticky):?>
    </div>
    <?php endif;?>
</div>

<?php if(!$sticky): ?>
</div>
<?php endif; ?>
    <input type="hidden" name="c" value="pageids" />
    <input type="hidden" name="view" value="pageids" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>" />
    <input type="hidden" name="format" value="html" />
    <input type="hidden" name="shajax" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
