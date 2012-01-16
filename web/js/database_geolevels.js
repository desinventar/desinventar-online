/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyGeolevels()
{
	doGeolevelsUploaderCreate();

	jQuery('#tbodyGeolevels_List').on('click', 'tr', function(e) {
		jQuery('#frmGeolevels_Edit .GeoLevelId').val(jQuery('.GeoLevelId',this).text());
		jQuery('#frmGeolevels_Edit .GeoLevelName').val(jQuery('.GeoLevelName',this).text());
		jQuery('#frmGeolevels_Edit .GeoLevelDesc').val(jQuery('.GeoLevelDesc',this).prop('title'));
		jQuery('#frmGeolevels_Edit .GeoLevelActiveLabel').hide();
		jQuery('#frmGeolevels_Edit .GeoLevelActiveCheckbox').prop('checked', jQuery('.GeoLevelActive :input',this).is(':checked')).change().hide();
		jQuery('#divGeolevels_Edit').show();
		jQuery('#btnGeolevels_Add').hide();
		jQuery('#frmGeocarto .GeoLevelId').val(jQuery('.GeoLevelId',this).text());
		jQuery('#divGeocarto_Edit').show();
	});
	jQuery('#btnGeolevels_Add').click(function() {
		jQuery('#divGeolevels_Edit').show();
		jQuery(this).hide();
		jQuery('#frmGeolevels_Edit .GeoLevelId').val('-1');
		jQuery('#frmGeolevels_Edit .GeoLevelName').val('');
		jQuery('#frmGeolevels_Edit .GeoLevelDesc').val('');
		jQuery('#frmGeolevels_Edit .GeoLevelActiveLabel').hide();
		jQuery('#frmGeolevels_Edit .GeoLevelActiveCheckbox').prop('checked', true).change().hide();
	});

	jQuery('#frmGeolevels_Edit .btnSave').click(function() {
		jQuery('#frmGeolevels_Edit').trigger('submit');
	});

	jQuery('#frmGeolevels_Edit .btnCancel').click(function() {
		jQuery('#divGeolevels_Edit').hide();
		jQuery('#divGeocarto_Edit').hide();
		jQuery('#btnGeolevels_Add').show();
	});

	jQuery('#frmGeolevels_Edit .GeoLevelActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#frmGeolevels_Edit .GeoLevelActive').val(v);
	});

	jQuery('#frmGeolevels_Edit').submit(function() {
		var bContinue = true;
		if (bContinue && jQuery.trim(jQuery('#frmGeolevels_Edit .GeoLevelName').val()) == '')
		{
			jQuery('#frmGeolevels_Edit .GeoLevelName').highlight();
			jQuery('#msgGeolevels_ErrorEmtpyFields').show();
			setTimeout(function () {
				jQuery('#frmGeolevels_Edit .GeoLevelName').unhighlight();
				jQuery('.clsGeolevelsStatus').hide();
			}, 2500);
			bContinue = false;
		}

		if (bContinue)
		{
			jQuery('body').trigger('cmdMainWaitingShow');
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdGeolevelsUpdate',
					RegionId : jQuery('#desinventarRegionId').val(),
					GeoLevel : jQuery('#frmGeolevels_Edit').serializeObject()
				},
				function(data)
				{
					jQuery('body').trigger('cmdMainWaitingHide');
					if (parseInt(data.Status) > 0)
					{
						jQuery('#divGeolevels_Edit').hide();
						jQuery('#btnGeolevels_Add').show();
						jQuery('#msgGeolevels_UpdateOk').show();
						doGeolevelsPopulateList(data.GeolevelsList);
					}
					else
					{
						jQuery('#msgGeolevels_UpdateError').show();
					}					
					setTimeout(function () {
						jQuery('.clsGeolevelsStatus').hide();
					}, 2500);
				},
				'json'
			);
		}		
		return false;
	});

	// Attach events to main page
	jQuery('body').on('cmdGeolevelsShow', function() {
		jQuery('body').trigger('cmdMainWaitingShow');
		jQuery('.clsGeolevelsStatus').hide();
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdGeolevelsGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data)
			{
				jQuery('body').trigger('cmdMainWaitingHide');
				if (parseInt(data.Status) > 0)
				{
					doGeolevelsPopulateList(data.GeolevelsList);
				}
			},
			'json'
		);
	});
} //onReadyGeolevels()

