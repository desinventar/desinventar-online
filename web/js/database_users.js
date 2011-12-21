/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseUsers()
{
	jQuery('#btnDatabaseUsers_Add').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').show();
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
}

function doDatabaseUsersReset()
{
	jQuery('#fldDatabaseUsers_UserId').val('');
	jQuery('#fldDatabaseusers_UserRole').val('');
}

function doDatabaseUsersPopulateLists()
{
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
				jQuery.each(data.UserList, function(key, value) {
					jQuery('#fldDatabaseUsers_UserId').append(jQuery('<option>', { value : key }).text(value));
				});
				
				jQuery.each(data.UserRoleList, function(index, value) {
					var clonedRow = jQuery('#tbodyDatabaseUsers_List tr:last').clone().show();
					jQuery('.UserId', clonedRow).html(index);
					jQuery('.UserName', clonedRow).html(index);
					jQuery('.UserRole', clonedRow).html(value);
					jQuery('#tbodyDatabaseUsers_List').append(clonedRow);
				});
				jQuery('#tblDatabaseUsers_List .UserId').hide();
			}
		},
		'json'
	);
}

