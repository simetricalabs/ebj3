<script type="text/javascript">
/*! Jquery scrollto function */
(function(a,c){var b=(function(){var d=c(a.documentElement),f=c(a.body),e;if(d.scrollTop()){return d}else{e=f.scrollTop();if(f.scrollTop(e+1).scrollTop()==e){return d}else{return f.scrollTop(e)}}}());c.fn.smoothScroll=function(d){d=~~d||400;return this.find('a[href*="#"]').click(function(f){var g=this.hash,e=c(g);if(location.pathname.replace(/^\//,'')===this.pathname.replace(/^\//,'')&&location.hostname===this.hostname){if(e.length){f.preventDefault();b.stop().animate({scrollTop:e.offset().top},d,function(){location.hash=g})}}}).end()}}(document,jQuery));
jQuery(document).ready(function(){
		jQuery('html').smoothScroll(700);
	});
</script>
<div id="s5_scrolltopvar">
<a href="#s5_scrolltotop" class="s5_scrolltotop"><?php if ($template_date == "June 2011") {?>Scroll Up<?php } ?></a>
</div>