function doGeolevelsPopulateList(GeolevelsList)
{
	jQuery('#divGeolevels_Edit').hide();
	jQuery('#tbodyGeolevels_List').find('tr:gt(0)').remove();
	jQuery('#tbodyGeolevels_List').find('tr:first').hide();
	jQuery('#tbodyGeolevels_List').find('tr').removeClass('under');
	jQuery.each(GeolevelsList, function(index, value) {
		var clonedRow = jQuery('#tbodyGeolevels_List tr:last').clone().show();
		jQuery('.GeoLevelId', clonedRow).html(index);
		jQuery('.GeoLevelName', clonedRow).html(value.GeoLevelName);
		jQuery('.GeoLevelDesc', clonedRow).html(value.GeoLevelDesc.substring(0,150));
		jQuery('.GeoLevelDesc', clonedRow).prop('title', value.GeoLevelDesc);
		jQuery('.GeoLevelActive :input', clonedRow).prop('checked', value.GeoLevelActive>0);
		jQuery('.GeoLevelLayerFile', clonedRow).html(value.GeoLevelLayerFile);
		jQuery('.GeoLevelLayerCode', clonedRow).html(value.GeoLevelLayerCode);
		jQuery('.GeoLevelLayerName', clonedRow).html(value.GeoLevelLayerName);
		jQuery('#tbodyGeolevels_List').append(clonedRow);
	});
	jQuery('#tblGeolevels_List .GeoLevelId').hide();
	jQuery('#tblGeolevels_List .GeoLevelActive').hide();
	jQuery('#tblGeolevels_List .GeoLevelLayerFile').hide();
	jQuery('#tblGeolevels_List .GeoLevelLayerCode').hide();
	jQuery('#tblGeolevels_List .GeoLevelLayerName').hide();
	jQuery('#tbodyGeolevels_List tr:even').addClass('under');
} //doGeolevelsPopulateList()

function doGeolevelsUploaderCreate()
{
	var uploader = new qq.FileUploader({
		element: document.getElementById('divGeolevels_FileUploaderControl'),
		action: jQuery('#desinventarURL').val() + '/',
		params:
		{
			cmd        : 'cmdGeolevelsCartographyUpload',
			RegionId   : jQuery('#desinventarRegionId').val(),
			UploadExt  : jQuery('#frmGeocarto .FileExt').val(),
			GeoLevelId : jQuery('#frmGeocarto .GeoLevelId').val()
		},
		debug:false,
		multiple:false,
		allowedExtensions: ['shp','shx','dbf'],
		onSubmit: function(id, Filename)
		{
			jQuery('#frmGeocarto .Filename').text(Filename);

			jQuery('#frmGeocarto .UploadId').val(id);
			jQuery('#frmGeocarto .ProgressBar').show();
			jQuery('#frmGeocarto .ProgressMark').css('width', '0px');
			jQuery('#divGeolevels_FileUploaderControl .qq-upload-button-text').hide();
			jQuery('#frmGeocarto .btnUploadCancel').show();
			//doDatabaseUploadStatusMsg('msgDatabaseUploadWaitForUpload');
		},
		onProgress: function(id, Filename, loaded, total)
		{
			var maxWidth = jQuery('#frmGeocarto .ProgressBar').width();
			var percent  = parseInt(loaded/total * 100);
			var width    = parseInt(percent * maxWidth/100);
			jQuery('#frmGeocarto .ProgressMark').css('width', width);
		},
		onComplete: function(id, Filename, data)
		{
			doGeolevelsUploaderReset();
			jQuery('#frmGeocarto .Filename').text(data.filename);
			if (parseInt(data.Status)>0)
			{
			}
			else
			{
				//doDatabaseUploadStatusMsg('msgDatabaseUploadErrorOnUpload');
			}
		},
		onCancel: function(id, Filename)
		{
		}
	});
	jQuery('#divGeolevels_FileUploaderControl .qq-upload-button-text').html(jQuery('#msgGeolevels_UploadChooseFile').text());
	jQuery('#divGeolevels_FileUploaderControl .qq-upload-list').hide();

	jQuery('#frmGeocarto .btnUploadCancel').click(function() {
		//doDatabaseUploadReset();
		uploader.cancel(jQuery('#frmGeocarto .UploadId').val());
	});
} //doGeolevelsUploaderCreate()

function doGeolevelsUploaderReset()
{
	jQuery('#frmGeocarto .ProgressBar').hide();
	jQuery('#frmGeocarto .ProgressMark').css('width', '0px');
	jQuery('#frmGeocarto .UploadCancel').hide();
	jQuery('#divGeolevels_FileUploaderControl .qq-upload-button-text').show();
} //doGeolevelsUplaoderReset()
