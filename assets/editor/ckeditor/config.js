/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	// Add WIRIS to the plugin list
	config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'ckeditor_wiris';	
	config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'imagepaste';
	config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'pastefromword';
	config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'dialog';
	config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'youtube';
	//config.extraPlugins = 'mediaembed';
	//config.extraPlugins =  '**audio**';
	//config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'allmedias';
	//config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') + 'eqneditor';
	//config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') +  'hindiFont';
	//config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') +  'hindiKeys';
	//config.extraPlugins += (config.extraPlugins.length == 0 ? '' : ',') +  'scientificSymbols';
	
	//CKEDITOR.config.pasteFromWordCleanupFile = 'plugins/pastefromword/filter/default.js';
	
	// Add WIRIS buttons to the "Full toolbar"
	// Optionally, you can remove the following line and follow http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar
	config.toolbar_Full.push({name:'wiris', items:['ckeditor_wiris_formulaEditor', 'ckeditor_wiris_CAS']});
	//config.toolbar_Full.push({name:'Hindi', items:['HindiFont', 'HindiKeys']});
	//config.toolbar_Full.push({name:'science', items:['scientificSymbols']});
	config.allowedContent = true;
	config.autoParagraph = false;
	config.enterMode = CKEDITOR.ENTER_BR; // <p></p> to <br />
  	config.entities = false;
	config.basicEntities = false;
	config.fillEmptyBlocks = false;
	config.fullPage = false;
	config.ignoreEmptyParagraph = true;
};