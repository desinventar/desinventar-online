/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGeolevels()
{
	doGeolevelsUploaderCreate();

	jQuery('#tbodyGeolevels_List').on('click', 'tr', function(e) {
		jQuery('#frmGeolevel .GeoLevelId').val(jQuery('.GeoLevelId',this).text());
		jQuery('#frmGeolevel .GeoLevelName').val(jQuery('.GeoLevelName',this).text());
		jQuery('#frmGeolevel .GeoLevelDesc').val(jQuery('.GeoLevelDesc',this).prop('title'));
		jQuery('#frmGeolevel .GeoLevelActiveLabel').hide();
		jQuery('#frmGeolevel .GeoLevelActiveCheckbox').prop('checked', jQuery('.GeoLevelActive :input',this).is(':checked')).change().hide();
		jQuery('#divGeolevels_Edit').show();
		jQuery('#btnGeolevels_Add').hide();
		jQuery('#frmGeolevel .GeoLevelLayerName').val('');
		jQuery('#frmGeolevel .GeoLevelLayerCode').val('');
		jQuery('#frmGeolevel .GeoLevelLayerParentCode').val('');
		jQuery('#frmGeolevel .filename').val('');
		jQuery('#frmGeolevel .uploaded').text('');
	}).on('mouseover', 'tr', function(event) {
			jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	});

	jQuery('#btnGeolevels_Add').click(function() {
		jQuery('#divGeolevels_Edit').show();
		jQuery(this).hide();
		jQuery('#frmGeolevel .GeoLevelId').val('-1');
		jQuery('#frmGeolevel .GeoLevelName').val('');
		jQuery('#frmGeolevel .GeoLevelDesc').val('');
		jQuery('#frmGeolevel .GeoLevelActiveLabel').hide();
		jQuery('#frmGeolevel .GeoLevelActiveCheckbox').prop('checked', true).change().hide();
	});

	jQuery('#frmGeolevel .btnSave').click(function() {
		jQuery('#frmGeolevel').trigger('submit');
	});

	jQuery('#frmGeolevel .btnCancel').click(function() {
		jQuery('#frmGeolevel .Filename').val('');
		jQuery('#frmGeolevel .uploaded').hide();
		jQuery('#divGeolevels_Edit').hide();
		jQuery('#btnGeolevels_Add').show();
	});

	jQuery('#frmGeolevel .GeoLevelActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#frmGeolevel .GeoLevelActive').val(v);
	});

	jQuery('#frmGeolevel').submit(function() {
		var bContinue = true;
		jQuery(':input', this).each(function() {
			jQuery(this).val(jQuery.trim(jQuery(this).val()));
		});

		jQuery('#frmGeolevel .GeoLevelLayerCode').val(jQuery('#frmGeolevel .GeoLevelLayerCode').val().toUpperCase());
		jQuery('#frmGeolevel .GeoLevelLayerParentCode').val(jQuery('#frmGeolevel .GeoLevelLayerParentCode').val().toUpperCase());
		jQuery('#frmGeolevel .GeoLevelLayerName').val(jQuery('#frmGeolevel .GeoLevelLayerName').val().toUpperCase());

		if (bContinue && jQuery.trim(jQuery('#frmGeolevel .GeoLevelName').val()) == '')
		{
			jQuery('#frmGeolevel .GeoLevelName').highlight();
			jQuery('div.status .statusRequiredFields').show();
			bContinue = false;
		}
		if (bContinue)
		{
			var iSize = jQuery('#frmGeolevel .filename').size();
			var iCount = 0;
			var bUpdateCarto = false;
			jQuery('#frmGeolevel .filename').each(function() {
				if (jQuery(this).val() != '')
				{
					iCount++;
				}
			});
			bUpdateCarto = (iCount > 0);
			if (bUpdateCarto && bContinue && (iCount < iSize))
			{
				bContinue = false;
				jQuery('div.status .statusMissingFiles').show();
			}
			if (bUpdateCarto && bContinue && jQuery('#frmGeolevel .GeoLevelLayerCode').val() == '')
			{
				jQuery('#frmGeolevel .GeoLevelLayerCode').highlight();
				jQuery('div.status .statusRequiredFields').show();
				bContinue = false;
			}

			if (bUpdateCarto && bContinue && jQuery('#frmGeolevel .GeoLevelLayerName').val() == '')
			{
				jQuery('#frmGeolevel .GeoLevelLayerName').highlight();
				jQuery('div.status .statusRequiredFields').show();
				bContinue = false;
			}
		}

		if (bContinue)
		{
			jQuery('body').trigger('cmdMainWaitingShow');
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdGeolevelsUpdate',
					RegionId : jQuery('#desinventarRegionId').val(),
					GeoLevel : jQuery('#frmGeolevel').toObject()
				},
				function(data)
				{
					jQuery('body').trigger('cmdMainWaitingHide');
					if (parseInt(data.Status) > 0)
					{
						jQuery('#divGeolevels_Edit').hide();
						jQuery('#btnGeolevels_Add').show();
						jQuery('div.status .statusUpdateOk').show();
						doGeolevelsPopulateList(data.GeolevelsList);
					}
					else
					{
						jQuery('div.status .statusUpdateError').show();
					}
					//If empty geography items, create from DBF
					if (parseInt(data.GeographyItemsCount) == 0)
					{
						jQuery('div.status span.status').hide();
						jQuery('div.status span.statusCreatingGeography').show();
						jQuery.post(
							jQuery('#desinventarURL').val() + '/',
							{
								cmd           : 'cmdGeolevelsImportGeography',
								RegionId      : jQuery('#desinventarRegionId').val(),
								GeoLevel      : jQuery('#frmGeolevel').toObject()
							},
							function(data)
							{
								jQuery('div.status span.statusCreatingGeography').hide();
								jQuery('div.status .statusUpdateOk').show();
								setTimeout(function () {
									jQuery('div.status span.status').hide();
								}, 3000);
							},
							'json'
						);
					}
					else
					{
						setTimeout(function () {
							jQuery('div.status span.status').hide();
						}, 2500);
					}
				},
				'json'
			);
		}		
		else
		{
			setTimeout(function() {
				jQuery('div.status .status').hide();
				jQuery('#frmGeolevel .GeoLevelName').unhighlight();
				jQuery('#frmGeolevel .GeoLevelLayerCode').unhighlight();
				jQuery('#frmGeolevel .GeoLevelLayerName').unhighlight();
			}, 2500);
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
		var HasMap = ((value.GeoLevelLayerFile != undefined) && (value.GeoLevelLayerFile != ''));
		jQuery('.HasMap :input', clonedRow).prop('checked', HasMap);
		jQuery('.GeoLevelLayerFile', clonedRow).html(value.GeoLevelLayerFile);
		GeoLevelLayerParentCode = value.GeoLevelLayerCode;
		jQuery('#tbodyGeolevels_List').append(clonedRow);
	});
	jQuery('#tblGeolevels_List .GeoLevelId').hide();
	jQuery('#tblGeolevels_List .GeoLevelActive').hide();
	jQuery('#tbodyGeolevels_List tr:even').addClass('under');
} //doGeolevelsPopulateList()

