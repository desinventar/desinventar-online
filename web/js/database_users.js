/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDatabaseUsers()
{
	jQuery('.clsDatabaseUsersStatus').hide();
	jQuery('#divDatabaseUsers_Edit').hide();

	jQuery('#frmDiffusion .RegionActiveText').click(function() {
		jQuery('#frmDiffusion .RegionActive').trigger('click');
		return false;
	});
	jQuery('#frmDiffusion .RegionPublicText').click(function() {
		jQuery('#frmDiffusion .RegionPublic').trigger('click');
		return false;
	});

	jQuery('#frmDiffusion .btnCancel').click(function() {
		var RegionInfo = new Array();
		RegionInfo.RegionStatus = jQuery('#fldDatabaseUsers_RegionStatus').val();
		doDatabaseUsersUpdateOptions(RegionInfo);
		return false;
	});

	jQuery('#frmDiffusion .btnSave').click(function() {
		var RegionStatus = jQuery('#frmDiffusion .RegionStatus');
		RegionStatus.val(0);
		if (jQuery('#frmDiffusion .RegionActive').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 1);
		}
		if (jQuery('#frmDiffusion .RegionPublic').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 2);
		}
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd          : 'cmdDatabaseUsersUpdateOptions',
				RegionId     : jQuery('#desinventarRegionId').val(),
				RegionStatus : jQuery('#frmDiffusion .RegionStatus').val()
			},
			function(data)
			{
				jQuery('.clsDatabaseUsersStatus').hide();
				if (parseInt(data.Status) > 0)
				{
					doDatabaseUsersUpdateOptions(data.RegionInfo);
					jQuery('#txtDatabaseUsers_OptionsStatusOk').show();
				}
				else
				{
					jQuery('#txtDatabaseUsers_OptionsStatusError').show();
				}
				setTimeout(function() {
					jQuery('.clsDatabaseUsersStatus').hide();
				}, 3000);
			},
			'json'
		);
		return false;
	});
		
	jQuery('#btnDatabaseUsers_Add').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').show();
		return false;
	});

	jQuery('#frmUsers .btnCancel').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').hide();
		return false;
	});

	jQuery('#frmUsers .btnSave').click(function() {
		var bContinue = true;
		//Validation
		if (jQuery('#frmUsers .UserId').val() == '')
		{
			// Error Message
			jQuery('.clsDatabaseUsersStatus').hide();
			jQuery('#txtDatabaseUsers_RoleListEmptyFields').show();
			bContinue = false;
			setTimeout(function() {
				jQuery('.clsDatabaseUsersStatus').hide();
			}, 3000);
		} //if
		if (bContinue)
		{
			jQuery('#frmUsers').data('ReloadPageAfter', false);
			// User cannot remove his/her own AdminRegion permission
			if (jQuery('#frmUsers .UserId').val() == jQuery('#desinventarUserId').val())
			{
				if ( (jQuery('#frmUsers .UserRolePrev').val() == 'ADMINREGION') &&
				     (jQuery('#frmUsers .UserRolePrev').val() != jQuery('#frmUsers .UserRole').val()) )
				{
					bContinue = false;
					jQuery('#frmUsers .UserRole').val('ADMINREGION');
					jQuery('#txtDatabaseUsers_RoleListCannotRemoveAdminRole').show();	
					setTimeout(function() {
						jQuery('#txtDatabaseUsers_RoleListCannotRemoveAdminRole').hide();
					}, 2500);
				}
			} //if

			// If trying to assign a new AdminRegion confirm the change
			if ( (jQuery('#desinventarUserId').val() != 'root') && 
			     (jQuery('#desinventarUserId').val() != jQuery('#frmUsers .UserId').val()) )
			{
				if (jQuery('#frmUsers .UserRole').val() == 'ADMINREGION')
				{
					bContinue = false;
					sAdminNew     = jQuery('#frmUsers .UserId option[value="' + jQuery('#frmUsers .UserId').val() + '"]').text();

					var sAdminCurrent = '';
					jQuery('#tbodyDatabaseUsers_List tr:gt(0)').each(function() {
						if (jQuery('.UserRole', this).text() == 'ADMINREGION')
						{
							sAdminCurrent = jQuery('.UserName', this).text();
						}
					});

					sConfirmMsg = jQuery('#msgDatabaseUsers_ConfirmManagerPrompt1').text() + ' ' + 
					              sAdminCurrent + ' ' + 
					              jQuery('#msgDatabaseUsers_ConfirmManagerPrompt2').text() + ' ' + 
					              sAdminNew + ' ?';
					              //jQuery('#msgDatabaseUsers_ConfirmManagerPrompt3').text();
					Ext.Msg.show({
						title   : jQuery('#msgDatabaseUsers_ConfirmManagerTitle').text(),
						msg     : sConfirmMsg,
						buttons :
						{
							ok    : jQuery('#msgDatabaseUsers_Yes').text(),
							cancel: jQuery('#msgDatabaseUsers_No').text()
						},
						fn : function(whichButton)
						{
							if (whichButton == 'ok')
							{
								jQuery('#frmUsers').data('ReloadPageAfter', true);
								jQuery('#frmUsers').trigger('submit');
							}
							/*
							else
							{
								jQuery('#frmUsers .UserRole').val(jQuery('#frmUsers .UserRolePrev').val());
							}
							*/
						} //function
					});
				} //if
			} //if
			//Ext.MessageBox.confirm('Confirm','Are you sure you want to do that ?', doDatabaseUsersSaveRole);
			if (bContinue)
			{
				jQuery('#frmUsers').trigger('submit');
			}
		} //if
		return false;
	});

	jQuery('#frmUsers').submit(function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseUsersSetRole',
				RegionId : jQuery('#desinventarRegionId').val(),
				UserId   : jQuery('#frmUsers .UserId').val(),
				UserRole : jQuery('#frmUsers .UserRole').val()
			},
			function(data)
			{
				jQuery('.clsDatabaseUsersStatus').hide();
				if (parseInt(data.Status) > 0)
				{
					doDatabaseUsersPopulateUserRoleList(data.UserRoleList);
					jQuery('#divDatabaseUsers_Edit').hide();
					jQuery('#txtDatabaseUsers_RoleListStatusOk').show();
				}
				else
				{
					jQuery('#txtDatabaseUsers_RoleListStatusError').show();
				}
				setTimeout(function() {
					jQuery('.clsDatabaseUsersStatus').hide();
				}, 3000);
				var ReloadPageAfter = jQuery('#frmUsers').data('ReloadPageAfter');
				if (ReloadPageAfter)
				{
					window.location.reload(false);
				}				
			},
			'json'
		);
		return false;
	});

	jQuery('#tbodyDatabaseUsers_List').on('click', 'tr', function(e) {
		var UserId = jQuery.trim(jQuery('.UserId', this).text());
		jQuery('#frmUsers .UserId').val(UserId);
		jQuery('#frmUsers .UserRole').val(jQuery('.UserRole', this).text());
		jQuery('#frmUsers .UserRolePrev').val(jQuery('.UserRole', this).text());
		jQuery('#frmUsers .UserId').prop('disabled', false);
		jQuery('#frmUsers .UserRole').prop('disabled', false);
		
		jQuery('#txtDatabaseUsers_RoleListCannotRemoveAdminRole').hide();
		jQuery('#divDatabaseUsers_Edit').show();
	}).on('mouseover', 'tr', function(event) {
			jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	});

	jQuery('#frmUsers .UserId').change(function() {
		var UserId = jQuery(this).val();
		jQuery('#frmUsers .UserRole').val('');
		jQuery('#tbodyDatabaseUsers_List tr').each(function(index, Element) {
			if (jQuery('.UserId', this).text() == UserId)
			{
				jQuery(this).trigger('click');
			}
		});
	});
	
	jQuery('body').on('cmdDatabaseUsersShow', function() {
		doDatabaseUsersPopulateLists();
	});
} //onReadyDatabaseUsers()

