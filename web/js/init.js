/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyInit()
{
	jQuery('body').on('cmdDatabaseLoadData', function() {
		doDatabaseLoadData();
	});

	jQuery(window).bind('hashchange', function(e) {
		var url = jQuery.param.fragment();
		var options = url.split('/');
		console.log('hashchange : ' + options[0]);
		switch(options[0])
		{
			default:
				var RegionId = options[0];
				jQuery('#desinventarRegionId').val(RegionId);
				console.log('cmdDatabaseLoadData');
				jQuery('body').trigger('cmdDatabaseLoadData');
			break;
		}
	});
} //onReadyInit()

function doDatabaseLoadData()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd      : 'cmdDatabaseLoadData',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('body').data('params', data.params);
				//Compatibility with old methods desinventarinfo.tpl
				jQuery('#desinventarUserId').val(data.params.UserId);
				jQuery('#desinventarUserFullName').val(data.params.UserFullName);
				jQuery('#desinventarUserRole').val(data.params.UserRole);
				jQuery('#desinventarUserRoleValue').val(data.params.UserRoleValue);
				console.log(data.RegionId);
				if (data.RegionId != '')
				{
					// Initialize data-* components for body
					jQuery('body').data('RegionId', data.RegionId);
					jQuery('body').data('GeolevelsList', data.GeolevelsList);
					jQuery('body').data('EventList', data.EventList);
					jQuery('body').data('CauseList', data.CauseList);
					jQuery('body').data('EEFieldList', data.EEFieldList);
					jQuery('body').data('RecordCount', data.RecordCount);

					
					var dataItems = jQuery('body').data();
					jQuery.each(dataItems, function(index, value) {
						if (index.substr(0,13) === 'GeographyList')
						{
							jQuery('body').removeData(index);
						}
					});
					jQuery('body').data('GeographyList', data.GeographyList);
					
					jQuery('#desinventarLang').val(data.params.LangIsoCode);
					jQuery('#desinventarRegionId').val(data.params.RegionId);
					jQuery('#desinventarRegionLabel').val(data.params.RegionLabel);
					jQuery('#desinventarNumberOfRecords').val(data.RecordCount);
					// Trigger event on mainblock components to update them
					console.log('before initialize');
					jQuery('.mainblock').trigger('cmdInitialize');
					console.log('after initialize');
				}
			}
			else
			{
				jQuery('#desinventarRegionId').val('');
				window.location.hash = '';
			}
			// Trigger ViewportShow
			jQuery('body').trigger('cmdViewportShow');
		},
		'json'
	);
} //doDatabaseLoadData()
