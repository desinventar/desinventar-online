/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseUpload()
{
	jQuery('#divDatabaseUploadControl').show();
	jQuery('#divDatabaseUploadParameters').hide();
	jQuery('#txtDatabaseUploadFilename').attr('readonly',true);
	doDatabaseUploadStatusMsg('');

	doDatabaseUploadCreate();
	doAdminDatabaseCreateUploader();

	jQuery('#btnDatabaseUploadReplace').click(function() {
		jQuery('#divDatabaseUploadParameters').hide();
		doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForUpdate');
		jQuery.post(jQuery('#desinventarURL').val() + '/',
			{
				cmd: 'cmdDatabaseReplace',
				RegionId: jQuery('#desinventarRegionId').val(),
				Filename: jQuery('#txtDatabaseUploadFilename').val()
			},
			function(data)
			{
				doDatabaseUploadStatusMsg('');
				if (parseInt(data.Status) > 0)
				{
					jQuery('#divDatabaseUploadParameters').hide();
					doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateOk');
					alert(jQuery('#msgDatabaseUploadComplete').val());
					doWindowReload();
				}
				else
				{
					doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateError');
				}
			},
			'json'
		);
	});

	jQuery('#btnDatabaseUploadReplaceCancel').click(function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd: 'cmdDatabaseReplaceCancel',
				RegionId: jQuery('#desinventarRegionId').val(),
				Filename: jQuery('#txtDatabaseUploadFilename').val()
			},
			function(data)
			{
				doDatabaseUploadReset();
			},
			'json'
		);				
	});

} //onReadyDatabaseUpload

function doAdminDatabaseCreateUploader()
{
	doDatabaseUploadReset();

	var uploader = new qq.FileUploader({
		element: document.getElementById('divFileUploaderControl'),
		action: jQuery('#desinventarURL').val() + '/',
		params:
		{
			cmd : 'cmdDatabaseUpload',
			RegionId: jQuery('#desinventarRegionId').val()
		},
		debug:false,
		multiple:false,
		allowedExtensions: ['zip'],
		onSubmit: function(id, Filename)
		{
			jQuery('#txtDatabaseUploadFilename').val(Filename);
			jQuery('#txtDatabaseUploadId').val(id);
			jQuery('#prgDatabaseUploadProgressMark').css('width', '0px');
			jQuery('#divFileUploaderControl .qq-upload-button-text').hide();
			jQuery('#btnDatabaseUploadCancel').show();
			doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForUpload');
		},
		onProgress: function(id, Filename, loaded, total)
		{
			var maxWidth = jQuery('#prgDatabaseUploadProgressBar').width();
			var percent  = parseInt(loaded/total * 100);
			var width    = parseInt(percent * maxWidth/100);
			jQuery('#prgDatabaseUploadProgressMark').css('width', width);
		},
		onComplete: function(id, Filename, data)
		{
			doDatabaseUploadStatusMsg('');
			jQuery('#btnDatabaseUploadCancel').hide();
			jQuery('#txtDatabaseUploadFilename').val(data.filename);
			if (parseInt(data.Status)>0)
			{
				jQuery('#txtDatabaseUploadRegionId').text(data.Info.RegionId);
				jQuery('#txtDatabaseUploadRegionLabel').text(data.Info.RegionLabel);
				jQuery('#txtDatabaseUploadLangIsoCode').text(data.Info.LangIsoCode);
				jQuery('#txtDatabaseUploadCountryIso').text(data.Info.CountryIso + ' - ' + data.Info.CountryName);
				jQuery('#txtDatabaseUploadRegionLastUpdate').text(data.Info.RegionLastUpdate);
				jQuery('#txtDatabaseUploadNumberOfRecords').text(data.Info.NumberOfRecords);
				doDatabaseUploadStatusMsg('');
				jQuery('#divDatabaseUploadControl').hide();
				jQuery('#divDatabaseUploadParameters').show();
			}
			else
			{
				doDatabaseUploadReset();
				doDatabaseUploadStatusMsg('msgDatabaseUploadErrorOnUpload');
			}
		},
		onCancel: function(id, Filename)
		{
		},
	});
	jQuery('#divFileUploaderControl .qq-upload-button-text').html(jQuery('#msgDatabaseUploadChooseFile').val());
	jQuery('#divFileUploaderControl .qq-upload-list').hide();

	jQuery('#btnDatabaseUploadCancel').click(function() {
		doDatabaseUploadReset();
		uploader.cancel(jQuery('#txtDatabaseUploadId').val());
	});
}

function doDatabaseUploadReset()
{
	doDatabaseUploadStatusMsg('');
	jQuery('#txtDatabaseUploadFilename').val('');
	jQuery('#prgDatabaseUploadProgressMark').css('width', '0px');
	jQuery('#btnDatabaseUploadCancel').hide();
	jQuery('#divFileUploaderControl .qq-upload-button-text').show();
	jQuery('#divDatabaseUploadControl').show();
	jQuery('#divDatabaseUploadParameters').hide();
	jQuery('#divFileUploaderControl .qq-upload-button-text').show();
}

function doDatabaseUploadSelectFile()
{
	jQuery('#divFileUploaderControl input').trigger('click');
}

function doDatabaseUploadStatusMsg(Id)
{
	jQuery('.clsDatabaseUploadStatusMsg').hide();
	if (Id != '')
	{
		jQuery('.clsDatabaseUploadStatusMsg#' + Id).show();
	}
} //function

function doDatabaseUploadCreate()
{
	// Database Upload
	var w = new Ext.Window({id:'wndDatabaseUpload', 
		el: 'divDatabaseUploadWin', layout:'fit', 
		width:400, height:200, modal:false,
		plain: false, animCollapse: false,
		closeAction: 'hide',
		items: new Ext.Panel({
			contentEl: 'divDatabaseUploadContent',
			autoScroll: true
		}),
	});
	w.on('hide', function() {
		if (jQuery('#txtDatabaseUploadFilename').val() != '')
		{
			jQuery('#btnDatabaseUploadReplaceCancel').trigger('click');
		}
	});
	
	jQuery('#fldDatabaseUploadSave').val(1);
	jQuery('.clsDatabaseUploadStatusMsg').hide();
} // doDatabaseUploadCreate()

function doDatabaseUploadShow()
{
	jQuery('.clsDatabaseUpload').hide();
	doDatabaseUploadReset();
	Ext.getCmp('wndDatabaseUpload').show();
} // doDatabaseUploadAction