function doDatabaseUsersReset()
{
	jQuery('#frmDiffusion .RegionActive').prop('checked', true);
	jQuery('#frmDiffusion .RegionPublic').prop('checked', true);	
	jQuery('#frmUsers .UserId').val('');
	jQuery('#frmUsers .UserRole').val('');
	jQuery('#frmUsers .UserId').prop('disabled', false);
	jQuery('#frmUsers .UserRole').prop('disabled', false);
	jQuery('#tblDatabaseUsers_List .UserId').hide();
	jQuery('#tblDatabaseUsers_List .UserRole').hide();
	jQuery('.clsDatabaseUsersStatus').hide();
} //doDatabaseUsersReset()

function doDatabaseUsersPopulateUserRoleList(UserRoleList)
{
	jQuery('#tbodyDatabaseUsers_List').find('tr:gt(0)').remove();
	jQuery('#tbodyDatabaseUsers_List').find('tr').hide();
	
	jQuery.each(UserRoleList, function(index, value) {
		var clonedRow = jQuery('#tbodyDatabaseUsers_List tr:last').clone().show();
		jQuery('.UserId', clonedRow).html(index);
		jQuery('.UserName', clonedRow).html(value.UserName);
		jQuery('.UserRole', clonedRow).html(value.UserRole);
		jQuery('.UserRoleLabel', clonedRow).html(jQuery('#frmUsers .UserRole option[value="' + value.UserRole + '"]').text());
		jQuery('#tbodyDatabaseUsers_List').append(clonedRow);
	});
	jQuery('#tblDatabaseUsers_List .UserId').hide();
	jQuery('#tblDatabaseUsers_List .UserRole').hide();

	var sAdminCurrent = '';
	jQuery('#tbodyDatabaseUsers_List tr:gt(0)').each(function() {
		if (jQuery('.UserRole', this).text() == 'ADMINREGION')
		{
			sAdminCurrent = jQuery('.UserName', this).text();
		}
	});
	jQuery('#tbodyDatabaseUsers_List tr:even').addClass('under');
} //doDatabaseUsersPopulateUserRoleList()

function doDatabaseUsersUpdateOptions(RegionInfo)
{
	jQuery('#frmDiffusion .RegionStatus').val(RegionInfo.RegionStatus);
	// RegionActive/RegionPublic are set based on RegionStatus value
	jQuery('#frmDiffusion .RegionActive').prop('checked', false);
	if (parseInt(RegionInfo.RegionStatus) & 1)
	{
		jQuery('#frmDiffusion .RegionActive').prop('checked', true);
	}
	jQuery('#frmDiffusion .RegionPublic').prop('checked', false);
	if (parseInt(RegionInfo.RegionStatus) & 2)
	{
		jQuery('#frmDiffusion .RegionPublic').prop('checked', true);
	}
} //doDatabaseUsersUpdateOptions()

function doDatabaseUsersPopulateLists()
{
	jQuery('body').trigger('cmdMainWaitingShow');
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
				jQuery('#frmUsers .UserId').empty();
				jQuery.each(data.UserList, function(key, value) {
					jQuery('#frmUsers .UserId').append(jQuery('<option>', { value : key }).text(value));
				});
				doDatabaseUsersPopulateUserRoleList(data.UserRoleList);				
			}
			jQuery('body').trigger('cmdMainWaitingHide');
		},
		'json'
	);
} //doDatabaseUsersPopulateLists()
