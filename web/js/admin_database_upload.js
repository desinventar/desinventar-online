/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyAdminDatabaseUpload()
{
	jQuery('#divAdminDatabaseUploadParameters').hide();
	doAdminDatabaseUploadStatusMsg('');

	doAdminDatabaseUploadCreate();
	doAdminDatabaseCreateUploader();

	jQuery('#btnAdminDatabaseUploadReplace').click(function() {
		jQuery('#divAdminDatabaseUploadParameters').hide();
		doAdminDatabaseUploadStatusMsg('msgAdminDatabaseUploadWaitForUpdate');
		jQuery.post(jQuery('#desinventarURL').val(),
		{
			cmd: 'cmdDatabaseReplace',
			RegionId: jQuery('#desinventarRegionId').val(),
			Filename: jQuery('#txtAdminDatabaseUploadFilename').val()
		},
		function(data)
		{
			doAdminDatabaseUploadStatusMsg('');
			if (parseInt(data.Status) > 0)
			{
				jQuery('#divAdminDatabaseUploadParameters').hide();
				doAdminDatabaseUploadStatusMsg('msgAdminDatabaseUploadUpdateOk');
				alert(jQuery('#msgAdminDatabaseUploadComplete').val());
				doWindowReload();
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
		jQuery('#divFileUploaderControl .qq-upload-button-text').show();
	});

} //onReadyAdminDatabaseUpload

function doAdminDatabaseCreateUploader()
{
	doAdminDatabaseUploadReset();

	var uploader = new qq.FileUploader({
		element: document.getElementById('divFileUploaderControl'),
		action: jQuery('#desinventarURL').val(),
		params:
		{
			cmd : 'cmdDatabaseUpload',
			RegionId: jQuery('#desinventarRegionId').val()
		},
		debug:false,
		multiple:false,
		onSubmit: function(id, Filename)
		{
			jQuery('#txtAdminDatabaseUploadFilename').val(Filename);
			jQuery('#txtAdminDatabaseUploadId').val(id);
			jQuery('#prgAdminDatabaseUploadProgressMark').css('width', '0px');
			jQuery('#divFileUploaderControl .qq-upload-button-text').hide();
			jQuery('#btnAdminDatabaseUploadCancel').show();
		},
		onProgress: function(id, Filename, loaded, total)
		{
			var maxWidth = jQuery('#prgAdminDatabaseUploadProgressBar').width();
			var percent  = parseInt(loaded/total * 100);
			var width    = parseInt(percent * maxWidth/100);
			jQuery('#prgAdminDatabaseUploadProgressMark').css('width', width);
		},
		onComplete: function(id, Filename, data)
		{
			jQuery('#btnAdminDatabaseUploadCancel').hide();
			jQuery('#txtAdminDatabaseUploadFilename').val(data.filename);
			if (parseInt(data.Status)>0)
			{
				jQuery('#txtAdminDatabaseUploadRegionId').text(data.Info.RegionId);
				jQuery('#txtAdminDatabaseUploadRegionLabel').text(data.Info.RegionLabel);
				jQuery('#txtAdminDatabaseUploadLangIsoCode').text(data.Info.LangIsoCode);
				jQuery('#txtAdminDatabaseUploadCountryIso').text(data.Info.CountryIso);
				doAdminDatabaseUploadStatusMsg('');
				jQuery('#divAdminDatabaseUploadParameters').show();
			}
			else
			{
				doAdminDatabaseUploadStatusMsg('msgAdminDatabaseUploadErrorOnUpload');
			}
		},
		onCancel: function(id, Filename)
		{
		},
	});
	jQuery('#divFileUploaderControl .qq-upload-button-text').html(jQuery('#msgAdminDatabaseUploadChooseFile').val());
	jQuery('#divFileUploaderControl .qq-upload-list').hide();

	jQuery('#btnAdminDatabaseUploadCancel').click(function() {
		doAdminDatabaseUploadReset();
		uploader.cancel(jQuery('#txtAdminDatabaseUploadId').val());
	});
}

function doAdminDatabaseUploadReset()
{
	jQuery('#txtAdminDatabaseUploadFilename').val('');
	jQuery('#prgAdminDatabaseUploadProgressMark').css('width', '0px');
	jQuery('#btnAdminDatabaseUploadCancel').hide();
	jQuery('#divFileUploaderControl .qq-upload-button-text').show();
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
		width:600, height:330, modal:false,
		closeAction:'hide', plain: false, animCollapse: false,
		items: new Ext.Panel({
			contentEl: 'divDatabaseUploadContent',
			autoScroll: true
		}),
		buttons: [
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
	jQuery('.clsAdminDatabaseUpload').hide();
	Ext.getCmp('wndDatabaseUpload').show();
} // doAdminDatabaseUploadAction
