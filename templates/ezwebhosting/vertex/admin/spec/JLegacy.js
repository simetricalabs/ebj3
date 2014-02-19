/* Joomla Only - Start */
var items = [];
//console.log(vertexNoAdd);
function parseJoomla25Form(form) {
  form.find('input').filter(function(){if(jQuery(this).parents('#vertex_fader').length < 1) return true;}).each(function(){
    jQuery(this).parents('li').children('label').remove();
    var c = jQuery(this).parents('li').children();
    var vn = jQuery(this).attr('name').replace('jform[params][', '').replace(']', '').replace('jform_params_', '');
    //console.log(jQuery.inArray(vn, vertexNoAdd), vn);
    if(jQuery.inArray(vn, vertexNoAdd) != -1) items.push({c: c, el: this, on: jQuery(this).attr('name'), vn: jQuery(this).attr('name').replace('jform[params][', '').replace(']', ''), t: jQuery(this).attr('type'), i: jQuery(this).attr('id'), v: jQuery(this).val()});
  });
  form.find('select').filter(function(){if(jQuery(this).parents('#vertex_fader').length < 1) return true;}).each(function(){
    jQuery(this).parent().children('label').remove();
    var c = jQuery(this).parent().children();
    var vn = jQuery(this).attr('name').replace('jform[params][', '').replace(']', '').replace('jform_params_', '');
    //console.log(jQuery.inArray(vn, vertexNoAdd), vn);
    if(jQuery.inArray(vn, vertexNoAdd) != -1) items.push({c: c, el: this, on: jQuery(this).attr('name'), vn: jQuery(this).attr('name').replace('jform[params][', '').replace(']', '').replace('[]', ''), t: 'select', i: jQuery(this).attr('id'), v: jQuery(this).val()});
  });
  form.find('textarea').filter(function(){if(jQuery(this).parents('#vertex_fader').length < 1) return true;}).each(function(){
    jQuery(this).parent().children('label').remove();
    var c = jQuery(this).parent().children();
    var vn = jQuery(this).attr('name').replace('jform[params][', '').replace(']', '').replace('jform_params_', '');
    //console.log(jQuery.inArray(vn, vertexNoAdd), vn);
    if(jQuery.inArray(vn, vertexNoAdd) != -1) items.push({c: c, el: this, on: jQuery(this).attr('name'), vn: jQuery(this).attr('name').replace('jform[params][', '').replace(']', ''), t: 'textarea', i: jQuery(this).attr('id'), v: jQuery(this).val()});
  });
  //console.log({items: items});
}


function togvopts(a) {
  if(a) {
    var vt = document.getElementById('vertex');
    if(a.parentNode.className.match('active')) {
      vt.style.display = 'none';
      vt.className = '';
    } else {
      jQuery('.tab-content').children('div').removeClass('active');
      vt.style.display = 'block';
    }
  }
}
var resRegex = /\{MESSAGE\}(.+?)\{\/MESSAGE}/gi;
var vsm = '<div class="alert alert-message"><a data-dismiss="alert" class="close">×</a><h4 class="alert-heading">Message</h4><div><p>Vertex settings successfully saved</p></div></div>';
function getResult(res) {
  //res = res.match(resRegex);
  //res = res[0].replace('{MESSAGE}', '');
  //res = res.replace('{/MESSAGE}', '');
  if(res.result) {
    //document.getElementById('system-message').innerHTML = vsm;
    return false;
  }
  return false;
}
function setupSubBtns(v) {
  if(v >= 3) {
    jQuery('#toolbar-apply').children('button').attr('onclick', "doPrePost('apply')");
    jQuery('#toolbar-save').children('button').attr('onclick', "doPrePost('save')");
    jQuery('#toolbar-save-copy').children('button').attr('onclick', "doPrePost('save2copy')");
  }
  if(v < 3) {
    jQuery('#toolbar-apply').children('a').attr('onclick', "doPrePost('apply')");
    jQuery('#toolbar-save').children('a').attr('onclick', "doPrePost('save')");
    jQuery('#toolbar-save-copy').children('a').attr('onclick', "doPrePost('save2copy')");
  }
}

