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

	jQuery('#btnDatabaseUploadStart').click(function() {
		jQuery('#divDatabaseUploadParameters').hide();
		jQuery('#divDatabaseUploadControl').show();
	});

	jQuery('#btnDatabaseUploadReplace').click(function() {
		jQuery('.clsDatabaseUploadButtons').hide();
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
					jQuery('.clsDatabaseUploadButtons').show();
					doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateError');
				}
			},
			'json'
		);
	});

	jQuery('#btnDatabaseUploadCopy').click(function() {
		jQuery('.clsDatabaseUploadButtons').hide();
		doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForCopy');
		jQuery.post(jQuery('#desinventarURL').val() + '/',
			{
				cmd: 'cmdDatabaseCopy',
				RegionId: jQuery('#txtDatabaseUploadRegionId').text(),
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
					jQuery('.clsDatabaseUploadButtons').show();
					doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateError');
				}
			},
			'json'
		);
	});

	jQuery('#btnDatabaseUploadReplaceCancel').click(function() {
		if (jQuery('#txtDatabaseUploadFilename').val() != '')
		{
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
					Ext.getCmp('wndDatabaseUpload').hide();
				},
				'json'
			);
		}
		else
		{
			Ext.getCmp('wndDatabaseUpload').hide();
		}
	});

} //onReadyDatabaseUpload

function doAdminDatabaseCreateUploader()
{
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
			jQuery('#prgDatabaseUploadProgressBar').show();
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
				jQuery('.clsDatabaseUploadType').hide();
				var CurRegionId = jQuery('#desinventarRegionId').val();
				if (CurRegionId != '') 
				{
					jQuery('#txtDatabaseUploadConfirmReplace').show();
					jQuery('#btnDatabaseUploadReplace').show();
				}
				else
				{
					jQuery('#txtDatabaseUploadConfirmCopy').show();
					jQuery('#btnDatabaseUploadCopy').show();
				}
				doDatabaseUploadSetParameters(data.RegionInfo);
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
		}
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
	jQuery('#txtDatabaseUploadRegionId').text('');
	jQuery('#txtDatabaseUploadRegionLabel').text('');
	jQuery('#txtDatabaseUploadCountryIso').text('');
	jQuery('#txtDatabaseUploadRegionLastUpdate').text('');
	jQuery('#txtDatabaseUploadNumberOfRecords').text('');
	
	jQuery('#prgDatabaseUploadProgressBar').hide();
	jQuery('#prgDatabaseUploadProgressMark').css('width', '0px');
	jQuery('#btnDatabaseUploadCancel').hide();
	jQuery('.clsDatabaseUploadButtons').show();
	jQuery('#divFileUploaderControl .qq-upload-button-text').show();

	jQuery('#divDatabaseUploadControl').hide();
	jQuery('#divDatabaseUploadParameters').hide();

	if (jQuery('#desinventarRegionId').val() == '')
	{
		jQuery('#divDatabaseUploadControl').show();
	}
	else
	{
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseGetInfo',
				RegionId : jQuery('#desinventarRegionId').val()				
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					doDatabaseUploadSetParameters(data.RegionInfo);
					jQuery('#divDatabaseUploadParameters').show();
					jQuery('.clsDatabaseUploadType').hide();
					jQuery('#txtDatabaseUploadConfirmStart').show();
					jQuery('#btnDatabaseUploadStart').show();
					jQuery('#btnDatabaseUploadReplaceCancel').show();
				}
			},
			'json'
		);
	}
}

function doDatabaseUploadSetParameters(RegionInfo)
{
	jQuery('#txtDatabaseUploadRegionId').text(RegionInfo.RegionId);
	jQuery('#txtDatabaseUploadRegionLabel').text(RegionInfo.RegionLabel);
	jQuery('#txtDatabaseUploadCountryIso').text(RegionInfo.CountryIso + ' - ' + RegionInfo.CountryName);
	jQuery('#txtDatabaseUploadRegionLastUpdate').text(RegionInfo.RegionLastUpdate);
	jQuery('#trDatabaseUploadNumberOfRecords').show();
	jQuery('#txtDatabaseUploadNumberOfRecords').text(RegionInfo.NumberOfRecords);
	if (parseInt(RegionInfo.NumberOfRecords) < 1)
	{
		jQuery('#trDatabaseUploadNumberOfRecords').hide();
	}
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
		})
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
