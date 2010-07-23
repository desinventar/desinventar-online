function onReadyDatabaseImport() {
	jQuery('#btnDBImportCancel').hide();
	jQuery('#divDBImportParameters').hide();
	doDBImportStatusMsg('');
	
	// Copy Select Control with Language List to this form
	jQuery('#desinventarLanguageList').clone().attr('id','LangIsoCode').appendTo('#frmDBImport #spanLangIsoCode').show();
	jQuery('#desinventarCountryList').clone().attr('id','CountryIso').appendTo('#frmDBImport #spanCountryIso').show();
	// These controls are readonly
	jQuery('#frmDBImport #LangIsoCode').attr('disabled', true);
	jQuery('#frmDBImport #CountryIso').attr('disabled', true);

	// Create a SWFUpload instance and attach events...
	jQuery('#divDBImportControl').swfupload({
		upload_url: 'index.php', //?cmd=fileupload', //'&t=' + new Date().getTime(),
		post_params: {'cmd':'fileupload'},
		file_size_limit : "204800",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : "0",
		flash_url: 'external/swfupload/swfupload.swf',
		button_image_url : 'images/list_manager_48x48_sprite.png',
		button_width : 48,
		button_height : 48,
		button_placeholder_id : 'btnDBImportSelectFile',
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
		if (data.Status == 'OK') {
			doDBImportStatusMsg('msgDBImportUploadOk');
			jQuery.each(data, function(index, value) {
				if ( (value != null) && (typeof(value) == 'object') ) {
					jQuery.each(value, function(index, value) {
						jQuery('#frmDBImport #' + index).val(value);
					});
					jQuery('#frmDBImport #RegionId_Prev').val(value.RegionId);
					jQuery('#frmDBImport #RegionLabel_Prev').val(value.RegionLabel);
				} else {
				}
			});
			jQuery('#divDBImportParameters').show();
		} else {
			// Upload error (file size ?)
			doDBImportStatusMsg('msgDBImportUploadError');
		}
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
	
	jQuery('.radioDBImportOption').change(function() {
		switch(jQuery(this).val()) {
			case 'NEW':
				jQuery('#frmDBImport #RegionId').val(doCreateNewRegionId(jQuery('#frmDBImport #CountryIso').val()));
				jQuery('#frmDBImport #RegionLabel').val(jQuery('#frmDBImport #CountryIso :selected').html() + ' ' + jQuery('#frmDBImport #RegionId').val());
			break;
			case 'UPDATE':
				jQuery('#frmDBImport #RegionId').val(jQuery('#frmDBImport #RegionId_Prev').val());
				jQuery('#frmDBImport #RegionLabel').val(jQuery('#frmDBImport #RegionLabel_Prev').val());
			break;
		}
	});
	
	// Debug Lines (remember to remove!)
	jQuery('#divDBImportParameters').show();
	
} //onReady

function doDBImportStatusMsg(Id) {
	jQuery('.DBImportStatusMsg').hide();
	if (Id != '') {
		jQuery('.DBImportStatusMsg#' + Id).show();
	}
} //function

function doCreateNewRegionId(CountryIso) {
	var t = new Date();
	var RegionId = '';
	if (CountryIso == '') {
		RegionId = 'DESINV';
	} else {
		RegionId = CountryIso;
	}
	RegionId = RegionId + '-' + padNumber(t.getUTCFullYear(), 4) + 
	                            padNumber(t.getUTCMonth()+1 , 2) + 
	                            padNumber(t.getUTCDate()    , 2) + 
	                            padNumber(t.getUTCHours()   , 2) +
	                            padNumber(t.getUTCMinutes() , 2) +
	                            padNumber(t.getUTCSeconds() , 2);
	return RegionId;
}

function padNumber(myValue, myLen) {
	var value = '' + myValue;
	while (value.length < myLen) {
		value = '0' + value;
	}
	return value;
}