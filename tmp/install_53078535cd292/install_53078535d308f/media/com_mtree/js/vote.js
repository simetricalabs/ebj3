var runningTimer=new Array();
var rated=false;
jQuery(document).ready(function(){
	var ratingid = "#rating1,#rating2,#rating3,#rating4,#rating5";
	var link_rating=0;
	if( jQuery("#rating1").attr('src') != null ) {
		var src='';
		for(var i=1;i<=5;i++) {
			src = jQuery("#rating"+i).attr('src');
			if( src.indexOf( ratingImagePath+"star_10.png" ) != -1 ) {
				link_rating++;
			} else if( src.indexOf( ratingImagePath+"star_05.png" ) != -1 ) {
				link_rating=link_rating+0.5;
			} else {
				break
			}
		}
	}
	jQuery(ratingid).mouseout(function(){
		if(!rated) runningTimer.push(setTimeout('updateRating('+link_rating+',1)',1000));
	});
	jQuery(ratingid).hover(function(){
		if(!rated){
			var rating = getRating(jQuery(this).attr("id"));
			updateRating( rating, 0 );
			clearTimer();
		}
	},function(){});
});
function getRating(id){return (id.split('rating'))[1]}
function updateRating(rating,linkrating) {
	for(var i=0;i<Math.floor(rating);i++){
		jQuery("#rating"+(i+1)).attr("src",JURI_ROOT+ratingImagePath+"star_10.png");
	}
	if( (rating-i) >= 0.5 && rating > 0 ) {
		jQuery("#rating"+(i+1)).attr("src",JURI_ROOT+ratingImagePath+"star_05.png");
		i++;
	}
	for(i=Math.ceil(rating);i<5;i++){
		jQuery("#rating"+(i+1)).attr("src",JURI_ROOT+ratingImagePath+"star_00.png");
	}
	if(linkrating) {
		jQuery('#rating-msg').html(langRateThisListing);
	} else {
		jQuery('#rating-msg').html(ratingText[rating]);
	}
}
function rateListing(link_id,rating){
	if(!rated){
		jQuery.ajax({
		  type: "POST",
		  url: JURI_ROOT+"/index.php",
		  data: "option=com_mtree&task=addrating&link_id="+link_id+"&rating="+rating+"&"+mtoken+"=1&tmpl=component&format=json",
		  dataType: "json",
		  success: function(data){
				if(data.status == 'OK') {
					jQuery('#rating-msg').fadeOut("fast",function(){jQuery('#rating-msg').html(data.message);});
					jQuery('#total-votes').fadeOut("fast",function(){jQuery('#total-votes').html(data.total_votes_text);});
					jQuery('#rating-msg').fadeIn("fast");
					jQuery('#total-votes').fadeIn("fast");
				}
			}
		});
		clearTimer();
		rated=true;
		for(var i=1;i<=5;i++) jQuery("a[onclick='return(rateListing("+link_id+","+i+"))']").css('cursor','default');
	}
}
function voteHelpful(rev_id,vote){
	jQuery.ajax({
	  type: "POST",
	  url: JURI_ROOT+"/index.php",
	  data: "option=com_mtree&task=votereview&rev_id="+rev_id+"&vote="+vote+"&"+mtoken+"=1&format=json&tmpl=component",
	  dataType: "json",
	  success: function(data){
		if(data.status == 'OK') {
			var id="#rh"+rev_id;
			if(jQuery('#rhc'+rev_id).css('display')=='none'){
				jQuery(id).html(data.helpful_text);
				jQuery('#rhc'+rev_id).slideDown("fast");
				
			} else {
				jQuery(id).fadeOut("slow",function(){
					// jQuery(id).html(result[0]);
					jQuery(id).html(data.helpful_text);
					jQuery(id).fadeIn("fast");
				});
			}
			jQuery('#ask'+rev_id).html(data.message);
			jQuery('#rhaction'+rev_id).html('');
		}
	  }
	});
}
function clearTimer() {
	if(runningTimer.length>0) {
		len=runningTimer.length;
		for(i=0;i<len;i++) {
			clearTimeout(runningTimer[i]);
		}
	}
}
function fav(link_id,action){
	jQuery.ajax({
	  type: "POST",
	  url: JURI_ROOT+"/index.php",
	  data: "option=com_mtree&task=fav&link_id="+link_id+"&action="+action+"&"+mtoken+"=1&format=json&tmpl=component",
	  dataType: "json",
	  success: function(data){
		if(data.status == 'OK') {
			jQuery('#fav-msg').fadeOut("fast",function(){jQuery('#fav-msg').html(data.message);});
			jQuery('#fav-count').fadeOut("fast",function(){jQuery('#fav-count').html(data.total_fav);});
			jQuery('#fav-msg').fadeIn("fast");
			jQuery('#fav-count').fadeIn("fast");
		}
	  }
	});
}