function doGeolevelsPopulateFieldList(prmSelector, prmValues)
{
	jQuery(prmSelector).empty();
	jQuery(prmSelector).append(jQuery('<option>', { value : '' }).text('--'));
	jQuery.each(prmValues, function(key, value) {
		jQuery(prmSelector).append(jQuery('<option>', { value : value }).text(value));
	});
}

function doGeolevelsUploaderCreate()
{
	jQuery('#frmGeolevel tr.FileUploader').each(function() {
		var fileExt = jQuery(this).data('ext');
		var fileUploaderControlId = jQuery(this).find('.FileUploaderControl').attr('id');
		var uploader = new qq.FileUploader({
			element: document.getElementById(fileUploaderControlId),
			action: jQuery('#desinventarURL').val() + '/',
			params:
			{
				cmd        : 'cmdGeolevelsUpload',
				RegionId   : jQuery('#desinventarRegionId').val(),
				UploadExt  : fileExt,
				GeoLevelId : jQuery('#frmGeolevel .GeoLevelId').val()
			},
			debug:false,
			multiple:false,
			allowedExtensions: [fileExt],
			onSubmit: function(id, Filename)
			{
				var ext = this.allowedExtensions[0];
				var row = jQuery('#frmGeolevel tr:data("ext=' + ext + '")');
				jQuery('.UploadId', row).val(id);
				jQuery('.uploaded', row).hide();
				jQuery('#frmGeolevel .ProgressBar').show();
				jQuery('#frmGeolevel .ProgressMark').css('width', '0px');
				jQuery('.FileUploaderControl .qq-upload-button-text', this).hide();
				jQuery('#frmGeolevel .btnUploadCancel').show();
			},
			onProgress: function(id, Filename, loaded, total)
			{
				var maxWidth = jQuery('#frmGeolevel .ProgressBar').width();
				var percent  = parseInt(loaded/total * 100);
				var width    = parseInt(percent * maxWidth/100);
				jQuery('#frmGeolevel .ProgressMark').css('width', width);
			},
			onComplete: function(id, Filename, data)
			{
				var ext = this.allowedExtensions[0];
				var row = jQuery('#frmGeolevel tr:data("ext=' + ext + '")');
				doGeolevelsUploaderReset();
				jQuery('div.status .status').hide();
				jQuery('#frmGeolevel .btnUploadCancel').hide();
				if (parseInt(data.Status)>0)
				{
					jQuery('.filename', row).val(data.filename);
					jQuery('.uploaded', row).text(data.filename_orig).show();
					jQuery('div.status .statusuploadOk').show();
					if (data.DBFFields != undefined)
					{
						doGeolevelsPopulateFieldList('#frmGeolevel .GeoLevelLayerName', data.DBFFields);
						doGeolevelsPopulateFieldList('#frmGeolevel .GeoLevelLayerCode', data.DBFFields);
						doGeolevelsPopulateFieldList('#frmGeolevel .GeoLevelLayerParentCode', data.DBFFields);
					}
					setTimeout(function() {
						jQuery('div.status .status').hide();
					}, 2000);
				}
				else
				{
					jQuery('div.status .statusUploadError').show();
					setTimeout(function() {
						jQuery('div.status .status').hide();
					}, 2000);
				}
			},
			onCancel: function(id, Filename)
			{
				doGeolevelsUploaderReset();
			}
		});
	});
	jQuery('#frmGeolevel .FileUploaderControl .qq-upload-button-text').html(jQuery('#msgGeolevels_UploadChooseFile').text());
	jQuery('#frmGeolevel .FileUploaderControl .qq-upload-list').hide();
	jQuery('#frmGeolevel .btnUploadCancel').click(function() {
		jQuery('#frmGeolevel .UploadId').each(function() {
			uploader.cancel(jQuery(this).val());
		});
	}).hide();
	jQuery('#frmGeolevel .uploaded').hide();
	jQuery('div.status .status').hide();
} //doGeolevelsUploaderCreate()

function doGeolevelsUploaderReset()
{
	jQuery('#frmGeolevel .ProgressBar').hide();
	jQuery('#frmGeolevel .ProgressMark').css('width', '0px');
	jQuery('#frmGeolevel .UploadCancel').hide();
	jQuery('#divGeolevels_FileUploaderControl .qq-upload-button-text').show();
} //doGeolevelsUplaoderReset()