function doPrePost(t) {
  if(t=='apply') {
    var res = doPost();
    if(res) javascript:Joomla.submitbutton('style.apply');
    return false;
  }
  if(t=='save') {
    var res = doPost();
    if(res) javascript:Joomla.submitbutton('style.save');
    return false;
  }
  if(t=='save2copy') {
    var res = doPost();
    if(res) javascript:Joomla.submitbutton('style.save2copy');
    return false;
  }
  return false;
}
function doPost() {
  var form = jQuery('#vertex_admin_form');
  var res = false;
  var style_name = jQuery('#jform_title').val();
  var style_name_val = jQuery('#jform_title').val();
  if(style_name_val != style_name) {
    clear = style_name;
    style_name = style_name_val;
  }
  jQuery.each(items, function(i, i2) {
    //console.log(i2.el);
    if(i2.t != 'textarea') {
      var c = jQuery(i2.el).parents('div.vItem').find('label').attr('class');
      //console.log(jQuery(i2.el).parents('div.vItem').find('label').attr('class'));
      if(c == 'db') jQuery(i2.el).appendTo('#style-form');
      else jQuery(i2.el).attr('name', i2.vn);
      //console.log(i2.vn);
    } else {
      if(tinyMCE) tinyMCE.execCommand('mceToggleEditor', false, i2.i);
      var c = jQuery(i2.el).parents('div.vItem').find('label').attr('class');
      //console.log(jQuery(i2.el).parents('div.vItem').find('label').attr('class'));
      if(c == 'db') jQuery(i2.el).appendTo('#style-form');
      else jQuery(i2.el).attr('name', i2.vn);
    }
  });
  jQuery.ajax({
    type: 'POST',
    url: '',
    async: false,
    dataType: 'json',
    data: {vertex: form.serializeArray(), style: jQuery('#jform_template').val(), style_name: style_name},
    success: function(json) {
      //jQuery('#s5_menu_type').appendTo('#style-form').attr('name', 'jform[params][s5_menu_type]');
      //jQuery('#xml_s5_hide_component_items').appendTo('#style-form');
      /*jQuery.each(items, function(i, i2) {
        console.log(i2.el);
        if(i2.t != 'textarea') {
          var c = jQuery(i2.el).parents('div.vItem').find('label').attr('class');
          //console.log(jQuery(i2.el).parents('div.vItem').find('label').attr('class'));
          if(c == 'db') jQuery(i2.el).appendTo('#style-form');
          else jQuery(i2.el).attr('name', i2.vn);
        } else {
          if(tinyMCE) tinyMCE.execCommand('mceToggleEditor', false, i2.i);
          jQuery(i2.el).appendTo('#style-form');
        }
      });*/
      if(getResult(json)) res = true;
    }
  });
  //return false;
  return true;
}

