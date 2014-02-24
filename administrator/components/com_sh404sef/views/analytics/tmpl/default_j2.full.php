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

?>

<!-- start analytics panel markup -->
<div class="sh404sef-analytics">


  <?php

    if($this->isAjaxTemplate) :

  ?>
      <dl id="system-message">
      <dt class="error"></dt>
      <dd class="error message fade">
        <div id="sh-error-box">
      <?php if (!empty( $this->errors)) : ?>
          <div id="error-box-content">
            <ul>
            <?php
              foreach ($this->errors as $error) :
                echo '<li>' . $error . '</li>';
              endforeach;
            ?>
            </ul>
          </div>
        <?php endif; ?>
        </div>
      </dd>
      </dl>

      <dl id="system-message">
      <dt class="message"></dt>
      <dd class="message message fade">
      <div id="sh-message-box">
      <?php if (!empty( $this->message)) : ?>
        <ul>
          <li><div id="message-box-content"><?php if (!empty( $this->message)) echo $this->message; ?></div></li>
        </ul>
        <?php endif; ?>
        </div>
      </dd>
      </dl>

  <?php

      echo $this->loadTemplate( $this->joomlaVersionPrefix . '_' . $this->options['report']);
      ?>
      <div class="sh404sef-footer-container">
      	<?php echo $this->footerText; ?>
      </div>
      <?php
    else:

      // this is one of the ajax calls to fetch one of the bits making up the reports
      // headers, global, visits, perf, top5referrers, top5urls
      // if there was an error while fetching data (due to credentials not set for instance)
      // we don't display anything, except for the 'headers' request, which is
      // precisely the template where the 'error' or information message
      // will be displayed
      if (!empty($this->analytics->status) || $this->options['subrequest'] == 'headers') {

        echo $this->loadTemplate( $this->joomlaVersionPrefix . '_' . $this->options['subrequest']);

      }

    endif;

	  ?>

</div>
<!-- end analytics panel markup -->
