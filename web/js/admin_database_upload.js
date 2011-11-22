/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabaseUpload()
{
	jQuery('#btnAdminDatabaseUploadCancel').hide();
	jQuery('#divAdminDatabaseUploadParameters').hide();
	doAdminDatabaseUploadStatusMsg('');

	doAdminDatabaseUploadCreate();
	doAdminDatabaseCreateUploader();

	jQuery('#btnAdminDatabaseUploadReplace').click(function() {
		jQuery.post(jQuery('#desinventarURL').val(),
		{
			cmd: 'cmdDatabaseReplace',
			RegionId: jQuery('#desinventarRegionId').val(),
			Filename: jQuery('#txtAdminDatabaseUploadFilename').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('#divAdminDatabaseUploadParameters').hide();
				doAdminDatabaseUploadStatusMsg('msgAdminDatabaseUploadUpdateOk');
			}
			else
			{
				doAdminDatabaseUploadStatusMsg('msgAdminDatabaseUploadUpdateError');
			}
		},
		'json'
		);
	});

	jQuery('#btnAdminDatabaseUploadReplaceCancel').click(function() {
		doAdminDatabaseUploadReset();
		jQuery('#divAdminDatabaseUploadParameters').hide();
	});
} //onReadyAdminDatabaseUpload

function doAdminDatabaseCreateUploader()
{
	doAdminDatabaseUploadReset();

	var uploader = new qq.FileUploaderBasic({
		button: document.getElementById('divFileUploaderControl'),
		action: jQuery('#desinventarURL').val(),
		params:
		{
			cmd : 'cmdDatabaseUpload',
			RegionId: jQuery('#desinventarRegionId').val()
		},
		debug:false,
		multiple:false,
		onSubmit: function(id, fileName)
		{
			jQuery('#txtAdminDatabaseUploadFilename').val(fileName);
			jQuery('#txtAdminDatabaseUploadId').val(id);
			jQuery('#prgAdminDatabaseUploadProgressMark').css('width', '0px');
			jQuery('#prgAdminDatabaseUploadPercent').text('');
			jQuery('#btnAdminDatabaseUploadCancel').show();
		},
		onProgress: function(id, fileName, loaded, total)
		{
			var maxWidth = jQuery('#prgAdminDatabaseUploadProgressBar').width();
			var percent  = parseInt(loaded/total * 100);
			var width    = parseInt(percent * maxWidth/100);
			jQuery('#prgAdminDatabaseUploadProgressMark').css('width', width);
			jQuery('#prgAdminDatabaseUploadPercent').text(percent + '%');
		},
		onComplete: function(id, fileName, data)
		{~
			jQuery('#btnAdminDatabaseUploadCancel').hide();
			jQuery('#txtAdminDatabaseUploadFilename').val(data.Filename);
			jQuery('#txtAdminDatabaseUploadRegionId').text(data.Info.RegionId);
			jQuery('#txtAdminDatabaseUploadRegionLabel').text(data.Info.RegionLabel);
			jQuery('#txtAdminDatabaseUploadLangIsoCode').text(data.Info.LangIsoCode);
			jQuery('#txtAdminDatabaseUploadCountryIso').text(data.Info.CountryIso);
			doAdminDatabaseUploadStatusMsg('');
			jQuery('#divAdminDatabaseUploadParameters').show();
		},
		onCancel: function(id, fileName)
		{
		},
	});
	jQuery('#btnAdminDatabaseUploadCancel').click(function() {
		doAdminDatabaseUploadReset();
		uploader.cancel(jQuery('#txtAdminDatabaseUploadId').val());
	});
}

function doAdminDatabaseUploadReset()
{
	jQuery('#txtAdminDatabaseUploadFilename').val('');
	jQuery('#prgAdminDatabaseUploadProgressMark').css('width', '0px');
	jQuery('#prgAdminDatabaseUploadPercent').text('');
	jQuery('#btnAdminDatabaseUploadCancel').hide();
}

function doAdminDatabaseUploadSelectFile()
{
	jQuery('#divFileUploaderControl input').trigger('click');
}

function doAdminDatabaseUploadStatusMsg(Id)
{
	jQuery('.clsAdminDatabaseUploadStatusMsg').hide();
	if (Id != '')
	{
		jQuery('.clsAdminDatabaseUploadStatusMsg#' + Id).show();
	}
} //function

function doAdminDatabaseUploadCreate()
{
	// Database Upload
	var w = new Ext.Window({id:'wndDatabaseUpload', 
		el: 'divDatabaseUploadWin', layout:'fit', 
		width:600, height:400, modal:false,
		closeAction:'hide', plain: false, animCollapse: false,
		items: new Ext.Panel({
			contentEl: 'divDatabaseUploadContent',
			autoScroll: true
		}),
		buttons: [
			{
				text: jQuery('#msgAdminDatabaseUploadButtonChooseFile').text(),
				handler: function() 
				{
					doAdminDatabaseUploadSelectFile();
				}
			},
			{
				text: jQuery('#msgAdminDatabaseUploadButtonClose').text(),
				handler: function()
				{
					jQuery('#fldAdminDatabaseUploadSave').val(0);
					jQuery('#imgAdminDatabaseUploadWait').attr('src','');
					Ext.getCmp('wndDatabaseUpload').hide();
				} //handler
			}
		] //button
	});
	jQuery('#fldAdminDatabaseUploadSave').val(1);
	jQuery('.clsAdminDatabaseUploadStatusMsg').hide();
} // doAdminDatabaseUploadCreate()

function doAdminDatabaseUploadAction()
{
	/*
	jQuery('.clsAdminDatabaseUpload').hide();
	Ext.getCmp('wndDatabaseUpload').show();
	jQuery('.clsAdminDatabaseUpload').hide();
	jQuery('#divAdminDatabaseUploadProgress').show();
	
	jQuery('#imgAdminDatabaseUploadWait').attr('src', jQuery('#fldAdminDatabaseUploadImage').val());
	jQuery('#imgAdminDatabaseUploadWait').show();
	
	jQuery('#fldAdminDatabaseUploadSave').val(1);
	jQuery.post(jQuery('#desinventarURL').val(),
		{
			cmd      : 'cmdAdminDatabaseUpload',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			jQuery('.clsAdminDatabaseUpload').hide();
			if (parseInt(data.Status) > 0)
			{
				jQuery('#divAdminDatabaseUploadResults').show();
				jQuery('#imgAdminDatabaseUploadWait').attr('src','').hide();
				// Hide Ext.Window
				Ext.getCmp('wndDatabaseUpload').hide();
				if (parseInt(jQuery('#fldAdminDatabaseUploadSave').val()) > 0)
				{
					// Open the backup file for download
					window.location = data.URL;
				}
			}
			else
			{
				jQuery('#divAdminDatabaseUploadError').show();
			}
		},
		'json'
	);
	*/
} // doAdminDatabaseUploadAction