var s5_hide_component_items_array = {};
jQuery(document).ready(function() {
  //var s5_menu_type = jQuery('#jform_params_s5_menu_type');
  //var s5_menu_type_id = s5_menu_type.attr('id');
  //var s5_menu_type_name = s5_menu_type.attr('id');
  //s5_menu_type_id = s5_menu_type_id.replace('jform_params_', '');
  //s5_menu_type_name = s5_menu_type_name.replace('jform_params_', '');
  //s5_menu_type.attr('id', s5_menu_type_id);
  //s5_menu_type.attr('name', s5_menu_type_name);
  
  //console.log(s5_hide_component_items_array);
  
  jQuery('ul.nav-tabs').append('<li><a data-toggle="tab" href="#vertex" onclick="togvopts(this);">Vertex</a></li>');
  jQuery('a').filter(function(){
    if(this.href.match('#options')) jQuery(this).parent('li').remove();
  });
  var style_form = jQuery('#style-form');
  var new_admin = jQuery('<div />').attr('id', 'vertex').css({display: 'none', minHeight: 500});
  style_form.after(new_admin);
  new_admin.append(jQuery('<form />').attr({id: 'vertex_admin_form', method: 'post', action: ''}).addClass('vertex-admin-form'));
  
  var vertex_form = jQuery('#vertex_admin_form_in');
  var form = jQuery('#vertex_admin_form');
  form.append(vertex_form);
  parseJoomla25Form(vertex_form);
  //console.log(vertex_cmsversion);
  setupSubBtns(vertex_cmsversion);
  //var old = jQuery('#style-form');
  style_form.find('.width-40').remove();
  style_form.css({width:'50%','float':'left'}).children('.width-60').width('100%');
  //console.log(style_form, vertex_form, old);
  //var vertex_form_new = old.find('#vertex_fader');
  //jQuery(old).remove();
  
  
  /*
  jQuery('#vertex_admin_wrap').empty().append(vertex_form_new);
  
  var send_data = {state: 'start', style: jQuery('#jform_template').val(), style_name: jQuery('#jform_title').val(), vertex_xml: '/Vertex.xml', template_xml: '/templateDetails.xml', image_path: img_path};
  var style_name = jQuery('#jform_title').val();
  var clear = false;
  */
  
  jQuery.each(items, function(i, i2){
    jQuery('#vertex_admin_wrap').find('.vItem').each(function() {
      var forattr = jQuery(this).children('.vItemName').find('label').attr('for');
      if(forattr == i2.vn) {
		if(jQuery(this).children('.vItemValue').children().length < 1) jQuery(this).children('.vItemValue').append(i2.c);
      }
    });
  });
  
  var s5_hide_component_items = jQuery('#jformparamss5_hide_component_items').attr('name', 'xml_s5_hide_component_items');
  
  setTimeout(function(){
    jQuery(document).trigger('vertex');
  }, 1000);
  
  jQuery(document).bind('vertex', function () {
    //console.log('Vertex Loaded!');
    jQuery(function() {
      jQuery('.vertex-admin-panel').hide();
      jQuery('#vertex_fader').buildVertexToggle();
      jQuery('#vertex_fader').buildVertexColorpicker();
      jQuery('#vertex_fader').buildVertexTooltips(img_path);
      jQuery('#vertex_fader').fixVertexTooltips();
      jQuery('#vertex_fader').buildVertexAutoComplete();
      jQuery('#vertex_fader').buildVertexAjaxAutoComplete('google_fonts.php');
      jQuery('#vertex_fader').buildVertexMultiple(s5_hide_component_items_array);
      //jQuery('#vertex_fader').buildVertexMultiple();
      jQuery('#vertex_fader').buildVertexModuleSlider('automatic', 'manual');
      jQuery('#vertex_fader').buildVertexSelects();
      jQuery('#vertex_fader').linkMenus();
      var font_preview = jQuery('<span />').attr('id', 'font_preview').addClass('vFloatDesc').css({
        top: 0,
        left: 0,
        width: 300,
        position: 'absolute',
        'z-index': 9999,
      }).hide();
      jQuery('#vertex_admin_form').append(font_preview);
      jQuery('.vertex-admin-wrap input:text').addClass('ui-widget ui-widget-content ui-corner-all vertex-input');
      jQuery('.font-0').css('font-size','0');
      jQuery('.vertex-admin-wrap input:text').addClass('ui-widget ui-widget-content ui-corner-all vertex-input');
      var loader = jQuery('<img />').attr({src: vertex_ajax_url + '/df-images/loading.gif', height: 20, width: 220});
      jQuery('#showVertexMsg').css('height', 'auto');
      //jQuery('#showVertexMsg').empty().append(loader);
      
      jQuery(document).bind('select_change',function(o, d){
        if(d.select.attr('id') == 'xml_s5_fixed_fluid') {
          var current_body_width = document.getElementById("xml_s5_body_width").value;
          if(d.value == "Fluid") {
            if(current_body_width > 100) document.getElementById("xml_s5_body_width").value = "100";
          } else if(current_body_width < 200) {
            document.getElementById("xml_s5_body_width").value = "960";
          } else document.getElementById("xml_s5_body_width").value = current_body_width;
        }
		if(d.select.attr('id') == 'xml_s5_columns_fixed_fluid') {
          if(d.value == "Fluid") {
            document.getElementById("xml_s5_left_width").value = "20";
			document.getElementById("xml_s5_right_width").value = "20";
			document.getElementById("xml_s5_left_inset_width").value = "20";
			document.getElementById("xml_s5_right_inset_width").value = "20";
          } else {
            document.getElementById("xml_s5_left_width").value = "240";
			document.getElementById("xml_s5_right_width").value = "240";
			document.getElementById("xml_s5_left_inset_width").value = "240";
			document.getElementById("xml_s5_right_inset_width").value = "240";
          } 
        }
        //end
        if(d.select.attr('id') == 'xml_s5_hide_component_items') {
          
        }
      });
    });
  });
  jQuery('#vertex').css({width:'50%','float':'left', 'margin-top': 17}).show();
  //jQuery(old).find('font').remove();
});
/* Joomla Only - End */