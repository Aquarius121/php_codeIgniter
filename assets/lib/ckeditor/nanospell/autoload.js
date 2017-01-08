/*
 *  # NanoSpell Spell Check Plugin for CKEditor #
 *
 *  (C) Copyright nanospell.com (all rights reserverd)
 *  License:  http://ckeditor-spellcheck.nanospell.com/license
 *
 *
 *	# Resources #
 *
 *	Getting Started - http://ckeditor-spellcheck.nanospell.com
 *	Installation 	- http://ckeditor-spellcheck.nanospell.com/how-to-install
 *	Settings 		- http://ckeditor-spellcheck.nanospell.com/plugin-settings
 *	Dictionaries 	- http://ckeditor-spellcheck.nanospell.com/ckeditor-spellchecking-dictionaries
 *
 */
/*
 * A huge thanks To Frederico Knabben and all contributirs to CKEditor for releasing and maintaining a world class javascript HTML Editor.
 * FCK and CKE have enabled a new generation of online software , without your excelent work this project would be pointless.
 */

'use strict';
var nanospell = {
	ckeditor: function(selector, settings) {
		if (typeof(window.CKEDITOR) == undefined) {
			setTimeout(function() {
				window.nanospell.ckeditor(selector, settings), 300
			})
		}
		if (!selector) {
			selector = 'all'
		};
		if (!settings) {
			settings = {}
		};
		nanospell.wysiwyg.cke.inject_plugin(selector, settings);
	},
	spell_ajax_folder_path: null,
	base_path: function() {
		// must be loaded from current subdomain not assets base
		return CKEDITOR_BASEPATH + "nanospell/";
	}
};
nanospell.wysiwyg = {};
nanospell.wysiwyg.cke = {
	list: {},
	inject_plugin: function(selector, settings) {
		var plugin_name = 'nanospell'
		var plugin_url = nanospell.base_path() + "nanospell.ckeditor/plugin.js"
		if (typeof(CKEDITOR) == 'undefined') {
			return;
		}
		if (CKEDITOR.version < "4") {
			console.log("nanospell can not work with old CKEditor instances with versions less than 4.  Your version is " + CKEDITOR.version + " !")
			return;
		}
		for (var i in CKEDITOR.instances) {
			if (selector.toLowerCase() === 'all' || CKEDITOR.instances[i].element.$.id === selector) {
				nanospell.wysiwyg.cke.list[i] = true;
			}
		}
		CKEDITOR.plugins.addExternal(plugin_name, plugin_url, '');
		CKEDITOR.plugins.load(plugin_name, function(plugins) {
			for (var i in nanospell.wysiwyg.cke.list) {
				var editor = CKEDITOR.instances[i];
				editor.config.nanospell = settings;
				editor.config.removePlugins += ',wsc,scayt';
				plugins[plugin_name].init(editor)
			 
			}
		});
	}
}

// access in global scope when 
// loaded via jQuery getScript()
window.nanospell = nanospell;