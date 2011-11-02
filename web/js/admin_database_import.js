/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseImport() {
	jQuery('#btnDBImportCancelUpload').hide();
	jQuery('#divDBImportParameters').hide();
	doDBImportStatusMsg('');
	
	// Copy Select Control with Language List to this form
	jQuery('#desinventarLanguageList').clone().attr('id','LangIsoCode').attr('name','LangIsoCode').appendTo('#frmDBImport #spanLangIsoCode').show();
	jQuery('#desinventarCountryList').clone().attr('id','CountryIso').attr('name','CountryIso').appendTo('#frmDBImport #spanCountryIso').show();
	

	// Create a SWFUpload instance and attach events...
	jQuery('#divDBImportControl').swfupload({
		upload_url: 'index.php', //?cmd=fileupload', //'&t=' + new Date().getTime(),
		post_params: {cmd : 'fileupload', 
		              SessionId : ('' + document.cookie.match(/DI8SESSID=[^;]+/)).substr(10)
		             },
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
		jQuery('#btnDBImportCancelUpload').attr('file_id', file.id).show();
		jQuery('#divDBImportParameters').hide();
		doDBImportStatusMsg('');
		jQuery(this).swfupload('startUpload');
	})
	.bind('uploadProgress', function(event, file, bytesLoaded) {
		// Show Progress
		var percentage = Math.round((bytesLoaded/file.size)*100);
		jQuery('#prgDBImportProgressMark').css('width', percentage + '%');
	})
	.bind('uploadSuccess', function(event, file, serverData) {
		jQuery('#btnDBImportCancelUpload').hide();
		var data = eval('(' + serverData + ')');
		if (parseInt(data.Status) > 0) {
			doDBImportStatusMsg('msgDBImportUploadOk');
			jQuery('#frmDBImport #Filename').val(data.Filename);
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
			jQuery('#frmDBImport #DBExist').val(data.DBExist);
			if (parseInt(data.DBExist) < 1) {
				jQuery('#spanDBImportClone').show();
				jQuery('#spanDBImportUpdate').hide();
				jQuery('#radioDBImportOptionClone').attr('checked',true).trigger('change');
			} else {
				jQuery('#spanDBImportClone').hide();
				jQuery('#spanDBImportUpdate').show();
				jQuery('#radioDBImportOptionUpdate').attr('checked',true).trigger('change');
			}
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
		
	jQuery('#btnDBImportCancelUpload').click(function() {
		var swfu = jQuery.swfupload.getInstance('#divDBImportControl');
		swfu.cancelUpload(jQuery(this).attr('file_id'));
		jQuery('#prgDBImportProgressMark').css('width', '0px');
		jQuery('#btnDBImportCancelUpload').hide();
		jQuery('#txtDBImportFileName').val('');
	});
	
	jQuery('.radioDBImportOption').change(function() {
		switch(jQuery(this).val()) {
			case 'NEW':
				jQuery('#frmDBImport #RegionId').val(doCreateNewRegionId(jQuery('#frmDBImport #CountryIso').val()));
				jQuery('#frmDBImport #RegionLabel').val(jQuery('#frmDBImport #CountryIso :selected').html() + ' ' + jQuery('#frmDBImport #RegionId').val());
			break;
			case 'CLONE':
			case 'UPDATE':
				jQuery('#frmDBImport #RegionId')
					.val(jQuery('#frmDBImport #RegionId_Prev').val())
					.attr('disabled',true);
				jQuery('#frmDBImport #RegionLabel')
					.val(jQuery('#frmDBImport #RegionLabel_Prev').val())
					.attr('disabled',true)
					.css('background-color','#ccc');
			break;
		} //switch
		doUpdateDBImportFormOptions(jQuery(this).val());
	});
	
	jQuery('#btnDBImportCancel').click(function() {
		jQuery('#divDBImportParameters').hide();
	});
	
	jQuery('#frmDBImport').submit(function() {
		// Enable fields to the value can be send with serialize...
		jQuery('#frmDBImport input').attr('disabled',false);
		jQuery('#frmDBImport select').attr('disabled', false);
		
		jQuery.post('index.php',
			{cmd : 'dbzipimport', 
			 RegionInfo : jQuery('#frmDBImport').serializeObject()
			},
			function(data) {
				if (parseInt(data.Status) > 0) {
					jQuery('#divDBImportParameters').hide();
					doDBImportStatusMsg('msgDBImportDBUpdated');
				} else {
					jQuery('#divDBImportParameters').hide();
					doDBImportStatusMsg('msgDBImportUpdateError');
				}
				// Restore form again by enable/disable fields to their previous state
				var x = jQuery('#frmDBImport .radioDBImportOption').serializeArray();
				doUpdateDBImportFormOptions(x[0].value);
			},
			'json'
		);
		return false;
	});

	jQuery('#radioDBImportOptionUpdate').trigger('change');
} //onReady

function doUpdateDBImportFormOptions(value) {
	// These controls are readonly
	jQuery('#frmDBImport #RegionId').attr('disabled', true).css('background-color','#ccc');
	jQuery('#frmDBImport #LangIsoCode').attr('disabled', true).css('background-color','#ccc');
	jQuery('#frmDBImport #CountryIso').attr('disabled', true).css('background-color','#ccc');

	switch(value) {
		case 'NEW':
			jQuery('#frmDBImport #RegionId').attr('disabled',true);
			jQuery('#frmDBImport #RegionLabel').attr('disabled',false).css('background-color','#fff');
		break;
		case 'UPDATE':
			jQuery('#frmDBImport #RegionId').attr('disabled',true);
			jQuery('#frmDBImport #RegionLabel').attr('disabled',true).css('background-color','#ccc');
		break;
	} //switch
}

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