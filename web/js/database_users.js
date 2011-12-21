/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/

function onReadyDatabaseUsers()
{
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
					var clonedRow = jQuery('#tbodyDatabaseUsersList tr:last').clone().show();
					jQuery('.UserId', clonedRow).html(index);
					jQuery('.UserRole', clonedRow).html(value);
					jQuery('#tbodyDatabaseUsersList').append(clonedRow);
				});
			}
		},
		'json'
	);
}

