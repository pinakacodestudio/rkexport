<?php
if(isset($controlname)){
?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckeditor/plugins/ckeditor_wiris/core/display.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckfinder/ckfinder.js"></script>
<style>
	#iconsDiv {
	display: block;
	}
	#langFormDiv {
	display: block;
	margin-left: 640px;
	}
</style>
<textarea style="height:300px"  name="<?=$controlname;?>" id="<?=$controlname;?>"><?php if(isset($controldata)){echo $controldata;}?></textarea>
<script type="text/javascript">

	//CKEDITOR.replace( '<?=$controlname;?>', { toolbar : [ [ 'EqnEditor', 'Bold', 'Italic' ] ] });
	
	CKEDITOR.config.toolbar_Full =
		[
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },

			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'Audio' ] },
			{ name: 'youtube', items: [ 'Youtube' ] },
			/*{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'others', items: [ '-' ] },
			{ name: 'about', items: [ 'About' ] }*/
		];
		
		CKEDITOR.config.ForcePasteAsPlainText = false;

	CKEDITOR.config.fillEmptyBlocks = false;
  
	var editor = CKEDITOR.replace('<?=$controlname;?>', {
		language: 'en',
		width: '100%',
		toolbar: 'Full',
		filebrowserBrowseUrl : '<?php echo base_url(); ?>assets/editor/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : '<?php echo base_url(); ?>assets/editor/ckfinder/ckfinder.html?type=Images',
		filebrowserFlashBrowseUrl : '<?php echo base_url(); ?>assets/editor/ckfinder/ckfinder.html?type=Flash',
		filebrowserUploadUrl : '<?php echo base_url(); ?>assets/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : '<?php echo base_url(); ?>assets/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : '<?php echo base_url(); ?>assets/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	});
	/*CKEDITOR.config.toolbar_Full =
		[
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'Audio'] },
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },		
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'others', items: [ '-' ] },
			{ name: 'about', items: [ 'About' ] }
		];
		
		CKEDITOR.config.ForcePasteAsPlainText = false;
	
	var editor = CKEDITOR.replace('<?=$controlname;?>', {
		language: 'en',
		width: '100%',
		toolbar: 'Full',
		filebrowserBrowseUrl : 'admin/editor/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : 'admin/editor/ckfinder/ckfinder.html?type=Images',
		filebrowserFlashBrowseUrl : 'admin/editor/ckfinder/ckfinder.html?type=Flash',
		filebrowserUploadUrl : 'admin/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : 'admin/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : 'admin/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	});*/
	CKFinder.setupCKEditor( editor, '../' );
</script>
<?php	
}else if(isset($controlname1)){
?>
	<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckeditor/plugins/ckeditor_wiris/core/display.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckfinder/ckfinder.js"></script>
<style>
	#iconsDiv {
	display: block;
	}
	#langFormDiv {
	display: block;
	margin-left: 640px;
	}
</style>
<textarea style="height:300px"  name="<?=$controlname1;?>" id="<?=$controlname1;?>"><?php if(isset($controldata)){echo $controldata;}?></textarea>
<script type="text/javascript">

	CKEDITOR.config.toolbar_Full =
		[
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
			{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
			{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
			{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe', 'Audio'] },
			{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },		
			{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
			{ name: 'others', items: [ '-' ] },
			{ name: 'about', items: [ 'About' ] }
		];
		
		CKEDITOR.config.ForcePasteAsPlainText = false;
	
	var editor = CKEDITOR.replace('<?=$controlname1;?>', {
		language: 'en',
		width: '100%',
		toolbar: 'Full',
		filebrowserBrowseUrl : 'admin/editor/ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : 'admin/editor/ckfinder/ckfinder.html?type=Images',
		filebrowserFlashBrowseUrl : 'admin/editor/ckfinder/ckfinder.html?type=Flash',
		filebrowserUploadUrl : 'admin/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : 'admin/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : 'admin/editor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	});
	CKFinder.setupCKEditor( editor, '../' );
</script>

<?php	
}else{
?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/editor/ckeditor/ckeditor.js"></script>
	<style>
		#iconsDiv {
		display: block;
		}
		#langFormDiv {
		display: block;
		margin-left: 640px;
		}
	</style>
	<textarea style="height:300px"  name="<?=$controlname2;?>" id="<?=$controlname2;?>"><?php if(isset($controldata2)){echo $controldata2;}?></textarea>
	<script type="text/javascript">

		CKEDITOR.config.toolbar_Full =
			[
				{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
			];
			
			CKEDITOR.config.ForcePasteAsPlainText = false;
		
		var editor = CKEDITOR.replace('<?=$controlname2;?>', {
			language: 'en',
			width: '100%',
			height: '120px',
			toolbar: 'Full',
		});
	</script>
<?php	
}
?>