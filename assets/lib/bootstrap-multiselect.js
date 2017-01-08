!function(e){"use strict";function t(e){return ko.isObservable(e)&&!(e.destroyAll===undefined)}function n(e,t){for(var n=0;n<e.length;++n){t(e[n])}}function r(t,n){this.options=this.mergeOptions(n);this.$select=e(t);this.originalOptions=this.$select.clone()[0].options;this.query="";this.searchTimeout=null;this.options.multiple=this.$select.attr("multiple")==="multiple";this.options.onChange=e.proxy(this.options.onChange,this);this.options.onDropdownShow=e.proxy(this.options.onDropdownShow,this);this.options.onDropdownHide=e.proxy(this.options.onDropdownHide,this);this.options.onDropdownShown=e.proxy(this.options.onDropdownShown,this);this.options.onDropdownHidden=e.proxy(this.options.onDropdownHidden,this);this.buildContainer();this.buildButton();this.buildDropdown();this.buildSelectAll();this.buildDropdownOptions();this.buildFilter();this.updateButtonText();this.updateSelectAll();if(this.options.disableIfEmpty){this.disableIfEmpty()}this.$select.hide().after(this.$container)}if(typeof ko!=="undefined"&&ko.bindingHandlers&&!ko.bindingHandlers.multiselect){ko.bindingHandlers.multiselect={init:function(r,i,s,o,u){var a=s().selectedOptions;var f=ko.utils.unwrapObservable(i());e(r).multiselect(f);if(t(a)){e(r).multiselect("select",ko.utils.unwrapObservable(a));a.subscribe(function(t){var i=[],s=[];n(t,function(e){switch(e.status){case"added":i.push(e.value);break;case"deleted":s.push(e.value);break}});if(i.length>0){e(r).multiselect("select",i)}if(s.length>0){e(r).multiselect("deselect",s)}},null,"arrayChange")}},update:function(n,r,i,s,o){var u=i().options,a=e(n).data("multiselect"),f=ko.utils.unwrapObservable(r());if(t(u)){u.subscribe(function(t){e(n).multiselect("rebuild")})}if(!a){e(n).multiselect(f)}else{a.updateOriginalOptions()}}}}r.prototype={defaults:{buttonText:function(t,n){if(t.length===0){return this.nonSelectedText+' <b class="caret"></b>'}else{if(t.length>this.numberDisplayed){return t.length+" "+this.nSelectedText+' <b class="caret"></b>'}else{var r="";t.each(function(){var t=e(this).attr("label")!==undefined?e(this).attr("label"):e(this).html();r+=t+", "});return r.substr(0,r.length-2)+' <b class="caret"></b>'}}},buttonTitle:function(t,n){if(t.length===0){return this.nonSelectedText}else{var r="";t.each(function(){r+=e(this).text()+", "});return r.substr(0,r.length-2)}},label:function(t){return e(t).attr("label")||e(t).html()},onChange:function(e,t){},onDropdownShow:function(e){},onDropdownHide:function(e){},onDropdownShown:function(e){},onDropdownHidden:function(e){},buttonClass:"btn btn-default",dropRight:false,selectedClass:"active",buttonWidth:"auto",buttonContainer:'<div class="btn-group" />',maxHeight:false,checkboxName:false,includeSelectAllOption:false,includeSelectAllIfMoreThan:0,selectAllText:" Select all",selectAllValue:"multiselect-all",enableFiltering:false,enableCaseInsensitiveFiltering:false,filterPlaceholder:"Search",filterBehavior:"text",preventInputChangeEvent:false,nonSelectedText:"None selected",nSelectedText:"selected",numberDisplayed:3,disableIfEmpty:false,templates:{button:'<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"></button>',ul:'<ul class="multiselect-container dropdown-menu"></ul>',filter:'<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',li:'<li><a href="javascript:void(0);"><label></label></a></li>',divider:'<li class="multiselect-item divider"></li>',liGroup:'<li class="multiselect-item group"><label class="multiselect-group"></label></li>'}},constructor:r,buildContainer:function(){this.$container=e(this.options.buttonContainer);this.$container.on("show.bs.dropdown",this.options.onDropdownShow);this.$container.on("hide.bs.dropdown",this.options.onDropdownHide);this.$container.on("shown.bs.dropdown",this.options.onDropdownShown);this.$container.on("hidden.bs.dropdown",this.options.onDropdownHidden)},buildButton:function(){this.$button=e(this.options.templates.button).addClass(this.options.buttonClass);if(this.$select.prop("disabled")){this.disable()}else{this.enable()}if(this.options.buttonWidth&&this.options.buttonWidth!=="auto"){this.$button.css({width:this.options.buttonWidth});this.$container.css({width:this.options.buttonWidth})}var t=this.$select.attr("tabindex");if(t){this.$button.attr("tabindex",t)}this.$container.prepend(this.$button)},buildDropdown:function(){this.$ul=e(this.options.templates.ul);if(this.options.dropRight){this.$ul.addClass("pull-right")}if(this.options.maxHeight){this.$ul.css({"max-height":this.options.maxHeight+"px","overflow-y":"auto","overflow-x":"hidden"})}this.$container.append(this.$ul)},buildDropdownOptions:function(){this.$select.children().each(e.proxy(function(t,n){var r=e(n).prop("tagName").toLowerCase();if(e(n).prop("value")===this.options.selectAllValue){return}if(r==="optgroup"){this.createOptgroup(n)}else if(r==="option"){if(e(n).data("role")==="divider"){this.createDivider()}else{this.createOptionValue(n)}}},this));e("li input",this.$ul).on("change",e.proxy(function(t){var n=e(t.target);var r=n.prop("checked")||false;var i=n.val()===this.options.selectAllValue;if(this.options.selectedClass){if(r){n.parents("li").addClass(this.options.selectedClass)}else{n.parents("li").removeClass(this.options.selectedClass)}}var s=n.val();var o=this.getOptionByValue(s);var u=e("option",this.$select).not(o);var a=e("input",this.$container).not(n);if(i){if(r){this.selectAll()}else{this.deselectAll()}}if(!i){if(r){o.prop("selected",true);if(this.options.multiple){o.prop("selected",true)}else{if(this.options.selectedClass){e(a).parents("li").removeClass(this.options.selectedClass)}e(a).prop("checked",false);u.prop("selected",false);this.$button.click()}if(this.options.selectedClass==="active"){u.parents("a").css("outline","")}}else{o.prop("selected",false)}}this.$select.change();this.updateButtonText();this.updateSelectAll();this.options.onChange(o,r);if(this.options.preventInputChangeEvent){return false}},this));e("li a",this.$ul).on("touchstart click",function(t){t.stopPropagation();var n=e(t.target);if(t.shiftKey){var r=n.prop("checked")||false;if(r){var i=n.parents("li:last").siblings('li[class="active"]:first');var s=n.parents("li").index();var o=i.index();if(s>o){n.parents("li:last").prevUntil(i).each(function(){e(this).find("input:first").prop("checked",true).trigger("change")})}else{n.parents("li:last").nextUntil(i).each(function(){e(this).find("input:first").prop("checked",true).trigger("change")})}}}n.blur()});this.$container.off("keydown.multiselect").on("keydown.multiselect",e.proxy(function(t){if(e('input[type="text"]',this.$container).is(":focus")){return}if((t.keyCode===9||t.keyCode===27)&&this.$container.hasClass("open")){this.$button.click()}else{var n=e(this.$container).find("li:not(.divider):not(.disabled) a").filter(":visible");if(!n.length){return}var r=n.index(n.filter(":focus"));if(t.keyCode===38&&r>0){r--}else if(t.keyCode===40&&r<n.length-1){r++}else if(!~r){r=0}var i=n.eq(r);i.focus();if(t.keyCode===32||t.keyCode===13){var s=i.find("input");s.prop("checked",!s.prop("checked"));s.change()}t.stopPropagation();t.preventDefault()}},this))},createOptionValue:function(t){if(e(t).is(":selected")){e(t).prop("selected",true)}var n=this.options.label(t);var r=e(t).val();var i=this.options.multiple?"checkbox":"radio";var s=e(this.options.templates.li);e("label",s).addClass(i);if(this.options.checkboxName){e("label",s).append('<input type="'+i+'" name="'+this.options.checkboxName+'" />')}else{e("label",s).append('<input type="'+i+'" />')}var o=e(t).prop("selected")||false;var u=e("input",s);u.val(r);if(r===this.options.selectAllValue){s.addClass("multiselect-item multiselect-all");u.parent().parent().addClass("multiselect-all")}e("label",s).append(" "+n);this.$ul.append(s);if(e(t).is(":disabled")){u.attr("disabled","disabled").prop("disabled",true).parents("a").attr("tabindex","-1").parents("li").addClass("disabled")}u.prop("checked",o);if(o&&this.options.selectedClass){u.parents("li").addClass(this.options.selectedClass)}},createDivider:function(t){var n=e(this.options.templates.divider);this.$ul.append(n)},createOptgroup:function(t){var n=e(t).prop("label");var r=e(this.options.templates.liGroup);e("label",r).text(n);this.$ul.append(r);if(e(t).is(":disabled")){r.addClass("disabled")}e("option",t).each(e.proxy(function(e,t){this.createOptionValue(t)},this))},buildSelectAll:function(){var t=this.hasSelectAll();if(!t&&this.options.includeSelectAllOption&&this.options.multiple&&e("option",this.$select).length>this.options.includeSelectAllIfMoreThan){if(this.options.includeSelectAllDivider){this.$ul.prepend(e(this.options.templates.divider))}var n=e(this.options.templates.li);e("label",n).addClass("checkbox");if(this.options.checkboxName){e("label",n).append('<input type="checkbox" name="'+this.options.checkboxName+'" />')}else{e("label",n).append('<input type="checkbox" />')}var r=e("input",n);r.val(this.options.selectAllValue);n.addClass("multiselect-item multiselect-all");r.parent().parent().addClass("multiselect-all");e("label",n).append(" "+this.options.selectAllText);this.$ul.prepend(n);r.prop("checked",false)}},buildFilter:function(){if(this.options.enableFiltering||this.options.enableCaseInsensitiveFiltering){var t=Math.max(this.options.enableFiltering,this.options.enableCaseInsensitiveFiltering);if(this.$select.find("option").length>=t){this.$filter=e(this.options.templates.filter);e("input",this.$filter).attr("placeholder",this.options.filterPlaceholder);this.$ul.prepend(this.$filter);this.$filter.val(this.query).on("click",function(e){e.stopPropagation()}).on("input keydown",e.proxy(function(t){clearTimeout(this.searchTimeout);this.searchTimeout=this.asyncFunction(e.proxy(function(){if(this.query!==t.target.value){this.query=t.target.value;e.each(e("li",this.$ul),e.proxy(function(t,n){var r=e("input",n).val();var i=e("label",n).text();var s="";if(this.options.filterBehavior==="text"){s=i}else if(this.options.filterBehavior==="value"){s=r}else if(this.options.filterBehavior==="both"){s=i+"\n"+r}if(r!==this.options.selectAllValue&&i){var o=false;if(this.options.enableCaseInsensitiveFiltering&&s.toLowerCase().indexOf(this.query.toLowerCase())>-1){o=true}else if(s.indexOf(this.query)>-1){o=true}if(o){e(n).show().removeClass("filter-hidden")}else{e(n).hide().addClass("filter-hidden")}}},this))}this.updateSelectAll()},this),300,this)},this))}}},destroy:function(){this.$container.remove();this.$select.show();this.$select.data("multiselect",null)},refresh:function(){e("option",this.$select).each(e.proxy(function(t,n){var r=e("li input",this.$ul).filter(function(){return e(this).val()===e(n).val()});if(e(n).is(":selected")){r.prop("checked",true);if(this.options.selectedClass){r.parents("li").addClass(this.options.selectedClass)}}else{r.prop("checked",false);if(this.options.selectedClass){r.parents("li").removeClass(this.options.selectedClass)}}if(e(n).is(":disabled")){r.attr("disabled","disabled").prop("disabled",true).parents("li").addClass("disabled")}else{r.prop("disabled",false).parents("li").removeClass("disabled")}},this));this.updateButtonText();this.updateSelectAll()},select:function(t,n){if(!e.isArray(t)){t=[t]}for(var r=0;r<t.length;r++){var i=t[r];var s=this.getOptionByValue(i);var o=this.getInputByValue(i);if(s===undefined||o===undefined){continue}if(!this.options.multiple){this.deselectAll(false)}if(this.options.selectedClass){o.parents("li").addClass(this.options.selectedClass)}o.prop("checked",true);s.prop("selected",true)}this.updateButtonText();if(n&&t.length===1){this.options.onChange(s,true)}},clearSelection:function(){this.deselectAll(false);this.updateButtonText();this.updateSelectAll()},deselect:function(t,n){if(!e.isArray(t)){t=[t]}for(var r=0;r<t.length;r++){var i=t[r];var s=this.getOptionByValue(i);var o=this.getInputByValue(i);if(s===undefined||o===undefined){continue}if(this.options.selectedClass){o.parents("li").removeClass(this.options.selectedClass)}o.prop("checked",false);s.prop("selected",false)}this.updateButtonText();if(n&&t.length===1){this.options.onChange(s,false)}},selectAll:function(){var t=e("li input[type='checkbox']:enabled",this.$ul);var n=t.filter(":visible");var r=t.length;var i=n.length;n.prop("checked",true);e("li:not(.divider):not(.disabled)",this.$ul).filter(":visible").addClass(this.options.selectedClass);if(r===i){e("option:enabled",this.$select).prop("selected",true)}else{var s=n.map(function(){return e(this).val()}).get();e("option:enabled",this.$select).filter(function(t){return e.inArray(e(this).val(),s)!==-1}).prop("selected",true)}},deselectAll:function(t){var t=typeof t==="undefined"?true:t;if(t){var n=e("li input[type='checkbox']:enabled",this.$ul).filter(":visible");n.prop("checked",false);var r=n.map(function(){return e(this).val()}).get();e("option:enabled",this.$select).filter(function(t){return e.inArray(e(this).val(),r)!==-1}).prop("selected",false);if(this.options.selectedClass){e("li:not(.divider):not(.disabled)",this.$ul).filter(":visible").removeClass(this.options.selectedClass)}}else{e("li input[type='checkbox']:enabled",this.$ul).prop("checked",false);e("option:enabled",this.$select).prop("selected",false);if(this.options.selectedClass){e("li:not(.divider):not(.disabled)",this.$ul).removeClass(this.options.selectedClass)}}},rebuild:function(){this.$ul.html("");this.options.multiple=this.$select.attr("multiple")==="multiple";this.buildSelectAll();this.buildDropdownOptions();this.buildFilter();this.updateButtonText();this.updateSelectAll();if(this.options.disableIfEmpty){this.disableIfEmpty()}if(this.options.dropRight){this.$ul.addClass("pull-right")}},dataprovider:function(t){var r="";var i=0;e.each(t,function(t,s){if(e.isArray(s.children)){i++;r+='<optgroup label="'+(s.title||"Group "+i)+'">';n(s.children,function(e){r+='<option value="'+e.value+'">'+(e.label||e.value)+"</option>"});r+="</optgroup>"}else{r+='<option value="'+s.value+'">'+(s.label||s.value)+"</option>"}});this.$select.html(r);this.rebuild()},enable:function(){this.$select.prop("disabled",false);this.$button.prop("disabled",false).removeClass("disabled")},disable:function(){this.$select.prop("disabled",true);this.$button.prop("disabled",true).addClass("disabled")},disableIfEmpty:function(){if(e("option",this.$select).length<=0){this.disable()}else{this.enable()}},setOptions:function(e){this.options=this.mergeOptions(e)},mergeOptions:function(t){return e.extend(true,{},this.defaults,t)},hasSelectAll:function(){return e("li."+this.options.selectAllValue,this.$ul).length>0},updateSelectAll:function(){if(this.hasSelectAll()){var t=e("li:not(.multiselect-item):not(.filter-hidden) input:enabled",this.$ul);var n=t.length;var r=t.filter(":checked").length;var i=e("li."+this.options.selectAllValue,this.$ul);var s=i.find("input");if(r>0&&r===n){s.prop("checked",true);i.addClass(this.options.selectedClass)}else{s.prop("checked",false);i.removeClass(this.options.selectedClass)}}},updateButtonText:function(){var t=this.getSelected();e("button.multiselect",this.$container).html(this.options.buttonText(t,this.$select));e("button.multiselect",this.$container).attr("title",this.options.buttonTitle(t,this.$select))},getSelected:function(){return e("option",this.$select).filter(":selected")},getOptionByValue:function(t){var n=e("option",this.$select);var r=t.toString();for(var i=0;i<n.length;i=i+1){var s=n[i];if(s.value===r){return e(s)}}},getInputByValue:function(t){var n=e("li input",this.$ul);var r=t.toString();for(var i=0;i<n.length;i=i+1){var s=n[i];if(s.value===r){return e(s)}}},updateOriginalOptions:function(){this.originalOptions=this.$select.clone()[0].options},asyncFunction:function(e,t,n){var r=Array.prototype.slice.call(arguments,3);return setTimeout(function(){e.apply(n||window,r)},t)}};e.fn.multiselect=function(t,n,i){return this.each(function(){var s=e(this).data("multiselect");var o=typeof t==="object"&&t;if(!s){s=new r(this,o);e(this).data("multiselect",s)}if(typeof t==="string"){s[t](n,i);if(t==="destroy"){e(this).data("multiselect",false)}}})};e.fn.multiselect.Constructor=r;e(function(){e("select[data-role=multiselect]").multiselect()})}(window.jQuery)