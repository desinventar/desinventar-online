function onReadyDatabaseImport() {
	jQuery('#btnDBImportCancel').hide();
	//jQuery('#divDBEdit').hide();
	
	// Copy Select Control with Language List to this form
	jQuery('#desinventarLanguageList').clone().attr('id','LangIsoCode').appendTo('#frmDBEdit #spanLangIsoCode').show();
	jQuery('#desinventarCountryList').clone().attr('id','CountryIso').appendTo('#frmDBEdit #spanCountryIso').show();

	// Create a SWFUpload instance and attach events...
	jQuery('#divDBImportControl').swfupload({
		upload_url: 'index.php',
		post_params: {cmd : 'fileupload'},
		file_size_limit : "204800",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : "0",
		flash_url: 'external/swfupload/swfupload.swf',
		button_image_url : 'images/list_manager_48x48_sprite.png',
		button_width : 48,
		button_height : 48,
		button_placeholder_id : 'btnDBImportSelectFile',
		debug: false,
		custom_settings : {something : "here"}
	})
	.bind('fileQueued', function(event, file) {
		// start the upload since it's queued
		jQuery('#txtDBImportFileName').val(file.name);
		jQuery('#prgDBImportProgressMark').css('width', '0px');
		jQuery('#btnDBImportCancel').attr('file_id', file.id).show();
		jQuery('#divDBEdit').hide();
		jQuery(this).swfupload('startUpload');
	})
	.bind('uploadProgress', function(event, file, bytesLoaded) {
		// Show Progress
		var percentage = Math.round((bytesLoaded/file.size)*100);
		jQuery('#prgDBImportProgressMark').css('width', percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData) {
		jQuery('#btnDBImportCancel').hide();
		var data = eval('(' + serverData + ')');
		jQuery('#divDBEdit').show();
		jQuery('#txtDBEditInfo').html('');
		jQuery.each(data, function(index, value) {
			if ( (value != null) && (typeof(value) == 'object') ) {
				jQuery.each(value, function(index, value) {
					jQuery('#frmDBEdit #' + index).val(value);
				});
			} else {
				jQuery('#txtDBEditInfo').append(index + ' => ' + value + '<br />');
			}
		});
	})
	.bind('uploadComplete', function(event, file) {
		// upload has completed, lets try the next one in the queue
		jQuery(this).swfupload('startUpload');
	});

	jQuery('#txtDBImportFileName').attr('disabled',true).val('');
	jQuery('#prgDBImportProgressBar').css('width', jQuery('#txtDBImportFileName').css('width'));
		
	jQuery('#btnDBImportCancel').click(function() {
		var swfu = jQuery.swfupload.getInstance('#divDBImportControl');
		swfu.cancelUpload(jQuery(this).attr('file_id'));
		jQuery('#prgDBImportProgressMark').css('width', '0px');
		jQuery('#btnDBImportCancel').hide();
		jQuery('#txtDBImportFileName').val('');
	});
}
