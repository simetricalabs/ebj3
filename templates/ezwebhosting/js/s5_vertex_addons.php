<script type="text/javascript">//<![CDATA[

<?php if ($s5_login_url != "") { ?>
window.addEvent('domready', function() {
	function s5_login_url() {
		window.location = "<?php echo $s5_login_url; ?>";
	}
	if (document.getElementById("s5_login")) {
	document.getElementById("s5_login").className = "";
	document.getElementById("s5_login").onclick = s5_login_url;
	if (document.getElementById("s5_login").href) {
		document.getElementById("s5_login").href = "javascript:;";
	}
	}
});
<?php } ?>

<?php if ($s5_register_url != "") { ?>
window.addEvent('domready', function() {
	function s5_register_url() {
		window.location = "<?php echo $s5_register_url; ?>";
	}
	if (document.getElementById("s5_register")) {
	document.getElementById("s5_register").className = "";
	document.getElementById("s5_register").onclick = s5_register_url;
	if (document.getElementById("s5_register").href) {
		document.getElementById("s5_register").href = "javascript:;";
	}
	}
});
<?php } ?>

//]]></script>