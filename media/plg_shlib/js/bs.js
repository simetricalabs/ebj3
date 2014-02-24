/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2013
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.2.8.369
 * @date    2013-12-21
 */
var shlBootstrap = (function($) {
  var tmp = {
    updateBootstrap : function() {
      $('*[rel=tooltip]').tooltip();
      $('select').chosen({
        disable_search_threshold : 10,
        allow_single_deselect : true
      });

      // Turn radios into btn-group
      $('.radio.btn-group label').addClass('btn');
      $(".btn-group label:not(.active)").click(function() {
        var label = $(this);
        var input = $('#' + label.attr('for'));

        if (!input.prop('checked')) {
          label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
          if (input.val() == '') {
            label.addClass('active btn-primary');
          } else if (input.val() == 0) {
            label.addClass('active btn-danger');
          } else {
            label.addClass('active btn-success');
          }
          input.prop('checked', true);
        }
      });
      $(".btn-group input[checked=checked]").each(function() {
        if ($(this).val() == '') {
          $("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
        } else if ($(this).val() == 0) {
          $("label[for=" + $(this).attr('id') + "]").addClass('active btn-danger');
        } else {
          $("label[for=" + $(this).attr('id') + "]").addClass('active btn-success');
        }
      });
    },

    /** Modals handling */
    canOpenModal : true,
    modals : {},
    modalTemplate : "<div class='shmodal hide ' id='{%selector%}'><div class='shmodal-header'><button type='button' class='close' data-dismiss='modal'>×</button>{%title%}</div><div id='{%selector%}-container'></div></div>",

    selectedIdsUrl : '',

    setSelectedIdsUrl : function(ids) {
      shlBootstrap.selectedIdsUrl = ids;
    },

    getModalUrl : function(baseUrl) {
      return baseUrl + shlBootstrap.selectedIdsUrl;
    },

    closeModal : function() {
      var el = $('div.shmodal-header button.close');
      el.click();
    },

    setModalTitleFromModal : function(title) {
      var el = window.parent.jQuery('div.shmodal-header:visible');
      el.html('<h3>' + title + '</h3>');
    },

    registerModal : function(modal) {
      var tmp = {};
      tmp = $.extend({
        selector : '',
        title : '',
        url : '',
        width : 0.5,
        height : 0.5,
        onclose : '',
        footer : '',
        backdrop : true,
        keyboard : false
      }, modal);
      
      // then store
      shlBootstrap.modals[tmp.selector] = tmp;
    },

    renderModal : function(index, modal) {
      // insert modal markup in page
      $(shlBootstrap.modalTemplate.replace(new RegExp('{%selector%}', 'g'), modal.selector).replace('{%title%}', modal.title ? '<h3>'+modal.title+'</h3>' : '&nbsp;'))
          .appendTo('#shl-modals-container');

      // add an onshow handler
      $('#' + modal.selector).on(
          'show',
          function() {
            if (!shlBootstrap.canOpenModal)
              return false;
            var modalOptions = shlBootstrap.modals[this.id];
            // compute width and heigth
            var mW = modalOptions.width < 1 ? $(window).width() * modalOptions.width : modalOptions.width;
            var mH = modalOptions.height < 1 ? $(window).height() * modalOptions.height : modalOptions.height;
            var targetUrl = shlBootstrap.getModalUrl(modalOptions.url);
            var modalContainer = jQuery('#' + modalOptions.selector);
            jQuery('#' + modalOptions.selector + '-container').html(
                '<div class="shmodal-body" style="height:' + mH + 'px; width:' + mW
                    + 'px;"><iframe class="iframe" src="' + targetUrl + '" height="' + mH + '" width="'
                    + mW + '" ></iframe></div>' + modalOptions.footer);
            var h = modalContainer.height();
            var w = modalContainer.width();
            var pageheight = jQuery(window).height();
            var pagewidth = jQuery(window).width();
            var shleft = (pagewidth - w) / 2;
            var shtop = (pageheight - h) / 2;
            jQuery('#' + modalOptions.selector).css({
              'margin-top' : shtop,
              'top' : '0'
            });
            jQuery('#' + modalOptions.selector).css({
              'margin-left' : shleft,
              'left' : '0'
            });
          });

      $('#' + modal.selector).on('hide', function() {
        var modalOptions = shlBootstrap.modals[this.id];
        if (modalOptions.onclose)
          modalOptions.onclose();
        jQuery('#' + this.id + '-container').innerHTML = '';
      });

      // run BS modal code
      $('#' + modal.selector).modal({
        'keyboard' : modal.keyboard,
        'backdrop' : modal.backdrop,
        'show' : false
      });
    },

    renderModals : function() {
      $.each(shlBootstrap.modals, shlBootstrap.renderModal);
    },

    /* input char counters */
    inputCounters: {},
    registerInputCounter : function(counter) {
      var defaults = {  
          maxCharacterSize: -1,  
          originalStyle: 'badge-success',
          warningStyle: 'badge-warning',  
          errorStyle: 'badge-important',
          warningNumber: 20,
          errorNumber: 40,
          displayFormat: '#left',
          style: 'shl-char-counter',
          title:''
        }; 
      counter = $.extend(defaults, counter);
      
      // then store
      shlBootstrap.inputCounters[counter.selector] = counter;
    },
    
    renderInputCounters : function() {
      $.each(shlBootstrap.inputCounters, shlBootstrap.renderInputCounter);
    },
    
    renderInputCounter : function(index, counter) {
      $('#' + counter.selector).textareaCount(counter);
    },

    onReady : function() {
      $("<div id='shl-modals-container'></div>").appendTo("body");
      shlBootstrap.renderModals();
      shlBootstrap.renderInputCounters();
    },
    
  };
  jQuery(document).ready(tmp.onReady);
  return tmp;
})(jQuery);

/*
 * jQuery Textarea Characters Counter Plugin v 2.0
 * Examples and documentation at: http://roy-jin.appspot.com/jsp/textareaCounter.jsp
 * Copyright (c) 2010 Roy Jin
 * Version: 2.0 (11-JUN-2010)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Requires: jQuery v1.4.2 or later
 */
(function($){  
  $.fn.textareaCount = function(options, fn) {   
    var defaults = {  
      maxCharacterSize: -1,  
      originalStyle: 'originalTextareaInfo',
      warningStyle: 'warningTextareaInfo',  
      errorStyle: 'errorTextareaInfo',
      warningNumber: 20,
      errorNumber: 40,
      displayFormat: '#input characters | #words words',
      title:''
    };  
    var options = $.extend(defaults, options);
    
    var container = $(this);
    container.wrap("<div class='shl-char-counter-wrapper'></div>");
    $("<div class='charleft badge " + options.style + "' " + (options.title ? "title='" + options.title + "'" : "") + ">&nbsp;</div>").insertAfter(container);
    
    var charLeftInfo = getNextCharLeftInformation(container);
    charLeftInfo.addClass(options.originalStyle);
    
    var numInput = 0;
    var maxCharacters = options.maxCharacterSize;
    var numLeft = 0;
    var numWords = 0;
        
    container.bind('keyup', function(event){limitTextAreaByCharacterCount();})
         .bind('mouseover', function(event){setTimeout(function(){limitTextAreaByCharacterCount();}, 10);})
         .bind('paste', function(event){setTimeout(function(){limitTextAreaByCharacterCount();}, 10);});
    
    // initial display
    limitTextAreaByCharacterCount();
    
    function limitTextAreaByCharacterCount(){
      charLeftInfo.html(countByCharacters());
      //function call back
      if(typeof fn != 'undefined'){
        fn.call(this, getInfo());
      }
      return true;
    }
    
    function countByCharacters(){
      var content = container.val();
      var contentLength = content.length;
      
      //Start Cut
      if(options.maxCharacterSize > 0){
        //If copied content is already more than maxCharacterSize, chop it to maxCharacterSize.
        if(contentLength >= options.maxCharacterSize) {
          content = content.substring(0, options.maxCharacterSize);         
        }
        
        var newlineCount = getNewlineCount(content);
        
        // newlineCount new line character. For windows, it occupies 2 characters
        var systemmaxCharacterSize = options.maxCharacterSize - newlineCount;
        if (!isWin()){
           systemmaxCharacterSize = options.maxCharacterSize
        }
        if(contentLength > systemmaxCharacterSize){
          //avoid scroll bar moving
          var originalScrollTopPosition = this.scrollTop;
          container.val(content.substring(0, systemmaxCharacterSize));
          this.scrollTop = originalScrollTopPosition;
        }
        charLeftInfo.removeClass(options.warningStyle);
        charLeftInfo.removeClass(options.originalStyle);
        if(contentLength > options.errorNumber){
          charLeftInfo.addClass(options.errorStyle);
        } else if(contentLength > options.warningNumber){
          charLeftInfo.addClass(options.warningStyle);
        } else {
          charLeftInfo.addClass(options.originalStyle);
        }
        
        numInput = container.val().length + newlineCount;
        if(!isWin()){
          numInput = container.val().length;
        }
      
        numWords = countWord(getCleanedWordString(container.val()));
        
        numLeft = options.errorNumber - numInput;
      } else {
        //normal count, no cut
        var newlineCount = getNewlineCount(content);
        numInput = container.val().length + newlineCount;
        if(!isWin()){
          numInput = container.val().length;
        }
        numWords = countWord(getCleanedWordString(container.val()));
      }
      
      return formatDisplayInfo();
    }
    
    function formatDisplayInfo(){
      var format = options.displayFormat;
      format = format.replace('#input', numInput);
      format = format.replace('#words', numWords);
      //When maxCharacters <= 0, #max, #left cannot be substituted.
      if(maxCharacters > 0){
        format = format.replace('#max', maxCharacters);
        format = format.replace('#left', numLeft);
      }
      return format;
    }
    
    function getInfo(){
      var info = {
        input: numInput,
        max: maxCharacters,
        left: numLeft,
        words: numWords
      };
      return info;
    }
    
    function getNextCharLeftInformation(container){
        return container.next('.charleft');
    }
    
    function isWin(){
      var strOS = navigator.appVersion;
      if (strOS.toLowerCase().indexOf('win') != -1){
        return true;
      }
      return false;
    }
    
    function getNewlineCount(content){
      var newlineCount = 0;
      for(var i=0; i<content.length;i++){
        if(content.charAt(i) == '\n'){
          newlineCount++;
        }
      }
      return newlineCount;
    }
    
    function getCleanedWordString(content){
      var fullStr = content + " ";
      var initial_whitespace_rExp = /^[^A-Za-z0-9]+/gi;
      var left_trimmedStr = fullStr.replace(initial_whitespace_rExp, "");
      var non_alphanumerics_rExp = rExp = /[^A-Za-z0-9]+/gi;
      var cleanedStr = left_trimmedStr.replace(non_alphanumerics_rExp, " ");
      var splitString = cleanedStr.split(" ");
      return splitString;
    }
    
    function countWord(cleanedWordString){
      var word_count = cleanedWordString.length-1;
      return word_count;
    }
  };  
})(jQuery);
