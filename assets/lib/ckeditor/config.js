CKEDITOR.editorConfig = function( config ) {

	// The toolbar groups arrangement, 
	// optimized for a single toolbar row.
	config.toolbarGroups = [
		{ name: 'document',	  groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'forms' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'tools' },
		{ name: 'others', groups: [ 'nanospell' ] },
	];

	// the default plugins to use (most of these are build in to the compiled version)
	config.plugins = 'basicstyles,clipboard,contextmenu,enterkey,entities,indentlist,link,list,placeholder,toolbar,wysiwygarea';

	// The default plugins included in the basic setup define some buttons that
	// we don't want too have in a basic editor. We remove them here.
	config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Underline,Strike';
	
};
