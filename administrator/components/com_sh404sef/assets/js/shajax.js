/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2014
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.3.0.1671
 * @date    2014-01-23
 */

function shAjaxHandler(task, options, closewindow) {

  var form = jQuery('#adminForm');
	jQuery('#adminForm input[name=task]').val(task);
	jQuery('#adminForm input[name=format]').val('raw');
	jQuery('#adminForm input[name=shajax]').val('1');

	// Create a progress indicator
	var update = jQuery("#sh-progress-cpprogress")
	update.empty();
	update.html("<div class='sh-ajax-loading'>&nbsp;</div>");

	// Set the options of the form"s Request handler.
	var onSuccessFn = function(response) {
	  
	  // restore form
	  jQuery('#adminForm input[name=task]').val('');
	  jQuery('#adminForm input[name=format]').val('html');
	  jQuery('#adminForm input[name=shajax]').val('0');
	  
		//alert(response);
		var root, status, message;
		try {
			root = response.documentElement;
			status = root.getElementsByTagName("status").item(0).firstChild.nodeValue;
			message = "<div class='alert alert-success'>" + root.getElementsByTagName("message").item(0).firstChild.nodeValue + '</div>';
		} catch (err) {
			status = 'failure';
			message = "<div class='alert alert-error'>Sorry, something went wrong on the server while performing this action. Please retry or cancel.</div>";
		}

		// remove progress indicator
		var update = jQuery("#sh-progress-cpprogress").empty();
		var update = jQuery("#sh-message-box2").empty();

		// insert results
		if (status == "success") {
			update.html(message);
			if (closewindow) {
				setTimeout("shlBootstrap.closeModal()", 1500);
			} else {
				setTimeout("jQuery('#sh-message-box2').empty()", 3000);
			}
		} else if (status == 'redirect') {
			setTimeout("parent.window.location='" + message + "';", 100);
			shlBootstrap.closeModal();
		} else {
			jQuery('#sh-message-box').html(message);
			setTimeout("jQuery('#sh-message-box2').empty();", 5000);
		}

	};

	// Send the form.
	jQuery.post('index.php', form.serialize())
  .always(onSuccessFn);
};
