function loadcat(){
	cc(this.value);
}
function addSecCat(){
	if(active_cat>=0){
		if(jQuery('#other_cats').val()!=''){
			jQuery('#other_cats').val(jQuery('#other_cats').val()+','+active_cat);
		}else{
			jQuery('#other_cats').val(active_cat);
		}
		var newLi = document.createElement("LI");
		newLi.id = 'lc'+active_cat;
		var newLink=document.createElement("A");
		newLink.href="javascript:remSecCat("+active_cat+")";
		newLink.appendChild(document.createTextNode(Joomla.JText._('COM_MTREE_REMOVE')));
	    newLi.appendChild(newLink);
	    var liTxt = document.createTextNode(jQuery('#mc_active_pathway').text());
	    newLi.appendChild(liTxt);
		gebid('linkcats').appendChild(newLi);					
	}		
	toggleMcBut(active_cat);
	enforceSecCatLimit();
}
function enforceSecCatLimit() {
	var secCatTotal = jQuery('#other_cats').val().split(',').length;
	if( typeof maxSecCat != 'undefined' && jQuery('#other_cats').val() != '' && secCatTotal>=maxSecCat) {
		if(gebid('mcbut2')) { gebid('mcbut2').disabled=true; }
	} else {
		if(gebid('mcbut2')) { gebid('mcbut2').disabled=false; }
	}
}
function remSecCat(cat_id){
	var oc=jQuery('#other_cats').val().split(',');
	var new_oc=new Array();
	if(oc!=''){
		for (var i=0; i < oc.length; i++) {
			if(oc[i]!=cat_id) {
				new_oc.push(oc[i]);
			}
		};
	}
	jQuery('#other_cats').val(new_oc.join(','));
	var li=gebid('lc'+cat_id);
	li.parentNode.removeChild(li);
	toggleMcBut(active_cat);
	enforceSecCatLimit();
}
function updateMainCat(){
	var linkId = jQuery('#adminForm input[name=link_id]').val();
	var newLi = document.createElement("LI");
    var liTxt = document.createTextNode(jQuery('#mc_active_pathway').text());
	newLi.id = 'lc'+active_cat;
    newLi.appendChild(liTxt);
	var i=0;
	do {
		var oldLi = gebid('linkcats').childNodes[i++];
	} while(oldLi.nodeType != 1)
	gebid('linkcats').replaceChild(newLi,oldLi);
	jQuery('#lc'+active_cat).html(jQuery('#lc'+active_cat).html());
	document.adminForm.cat_id.value=active_cat;
	toggleMcBut(active_cat);
	togglemc();
	
	if(typeof(cachedFields) == 'undefined') {
		cachedFields = updateCachedFields();
	} else {
		var tmp = updateCachedFields();
		for (attrname in tmp) { 
			cachedFields[attrname] = tmp[attrname];
		}
	}
	jQuery.getJSON(JURI_ROOT+"?option=com_mtree&task=fields.list&cat_id="+active_cat+"&link_id="+linkId+"&format=json"+(jQuery('input[name=is_admin]').val()?'&is_admin=1':''), function(data) {
	  var items = [];
	  jQuery.each(data, function(key, field) {
		if(typeof field.jsValidation=='string'){
			var jsValidation = field.jsValidation;
			validations['cf'+field.id] = eval('('+field.jsValidation+')');
		}
		if(typeof(cachedFields['field_'+field.id])=='object') {
		    items.push(
				'<div class="control-group '+field.fieldTypeClassName+'" id="field_'+field.id+'">'
				+cachedFields['field_'+field.id].innerHTML
				+'</div>'
				);
		} else {
		    items.push(
				'<div class="control-group '+field.fieldTypeClassName+'" id="field_'+field.id+'">'
				+'<div class="control-label" id="caption_'+field.id+'">'
				+'<label for="'+field.name+'">' 
				+ (field.caption?field.caption:'') 
				+(field.isRequired?'<span class="star">&#160;*</span>':'')
				+'</label>'
				+'</div>'
				+'<div class="controls" id="input_' + field.id + '">'
				+(field.modPrefixText?field.modPrefixText:'') + field.inputHTML + (field.modSuffixText?field.modSuffixText:'')
				+'</div>'
				+'</div>'
				);
		}
	  });
	html = items.join('');

	jQuery('#mtfields div[id^="field_"]').remove();
	jQuery(html).appendTo('#mtfields');
	jQuery('#mtfields input, #mtfields select, #mtfields textarea').bind('change', function(event) {onChangeMTFieldsInput(event);});
	});
}
function updateCachedFields() {
	var fields = jQuery('#mtfields div[id^=field_]');
	var fields_length=fields.length;
	var results = {};
	for(var i=0;i<fields_length;i++)
	{
		field_id=fields[i].id.replace('field_','');
		results['field_'+field_id]=fields[i];
	}
	return results;
}

function cc(parent_cat_id){
	if(parent_cat_id >= 0 && parent_cat_id != '') {
			jQuery.getJSON( JURI_ROOT+"?option=com_mtree&task=ajax&task2=categories.list&cat_id="+parent_cat_id+"&format=json"+(jQuery('input[name=is_admin]').val()?'&is_admin=1&no_html=1':''), function(data) {
				if(data.length > 0)
				{
					clearList('browsecat');
					var c=0;
					jQuery.each(data, function(key, field) {
						switch(field.type)
						{
							case 'pathway':
								jQuery('#mc_active_pathway').html(field.text);
								break;
							case 'back':
							case 'category':
								gebid('browsecat').options[c-1] = new Option(field.text,field.cat_id);
								break;
							
						}
						c++;
					});
					active_cat = parent_cat_id;
					switch(jQuery('#adminForm input[name="task"]').val()){
						case 'editlink':
						case 'savelisting':
						case 'editcat':
							toggleMcBut(parent_cat_id);
							break;
						case 'cats_copy':
						case 'cats_move':
						case 'links_move':
						case 'links_copy':
							jQuery('#adminForm input[name="new_cat_parent"]').val(active_cat);
							break;
					}
				}
		});	
	}
}
function toggleMcBut(cat_id){
	if(gebid('mcbut1') != null) {
		if(inOtherCats(cat_id)){
			gebid('mcbut1').disabled=true;
			jQuery('#mc_active_pathway').css('background-color','#f9f9f9');
			jQuery('#mc_active_pathway').css('color','#C0C0C0');
		}else{
			gebid('mcbut1').disabled=false;
			jQuery('#mc_active_pathway').css('background-color','#FFF');
			jQuery('#mc_active_pathway').css('color','#000');
		}		
	}
}
function inOtherCats(target){
	if(target==jQuery('#adminForm input[name="cat_id"]').val()) {
		return true;
	}
	var other_cats = jQuery('#other_cats').val();
	if(other_cats != ''){
		other_cats = other_cats.split(',');
		for (var i=0; i < other_cats.length; i++) {
			if(other_cats[i] == target){
				return true;
			}
		}
	}
	return false;
}
function togglemc() {
	if(jQuery('#mc_con').css('display')=='none'){
		jQuery('#mc_con').slideDown('slow');
	} else {
		jQuery('#mc_con').slideUp('slow');
	}
}
function gebid(id){return document.getElementById(id);}
function clearList(id) {
	var clength = gebid(id).length;
	for(var i=(clength-1);i>=0;i--) {gebid(id).remove(i);}
}