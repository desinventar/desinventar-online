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
		jQuery('#frmGeocarto .GeoLevelLayerCode').val('');
		jQuery('#frmGeocarto .GeoLevelLayerName').val('');
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
		jQuery('#tbodyGeolevels_List').append(clonedRow);
	});
	jQuery('#tblGeolevels_List .GeoLevelId').hide();
	jQuery('#tblGeolevels_List .GeoLevelActive').hide();
	jQuery('#tbodyGeolevels_List tr:even').addClass('under');
} //doGeolevelsPopulateList()

function doGeolevelsUploaderCreate()
{
	jQuery('#frmGeocarto tr.FileUploader').each(function() {
		var fileExt = jQuery(this).data('ext');
		var fileUploaderControlId = jQuery(this).find('.FileUploaderControl').attr('id');
		var uploader = new qq.FileUploader({
			element: document.getElementById(fileUploaderControlId),
			action: jQuery('#desinventarURL').val() + '/',
			params:
			{
				cmd        : 'cmdGeocartoUpload',
				RegionId   : jQuery('#desinventarRegionId').val(),
				UploadExt  : fileExt,
				GeoLevelId : jQuery('#frmGeocarto .GeoLevelId').val()
			},
			debug:false,
			multiple:false,
			allowedExtensions: [fileExt],
			onSubmit: function(id, Filename)
			{
				var ext = this.allowedExtensions[0];
				var row = jQuery('#frmGeocarto tr:data("ext=' + ext + '")');
				jQuery('.UploadId', row).val(id);
				jQuery('.uploaded', row).hide();
				jQuery('#frmGeocarto .ProgressBar').show();
				jQuery('#frmGeocarto .ProgressMark').css('width', '0px');
				jQuery('.FileUploaderControl .qq-upload-button-text', this).hide();
				jQuery('#frmGeocarto .btnUploadCancel').show();
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
				var ext = this.allowedExtensions[0];
				var row = jQuery('#frmGeocarto tr:data("ext=' + ext + '")');
				doGeolevelsUploaderReset();
				jQuery('#frmGeocarto .status').hide();
				jQuery('#frmGeocarto .btnUploadCancel').hide();
				if (parseInt(data.Status)>0)
				{
					jQuery('.filename', row).val(data.filename);
					jQuery('.uploaded', row).text(data.filename_orig).show();
					jQuery('#frmGeocarto .statusuploadOk').show();
					setTimeout(function() {
						jQuery('#frmGeocarto .status').hide();
					}, 2000);
				}
				else
				{
					jQuery('#frmGeocarto .statusUploadError').show();
					setTimeout(function() {
						jQuery('#frmGeocarto .status').hide();
					}, 2000);
				}
			},
			onCancel: function(id, Filename)
			{
				doGeolevelsUploaderReset();
			}
		});
	});
	jQuery('#frmGeocarto .FileUploaderControl .qq-upload-button-text').html(jQuery('#msgGeolevels_UploadChooseFile').text());
	jQuery('#frmGeocarto .FileUploaderControl .qq-upload-list').hide();
	jQuery('#frmGeocarto .btnUploadCancel').click(function() {
		jQuery('#frmGeocarto .UploadId').each(function() {
			uploader.cancel(jQuery(this).val());
		});
	}).hide();
	jQuery('#frmGeocarto .uploaded').hide();
	jQuery('#frmGeocarto .status').hide();
	jQuery('#frmGeocarto .btnCancel').click(function() {
		jQuery('#frmGeocarto .Filename').val('');
		jQuery('#frmGeocarto .uploaded').hide();
	});
	jQuery('#frmGeocarto .btnSave').click(function() {
		jQuery('#frmGeocarto').trigger('submit');
	});
	jQuery('#frmGeocarto').submit(function() {
		var bContinue = true;
		jQuery('#frmGeocarto .filename').each(function() {
			if (jQuery(this).val() == '')
			{
				bContinue = false;
			}
		});
		if (bContinue == false)
		{
			jQuery('#frmGeocarto .statusMissingFiles').show();
		}

		if (jQuery('#frmGeocarto .GeoLevelLayerCode').val() == '')
		{
			jQuery('#frmGeocarto .GeoLevelLayerCode').highlight();
			jQuery('#frmGeocarto .statusRequiredFields').show();
		}

		if (jQuery('#frmGeocarto .GeoLevelLayerName').val() == '')
		{
			jQuery('#frmGeocarto .GeoLevelLayerName').highlight();
			jQuery('#frmGeocarto .statusRequiredFields').show();
		}

		if (bContinue)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				jQuery.extend(jQuery(this).serializeObject(),
				{
					cmd : 'cmdGeocartoUpdate',
					RegionId : jQuery('#desinventarRegionId').val()
				}),
				function(data)
				{
					if (parseInt(data.Status) > 0)
					{
						jQuery('#frmGeocarto .statusUpdateOk').show();
					}
					else
					{
						jQuery('#frmGeocarto .statusUpdateError').show();
					}
					setTimeout(function() {
						jQuery('#frmGeocarto .status').hide();
					}, 3000);
				},
				'json'
			);
		}
		else
		{
			setTimeout(function() {
				jQuery('#frmGeocarto .status').hide();
				jQuery('#frmGeocarto .GeoLevelLayerCode').unhighlight();
				jQuery('#frmGeocarto .GeoLevelLayerName').unhighlight();
			}, 2500);
		}
		return false;
	});
} //doGeolevelsUploaderCreate()

function doGeolevelsUploaderReset()
{
	jQuery('#frmGeocarto .ProgressBar').hide();
	jQuery('#frmGeocarto .ProgressMark').css('width', '0px');
	jQuery('#frmGeocarto .UploadCancel').hide();
	jQuery('#divGeolevels_FileUploaderControl .qq-upload-button-text').show();
} //doGeolevelsUplaoderReset()
