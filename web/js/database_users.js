/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseUsers()
{
	jQuery('#divDatabaseUsers_Edit').hide();
	jQuery('#txtDatabaseUsers_RegionActive').click(function() {
		jQuery('#fldDatabaseUsers_RegionActive').trigger('click');
	});
	jQuery('#txtDatabaseUsers_RegionPublic').click(function() {
		jQuery('#fldDatabaseUsers_RegionPublic').trigger('click');
	});

	jQuery('#btnDatabaseUsers_OptionsSave').click(function() {
		var RegionStatus = jQuery('#fldDatabaseUsers_RegionStatus');
		RegionStatus.val(0);
		if (jQuery('#fldDatabaseUsers_RegionActive').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 1);
		}
		if (jQuery('#fldDatabaseUsers_RegionPublic').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 2);
		}
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd          : 'cmdDatabaseUsersUpdateOptions',
				RegionId     : jQuery('#desinventarRegionId').val(),
				RegionStatus : jQuery('#fldDatabaseUsers_RegionStatus').val()
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					doDatabaseUsersUpdateOptions(data.RegionInfo);
				}
			},
			'json'
		);
	});
		
	jQuery('#btnDatabaseUsers_Add').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').show();
	});

	jQuery('#btnDatabaseUsers_Save').click(function() {
		//Validation
		if (jQuery('#fldDatabaseUsers_UserId').val() == '')
		{
			// Error Message
		}
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseUsersSetRole',
				RegionId : jQuery('#desinventarRegionId').val(),
				UserId   : jQuery('#fldDatabaseUsers_UserId').val(),
				UserRole : jQuery('#fldDatabaseUsers_UserRole').val()
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					doDatabaseUsersPopulateUserRoleList(data.UserRoleList);
					jQuery('#divDatabaseUsers_Edit').hide();
				}
			},
			'json'
		);
	});

	jQuery('#btnDatabaseUsers_Cancel').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').hide();
	});

	jQuery('#tbodyDatabaseUsers_List').delegate('tr', 'click', function(e) {
		jQuery('#fldDatabaseUsers_UserId').val(jQuery('.UserId', this).text());
		jQuery('#fldDatabaseUsers_UserRole').val(jQuery('.UserRole', this).text());
		jQuery('#divDatabaseUsers_Edit').show();
	});

	jQuery('#fldDatabaseUsers_UserId').change(function() {
		var UserId = jQuery(this).val();
		jQuery('#fldDatabaseUsers_UserRole').val('');
		jQuery('#tbodyDatabaseUsers_List tr').each(function(index, Element) {
			if (jQuery('.UserId', this).text() == UserId)
			{
				jQuery('#fldDatabaseUsers_UserRole').val(jQuery('.UserRole', this).text());
			}
		});
	});
}

function doDatabaseUsersReset()
{
	jQuery('#fldDatabaseUsers_RegionActive').prop('checked', true);
	jQuery('#fldDatabaseUsers_RegionPublic').prop('checked', true);	
	jQuery('#fldDatabaseUsers_UserId').val('');
	jQuery('#fldDatabaseusers_UserRole').val('');
	jQuery('#tblDatabaseUsers_List .UserId').hide();
	jQuery('#tblDatabaseUsers_List .UserRole').hide();
}

function doDatabaseUsersPopulateUserRoleList(UserRoleList)
{
	jQuery('#tbodyDatabaseUsers_List').find('tr:gt(0)').remove();
	jQuery.each(UserRoleList, function(index, value) {
		var clonedRow = jQuery('#tbodyDatabaseUsers_List tr:last').clone().show();
		jQuery('.UserId', clonedRow).html(index);
		jQuery('.UserName', clonedRow).html(jQuery('#fldDatabaseUsers_UserId option[value="' + index + '"]').text());
		jQuery('.UserRole', clonedRow).html(value.UserRole);
		jQuery('.UserRoleLabel', clonedRow).html(jQuery('#fldDatabaseUsers_UserRole option[value="' + value.UserRole + '"]').text());
		jQuery('#tbodyDatabaseUsers_List').append(clonedRow);
	});
	jQuery('#tblDatabaseUsers_List .UserId').hide();
	jQuery('#tblDatabaseUsers_List .UserRole').hide();
}

function doDatabaseUsersUpdateOptions(RegionInfo)
{
	// RegionActive/RegionPublic are set based on RegionStatus value
	jQuery('#fldDatabaseUsers_RegionActive').prop('checked', false);
	if (parseInt(RegionInfo.RegionStatus) & 1)
	{
		jQuery('#fldDatabaseUsers_RegionActive').prop('checked', true);
	}
	jQuery('#fldDatabaseUsers_RegionPublic').prop('checked', false);
	if (parseInt(RegionInfo.RegionStatus) & 2)
	{
		jQuery('#fldDatabaseUsers_RegionPublic').prop('checked', true);
	}
} //doDatabaseUsersUpdateOptions()

function doDatabaseUsersPopulateLists()
{
	doDatabaseUsersReset();
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd      : 'cmdDatabaseUsersGetList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				doDatabaseUsersUpdateOptions(data.RegionInfo);
				jQuery.each(data.UserList, function(key, value) {
					jQuery('#fldDatabaseUsers_UserId').append(jQuery('<option>', { value : key }).text(value));
				});
				doDatabaseUsersPopulateUserRoleList(data.UserRoleList);				
			}
		},
		'json'
	);
}

