jQuery(document).ready(function(){
	toggleMcBut(active_cat);
	enforceSecCatLimit();
	jQuery('#browsecat').click(function(){
		cc(jQuery(this).val());
	});
	if(jQuery("#sortableimages").length>0)
	{
		jQuery("#sortableimages").sortable();
		jQuery('ul#sortableimages li input:checkbox').click(function(){
			enforceImageLimit();
			if(!jQuery(this).attr('checked')) {
				jQuery(this).parent().css('opacity',0.3);
			} else {
				jQuery(this).parent().css('opacity',1);
			}
		});
		enforceImageLimit();
	}
	if(jQuery("#show_map").val() == '0')
	{
		toggleMap();
	}

	jQuery('#adminForm .controls input, #adminForm .controls textarea, #adminForm .controls select').on({
		  'change focusout': function(event){
			if(event.currentTarget.id != 'browsecat')
			{
				onChangeMTFieldsInput(event);
			}
		  }
	});
});
function onChangeMTFieldsInput(event){
	var target=event.currentTarget;
	var id=event.currentTarget.id;
	var field_id=getCfId(target.id);
	var validation_failed=false;
	if(!mtValidate(target)){
		validation_failed=true;
		mtShowAdvice('cf'+field_id);
	}
	if(target.required && !mtValidateIsEmpty(target)){
		validation_failed=true;
	}
	if(target.type=='checkbox' && target.name.substr(0,5)=='keep_'){
		var file_element=document.adminForm['cf'+field_id];
		if(
			!target.checked
			&&
			file_element.required 
			&& 
			!mtValidateIsEmpty(file_element)
		){
			validation_failed=true;
		}
		
	}
	if(validation_failed){
		addValidationErrorHighlight(field_id);
	}else{
		mtRemoveAdvice(id);
		removeValidationErrorHighlight(field_id);
	}
}
function submitbutton(pressbutton){
	var validation_all_passed=true;
	var scroll = new Fx.SmoothScroll({links:'adminForm',wheelStops:false})
	var first_invalidated_input_id=0;
	var first_invalidated_input_element;
	
	jQuery('#adminForm .controls input[id^="cf"]').each(function(index,el){
		var target=el;
		var validation_failed=false;
		var id=getCfId(target.id);
		var field_id=getCfId(target.id);
		if(!mtValidate(el)){
			validation_failed=true;
			validation_all_passed=false;
			mtShowAdvice(target.id);
		}
		if(target.required && !mtValidateIsEmpty(target)){
			validation_failed=true;
			validation_all_passed=false;
		}
		if(validation_failed){
			addValidationErrorHighlight(field_id);
			if(first_invalidated_input_id==0){
				first_invalidated_input_id=field_id;
				first_invalidated_input_element=el;
			}
		}else{
			mtRemoveAdvice(id);
			removeValidationErrorHighlight(field_id);
		}
	});
	if(validation_all_passed){
		var form = document.adminForm;
		Joomla.submitform(pressbutton, document.getElementById('adminForm'));
	}else{
		var li_element=document.getElementById('field_'+first_invalidated_input_id);
		scroll.toElement(li_element);
		first_invalidated_input_element.focus();
		first_invalidated_input_element.select();
		// jQuery('#validate-advice-'+first_invalidated_input_id).fadeToggle('fast').fadeToggle('slow');
	}
}
function mtShowAdvice(id){
	if(jQuery('#validate-advice-'+id).length==0){
		jQuery('#'+id).after('<span class="validation-advice" id="validate-advice-'+id+'">'+validations[id].message+'</strong>');
	}
}
function mtFlashAdvice(id){
	jQuery('#validate-advice-'+id).fadeToggle('fast').fadeToggle('slow');
}
function mtRemoveAdvice(id){
	jQuery('#validate-advice-'+id).remove();
}
function mtValidateNonCheckboxesRadios(target){
	var id=target.id;
	var field_id=id.slice(2);
	
	var advice_id='validate-advice-'+id;
	if(!mtValidate(target)) {
		if(jQuery('#'+advice_id).length==0){
			mtShowAdvice(id);
			addValidationErrorHighlight(field_id);
		}else{
			mtFlashAdvice(id);
		}
	} else {
		mtRemoveAdvice(id);
		removeValidationErrorHighlight(field_id);
	}
	if(jQuery('#'+id).attr('required')=='required'){
		if(target.value==''){
			if(target.type=='file'){
				if(
					(
						(typeof document.adminForm['keep_'+target.name] == 'undefined')
						||
						(typeof document.adminForm['keep_'+target.name] == 'object' && document.adminForm['keep_'+target.name].checked == false)
					)
				){
					return false;
				}else{
					return true;
				}
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}
function mtValidateCheckboxesRadios(target){
	var field_id=target.id.slice(2).split('_').shift().toInt();
	var field_name=target.id.split('_').shift();
	if(
		jQuery('#adminForm .controls input[required][name="'+jQuery('#'+target.id).attr('name')+'"]:checked').length==0
	) {
		return false;
	} else {
		return true;
	}
}
function mtValidateIsEmpty(target){
	if(target.type=='checkbox'||target.type=='radio'){
		return mtValidateCheckboxesRadios(target);
	}else{
		return mtValidateNonCheckboxesRadios(target);
	}
}
function mtValidate(target){
	field_validation=validations[target.id];
	if(typeof field_validation=='object' && typeof field_validation.execute=='function' && target.value != '')
	{
		validate=field_validation.execute(target);
		if(!validate){
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}
function runPresubmitFunctions(form,validation_fields){
	for(var index=0;index<validation_fields.length;index++)
	{
		var presubmitFunction=presubmitFunctions[validation_fields[index].id];
		if(typeof presubmitFunction=='function')
		{
			presubmitFunction(form);
		}
		
	}
}
function addValidationErrorHighlight(field_id){
	jQuery('#caption_'+field_id+' label').addClass('invalid');
	jQuery('#input_'+field_id+' input').addClass('invalid');
}
function removeValidationErrorHighlight(field_id){
	jQuery('#caption_'+field_id+' label').removeClass('invalid');
	jQuery('#input_'+field_id+' input').removeClass('invalid');
}
function isEmpty(element){
	var fe=document.adminForm.elements[element];
	if(typeof fe == 'undefined') {
		fe=document.adminForm.elements[element+'[]'];
	}
	if(fe.type==undefined){
		for(var i=0;i<fe.length;i++) {
			if(fe[i].checked){return false;}
		}
		return true;
	} else if ((fe.type=='radio'||fe.type=='checkbox') && fe.checked==false) {
		return true;	
	} else if (fe.value=='') {
		return true;
	} else {
		return false;
	}
}
function addAtt() {
	if((attCount + jQuery('#uploadimages li input:checkbox:checked').length)<=maxAtt) {
		var newLi = document.createElement("LI");
		newLi.id="att"+attNextId;
		newLi.style.float="none";
		var newFile=document.createElement("INPUT");
		newFile.className="";
		newFile.name="image[]";
		newFile.type="file";
		newFile.size="28";
		newFile.multiple=true;
		newLi.appendChild(newFile);
		var newLink=document.createElement("A");
		newLink.href="javascript:remAtt("+attNextId+")";
		newLink.appendChild(document.createTextNode(Joomla.JText._('COM_MTREE_REMOVE')));
		newLi.appendChild(newLink);
		gebid('uploadimages').appendChild(newLi);
		attCount++;
		attNextId++;
	}
	enforceImageLimit();
}
function remAtt(id) {gebid('uploadimages').removeChild(gebid('att'+id));attCount--;enforceImageLimit();}
function enforceImageLimit() {
	var attTotal = attCount + jQuery('#uploadimages li input:checkbox:checked').length;
	if( typeof maxAtt != 'undefined' && attTotal>=maxAtt) {
		jQuery('#add_att').text('');
		if(attTotal>maxAtt) {
			remAtt(--attNextId);
		}
	} else {
		jQuery('#add_att').text(Joomla.JText._('COM_MTREE_ADD_AN_IMAGE'));
	}
}
function hasExt(string,ext){
	var ext=string.match(new RegExp("("+ext+")$","i"));
	if(string != '' && ext==null){
		return false;
	}else{
		return true;
	}
}
function checkImgExt(attCount,img){
	if(attCount==1) {
		if(img.val() != '' && !hasExt(img.val(),'png|jpe?g|gif')){
			return false;
		}
	} else {
		img.each(function(i){
			if(jQuery(this).val() != '' && !hasExt(jQuery(this).val(),'png|jpe?g|gif')){
				return false;
			}
		 });

	}
	return true;
}
function toggleMap() {
	jQuery('#mapcon').slideToggle(function(){
		if(jQuery('#mapcon').css('display') == 'none') {
			jQuery('#togglemap').html(Joomla.JText._('COM_MTREE_SHOW_MAP'));
			jQuery('#show_map').val(0);
		} else {
			jQuery('#togglemap').html(Joomla.JText._('COM_MTREE_REMOVE_MAP'));
			jQuery('#show_map').val(1);
		}
	});
}
function getCfId(name){
	if(name.substr(0,2)=='cf'){
		var str=name;
	}else if(name.substr(0,5)=='keep_'){
		var str=name.substr(5);
	}
	return str.slice(2).split('_').shift().toInt();
}