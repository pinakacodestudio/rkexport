<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<script type="text/javascript" src="./ckeditor/plugins/ckeditor_wiris/core/display.js"></script>
		<script type="text/javascript" src="./ckeditor/ckeditor.js"></script>
		<title>CKEditor WIRIS integration on PHP | Educational mathematics</title>
		<style>
			#iconsDiv {display:inline-block;}
			#langFormDiv { display:inline-block; margin-left:640px;}
		</style>
		<!--[if IE]><style>#langFormDiv { margin-left:640px; }</style><![endif]-->
		<!--[if lt IE 9]><style>#langFormDiv { margin-left:645px; }</style><![endif]-->
		<!--[if lt IE 8]><style>#iconsDiv {display:inline;zoom:1; margin-bottom:-20px;} #langFormDiv { display:inline; zoom:1; margin-bottom:-20px;}</style><![endif]-->		
		
	</head>
	<body>
		<form name="exampleForm" method="POST" action="display.php">
			<textarea id="example" name="example"><?php
				if (isset($_POST['content'])) {
					echo htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');
				}
			?></textarea>
			
			<script type="text/javascript">
				CKEDITOR.config.toolbar_Full =
				[
					{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
					{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
					{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
					{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
					'/',
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
					{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
					{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
					{ name: 'insert', items: [ 'Image', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
					'/',
					{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
					{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
					{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
					{ name: 'others', items: [ '-' ] },
					{ name: 'about', items: [ 'About' ] }
				];
			
				CKEDITOR.replace('example', {
					//skin: 'kama',
					language: 'en',
					width: '850px',
					toolbar:'Full'
					//wirisimagecolor:'#000000',
					//wirisbackgroundcolor:'#ffffff',
					//wirisimagefontsize:'16px'					
				});
			</script>
			
			<input id="previewButton" type="submit" value="Preview"/>
		</form>
	</body>
</html>
