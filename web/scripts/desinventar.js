/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyAdminDatabaseEdit()
{
	// Populate CountryList/LanguageList in frmRegionEdit form
	jQuery('#desinventarCountryList option').clone().appendTo('#frmRegionEdit_CountryIso');
	jQuery('#desinventarLanguageList option').clone().appendTo('#frmRegionEdit_LangIsoCode');

	jQuery('#frmRegionEdit_CountryIso').change(function() {
		if (jQuery('#frmRegionEdit_Cmd').val() == 'cmdDatabaseCreate')
		{
			jQuery.post(jQuery('#desinventarURL').val() + '/',
				{cmd        : 'cmdRegionBuildRegionId',
				 CountryIso : jQuery(this).val()
				},
				function(data) {
					if (parseInt(data.Status) > 0) {
						jQuery('#frmRegionEdit_RegionId').val(data.RegionId);
					}
				},
				'json'
			);
		}
	});
	
	jQuery('#frmRegionEdit #lblRegionId').dblclick(function() {
		if (jQuery('#frmRegionEdit_Cmd').val() == 'cmdDatabaseCreate')
		{
			jQuery('#frmRegionEdit_RegionId').removeAttr('disabled').focus();
		}
	});
	
	jQuery('#frmRegionEdit').submit(function() {
		// Validate Fields
		var bContinue = true;
		/*
		var a=new Array('CountryIso','RegionLabel','LangIsoCode','RegionUserAdmin');
		var bContinue = checkForm('frmRegionEdit',a, '{-#errmsgfrm#-}');
		*/
		var RegionStatus = jQuery('#frmRegionEdit_RegionStatus');
		RegionStatus.val(0);
		if (jQuery('#frmRegionEdit_RegionActive').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 1);
		}
		if (jQuery('#frmRegionEdit_RegionPublic').attr('checked')) {
			RegionStatus.val(parseInt(RegionStatus.val()) | 2);
		}
		jQuery('#frmRegionEdit_RegionId').removeAttr('disabled');
		var params = jQuery(this).serializeObject();
		jQuery('#frmRegionEdit_RegionId').attr('disabled','disabled');
		if (bContinue) {
			jQuery('#frmRegionEdit :input').unhighlight();
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				params, 
				function(data) {
					if (parseInt(data.Status) > 0) {
						jQuery('#divDatabaseEditResult').html(data.Status + ' ' + data.RegionId);
						// (jhcaiced) Change this for a method to update only the 
						// affected row intead of loading all list again
						updateList('dbl', jQuery('#desinventarURL').val() + '/region.php', 'cmd=adminreg');
					}
				},
				'json'
			);
		}
		return false;
	});

	jQuery('#frmRegionEdit_Cancel').click(function() {
		jQuery(this).trigger('evAdminDatabaseCancel');
	});
} //onReadyAdminDatabaseEdit()

function doAdminDatabaseGetInfo(RegionId)
{
	// Load Information about database...
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{cmd      : 'cmdAdminDatabaseGetInfo',
		 RegionId : RegionId
		},
		function(data)
		{
			jQuery('#frmRegionEdit_Cmd').val('cmdDatabaseUpdate');
			jQuery('#frmRegionEdit_CountryIso').val(data.Region.CountryIso);
			jQuery('#frmRegionEdit_RegionId').val(data.Region.RegionId);
			jQuery('#frmRegionEdit_RegionLabel').val(data.Region.RegionLabel);
			jQuery('#frmRegionEdit_LangIsoCode').val(data.Region.LangIsoCode);
			jQuery('#frmRegionEdit_RegionStatus').val(data.Region.RegionStatus);
			
			// RegionActive/RegionPublic are set based on RegionStatus value
			jQuery('#frmRegionEdit_RegionActive').removeAttr('checked');
			if (parseInt(data.Region.RegionStatus) & 1)
			{
				jQuery('#frmRegionEdit_RegionActive').attr('checked','checked');
			}
			jQuery('#frmRegionEdit_RegionPublic').removeAttr('checked');
			if (parseInt(data.Region.RegionStatus) & 2)
			{
				jQuery('#frmRegionEdit_RegionPublic').attr('checked','checked');
			}
		},
		'json'
	);
} //doAdminDatabaseGetInfo()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyAdminDatabase()
{
	onReadyAdminDatabaseEdit();
	onReadyDatabaseExport();

	// Highlight row on mouseOver
	jQuery('#tblDatabaseList tr').live({
		mouseenter: function() {
			jQuery(this).addClass('highlight');
		},
		mouseleave: function() {
			jQuery(this).removeClass('highlight');
		},
		click:  function() {
			jQuery('#divAdminDatabaseList').hide();
			jQuery('#divAdminDatabaseUpdate .RegionLabel').text(jQuery('.RegionLabel',this).html());
			jQuery('#divAdminDatabaseUpdate .RegionId').text(jQuery('.RegionId',this).html());
			jQuery('.clsAdminDatabaseButton').show();
			jQuery('#btnAdminDatabaseNew').hide();
			jQuery('.clsAdminDatabase').hide();
			jQuery('#divAdminDatabaseUpdate').show();
		}
	});

	jQuery('#btnAdminDatabaseEdit').click(function() {
		jQuery('.clsAdminDatabase').hide();
		var RegionId = jQuery('#divAdminDatabaseUpdate .RegionId').text();
		doAdminDatabaseGetInfo(RegionId);
		jQuery('#divAdminDatabaseEdit').show();
	});

	jQuery('#btnAdminDatabaseImport').click(function() {
		jQuery('.clsAdminDatabase').hide();
		jQuery('#divAdminDatabaseImport').show();
	});

	// Add New Region
	jQuery('#btnAdminDatabaseNew').live('click', function() {
		jQuery('#regionpaaddsect').show();
		setRegionPA('','', '', '', '', true,false);
		jQuery('#frmRegionEdit_Cmd').val('cmdDatabaseCreate');
	}).hide();

	jQuery('.clsAdminDatabaseButton').hide();

	// Select Database from List
	jQuery('#btnAdminDatabaseSelect').live('click', function() {
		jQuery('#divAdminDatabaseUpdate').hide();
		jQuery('#divAdminDatabaseList').show();
	});

	jQuery('#divAdminDatabaseUpdate').on('evAdminDatabaseCancel', function() {
		jQuery('.clsAdminDatabase').hide();
	});
} //onReadyAdminDatabase()

function doAdminDatabaseUpdateList()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd:'cmdAdminDatabaseGetList'
		},
		function(data)
		{
			jQuery('#divAdminDatabaseList').show();
			jQuery.each(data.RegionList, function(index, value) {
				var clonedRow = jQuery('#tbodyDatabaseList tr:last').clone().show();
				jQuery('.CountryIso'     , clonedRow).html(value.CountryIso);
				jQuery('.RegionLabel'    , clonedRow).html(value.RegionLabel);
				jQuery('.RegionAdminUserId', clonedRow).text(value.RegionAdminUserId);
				jQuery('.RegionAdminUserFullName', clonedRow).text(value.RegionAdminUserFullName).show();
				jQuery('.RegionActive'   , clonedRow).attr('checked', value.RegionActive).attr('disabled',true);
				jQuery('.RegionPublic'   , clonedRow).attr('checked', value.RegionPublic).attr('disabled',true);
				jQuery('.RegionId'       , clonedRow).html(value.RegionId).hide();
				jQuery('.LangIsoCode'    , clonedRow).html(value.LangIsoCode).hide();
				jQuery('#tbodyDatabaseList').append(clonedRow);
			});
			// Table Stripes...
			jQuery('#tblDatabaseList tr:odd').removeClass('normal').addClass('normal');
			jQuery('#tblDatabaseList tr:even').removeClass('normal').addClass('under');
			
			jQuery('#tblDatabaseList #RegionId').hide();
			jQuery('#tblDatabaseList #LangIsoCode').hide();
			if (jQuery('#desinventarUserId').val() == 'root') 
			{
				jQuery('#btnAdminDatabaseNew').show();
			}
		},
		'json'
	);
}

function setRegionPA(prmRegionId, prmCountryIso, prmRegionLabel, 
					 prmLangIsoCode, prmUserId_AdminRegion, 
					 prmRegionActive, prmRegionPublic) {
	mod = "regionpa";
	jQuery('#regionpaaddsect').show();
	jQuery('#frmRegionEdit #RegionId').val(prmRegionId);
	jQuery('#frmRegionEdit #CountryIso').val(prmCountryIso);
	jQuery('#frmRegionEdit #RegionLabel').val(prmRegionLabel);
	jQuery('#frmRegionEdit #LangIsoCode').val(prmLangIsoCode);
	jQuery('#frmRegionEdit #RegionUserAdmin').val(prmUserId_AdminRegion);
	jQuery('#frmRegionEdit #RegionActive').attr('checked', prmRegionActive);
	jQuery('#frmRegionEdit #RegionPublic').attr('checked', prmRegionPublic);
	// RegionId is readonly by default
	jQuery('#frmRegionEdit #RegionId').attr('disabled','disabled');
}
function onReadyAdminUsers()
{
	jQuery('div.AdminUsers table.UserList tbody').on('mouseover', 'tr', function() {
		jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function() {
		jQuery(this).removeClass('highlight');
	}).on('click', 'tr', function() {
		var UserId = jQuery('.UserId', this).text();
		jQuery.getJSON(jQuery('#desinventarURL').val() + '/user.php' + '?cmd=getUserInfo&UserId=' + UserId, function(data) {
			jQuery('#divAdminUsers #txtUserId').attr('readonly','true');
			jQuery('#divAdminUsers #txtUserId').val(data.UserId);
			jQuery('#divAdminUsers #selCountryIso').val(data.CountryIso);
			jQuery('#divAdminUsers #txtUserEMail').val(data.UserEMail);
			jQuery('#divAdminUsers #txtUserFullName').val(data.UserFullName);
			jQuery('#divAdminUsers #txtUserCity').val(data.UserCity);
			jQuery('#divAdminUsers #chkUserActive').attr('checked', data.UserActive);
			jQuery('#divAdminUsers #txtUserEditCmd').val('update');
		});
		UserEditFormUpdateStatus('');
		jQuery('#divAdminUsers #divUserEdit').show();
	});

	// Add new User...
	jQuery('body').on('click', '#divAdminUsers #btnUserAdd', function() {
		clearUserEditForm();
		jQuery('#divAdminUsers #txtUserId').removeAttr('readonly');
		jQuery('#divAdminUsers #txtUserEditCmd').val('insert');
		UserEditFormUpdateStatus('');
		jQuery('#divAdminUsers #divUserEdit').show();
	});

	// Cancel Edit, hide form
	jQuery('body').on('click', '#divAdminUsers #btnUserEditCancel', function() {
		jQuery('#divAdminUsers #divUserEdit').hide();
	});

	// Cancel Edit, hide form
	jQuery('body').on('click', '#divAdminUsers #btnUserEditSubmit', function() {
		jQuery('#divAdminUsers #frmUserEdit').trigger('submit');
	});

	// Submit - Finish edit, validate form and send data...
	jQuery('body').on('submit', '#divAdminUsers #frmUserEdit', function() {
		UserEditFormUpdateStatus('');
		// validate Form
		var bReturn = validateUserEditForm();
		if (bReturn > 0) {
			// Remove the readonly attribute, this way the data is sent to processing
			jQuery('#divAdminUsers #txtUserId').removeAttr('readonly');
			// Create an object with the information to send
			var user = jQuery('#divAdminUsers #frmUserEdit').serializeObject();
			// Checkboxes not selected are not passed by default to server, so we need
			// to checkout and set a value here.
			if (! jQuery('#divAdminUsers #chkUserActive').attr('checked')) {
				user['User[UserActive]'] = 'off';
			}
			// Send AJAX request to update information
			jQuery.post(jQuery('#desinventarURL').val() + '/user.php', 
				user, 
				function(data) {
					if (parseInt(data.Status) > 0)
					{
						// Reload user list on success
						jQuery('div.AdminUsers').trigger('cmdLoadData');
					}
					UserEditFormUpdateStatus(data.Status);
				},
				'json'
			);
		}
		return false;
	}); //submit
	doAdminUsersReset();
	// Populate Country List
	jQuery('#desinventarCountryList option').each(function() {
		jQuery('#selCountryIso').append(jQuery('<option>', { value : jQuery(this).attr('value') }).text(jQuery(this).text()));
	});
	
	jQuery('div.AdminUsers').on('cmdLoadData', function(event) {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd : 'cmdAdminUsersGetList'
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					var user_list = jQuery('div.AdminUsers table.UserList tbody');
					user_list.find('tr:gt(0)').remove();
					jQuery.each(data.UserList, function(key, value) {
						var clone = jQuery('tr:first', user_list).clone().show();
						jQuery('.UserId'            , clone).text(value.UserId);
						jQuery('.UserFullName'      ,clone).text(value.UserFullName);
						jQuery('.UserEMail'         ,clone).text(value.UserEMail);
						jQuery('.UserActive'        ,clone).text(value.UserActive);
						jQuery('.UserActiveCheckbox',clone).prop('checked', parseInt(value.UserActive)==1);						
						user_list.append(clone);
					});
				}
				doAdminUsersReset();
			},
			'json'
		);
	});
} //onReadyAdminUsers()

function doAdminUsersReset()
{
	// Start with Edit form hidden
	jQuery('#divAdminUsers #divUserEdit').hide();
	// Create table stripes
	jQuery('#divAdminUsers #tblUserList tr:odd').addClass('normal');
	jQuery('#divAdminUsers #tblUserList tr:even').addClass('under');
	jQuery('#divAdminUsers #txtUserId').removeAttr('readonly');
}

function UserEditFormUpdateStatus(value) {
	jQuery('#divAdminUsers .UserEditFormStatus').hide();
	MsgId = '';
	switch(value) {
		case  1:
			MsgId = 'UserEditFormStatusOk';
		break;
		case -1:
			MsgId = 'UserEditFormStatusError';
		break;
		case -100:
			MsgId = 'UserEditFormStatusDuplicateId';
		break;
		case -101:
			MsgId = 'UserEditFormStatusEmptyId';
		break;
	}
	if (MsgId != '') {
		jQuery('#divAdminUsers #' + MsgId).show();
	}
}

function validateUserEditForm()
{
	var bReturn = 1;
	jQuery('#divAdminUsers #txtUserId').unhighlight();
	if (jQuery('#divAdminUsers #txtUserId').val() == '') {
		bReturn = -101;
		jQuery('#divAdminUsers #txtUserId').highlight();
	}
	jQuery('#divAdminUsers #txtUserFullName').unhighlight();
	if (jQuery('#divAdminUsers #txtUserFullName').val() == '') {
		bReturn = -101;
		jQuery('#divAdminUsers #txtUserFullName').highlight();
	}
	jQuery('#divAdminUsers #txtUserEMail').unhighlight();
	if (jQuery('#divAdminUsers #txtUserEMail').val() == '') {
		bReturn = -101;
		jQuery('#divAdminUsers #txtUserEMail').highlight();
	}
	UserEditFormUpdateStatus(bReturn);
	return bReturn;		
}

function clearUserEditForm() {
	jQuery('#divAdminUsers #txtUserId').val('');
	jQuery('#divAdminUsers #selCountryIso').val('');
	jQuery('#divAdminUsers #txtUserEMail').val('');
	jQuery('#divAdminUsers #txtUserFullName').val('');
	jQuery('#divAdminUsers #txtUserCity').val('');
	jQuery('#divAdminUsers #chkUserActive').attr('checked', '');
}
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyCommon()
{
	// Initialize tooltip for elements with title attribute
	jQuery('[title]').tooltip();

	jQuery('body').on('cmdWindowReload', function() {
		// Destroy viewport, the loading... message should stay.
		doViewportDestroy();
		// Reload document window
		window.location.reload(false);
	});
} //onReadyCommon()

function onReadyPrototype()
{
	// Create periodic task to keep session alive...
	var pe = new PeriodicalExecuter(doKeepSessionAwake, 180);
} //onReadyPrototype()

function doKeepSessionAwake()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdSessionAwake'
		},
		function(data)
		{
		},
		'json'
	);
} //doKeepSessionAwake()

// jQuery Snippets Code

// http://css-tricks.com/snippets/jquery/serialize-form-to-json/
// Serialize Form to JSON
jQuery.fn.serializeObject = function()
{
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function() {
		if (o[this.name])
		{
			if (!o[this.name].push)
			{
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		}
		else
		{
			o[this.name] = this.value || '';
		}
	});
	return o;
};

jQuery.fn.highlight = function() {
	jQuery(this).attr('old-bg-color', jQuery(this).css('background-color'));
	jQuery(this).css('background-color', '#ffff66');
	return this;
}

jQuery.fn.unhighlight = function() {
	if (jQuery(this).attr('old-bg-color') != '')
	{
		jQuery(this).css('background-color', jQuery(this).attr('old-bg-color'));
	}
	return this;
}

jQuery.fn.disable = function() {
	jQuery(this).attr('disabled',true);
	jQuery(this).attr('readonly',true);
	jQuery(this).addClass('disabled');
	return this;
}

jQuery.fn.enable = function() {
	jQuery(this).removeAttr('disabled');
	jQuery(this).removeAttr('readonly');
	jQuery(this).removeClass('disabled');
	return this;
}

// Older Compatibility Functions

function checkForm(prmForm, prmFieldList, errmsg)
{
	var bReturn = true;
	jQuery.each(prmFieldList, function(index, value) {
		var selector = '#' + prmForm + ' #' + value;
		if (jQuery(selector).val().length < 1)
		{
			jQuery(selector).highlight();
			bReturn = false;
		} //if
	}); //.each
	return bReturn;
} //function

function doViewportDestroy()
{
	var viewport = Ext.getCmp('viewport');
	if (viewport != undefined)
	{
		viewport.destroy();
		jQuery('#loading').show();
		jQuery('#loading-mask').show();
	}
} //doViewportDestroy

function showtip(prmText)  //prmText, prmColor
{
	if (prmText != undefined)
	{
		var sColor = '#ffffff';
		if (arguments.length > 1)
		{
			sColor = arguments[1];
		}
		jQuery('#txtHelpArea').val(prmText).css('background-color', sColor);
	}
}

// Block characters according to type
function blockChars(e, value, type)
{
	var key = window.event ? e.keyCode : e.which;

	// 2010-08-19 (jhcaiced) Accept values in numeric keypad
	if (key >= 96 && key <= 105)
	{
		key = key - 48;
	}
	var keychar = String.fromCharCode(key);
	if (key == 190 || key == 110 || key == 188) { keychar = '.'; }
	var opt = type.split(":"); // 0=type; 1=minlength; 2=minval-maxval
		// Accept keys: backspace, tab, shift, ctrl, insert, delete
		//        pagedown, pageup, rows
	var spckey = (key==8 || key==9 || key==17 || key==20 ||
				  key==45 || key==46 || (key>=33 && key<=40) || key==0);
	var chk = true;
	var val = true; // validate characters
		// Check max length
	if (value.length >= parseInt(opt[1]))
	{
		var len = false;
	}
	else
	{
		var len = true;
	}
	// Check datatype
	switch (opt[0])
	{
		case "date" :
			reg = /^\d{4}-\d{0,2}-\d{0,2}$/;
			chk = reg.test(keychar);
		break;
		case "alphanumber" :
			reg = /^[a-z]|[A-Z]|[0-9]|[-_+.]+/; 
			chk = reg.test(keychar);
			break;
		case "integer" :
			reg = /\d/;
			chk = reg.test(keychar);
			break;
		case "double" :
			reg = /^[-+]?[0-9]|[.]+$/;
			chk = reg.test(keychar);
			break;
		default:;
	}
	// Block special characters: (like !@#$%^&'*" etc)
	val = !(key == 92 || key == 13 || key == 16)
  return (val && ((chk && len) || spckey));
}

function onlyText(e)
{
	var keynum;
	var keychar;
	var numcheck;
	if(window.event)
	{ // IE
		keynum = e.keyCode;
	}
	else if(e.which)
	{ // Netscape/Firefox/Opera
		keynum = e.which;
	}
	keychar = String.fromCharCode(keynum);
	numcheck = /\d/;
	return !numcheck.test(keychar);
}

function onlyNumber(e)
{
	var keynum;
	var keychar;
	if(window.event)
	{ // IE
		keynum = e.keyCode;
	}
	else if(e.which)
	{ // Netscape/Firefox/Opera
		keynum = e.which;
	}
	if (e.keyCode < 48 || e.keyCode > 57)
		return false;
	return true;
}
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/


function onReadyDatabaseCauses()
{
	//Attach main events
	jQuery('body').on('cmdDatabaseCausesShow', function() {
		doDatabaseCausesPopulateLists();
	});

	jQuery('.clsDatabaseCausesStatus').hide();

	jQuery('#tbodyDatabaseCauses_CauseListCustom,#tbodyDatabaseCauses_CauseListDefault').on('click', 'tr', function(event) {
		jQuery('#fldDatabaseCauses_CauseId').val(jQuery('.CauseId',this).text());
		jQuery('#fldDatabaseCauses_CauseName').val(jQuery('.CauseName',this).text());
		jQuery('#fldDatabaseCauses_CauseDesc').val(jQuery('.CauseDesc',this).prop('title'));
		jQuery('#fldDatabaseCauses_CauseActiveCheckbox').prop('checked', jQuery('.CauseActive :input',this).is(':checked')).change();
		jQuery('#fldDatabaseCauses_CausePredefined').val(jQuery('.CausePredefined',this).text());

		jQuery('#btnDatabaseCauses_Add').hide();
		doCausesFormSetup();
		jQuery('#divDatabaseCauses_Edit').show();
	}).on('mouseover', 'tr', function(event) {
			jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	});


	jQuery('#btnDatabaseCauses_Add').click(function() {
		jQuery('#divDatabaseCauses_Edit').show();
		jQuery(this).hide();
		jQuery('#fldDatabaseCauses_CauseId').val('');
		jQuery('#fldDatabaseCauses_CauseName').val('');
		jQuery('#fldDatabaseCauses_CauseDesc').val('');
		jQuery('#fldDatabaseCauses_CauseDesc').prop('disabled', false);
		jQuery('#fldDatabaseCauses_CauseActiveCheckbox').prop('checked', true).change();
		jQuery('#fldDatabaseCauses_CausePredefined').val(0);
		doCausesFormSetup();
	});

	jQuery('#btnDatabaseCauses_Save').click(function() {
		jQuery('#frmDatabaseCauses_Edit').trigger('submit');
	});

	jQuery('#btnDatabaseCauses_Cancel').click(function() {
		jQuery('#divDatabaseCauses_Edit').hide();
		jQuery('#btnDatabaseCauses_Add').show();
	});

	jQuery('#fldDatabaseCauses_CauseActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#fldDatabaseCauses_CauseActive').val(v);
	});

	jQuery('#frmDatabaseCauses_Edit').submit(function() {
		var bContinue = true;
		if (bContinue && jQuery.trim(jQuery('#fldDatabaseCauses_CauseName').val()) == '')
		{
			jQuery('#fldDatabaseCauses_CauseName').highlight();
			jQuery('#msgDatabaseCauses_ErrorEmtpyFields').show();
			setTimeout(function () {
				jQuery('#fldDatabaseCauses_CauseName').unhighlight();
				jQuery('.clsDatabaseCausesStatus').hide();
			}, 2500);
			bContinue = false;
		}

		if (bContinue)
		{
			jQuery('body').trigger('cmdMainWaitingShow');
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdDatabaseCausesUpdate',
					RegionId : jQuery('#desinventarRegionId').val(),
					Cause    : jQuery('#frmDatabaseCauses_Edit').serializeObject()
				},
				function(data)
				{
					jQuery('body').trigger('cmdMainWaitingHide');
					if (parseInt(data.Status) > 0)
					{
						jQuery('#divDatabaseCauses_Edit').hide();
						jQuery('#btnDatabaseCauses_Add').show();
						jQuery('#msgDatabaseCauses_UpdateOk').show();
						doDatabaseCausesPopulateList('tbodyDatabaseCauses_CauseListCustom' , data.CauseListCustom);
						doDatabaseCausesPopulateList('tbodyDatabaseCauses_CauseListDefault', data.CauseListDefault);
					}
					else
					{
						switch(data.Status)
						{
							case -15:
								jQuery('#msgDatabaseCauses_ErrorCannotDelete').show();
							break;
							default:
								jQuery('#msgDatabaseCauses_UpdateError').show();
							break;
						}
					}					
					setTimeout(function () {
						jQuery('.clsDatabaseCausesStatus').hide();
					}, 2500);
				},
				'json'
			);
		}		
		return false;
	});
} //onReadyDatabaseCauses()

function doCausesFormSetup()
{
	if (parseInt(jQuery('#fldDatabaseCauses_CausePredefined').val()) > 0)
	{
		jQuery('#divDatabaseCauses_Edit span.Custom').hide();
		jQuery('#divDatabaseCauses_Edit span.Predefined').show();
		jQuery('#fldDatabaseCauses_CauseDesc').prop('disabled', true);
		jQuery('#fldDatabaseCauses_CauseDesc').addClass('disabled');
	}
	else
	{
		jQuery('#divDatabaseCauses_Edit span.Custom').show();
		jQuery('#divDatabaseCauses_Edit span.Predefined').hide();
		jQuery('#fldDatabaseCauses_CauseDesc').prop('disabled', false);
		jQuery('#fldDatabaseCauses_CauseDesc').removeClass('disabled');
	}
}

function doDatabaseCausesPopulateLists()
{
	jQuery('body').trigger('cmdMainWaitingShow');
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdDatabaseCausesGetList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				doDatabaseCausesPopulateList('tbodyDatabaseCauses_CauseListCustom' , data.CauseListCustom);
				doDatabaseCausesPopulateList('tbodyDatabaseCauses_CauseListDefault', data.CauseListDefault);
			}
			jQuery('body').trigger('cmdMainWaitingHide');
		},
		'json'
	);
} //doDatabaseCausesPopulateLists()

function doDatabaseCausesPopulateList(tbodyId, CauseList)
{
	jQuery('#' + tbodyId).find('tr:gt(0)').remove();
	jQuery('#' + tbodyId).find('tr').removeClass('under');
	jQuery.each(CauseList, function(index, value) {
		var clonedRow = jQuery('#tbodyDatabaseCauses_CauseListCustom tr:last').clone().show();
		jQuery('.CauseId', clonedRow).html(index);
		jQuery('.CausePredefined',clonedRow).html(value.CausePredefined);
		jQuery('.CauseName', clonedRow).html(value.CauseName);
		jQuery('.CauseDesc', clonedRow).html(value.CauseDesc.substring(0,150));
		jQuery('.CauseDesc', clonedRow).prop('title', value.CauseDesc);
		jQuery('.CauseActive :input', clonedRow).prop('checked', value.CauseActive>0);
		jQuery('#' + tbodyId).append(clonedRow);
	});
	jQuery('#' + tbodyId + ' .CauseId').hide();
	jQuery('#' + tbodyId + ' .CausePredefined').hide();
	jQuery('#' + tbodyId + ' tr:odd').addClass('under');
} //doDatabaseCausesPopulateList()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDatabaseCreate()
{
	doDatabaseCreateSetup();
} //onReadyDatabaseCreate()

function doDatabaseCreateSetup()
{
	// Database Create
	var w = new Ext.Window({id:'wndDatabaseCreate', 
		el: 'divDatabaseCreateWin', layout:'fit', 
		width:450, height:300, modal:false, constrainHeader: true,
		closeAction:'hide', plain: false, animCollapse: true,
		items: new Ext.Panel({
			contentEl: 'divDatabaseCreateContent',
			autoScroll: true
		}),
		buttons: [
		] //buttons
	});

	// Cancel Button - Hide Window and do nothing
	jQuery('#btnDatabaseCreateCancel').click(function() {
		Ext.getCmp('wndDatabaseCreate').hide();
		return false;
	});


	jQuery('#fldDatabaseEdit_RegionId').attr('readonly', true);

	jQuery('#fldDatabaseEdit_CountryIso').change(function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd        : 'cmdRegionBuildRegionId',
				CountryIso : jQuery(this).val()
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					jQuery('#fldDatabaseEdit_RegionId').val(data.RegionId);
				}
			},
			'json'
		);
	});

	// Send Button - Validate data and send command to backend
	jQuery('#btnDatabaseCreateSend').click(function() {
		var iReturn;
		iReturn = doDatabaseCreateValidate();
		if (iReturn > 0)
		{
			jQuery(this).attr('readonly', true);
			var RegionStatus = jQuery('#fldDatabaseEdit_RegionStatus');
			RegionStatus.val(0);
			if (jQuery('#fldDatabaseEdit_RegionActive').attr('checked'))
			{
				RegionStatus.val(parseInt(RegionStatus.val()) | 1);
			}
			if (jQuery('#fldDatabaseEdit_RegionPublic').attr('checked'))
			{
				RegionStatus.val(parseInt(RegionStatus.val()) | 2);
			}
			jQuery('#fldDatabaseEdit_RegionId').removeAttr('disabled');
			var params = jQuery('#frmDatabaseEdit').serializeObject();
			jQuery('#fldDatabaseEdit_RegionId').attr('disabled','disabled');

			jQuery('#frmDatabaseEdit :input').unhighlight();
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				jQuery.extend(params,
					{
						cmd : 'cmdDatabaseCreate'
					}
				),
				function(data)
				{
					jQuery('.clsDatabaseCreateStatus').hide();
					jQuery('#btnDatabaseCreateSend').attr('readonly', false);
					if (parseInt(data.Status) > 0)
					{
						jQuery('#txtDatabaseCreateOk').show();
						jQuery('#txtDatabaseCreate_RegionId').text(jQuery('#fldDatabaseEdit_RegionId').val());
						jQuery('#lnkDatabaseCreateDBURL').attr('href', jQuery('#desinventarURL').val() + '/' + jQuery('#txtDatabaseCreate_RegionId').text());
						jQuery('#lnkDatabaseCreateOpenDB').attr('href', jQuery('#desinventarURL').val() + '/' + jQuery('#txtDatabaseCreate_RegionId').text());
						jQuery('#divDatabaseCreateParams').hide();
						jQuery('#divDatabaseCreateResult').show();
					}
					else
					{
						jQuery('#txtDatabaseCreateError').show();
					}
				},
				'json'
			);
		}
		else
		{
			jQuery('#txtDatabaseCreateFormError').show();
			setTimeout(function() {
				jQuery('.clsDatabaseCreateStatus').hide();
			}, 3000);
		}
		return false;
	});

	// Hide Send button until the combobox has been populated
	jQuery('#btnDatabaseCreateSend').hide();
} //doDatabaseCreateSetup()

function doDatabaseCreateShow()
{
	// Populate fields if neccesary
	var iCount = jQuery('#fldDatabaseEdit_CountryIso option').length;
	if (iCount < 2)
	{
		doDatabaseCreatePopulateLists();
	} 

	// Set default values in form
	jQuery('.clsDatabaseCreateStatus').hide();
	jQuery('#fldDatabaseEdit_RegionId').val('');
	jQuery('#fldDatabaseEdit_CountryIso').val('');
	jQuery('#fldDatabaseEdit_RegionLabel').val('');
	jQuery('#fldDatabaseEdit_RegionActive').attr('checked',true);
	jQuery('#trDatabaseEdit_RegionActive').hide();
	jQuery('#fldDatabaseEdit_RegionPublic').attr('checked',false);
	jQuery('#fldDatabaseEdit_LangIsoCode').val(jQuery('#desinventarLang').val());

	jQuery('#divDatabaseCreateParams').show();
	jQuery('#divDatabaseCreateResult').hide();

	// Show form
	Ext.getCmp('wndDatabaseCreate').show();
	jQuery('#fldDatabaseEdit_CountryIso').focus();
} //doDatabaseCreateShow()

function doDatabaseCreateValidate()
{
	var iReturn = 1;
	if ( (iReturn > 0) && (jQuery.trim(jQuery('#fldDatabaseEdit_RegionLabel').val()) == '') )
	{
		iReturn = -1;
	}
	if ( (iReturn > 0) && (jQuery('#fldDatabaseEdit_RegionId').val() == '') )
	{
		iReturn = -1;
	}
	return iReturn;
}

function doDatabaseCreatePopulateLists()
{
	// async Populate CountryIso - LanguageList fields
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGetLocaleList'
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('#fldDatabaseEdit_LangIsoCode').empty();
				jQuery.each(data.LanguageList, function(key, value) {
					jQuery('#fldDatabaseEdit_LangIsoCode').append(jQuery('<option>', { value : key }).text(value));
				});
				jQuery('#fldDatabaseEdit_LangIsoCode').val(jQuery('#desinventarLang').val());
				jQuery('#fldDatabaseEdit_CountryIso').empty();
				jQuery.each(data.CountryList, function(key, value) {
					jQuery('#fldDatabaseEdit_CountryIso').append(jQuery('<option>', { value: key }).text(value));
				});
				jQuery('#btnDatabaseCreateSend').show();
				jQuery('#fldDatabaseEdit_CountryIso').focus();
			}
		},
		'json'
	);
} //doDatabaseCreatePopulateLists()


/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDatabaseEvents()
{
	//Attach main events
	jQuery('body').on('cmdDatabaseEventsShow', function() {
		doDatabaseEventsPopulateLists();
	});

	jQuery('.clsDatabaseEventsStatus').hide();

	jQuery('#tbodyDatabaseEvents_EventListCustom,#tbodyDatabaseEvents_EventListDefault').on('click', 'tr', function(event) {
		jQuery('#fldDatabaseEvents_EventId').val(jQuery('.EventId',this).text());
		jQuery('#fldDatabaseEvents_EventName').val(jQuery('.EventName',this).text());
		jQuery('#fldDatabaseEvents_EventDesc').val(jQuery('.EventDesc',this).prop('title'));
		jQuery('#fldDatabaseEvents_EventActiveCheckbox').prop('checked', jQuery('.EventActive :input',this).is(':checked')).change();
		jQuery('#fldDatabaseEvents_EventPredefined').val(jQuery('.EventPredefined',this).text());

		jQuery('#btnDatabaseEvents_Add').hide();
		doEventsFormSetup();
		jQuery('#divDatabaseEvents_Edit').show();
	}).on('mouseover', 'tr', function(event) {
			jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	});


	jQuery('#btnDatabaseEvents_Add').click(function() {
		jQuery('#divDatabaseEvents_Edit').show();
		jQuery(this).hide();
		jQuery('#fldDatabaseEvents_EventId').val('');
		jQuery('#fldDatabaseEvents_EventName').val('');
		jQuery('#fldDatabaseEvents_EventDesc').val('');
		jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false);
		jQuery('#fldDatabaseEvents_EventActiveCheckbox').prop('checked', true).change();
		jQuery('#fldDatabaseEvents_EventPredefined').val(0);
		doEventsFormSetup();
	});

	jQuery('#btnDatabaseEvents_Save').click(function() {
		jQuery('#frmDatabaseEvents_Edit').trigger('submit');
	});

	jQuery('#btnDatabaseEvents_Cancel').click(function() {
		jQuery('#divDatabaseEvents_Edit').hide();
		jQuery('#btnDatabaseEvents_Add').show();
	});

	jQuery('#fldDatabaseEvents_EventActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('#fldDatabaseEvents_EventActive').val(v);
	});

	jQuery('#frmDatabaseEvents_Edit').submit(function() {
		var bContinue = true;
		if (bContinue && jQuery.trim(jQuery('#fldDatabaseEvents_EventName').val()) == '')
		{
			jQuery('#fldDatabaseEvents_EventName').highlight();
			jQuery('#msgDatabaseEvents_ErrorEmtpyFields').show();
			setTimeout(function () {
				jQuery('#fldDatabaseEvents_EventName').unhighlight();
				jQuery('.clsDatabaseEventsStatus').hide();
			}, 2500);
			bContinue = false;
		}

		if (bContinue)
		{
			jQuery('body').trigger('cmdMainWaitingShow');
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdDatabaseEventsUpdate',
					RegionId : jQuery('#desinventarRegionId').val(),
					Event    : jQuery('#frmDatabaseEvents_Edit').serializeObject()
				},
				function(data)
				{
					jQuery('body').trigger('cmdMainWaitingHide');
					if (parseInt(data.Status) > 0)
					{
						jQuery('#divDatabaseEvents_Edit').hide();
						jQuery('#btnDatabaseEvents_Add').show();
						jQuery('#msgDatabaseEvents_UpdateOk').show();
						doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListCustom' , data.EventListCustom);
						doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListDefault', data.EventListDefault);
					}
					else
					{
						switch(data.Status)
						{
							case -15:
								jQuery('#msgDatabaseEvents_ErrorCannotDelete').show();
							break;
							default:
								jQuery('#msgDatabaseEvents_UpdateError').show();
							break;
						}
					}					
					setTimeout(function () {
						jQuery('.clsDatabaseEventsStatus').hide();
					}, 2500);
				},
				'json'
			);
		}		
		return false;
	});
} //onReadyDatabaseEvents()

function doEventsFormSetup()
{
	if (parseInt(jQuery('#fldDatabaseEvents_EventPredefined').val()) > 0)
	{
		jQuery('#divDatabaseEvents_Edit span.Custom').hide();
		jQuery('#divDatabaseEvents_Edit span.Predefined').show();
		jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', true);
		jQuery('#fldDatabaseEvents_EventDesc').addClass('disabled');
	}
	else
	{
		jQuery('#divDatabaseEvents_Edit span.Custom').show();
		jQuery('#divDatabaseEvents_Edit span.Predefined').hide();
		jQuery('#fldDatabaseEvents_EventDesc').prop('disabled', false);
		jQuery('#fldDatabaseEvents_EventDesc').removeClass('disabled');
	}
}

function doDatabaseEventsPopulateLists()
{
	jQuery('body').trigger('cmdMainWaitingShow');
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdDatabaseEventsGetList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListCustom' , data.EventListCustom);
				doDatabaseEventsPopulateList('tbodyDatabaseEvents_EventListDefault', data.EventListDefault);
			}
			jQuery('body').trigger('cmdMainWaitingHide');
		},
		'json'
	);
} //doDatabaseEventsPopulateLists()

function doDatabaseEventsPopulateList(tbodyId, EventList)
{
	jQuery('#' + tbodyId).find('tr:gt(0)').remove();
	jQuery('#' + tbodyId).find('tr').removeClass('under');
	jQuery.each(EventList, function(index, value) {
		var clonedRow = jQuery('#tbodyDatabaseEvents_EventListCustom tr:last').clone().show();
		jQuery('.EventId', clonedRow).html(index);
		jQuery('.EventPredefined',clonedRow).html(value.EventPredefined);
		jQuery('.EventName', clonedRow).html(value.EventName);
		jQuery('.EventDesc', clonedRow).html(value.EventDesc.substring(0,150));
		jQuery('.EventDesc', clonedRow).prop('title', value.EventDesc);
		jQuery('.EventActive :input', clonedRow).prop('checked', value.EventActive>0);
		jQuery('#' + tbodyId).append(clonedRow);
	});
	jQuery('#' + tbodyId + ' .EventId').hide();
	jQuery('#' + tbodyId + ' .EventPredefined').hide();
	jQuery('#' + tbodyId + ' tr:even').addClass('under');
} //doDatabaseEventsPopulateList()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDatabaseExport()
{
	doDatabaseExportCreate();
	jQuery('body').on('cmdDatabaseExport', function() {
		doDatabaseExportAction();
	});
} //onReadyDatabaseExport()

function doDatabaseExportCreate()
{
	// Database Export
	var w = new Ext.Window({id:'wndDatabaseExport', 
		el: 'divDatabaseExportWin', layout:'fit', 
		width:400, height:200, modal:false, constrainHeader: true,
		closeAction:'hide', plain: false, animCollapse: false,
		items: new Ext.Panel({
			contentEl: 'divDatabaseExportContent',
			autoScroll: true
		}),
		buttons: [
			/*{
				id: 'btnDatabaseExportSend',
				text: jQuery('#msgDatabaseExportButtonSend').text(),
				handler: function()
				{
					doDatabaseExportAction();
				} //handler
			},
			{
				text: jQuery('#msgDatabaseExportButtonClose').text(),
				handler: function()
				{
					jQuery('#fldDatabaseExportSave').val(0);
					jQuery('#imgDatabaseExportWait').attr('src','');
					Ext.getCmp('wndDatabaseExport').hide();
				} //handler
			}
			*/
		] //button
	});
	jQuery('#fldDatabaseExportSave').val(1);
} // doDatabaseExportCreate()

function doDatabaseExportAction()
{
	jQuery('.clsDatabaseExport').hide();
	Ext.getCmp('wndDatabaseExport').show();
	jQuery('.clsDatabaseExport').hide();
	jQuery('#divDatabaseExportProgress').show();
	
	jQuery('#imgDatabaseExportWait').attr('src', jQuery('#fldDatabaseExportImage').val());
	jQuery('#imgDatabaseExportWait').show();
	
	jQuery('#fldDatabaseExportSave').val(1);
	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
			cmd      : 'cmdDatabaseExport',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			jQuery('.clsDatabaseExport').hide();
			if (parseInt(data.Status) > 0)
			{
				jQuery('#divDatabaseExportResults').show();
				jQuery('#imgDatabaseExportWait').attr('src','').hide();
				// Hide Ext.Window
				Ext.getCmp('wndDatabaseExport').hide();
				if (parseInt(jQuery('#fldDatabaseExportSave').val()) > 0)
				{
					// Open the backup file for download
					window.location = data.URL;
				}
			}
			else
			{
				jQuery('#divDatabaseExportError').show();
			}
		},
		'json'
	);
} // doDatabaseExportAction
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGeography()
{
	jQuery('div.Geography').on('change','select.ListHeader', function() {
		var geography_id = jQuery(this).val();
		var geolevel_id = jQuery(this).data('GeoLevelId');
		load_geography_list(geography_id, geolevel_id);
	});

	jQuery('div.Geography table.List tbody').on('dblclick','tr', function() {
		/*
		var geography_id = jQuery('.GeographyId', this).text();
		jQuery('div.Geography select.ListHeader:data("GeoLevelId=' + jQuery('.GeographyLevel', this).text() + '")').val(geography_id).change();
		*/
	}).on('mouseover', 'tr', function(event) {
		jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	}).on('click', 'tr', function(event) {
		var form = jQuery('div.Geography form.Edit');
		jQuery('.GeographyId'    , form).val(jQuery('.GeographyId'    , this).text());
		jQuery('.GeographyCode'  , form).val(jQuery('.GeographyCode'  , this).text());
		jQuery('.GeographyName'  , form).val(jQuery('.GeographyName'  , this).text());
		jQuery('.GeographyActive', form).val(jQuery('.GeographyActive', this).text());
		jQuery('.GeographyActiveCheckbox',form).prop('checked', parseInt(jQuery('.GeographyActive',this).text()) > 0).change();
		jQuery('div.Geography div.Add').hide();
		jQuery('div.Geography div.Edit').show();
	});

	jQuery('div.Geography form.Edit input.GeographyActiveCheckbox').change(function() {
		var v = 0;
		if (jQuery(this).is(':checked')) 
		{
			v = 1;
		}
		jQuery('div.Geography form.Edit input.GeographyActive').val(v);
	});

	jQuery('div.Geography a.Add').click(function() {
		jQuery('div.Geography form.Edit').each(function() {
			this.reset();
		});
		jQuery('div.Geography form.Edit input.GeographyId').val('');
		
		jQuery('div.Geography form.Edit input.GeographyActiveCheckbox').change();
		jQuery('div.Geography div.Add').hide();
		jQuery('div.Geography div.Edit').show();
	});

	jQuery('div.Geography a.Export').click(function() {
		var form = jQuery('div.Geography form.Export');
		jQuery('div.Geography form.Export').submit();
	});

	jQuery('div.Geography a.Cancel').click(function() {
		jQuery('div.Geography form.Edit').each(function() {
			this.reset();
		});
		jQuery('div.Geography div.Add').show();
		jQuery('div.Geography div.Edit').hide();
	});

	jQuery('div.Geography a.Save').click(function() {
		jQuery('div.Geography form.Edit').submit();
	});

	jQuery('div.Geography form.Edit').submit(function() {
		var bContinue = 1;
		var w = '';
		if (bContinue > 0)
		{
			w = jQuery('div.Geography form.Edit input.GeographyCode');
			if (w.val() == '')
			{
				w.highlight();			
			}
		}
		if (bContinue > 0)
		{
			w = jQuery('div.Geography form.Edit input.GeographyName');
			if (w.val() == '')
			{
				w.highlight();
			}
		}
		if (bContinue > 0)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd       : 'cmdGeographyUpdate',
					RegionId  : jQuery('#desinventarRegionId').val(),
					Geography : jQuery('div.Geography form.Edit').toObject(),
					ParentId  : jQuery('div.Geography input.ParentId').val()
				},
				function(data)
				{
					jQuery('div.Geography div.Status span').hide();
					if (parseInt(data.Status) > 0)
					{
						populate_geography_list(data.GeographyList,data.GeographyListCount);
						jQuery('div.Geography div.Status span.Ok').show();
						setTimeout(function() {
							jQuery('div.Geography div.Status span').hide();
							jQuery('div.Geography div.Add').show();
							jQuery('div.Geography div.Edit').hide();
						}, 2000);
					}
					switch(parseInt(data.Status))
					{
						case 1:
						break;
						case -44:
							jQuery('div.Geography div.Status span.DuplicatedCode').show();
						break;
						case -48:
							jQuery('div.Geography div.Status span.WithDatacards').show();
						break;
						default:
							jQuery('div.Geography div.Status span.Error').show();
						break;
					}
					setTimeout(function() {
						jQuery('div.Geography div.Status span').hide();
					}, 4000);
				},
				'json'
			);
		}
		return false;
	});

	jQuery('body').on('cmdGeographyShow', function() {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd : 'cmdGeolevelsGetList',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data) {
				if (parseInt(data.Status) > 0)
				{
					jQuery('div.Geography table.ListHeader tr td:not(:first)').remove();
					jQuery.each(data.GeolevelsList, function(key, value) {
						var clonedCell = jQuery('div.Geography table.ListHeader tr td:last').clone().show();
						jQuery(clonedCell).data('GeoLevelId', key);
						jQuery('span.title', clonedCell).text(parseInt(key + 1) + ' - ' + value.GeoLevelName);
						jQuery('select', clonedCell).data('GeoLevelId', key);
						jQuery('div.Geography table.ListHeader tr').append(clonedCell);
						jQuery('div.Geography select.ListHeader:data("GeoLevelId=' + value.GeoLevelId + '")').disable();
					});
					jQuery('div.Geography input.GeoLevelCount').val(data.GeolevelsList.length);
					if (data.GeolevelsList.length > 0)
					{
						jQuery('div.Geography select.ListHeader:first').change();
					}
				}
			},
			'json'
		);
	});

	// Initialize
	jQuery('div.Geography div.Add').show();
	jQuery('div.Geography div.Edit').hide();
	jQuery('div.Geography div.Status span').hide();

	// Initialize labels for csv geography export	
	var labels = '';
	var count = 0;
	jQuery('div.Geography table.List thead td').each(function() {
		if (count > 0)
		{
			labels = labels + ',';
		}
		labels = labels + '"' + jQuery(this).text().trim() + '"';
		count++;
	});
	jQuery('div.Geography form.Export input.Labels').val(labels);
} //onReadyGeography()

function populate_geography_list(prmGeographyList,prmGeographyListCount)
{
	var prmGeoLevelId = parseInt(jQuery('div.Geography input.GeoLevelId').val());
	var prmParentId = jQuery('div.Geography input.ParentId').val();
	var geolevel_count = parseInt(jQuery('div.Geography input.GeoLevelCount').val()) - 1;
	jQuery('div.Geography select.ListHeader').each(function() {
		if (parseInt(jQuery(this).data('GeoLevelId')) > prmGeoLevelId)
		{
			jQuery(this).val(jQuery('option:first', this).val());
			jQuery(this).disable();
		}
	});
	var select = jQuery('div.Geography select.ListHeader:data("GeoLevelId=' + prmGeoLevelId + '")');
	select.empty();
	select.append(jQuery('<option>', { value : prmParentId }).text(jQuery('div.Geography span.All').text()));
	jQuery('div.Geography table.List tbody tr').remove();
	jQuery.each(prmGeographyList, function(key, value) {
		if (prmGeoLevelId < geolevel_count)
		{
			select.append(jQuery('<option>', { value : key }).text(value.GeographyName));
		}
		var clonedRow = jQuery('div.Geography table.List thead tr:first').clone();
		jQuery('.GeographyId'    ,clonedRow).html(value.GeographyId);
		jQuery('.GeographyLevel' ,clonedRow).html(value.GeographyLevel);
		jQuery('.GeographyCode'  ,clonedRow).html(value.GeographyCode);
		jQuery('.GeographyName'  ,clonedRow).html(value.GeographyName);
		jQuery('.GeographyActive',clonedRow).html(value.GeographyActive);
		jQuery('.GeographyStatus',clonedRow).html(jQuery('select.GeographyStatusText option[value="' + value.GeographyActive + '"]').text());
		jQuery('div.Geography table.List tbody').append(clonedRow);
	});
	if (jQuery('option',select).size() > 1)
	{
		select.enable();
	}
	jQuery('div.Geography table.List td.GeographyLevel').hide();
	jQuery('div.Geography table.List td.GeographyActive').hide();
	jQuery('div.Geography table.List tr').removeClass('under');
	jQuery('div.Geography table.List tr:even').addClass('under');
} //populate_geography_list

function load_geography_list(prmGeographyId, prmGeoLevelId)
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGeographyGetList',
			RegionId : jQuery('#desinventarRegionId').val(),
			GeographyId : prmGeographyId
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('div.Geography input.ParentId').val(prmGeographyId);
				jQuery('div.Geography input.GeoLevelId').val(data.GeoLevelId);
				populate_geography_list(data.GeographyList,data.GeographyListCount);
				jQuery('div.Geography div.Add').show();
				jQuery('div.Geography div.Edit').hide();
			}
		},
		'json'
	);
} //load_geography_list()
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
		jQuery('#divGeolevels_Edit .GeocartoEdit').show();
		jQuery('#btnGeolevels_Add').hide();
		jQuery('#frmGeolevel .GeoLevelLayerName').val('');
		jQuery('#frmGeolevel .GeoLevelLayerCode').val('');
		jQuery('#frmGeolevel .GeoLevelLayerParentCode').val('');
		jQuery('#frmGeolevel tr.FileUploader input.filename').val('');
		jQuery('#frmGeolevel tr.FileUploader span.uploaded').text('').show();
	}).on('mouseover', 'tr', function(event) {
			jQuery(this).addClass('highlight');
	}).on('mouseout', 'tr', function(event) {
		jQuery(this).removeClass('highlight');
	});

	jQuery('#btnGeolevels_Add').click(function() {
		jQuery('#divGeolevels_Edit').show();

		//jQuery('#divGeolevels_Edit .GeocartoEdit').hide();
		jQuery('#frmGeolevel .GeoLevelLayerName').val('');
		jQuery('#frmGeolevel .GeoLevelLayerCode').val('');
		jQuery('#frmGeolevel .GeoLevelLayerParentCode').val('');
		jQuery('#frmGeolevel tr.FileUploader input.filename').val('');
		jQuery('#frmGeolevel tr.FileUploader span.uploaded').text('').show();
		
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

	jQuery('#frmGeolevel .OptionImportGeographyCheckbox').change(function(event) {
		var v = 0;
		if (jQuery(this).is(':checked'))
		{
			v = 1;
		}
		jQuery('#frmGeolevel .OptionImportGeography').val(v);
	});
	jQuery('#frmGeolevel .OptionImportGeographyText').click(function() {
		jQuery('#frmGeolevel .OptionImportGeographyCheckbox').prop('checked', !jQuery('#frmGeolevel .OptionImportGeographyCheckbox').prop('checked')).change();
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
						jQuery('#frmGeolevel .GeoLevelId').val(data.GeoLevelId);
						jQuery('#divGeolevels_Edit').hide();
						jQuery('#btnGeolevels_Add').show();
						jQuery('div.status .statusUpdateOk').show();
						doGeolevelsPopulateList(data.GeolevelsList);

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
						jQuery('div.status .statusUpdateError').show();
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
				UploadExt  : fileExt
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
function onReadyDatabaseList()
{
	doDatabaseDeleteCreate();
	
	jQuery('#divDatabaseFindList table.databaseList').on('mouseover','td.RegionDelete', function() {
		jQuery(this).parent().highlight();
	}).on('mouseout','td.RegionDelete', function() {
		jQuery(this).parent().unhighlight();
	}).on('click','td.RegionDelete', function(event) {
		var RegionId = jQuery(this).parent().find('td.RegionId').text();
		var RegionLabel = jQuery(this).parent().find('span.RegionLabel').text();
		jQuery('#divDatabaseDeleteContent span.RegionId').text(RegionId);
		jQuery('#divDatabaseDeleteContent span.RegionLabel').text(RegionLabel);
		doDatabaseDeleteShow();
		event.preventDefault();
	});
	jQuery('#divDatabaseDeleteContent').on('click', 'a.buttonOk', function(event) {
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseDelete',
				RegionId : jQuery('div.DatabaseDelete span.RegionId').text()
			},
			function(data)
			{
				jQuery('div.DatabaseDelete span.status').hide();
				if (parseInt(data.Status) > 0)
				{
					jQuery('div.DatabaseDelete input.HasDeleted').val(1);
					jQuery('div.DatabaseDelete span.StatusOk').show();
					jQuery('div.DatabaseDelete a.button').hide();
					jQuery('div.DatabaseDelete a.buttonClose').show();
				}
				else
				{
					jQuery('div.DatabaseDelete input.HasDeleted').val(0);
					jQuery('div.DatabaseDelete span.StatusError').show();
					setTimeout(function() {
						jQuery('div.DatabaseDelete span.status').hide();
					}, 3000);
				}
			},
			'json'
		);
		event.preventDefault();
	});
	jQuery('div.DatabaseDelete').on('click', 'a.buttonCancel', function(event) {
		jQuery('div.DatabaseDelete input.HasDeleted').val(0);
		Ext.getCmp('wndDatabaseDelete').hide();
		event.preventDefault();
	});
	jQuery('div.DatabaseDelete').on('click', 'a.buttonClose', function(event) {
		Ext.getCmp('wndDatabaseDelete').hide();
		event.preventDefault();
	});
} //onReadyDatabaseList

function doDatabaseDeleteCreate()
{
	var w = new Ext.Window({id:'wndDatabaseDelete', 
		el: 'divDatabaseDeleteWin', layout:'fit', 
		width:450, height:200, modal:false, constrainHeader: true,
		plain: false, animCollapse: false,
		closeAction: 'hide',
		items: new Ext.Panel({
			contentEl: 'divDatabaseDeleteContent',
			autoScroll: true
		})
	});
	w.on('hide', function() {
		var HasDeleted = parseInt(jQuery('div.DatabaseDelete input.HasDeleted').val());
		if (HasDeleted > 0)
		{
			doUpdateDatabaseListByUser();
		}
	});
} // doDatabaseUploadCreate()

function doDatabaseDeleteShow()
{
	// Initialization
	jQuery('div.DatabaseDelete span.status').hide();
	jQuery('div.DatabaseDelete a.button').show();
	jQuery('div.DatabaseDelete a.buttonClose').hide();
	jQuery('div.DatabaseDelete input.HasDeleted').val(0);
	//Show
	Ext.getCmp('wndDatabaseDelete').show();
} // doDatabaseUploadAction

/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
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
					alert(jQuery('#msgDatabaseUploadReplaceComplete').val());
					jQuery('body').trigger('cmdWindowReload');
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
				cmd        : 'cmdDatabaseCopy',
				RegionId   : jQuery('#txtDatabaseUploadRegionId').text(),
				RegionLabel: jQuery('#txtDatabaseUploadRegionLabel').text(),
				Filename   : jQuery('#txtDatabaseUploadFilename').val()
			},
			function(data)
			{
				doDatabaseUploadStatusMsg('');
				if (parseInt(data.Status) > 0)
				{
					jQuery('#divDatabaseUploadParameters').hide();
					doDatabaseUploadStatusMsg('msgDatabaseUploadUpdateOk');
					alert(jQuery('#msgDatabaseUploadCopyComplete').val());
					window.location = jQuery('#desinventarURL').val() + '/' + jQuery('#txtDatabaseUploadRegionId').text();
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
					doDatabaseUploadReset(false);
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
	jQuery('#divFileUploaderControl').each(function() {
		var uploader = new qq.FileUploader({
			element: document.getElementById(jQuery(this).attr('id')),
			action: jQuery('#desinventarURL').val() + '/',
			params:
			{
				cmd        : 'cmdDatabaseUpload',
				UploadMode : jQuery('#fldDatabaseUploadMode').val(),
				RegionId   : jQuery('#desinventarRegionId').val()
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
					if (jQuery('#fldDatabaseUploadMode').val() == 'Copy')
					{
						jQuery('#txtDatabaseUploadConfirmCopy').show();
						jQuery('#btnDatabaseUploadCopy').show();
					}
					else
					{
						jQuery('#txtDatabaseUploadConfirmReplace').show();
						jQuery('#btnDatabaseUploadReplace').show();
					}
					doDatabaseUploadSetParameters(data.RegionInfo);
					doDatabaseUploadStatusMsg('');
					jQuery('#divDatabaseUploadControl').hide();
					jQuery('#divDatabaseUploadParameters').show();
				}
				else
				{
					doDatabaseUploadReset(false);
					switch(parseInt(data.Status)) {
						case -130: //ERR_INVALID_ZIPFILE
							doDatabaseUploadStatusMsg('msgDatabaseUploadErrorNoInfo');
						break;
						default:
							doDatabaseUploadStatusMsg('msgDatabaseUploadErrorOnUpload');
						break;
					}
				}
			},
			onCancel: function(id, Filename)
			{
			}
		});
	});
	jQuery('#divFileUploaderControl .qq-upload-button-text').html(jQuery('#msgDatabaseUploadChooseFile').val());
	jQuery('#divFileUploaderControl .qq-upload-list').hide();

	jQuery('#btnDatabaseUploadCancel').click(function() {
		doDatabaseUploadReset(true);
		uploader.cancel(jQuery('#txtDatabaseUploadId').val());
	});
} //doAdminDatabaseCreateUploader()

function doDatabaseUploadReset(prmShowRegionInfo)
{
	doAdminDatabaseCreateUploader();
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

	if ( (jQuery('#fldDatabaseUploadMode').val() == 'Copy') || (prmShowRegionInfo == false) )
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
		width:400, height:220, modal:false, constrainHeader: true,
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

function doDatabaseUploadShow(prmMode)
{
	jQuery('#fldDatabaseUploadMode').val(prmMode);
	if (prmMode == 'Copy')
	{
		Ext.getCmp('wndDatabaseUpload').setTitle(jQuery('#mnuDatabaseCopy').text());
	}
	else
	{
		Ext.getCmp('wndDatabaseUpload').setTitle(jQuery('#mnuDatabaseReplace').text());
	}
	jQuery('.clsDatabaseUpload').hide();
	doDatabaseUploadReset(true);
	Ext.getCmp('wndDatabaseUpload').show();
} // doDatabaseUploadAction
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
	});
	jQuery('#frmDiffusion .RegionPublicText').click(function() {
		jQuery('#frmDiffusion .RegionPublic').trigger('click');
	});

	jQuery('#frmDiffusion .btnCancel').click(function() {
		var RegionInfo = new Array();
		RegionInfo.RegionStatus = jQuery('#fldDatabaseUsers_RegionStatus').val();
		doDatabaseUsersUpdateOptions(RegionInfo);
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
	});
		
	jQuery('#btnDatabaseUsers_Add').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').show();
	});

	jQuery('#frmUsers .btnCancel').click(function() {
		doDatabaseUsersReset();
		jQuery('#divDatabaseUsers_Edit').hide();
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
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyDatacards()
{
	jQuery('#divDatacardWindow').hide();

	jQuery('div.Datacard').on('cmdInitialize', function() {
		doDatacardInitialize();
		return false;
	});

	// Limit length of text area fields using a maxlength attribute...	
	jQuery('#DICard textarea').keyup(function() {
		var maxlength = parseInt(jQuery(this).attr('maxlength'));
		if (! isNaN(maxlength) ) {
			var value     = jQuery(this).val();
			if (value.length > maxlength) {
				jQuery(this).val(value.substr(0,maxlength));
			}
		}
	});

	jQuery('#DICard').unbind('submit').submit(function() {
		jQuery('#DatacardCommand').val(jQuery('#_CMD').val());
		jQuery('#RecordAuthor').val(jQuery('#desinventarUserId').val());
		displayDatacardStatusMsg('');
		var params = jQuery(this).serializeObject();
		jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
			jQuery.extend(params, { RegionId : jQuery('#desinventarRegionId').val() }),
			function(data)
			{
				if (data.Status == 'OK')
				{
					jQuery('#DisasterId').val(data.DisasterId);
					jQuery('#RecordSerial').text(data.RecordSerial);
					jQuery('#RecordPublished').text(data.RecordPublished);
					jQuery('#RecordReady').text(data.RecordReady);
					switch (data.StatusCode)
					{
						case 'INSERTOK':
							displayDatacardStatusMsg('msgDatacardInsertOk');
							jQuery('#cardsRecordSource').val('');
							jQuery('#cardsRecordCount').val(data.RecordCount);
							jQuery('#cardsRecordNumber').val(data.RecordCount);
							jQuery('#divRecordStat').show();
						break;
						case 'UPDATEOK':
							displayDatacardStatusMsg('msgDatacardUpdateOk');
							jQuery('#divRecordStat').show();
						break;
					} //switch
					DisableEnableForm($('DICard'), true);
					changeOptions('btnDatacardSave');
					if (parseInt(jQuery('#cardsRecordNumber').val()) > 0)
					{
						jQuery('#RecordNumber').text(jQuery('#cardsRecordNumber').val());
						jQuery('#RecordCount').text(jQuery('#cardsRecordCount').val());
						jQuery('#divRecordNavigationInfo').show();
					}
				}
				else
				{
					switch(data.ErrorCode)
					{
						case -10:
						case -52:
							displayDatacardStatusMsg('msgDatacardNetworkError');
						break;
						case -54:
							displayDatacardStatusMsg('msgDatacardDuplicatedSerial');
						break;
						case -61:
							displayDatacardStatusMsg('msgDatacardWithoutEffects');
						break;
						case -62:
							displayDatacardStatusMsg('msgDatacardOutsideOfPeriod');
						break;
						default:
							jQuery('#msgDatacardCustom').text(data.StatusMsg);
							displayDatacardStatusMsg('msgDatacardCustom');
						break;
					}
				}
				showtip('','#ffffff');
			},
			'json'
		);
		return false;
	});
	
	// Enable/Disable related EffectSector fields based on value of other fields...	
	jQuery('.clsEffectDouble').blur(function(event) {
		var altField = jQuery(this).attr('altField');
		var value    = parseInt(jQuery(this).val());
		var field    = jQuery('#DICard #' + altField);
		if (value > 0)
		{
			field.attr('oldValue', field.val());
			field.val(-1);
		}
		else
		{
			if (value == 0)
			{
				if (field.attr('oldValue') == '')
				{
					field.attr('oldValue', 0);
				}
				field.val(field.attr('oldValue'));
				
			}
		}
	});

	// Enable loading of geographic levels when editing...
	jQuery('#divDatacard .tblGeography').on('change', '.GeoLevelSelect', function() {
		var GeographyLevel = parseInt(jQuery(this).data('GeographyLevel'));
		var NextGeographyLevel = GeographyLevel + 1;
		var myGeographyId = jQuery(this).val();
		var GeographyParentId = myGeographyId.substr(0, myGeographyId.length - 5);
		var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;

		// Clear values of following sublevels
		for(var i = NextGeographyLevel; i < GeoLevelCount; i++)
		{
			var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
			mySelect.empty();
			mySelect.append(jQuery('<option>', { value : '' }).text(''));
			mySelect.disable();
		}

		if (jQuery(this).val() == '')
		{
			var PrevGeographyLevel = GeographyLevel - 1;
			myGeographyId = '';
			if (PrevGeographyLevel >= 0)
			{
				myGeographyId = jQuery('#divDatacard .tblGeography #GeoLevel' + PrevGeographyLevel).val();
				jQuery('#divDatacard #GeographyId').val(myGeographyId);
			}
		}
		else
		{
			jQuery('#divDatacard #GeographyId').val(myGeographyId);
			if (NextGeographyLevel < GeoLevelCount)
			{
				updateGeoLevelSelect(jQuery(this).val(), true);
			}
		}
		jQuery(this).focus();
	});	

	// Validation of DisasterBeginTime and Suggest Serial for New Datacards
	jQuery('#DisasterBeginTime0').blur(function() {
		cmd = jQuery('#_CMD').val();
		if (cmd == 'insertDICard')
		{
			doDatacardSuggestSerial();
		}
	});

	jQuery('#DisasterBeginTime1').blur(function() {
		if (jQuery(this).val() == '' ||
		    parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 12 )
		{
			jQuery(this).val('');
			jQuery('#DisasterBeginTime2').val('');
		}
	});

	jQuery('#DisasterBeginTime2').blur(function() {
		if (parseInt(jQuery(this).val(),10) < 1 || 
			parseInt(jQuery(this).val(),10) > 31 )
		{
			jQuery(this).val('');
		}
	});

	// Button for suggesting serial of datacard or restoring initial Serial when editing...
	jQuery('#linkDatacardSuggestSerial').unbind('click').click(function() {
		if (jQuery('#DICard #Status').val() == 'NEW')
		{
			doDatacardSuggestSerial();
		}
		if (jQuery('#DICard #Status').val() == 'EDIT')
		{
			jQuery('#DisasterSerial').val(jQuery('#PrevDisasterSerial').val());
		}
	});

	// Apply some validation for several types of input fields
	jQuery('.inputInteger').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'integer:' + jQuery(this).attr('MaxLength'));
	});

	jQuery('.inputDouble').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'double:' + jQuery(this).attr('MaxLength'));
	}).blur(function() {
		/*
		var answer = validateInputDouble(jQuery(this).val());
		if (answer > 0)
		{
			jQuery(this).unhighlight();
		}
		else
		{
			jQuery(this).highlight();
			jQuery(this).focus();
		}
		*/
		return false;
	});

	jQuery('.inputText').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'text:');
	});

	jQuery('.inputAlphaNumber').keydown(function(event) {
		return blockChars(event, jQuery(this).val(), 'alphanumber:');
	});
	
	// Datacard New/Edit/Save Commands
	jQuery('#btnDatacardNew').click(function() {
		doDatacardClear();
		doDatacardNew();
		jQuery('#txtDatacardFind').val('');
		jQuery('#GeographyId').val('');
		jQuery('#DisasterId').val(Math.uuid());
		return false;
	});
	
	jQuery('#btnDatacardEdit').click(function() {
		jQuery('#txtDatacardFind').val('');
		doDatacardEdit();
		return false;
	});
	
	jQuery('#btnDatacardSave').click(function() {
		doDatacardSave();
		return false;
	});
	
	jQuery('#btnDatacardCancel').click(function() {
		doDatacardCancel();
		return false;
	});
	
	jQuery('#btnDatacardPrint').click(function() {
		window.print();
		return false;
	});

	// Datacard Navigation Functions
	jQuery('#btnDatacardGotoFirst').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoFirst();
		return false;
	});

	jQuery('#btnDatacardGotoLast').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoLast();
		return false;
	});
	
	jQuery('#btnDatacardGotoPrev').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoPrev();
		return false;
	});
	
	jQuery('#btnDatacardGotoNext').click(function() {
		jQuery('#divRecordStat').hide();
		jQuery('#txtDatacardFind').val('');
		doDatacardGotoNext();
		return false;
	});

	// Datatacard Find 
	jQuery('#txtDatacardFind').keydown(function(event) {
		if(event.keyCode == 13)
		{
			doDatacardFind();
		}
	});	
	
	jQuery('#btnDatacardFind').click(function() {
		doDatacardFind();
	});

	// Switch between Basic and Additional Effects
	jQuery('#linkDatacardShowEffectsBasic').click(function() {
		jQuery('#divDatacardEffectsBasic').show();
		jQuery('#divDatacardEffectsAdditional').hide();
		return false;
	});
	
	jQuery('#linkDatacardShowEffectsAditional').click(function() {
		jQuery('#divDatacardEffectsBasic').hide();
		jQuery('#divDatacardEffectsAdditional').show();
		return false;
	});

	jQuery('#divDatacard .EventId').on('mouseenter', 'option', function() {
		showtip(jQuery(this).data('tooltip'), 'lightblue');
	});
	jQuery('#divDatacard .EventId').mouseleave(function() {
		showtip('', '#fff');
	});

	jQuery('#divDatacard .CauseId').on('mouseenter', 'option', function() {
		showtip(jQuery(this).data('tooltip'), '#ffffc0');
	});
	jQuery('#divDatacard .CauseId').mouseleave(function() {
		showtip('', '#fff');
	});

	// Dependency between fields
	jQuery('#DICard').on('blur','#EffectRoads', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorTransport').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectFarmingAndForest', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorAgricultural').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectLiveStock', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorAgricultural').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectEducationCenters', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorEducation').val(-1);
		}
	});
	jQuery('#DICard').on('blur','#EffectMedicalCenters', function() {
		var v = jQuery.trim(jQuery(this).val());
		if ( (v != '') && (parseFloat(v) > 0) )
		{
			jQuery('#DICard #SectorHealth').val(-1);
		}
	});
	jQuery('div.Datacard .inputText').on('blur',function() {
		jQuery(this).val(jQuery(this).val().replace(/\n/,' '));
	});
	
	jQuery('#DICard').on('blur','#EffectOtherLosses', function() {
		jQuery(this).val(jQuery(this).val().replace(/\n/,' '));
		if (jQuery.trim(jQuery(this).val()) != '')
		{
			jQuery('#DICard #SectorOther').val(-1);
		}
		
	});

	// Attach events to main body
	jQuery('body').on('cmdDatacardShow', function() {
		doDatacardShow();
	});

	jQuery('body').on('cmdDatacardGoto', function(event, prmDisasterId, prmRecordNumber, prmRecordCount) {
		setDICardFromId(jQuery('#desinventarRegionId').val(), prmDisasterId, prmRecordNumber, prmRecordCount);
	});
	//Initialize components
	jQuery('#divDatacard .tblGeography tr:first').hide();
	jQuery('div.Datacard table.EffectList').on('focus','select.value', function(event) {
		showtip(jQuery(this).data('helptext'), '#f1bd41');
	}).on('focus','input.value', function(event) {
		showtip(jQuery(this).data('helptext'), '#f1bd41');
	});
} //onReadyDatacards()

function doDatacardInitialize()
{
	// Load EffectPeople List (ef1)
	jQuery('div.Datacard table.EffectListPeople select.value').jec({
		maxLength: 15,
		acceptedKeys : [48,49,50,51,52,53,54,55,56,57,58]
	}).blur(function() {
		if (jQuery(this).val() == '')
		{
			jQuery(this).val(0);
		}
	});

	// EffectLosses2 List (ef3)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
		var fieldname = jQuery(this).data('field');
		var field = jQuery('div.Datacard table.EffectListLosses2 tr.' + fieldname);
		jQuery('span.label' , field).text(jQuery('span.label', this).text());
		jQuery('input.value', field).data('helptext', jQuery('span.helptext',this).text());
	});	

	// EffectOther List (ef4)
	jQuery('div.desinventarInfo div.EffectList div.EffectOther').each(function() {
		var fieldname = jQuery(this).data('field');
		var field = jQuery('div.Datacard table.EffectListOther tr.' + fieldname);
		jQuery('span.label' , field).text(jQuery('span.label', this).text());
		jQuery('span.label' , field).attr('title', jQuery('span.tooltip', this).text());
		jQuery('input.value', field).data('helptext', jQuery('span.helptext',this).text());
	});
	
	// Additional Effect List (EEFieldList);	
	var effect_list = jQuery('div.Datacard table.EffectListAdditional');
	effect_list.find('div.EffectAdditional:gt(0)').remove();
	var column = 0;
	var max_column = 3;
	jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
		var field = key;
		var label = value[0];
		var tooltip = value[1];
		var type = value[2];

		var clone = jQuery('div.EffectAdditional:last', effect_list).clone().show();
		jQuery('span.label' , clone).text(label);
		jQuery('span.label' , clone).attr('title', tooltip);
		jQuery('input.value',clone).hide();
		var className='inputText';
		switch(type)
		{
			case 'INTEGER':
				className='inputInteger';
			break;
			case 'CURRENCY':
			case 'DOUBLE':
				className='inputDouble';
			break;
			default:
				className='inputText';
			break;			
		}
		jQuery('input', clone).attr('id','').attr('name','');
		jQuery('input.' + className, clone).show();
		jQuery('input.' + className, clone).attr('id', field);
		jQuery('input.' + className, clone).attr('name', field);
		jQuery('input.' + className, clone).data('helptext', tooltip);
		column = (column + 1) % max_column;
		jQuery('tr:last td:eq(' + column + ')',effect_list).append(clone);
	});
} //doDatacardInitialize()

function updateGeoLevelSelect(prmGeographyId, prmWithChilds)
{
	var GeographyList = jQuery('body').data('GeographyList-' + prmGeographyId);
				
	if (GeographyList === undefined)
	{
		// Load GeographyList using POST
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				'cmd'         : 'cmdGeographyGetItemsById',
				'RegionId'    : jQuery('#desinventarRegionId').val(),
				'GeographyId' : prmGeographyId
			},
			function(data)
			{
				if (parseInt(data.Status) > 0)
				{
					jQuery.each(data.GeographyList, function(key, value) {
						// Store result for later use from cache
						var NextGeographyLevel = parseInt(key.length)/5;
						jQuery('body').data('GeographyList-' + key, value);
						doUpdateGeoLevelSelect(NextGeographyLevel, value);
					});
				}
			},
			'json'
		);
	}
	else
	{
		// Enable sublevels and reuse data from local cache
		var GeoLevelCount = prmGeographyId.length/5 + 1;
		for(var GeographyLevel = 1; GeographyLevel < GeoLevelCount; GeographyLevel++)
		{
			var GeographyParent = prmGeographyId.substr(0, GeographyLevel * 5);
			var myGeographyList = jQuery('body').data('GeographyList-' + GeographyParent);
			doUpdateGeoLevelSelect(GeographyLevel, myGeographyList);
		}
	}
}

function doUpdateGeoLevelSelect(prmGeographyLevel, prmGeographyList)
{
	var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + prmGeographyLevel);
	var myPrevValue = mySelect.val();
	mySelect.empty();
	mySelect.append(jQuery('<option>', { value : '' }).text(''));
	jQuery.each(prmGeographyList, function(index, value) {
		mySelect.append(jQuery('<option>', { value : value.GeographyId }).text(value.GeographyName));
	});
	mySelect.val(myPrevValue);
	if (myPrevValue != '')
	{
		myGeographyId = myPrevValue;
	}
	mySelect.enable();
} //doUpdateGeoLevelSelect()

function doDatacardShow()
{
	//if (jQuery('#divDatacard').is(':hidden'))
	{
		//GeoLevel
		jQuery('#divDatacard .tblGeography tr:gt(0)').remove();
		jQuery('#divDatacard .tblGeography tr:first').hide();
		var GeolevelsList = jQuery('body').data('GeolevelsList');
		if (GeolevelsList == undefined)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd      : 'cmdDatabaseLoadData',
					RegionId : jQuery('#desinventarRegionId').val()
				},
				function(data)
				{
					jQuery('body').data('GeolevelsList', data.GeolevelsList);
					jQuery('body').data('EventList', data.EventList);
					jQuery('body').data('CauseList', data.CauseList);
					jQuery('body').data('RecordCount', data.RecordCount);
					var dataItems = jQuery('body').data();
					jQuery.each(dataItems, function(index, value) {
						if (index.substr(0,13) === 'GeographyList')
						{
							jQuery('body').removeData(index);
						}
					});
					jQuery('body').data('GeographyList', data.GeographyList);
					doDatacardUpdateDisplay();
				},
				'json'
			);
		}
		else
		{
			doDatacardUpdateDisplay();
		}
	}
	var UserRoleValue = jQuery('#desinventarUserRoleValue').val();
	if (UserRoleValue <= 2)
	{
		jQuery('#DICard select.RecordStatus option[value="PUBLISHED"]').disable();
		jQuery('#DICard select.RecordStatus option[value="DELETED"]').disable();
	}
	else
	{
		jQuery('#DICard select.RecordStatus option[value="PUBLISHED"]').enable();
		jQuery('#DICard select.RecordStatus option[value="DELETED"]').enable();
	}
} //doDatacardShow()

function doDatacardUpdateDisplay()
{	
	var GeolevelsList = jQuery('body').data('GeolevelsList');
	if (GeolevelsList != undefined)
	{
		jQuery.each(GeolevelsList, function(index, value) {
			var clonedRow = jQuery('#divDatacard .tblGeography tr:last').clone().show();
			jQuery('.GeoLevelId', clonedRow).text(index);
			jQuery('.GeoLevelName', clonedRow).text(value.GeoLevelName);
			jQuery('select', clonedRow).attr('id', 'GeoLevel' + index).attr('level', index).data('GeographyLevel', index);
			jQuery('.tblGeography').append(clonedRow);
		});
	}

	var FirstRow = jQuery('#divDatacard .tblGeography select:data("GeographyLevel=0")');
	FirstRow.empty();
	FirstRow.append(jQuery('<option>', { value : '' }).text(''));
	GeographyList = jQuery('body').data('GeographyList');
	if (GeographyList != undefined)
	{
		jQuery.each(GeographyList, function(index, value) {
			FirstRow.append(jQuery('<option>', { value : index }).text(value.GeographyName));
		});
	}

	jQuery('#divDatacard .EventId').empty();
	jQuery('#divDatacard .EventId').append(jQuery('<option>', { value : '' }).text(''));
	EventList = jQuery('body').data('EventList');
	if (EventList != undefined)
	{
		jQuery.each(EventList, function(index, value) {
			jQuery('#divDatacard .EventId').append(jQuery('<option>', { value : index }).text(value.EventName).data('tooltip',value.EventDesc));
		});
	}

	jQuery('#divDatacard .CauseId').empty();
	jQuery('#divDatacard .CauseId').append(jQuery('<option>', { value : '' }).text(''));
	CauseList = jQuery('body').data('CauseList');
	if (CauseList != undefined)
	{
		jQuery.each(CauseList, function(index, value) {
			jQuery('#divDatacard .CauseId').append(jQuery('<option>', { value : index }).text(value.CauseName).data('tooltip',value.CauseDesc));
		});
	}

	jQuery('#divDatacard #cardsRecordNumber').val(0);
	jQuery('#divDatacard #cardsRecordCount').val(jQuery('body').data('RecordCount'));

	// Initialize controls in form when it is displayed
	// Reset buttons
	doDatacardClear();
	// Hide StatusMessages
	displayDatacardStatusMsg('');
	jQuery('#divDatacardStatusMsg').show();
	// Hide window's parameters
	jQuery('#divDatacardParameter').hide();
	jQuery('#divRecordNavigationInfo').hide();
	
	DisableEnableForm($('DICard'), true);
	changeOptions();

	// Start with Basic Effects show
	jQuery('#linkDatacardShowEffectsBasic').trigger('click');
	
	//Show Command Buttons only for Role>=USER
	jQuery('.DatacardCmdButton').hide();
	jQuery('#btnDatacardPrint').show();
	if (parseInt(jQuery('#desinventarUserRoleValue').val()) >= 2)
	{
		jQuery('.DatacardCmdButton').show();
		displayDatacardStatusMsg('msgDatacardStartNew');
	}
	doDatacardNavButtonsEnable();

	var w = Ext.getCmp('wndDatacard');
	if (w != undefined)
	{
		w.show();
	}
} //doDatacardUpdateDisplay();

function displayDatacardStatusMsg(msgId)
{
	// First hide all items
	jQuery('.datacardStatusMsg').hide();
	// Show a specific message
	if (msgId != '')
	{
		jQuery('#' + msgId).show();
	}
}

var mod = "di";

function DisableEnableForm(xForm, disab)
{
	if (xForm != null)
	{
		objElems = xForm.elements;
		var myname = '';
		var mysty = '';
		if (disab)
		{
			col = '#eee';
		}
		else
		{
			col = '#fff';
		}
		for (i=0; i < objElems.length; i++)
		{
			myname = objElems[i].name + "";
			if (myname.substring(0,1) != "_")
			{
				objElems[i].disabled = disab;
				objElems[i].style.backgroundColor = col;
			}
		}
	}
}

function disenabutton(butid, disab)
{
	if (disab)
	{
		if (butid != null)
		{
			butid.disable();
		}
		Element.removeClassName(butid, 'bb');
		Element.addClassName(butid, 'disabled');
	}
	else
	{
		if (butid != null)
		{
			butid.enable();
		}
		Element.addClassName(butid, 'bb');
		Element.removeClassName(butid, 'disabled');
	}
}

function doDatacardNavButtonsDisable()
{
	disenabutton($('btnDatacardGotoFirst'), true);
	disenabutton($('btnDatacardGotoPrev'), true);
	disenabutton($('btnDatacardGotoNext'), true);
	disenabutton($('btnDatacardGotoLast'), true);
}

function doDatacardNavButtonsEnable()
{
	var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
	var RecordCount  = parseInt(jQuery('#cardsRecordCount').val());
	if (RecordNumber > 0)
	{
		if (RecordNumber > 1)
		{
			disenabutton($('btnDatacardGotoFirst'), false);
			disenabutton($('btnDatacardGotoPrev'), false);
		}
		else
		{
			disenabutton($('btnDatacardGotoFirst'), true);
			disenabutton($('btnDatacardGotoPrev'), true);
		}
		if (RecordNumber < RecordCount)
		{
			disenabutton($('btnDatacardGotoLast'), false);
			disenabutton($('btnDatacardGotoNext'), false);
		}
		else
		{
			disenabutton($('btnDatacardGotoLast'), true);
			disenabutton($('btnDatacardGotoNext'), true);
		}
	}
	else
	{
		disenabutton($('btnDatacardGotoPrev'), true);
		disenabutton($('btnDatacardGotoNext'), true);
		if (RecordCount > 0)
		{
			disenabutton($('btnDatacardGotoFirst'), false);
			disenabutton($('btnDatacardGotoLast'), false);
		}
		else
		{
			disenabutton($('btnDatacardGotoFirst'), true);
			disenabutton($('btnDatacardGotoLast'), true);
		}
	}
}

function changeOptions(but)
{
	switch (but)
	{
		case "btnDatacardNew":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), false);
			doDatacardNavButtonsDisable();
			disenabutton($('btnDatacardFind'), true);
		break;
		case "btnDatacardEdit":
			disenabutton($('btnDatacardNew'), true);
			disenabutton($('btnDatacardSave'), false);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), false);
			doDatacardNavButtonsDisable();
			disenabutton($('btnDatacardFind'), true);
		break;
		case "btnDatacardSave":
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), false);
			disenabutton($('btnDatacardCancel'), true);
			doDatacardNavButtonsEnable();
			disenabutton($('btnDatacardFind'), false);
		break;
		case "btnDatacardCancel":
			if ($('DisasterId').value == '')
			{
				disenabutton($('btnDatacardEdit'), true);
			}
			else
			{
				disenabutton($('btnDatacardEdit'), false);
			}
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardCancel'), true);
			disenabutton($('btnDatacardNew'), false);
			doDatacardNavButtonsEnable();
			disenabutton($('btnDatacardFind'), false);
		break;
		default:
			disenabutton($('btnDatacardNew'), false);
			disenabutton($('btnDatacardSave'), true);
			disenabutton($('btnDatacardEdit'), true);
			disenabutton($('btnDatacardCancel'), true);
		break;
	}
}

function requestDatacard(myCmd, myValue)
{
	var bReturn = true;
	var RegionId=jQuery('#desinventarRegionId').val();
	jQuery('#dostat').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif' + '" alt="" />');
	jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
		{
			cmd:myCmd,
			value:myValue,
			r:RegionId
		},
		function(data)
		{
			jQuery('#dostat').html('');
			if (myCmd == 'getNextSerial')
			{
				if (data.DisasterSerial.length >= 5)
				{
					jQuery('#DisasterSerial').val(myValue + '-' + data.DisasterSerial);
				}
			}
			else if (data.Status == 'OK')
			{
				displayDatacardStatusMsg('');
				if (data.DisasterId != '')
				{
					jQuery('#cardsRecordSource').val('');
					valid = setDICardFromId(RegionId, data.DisasterId, data.RecordNumber, data.RecordCount);
					if (jQuery('#desinventarUserRoleValue').val() >= 2)
					{
						disenabutton($('btnDatacardEdit'), false);
					}
					if (myCmd == 'getDisasterIdFromSerial')
					{
						displayDatacardStatusMsg('msgDatacardFound');
					}
				}
				else
				{
					displayDatacardStatusMsg('msgDatacardNotFound');
					bReturn = false;
				}
			}
			else
			{
				bReturn = false;
			}
		},
		'json'
	);
	jQuery('#dostat').html('');
	return bReturn;
}


function doDatacardFind()
{
	if(jQuery('#txtDatacardFind').val() !='')
	{
		requestDatacard('getDisasterIdFromSerial', jQuery('#txtDatacardFind').val());
	}
}

function doDatacardClear()
{
	jQuery('#DisasterId').val();
	$('DICard').reset();
	jQuery('#_CMD').val('insertDICard');
	jQuery('#cardsRecordNumber').val(0);
	jQuery('div.Datacard table.EffectListPeople .clsEffectNumeric').each(function() {
		jQuery(this).val(0);
		jQuery(this).jecValue('',false);
	});
	jQuery('div.Datacard select.clsEffectSector').each(function() {
		jQuery(this).val(0); // There weren't by default
	});

	jQuery('#DICard .inputDouble').each(function() {
		jQuery(this).val(0);
	});
	jQuery('#DICard .inputInteger').each(function() {
		jQuery(this).val(0);
	});
	jQuery('#DICard #DisasterBeginTime0').val('');
	jQuery('#DICard #DisasterBeginTime1').val('');
	jQuery('#DICard #DisasterBeginTime2').val('');
	jQuery('#DICard #EventDuration').val(0);
}

function doDatacardNew()
{

	DisableEnableForm($('DICard'), false);
	jQuery('#DisasterBeginTime0').focus();
	displayDatacardStatusMsg('msgDatacardFill');
	changeOptions('btnDatacardNew');
	jQuery('#divRecordNavigationInfo').hide();
	jQuery('#DICard #Status').val('NEW');

	// Clear values of following sublevels
	var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;
	for(var i = 1; i < GeoLevelCount; i++)
	{
		var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
		mySelect.empty();
		mySelect.append(jQuery('<option>', { value : '' }).text(''));
		mySelect.disable();
	}
}

function doDatacardEdit()
{
	displayDatacardStatusMsg('');
	var RegionId = jQuery('#desinventarRegionId').val();
	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
			'cmd'        : 'cmdDatacardLock',
			'RegionId'   : jQuery('#desinventarRegionId').val(),
			'DisasterId' : jQuery('#DisasterId').val()
		},
		function(data)
		{
			if (data.DatacardStatus == 'RESERVED')
			{
				DisableEnableForm($('DICard'), false);
				jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
				jQuery('#DisasterBeginTime0').focus();
				jQuery('#_CMD').val('updateDICard');
				displayDatacardStatusMsg('msgDatacardFill');
				changeOptions('btnDatacardEdit');

				// Clear values of following sublevels
				var GeoLevelCount = jQuery('.GeoLevelSelect').size() - 1;
				for(var i = 1; i < GeoLevelCount; i++)
				{
					var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + i);
					mySelect.disable();
				}
				updateGeoLevelSelect(jQuery('#DICard #GeographyId').val(), true);
				jQuery('#DICard #Status').val('EDIT');
			}
			else
			{
				displayDatacardStatusMsg('msgDatacardIsLocked');
			}
		},
		'json'
	);
} //doDatacardEdit()

function doDatacardSave()
{
	var bContinue = 1;
	var cmd = jQuery('#_CMD').val();
	var DisasterSerial = jQuery('#DisasterSerial').val();
	var PrevDisasterSerial = jQuery('#PrevDisasterSerial').val();
	var Status = jQuery('#DICard #Status').val();

	if (bContinue > 0)
	{
		var error_count = 0;
		var answer = 1;
		jQuery('#DICard .inputDouble').each(function() {
			answer = validateInputDouble(jQuery(this).val());
			if (answer > 0)
			{
				if (jQuery(this).attr('old-bg-color') != '') {
					jQuery(this).unhighlight();
				}
			}
			else
			{
				jQuery(this).highlight();
				error_count++;
			}
		});
		if (error_count > 0)
		{
			bContinue = 0;
			displayDatacardStatusMsg('msgDatacardInvalidNumber');
		}
	}	

	if (bContinue > 0)
	{
		// Validate Record Status
		if (jQuery('#DICard #RecordStatus').val() == '')
		{
			displayDatacardStatusMsg('msgDatacardWithoutStatus');
			jQuery('#DICard #RecordStatus').highlight().focus();
			bContinue = 0;
		}
	}
	
	if (bContinue > 0)
	{
		if (jQuery('#DICard #RecordStatus').val() == 'PUBLISHED')
		{
			jQuery('#DICard #DisasterSource').unhighlight();
			jQuery('#DICard #RecordStatus').unhighlight();
			var DisasterSource = jQuery('#DICard #DisasterSource').val();
			DisasterSource = jQuery.trim(DisasterSource);
			if (DisasterSource == '')
			{
				displayDatacardStatusMsg('msgDatacardWithoutSource');
				jQuery('#DICard #DisasterSource').highlight().focus();
				jQuery('#DICard #RecordStatus').highlight();
				bContinue = 0;
			}
		}
	}
	
	if (bContinue > 0)
	{
		// Validate Record Status
		if ( (jQuery('#DICard #RecordStatus').val() == 'PUBLISHED') ||
		     (jQuery('#DICard #RecordStatus').val() == 'DELETED'  ) )
		{
			if (jQuery('#desinventarUserRoleValue').val() <= 2)
			{
				displayDatacardStatusMsg('msgDatacardInvalidStatus');
				jQuery('#DICard #RecordStatus').highlight().focus();
				bContinue = 0;
			}
		}
	}
	
	if ( (bContinue > 0) && (jQuery('#GeographyId').val() == '') )
	{
		displayDatacardStatusMsg('msgDatacardInvalidGeography');
		jQuery('.GeoLevelSelect').highlight();
		jQuery('#GeoLevel0').focus();
		bContinue = 0;
	}

	jQuery('#DICard #EventId').unhighlight();
	if ( (bContinue > 0) && (jQuery('#DICard #EventId').val() == '') )
	{
		jQuery('#DICard #EventId').highlight().focus();
		bContinue = 0;
	}

	jQuery('#DICard #CauseId').unhighlight();
	if ( (bContinue > 0) && (jQuery('#DICard #CauseId').val() == '') )
	{
		jQuery('#DICard #CauseId').highlight().focus();
		bContinue = 0;
	}

	// Use AJAX to save datacard
	if (bContinue > 0)
	{
		if (jQuery('#DICard #Status').val() == 'SAVING')
		{
			// Do Nothing.. already saving datacard...
		}
		else
		{
			jQuery('#DICard #Status').val('SAVING');
			jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
				{
					'cmd'            : 'existDisasterSerial',
					'RegionId'       : jQuery('#desinventarRegionId').val(),
					'DisasterSerial' : DisasterSerial
				},
				function(data)
				{
					bContinue = 1;
					if ( (cmd == 'insertDICard') && (data.DisasterSerial != '') )
					{
						// Serial of new datacard already exists...
						bContinue = 0;
					}
					if (cmd == 'updateDICard')
					{
						if ( (DisasterSerial != PrevDisasterSerial) && (data.DisasterSerial != '') )
						{
							// Edited Serial exists in database...
							bContinue = 0;
						}
					}
					if (bContinue < 1)
					{
						displayDatacardStatusMsg('msgDatacardDuplicatedSerial');
						jQuery('#DICard #Status').val(Status);
						jQuery('#DICard #DisasterSerial').highlight().focus();
					}
					if (bContinue > 0)
					{
						//'DisasterSource', 
						var fl = new Array('DisasterSerial', 'DisasterBeginTime0', 
											'GeoLevel0', 'EventId', 'CauseId');
						if (checkForm('DICard', fl, jQuery('#msgDatacardFieldsError').text()))
						{
							jQuery('#PrevDisasterSerial').val(jQuery('#DisasterSerial').val());
							jQuery('#DICard').submit();
						}
						else
						{
							displayDatacardStatusMsg('msgDatacardFieldsError');
						}
						jQuery('#DICard #Status').val('VIEW');
					}
				},
				'json'
			);
		}
	}
} //doDatacardSave()

function doDatacardCancel()
{
	if (jQuery('#DICard #Status').val() == 'EDIT')
	{
		jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
			{
				'cmd'        : 'cmdDatacardRelease',
				'RegionId'   : jQuery('#desinventarRegionId').val(),
				'DisasterId' : jQuery('#DisasterId').val()
			},
			function(data)
			{
				DisableEnableForm($('DICard'), true);
				changeOptions('btnDatacardCancel');
				// clear Help text area
				showtip('','#ffffff');

				valid = setDICardFromId(jQuery('#desinventarRegionId').val(), jQuery('#DisasterId').val(), jQuery('#cardsRecordNumber').val(), jQuery('#cardsRecordCount').val());
				if (jQuery('#desinventarUserRoleValue').val() >= 2)
				{
					disenabutton($('btnDatacardEdit'), false);
				}
				displayDatacardStatusMsg('');
				doDatacardNavButtonsEnable();
				jQuery('#DICard #Status').val('VIEW');
			},
			'json'
		);
	}
	else
	{
		$('DICard').reset();
		DisableEnableForm($('DICard'), true);
		changeOptions('btnDatacardCancel');
		// clear Help text area
		showtip('','#ffffff');
		displayDatacardStatusMsg('msgDatacardStartNew');
		doDatacardNavButtonsEnable();
		jQuery('#DICard #Status').val('');
	}
	if (jQuery('div.Datacard #DisasterId').val() == '')
	{
		jQuery('div.Datacard select.clsEffectSector').each(function() {
			jQuery(this).val(0);
		});
	}
} //doDatacardCancel()

function doDatacardGotoFirst()
{
	displayDatacardStatusMsg('');
	bFound = requestDatacard('getDisasterIdFirst', jQuery('#DisasterId').val());
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoFirst()

function doDatacardGotoLast()
{
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data')
	{
		var RecordCount = parseInt(jQuery('#cardsRecordCount').val());
		var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordCount + ']').attr('DisasterId');
		valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId, RecordCount, RecordCount);
	}
	else
	{
		bFound = requestDatacard('getDisasterIdLast', jQuery('#DisasterId').val());
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoLast()

function doDatacardGotoPrev()
{
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data')
	{
		var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
		if (RecordNumber > 1)
		{
			RecordNumber--;
			var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
			valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId, RecordNumber, jQuery('#cardsRecordCount').val());
		}
	}
	else
	{
		bFound = requestDatacard('getDisasterIdPrev', jQuery('#cardsRecordNumber').val());
		if (bFound == false)
		{
			displayDatacardStatusMsg('msgDatacardNotFound');
		}
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoPrev()

function doDatacardGotoNext()
{
	displayDatacardStatusMsg('');
	if (jQuery('#cardsRecordSource').val() == 'data')
	{
		var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
		if (RecordNumber < parseInt(jQuery('#cardsRecordCount').val()) )
		{
			RecordNumber = RecordNumber + 1;
			var DisasterId = jQuery('.linkGridGotoCard[rowindex=' + RecordNumber + ']').attr('DisasterId');
			valid = setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId, RecordNumber, jQuery('#cardsRecordCount').val());
		}
	}
	else
	{
		bFound = requestDatacard('getDisasterIdNext', jQuery('#cardsRecordNumber').val());
		if (bFound == false)
		{
			displayDatacardStatusMsg('msgDatacardNotFound');
		}
	}
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //doDatacardGotoNext()

function doDatacardSuggestSerial()
{
	if (jQuery('#DisasterBeginTime0').val() != '')
	{
		requestDatacard('getNextSerial', jQuery('#DisasterBeginTime0').val());
	}
} //doDatacardSuggestSerial()

// SET DATACARD FORM
function setElementValue(formElement, value)
{
	switch(formElement.type)
	{
		case 'undefined': return;
		case 'radio': formElement.checked = value; break;
		case 'checkbox': formElement.checked = value; break;
		case 'select-one': 
			var unk = true;
			for (var w=0; w < formElement.length; w++)
			{
				if (formElement.options[w].value == value)
				{
					formElement.selectedIndex = w ;
					unk = false;
				}
			}
			if (unk)
				formElement[3]=new Option(value, value, false, true);
		break;
		case 'select-multiple':
			for(var x=0; x < formElement.length; x++)
				formElement[x].selected = value[x];
		break;
		default: 
			formElement.value = value; 
		break;
	}
} //setElementValue()

function setDICardFromId(prmRegionId, prmDisasterId, prmRecordNumber, prmRecordCount)
{
	jQuery('#cardsRecordNumber').val(prmRecordNumber);
	jQuery('#cardsRecordCount').val(prmRecordCount);
	jQuery.post(jQuery('#desinventarURL').val() + '/cards.php',
		{
			'cmd'       : 'getDatacard',
			'RegionId'  : prmRegionId,
			'DisasterId': prmDisasterId
		},
		function(data)
		{
			jQuery('#DICard .clsEffectNumeric').each(function() {
				jQuery(this).jecValue(data[jQuery(this).attr('id')], true);
			});
			setDICard(prmRegionId, data);
			jQuery('#divRecordNavigationInfo').hide();
			var RecordNumber = parseInt(jQuery('#cardsRecordNumber').val());
			var RecordCount  = parseInt(jQuery('#cardsRecordCount').val());
			if (RecordNumber > 0)
			{
				jQuery('#divRecordNavigationInfo').show();
				jQuery('#RecordNumber').text(RecordNumber);
				jQuery('#RecordCount').text(RecordCount);
			}
			doDatacardNavButtonsEnable();
			jQuery('#DICard #Status').val('VIEW');
			return true;
		},
		'json'
	);
	return false;
} //setDICardFromId()

function setDICard(prmRegionId, arr)
{
	var diform = null;
	var myForm = null;
	diform = $('DICard');
	myForm = jQuery('div.Datacard');
	
	var objElems = diform.elements; // DICard is DesInventar form..
	for (i=0; i < objElems.length; i++)
	{
		if ( (objElems[i].id == 'GeoLevel0') ||
			 (objElems[i].id == 'GeoLevel1') ||
			 (objElems[i].id == 'GeoLevel2') )
		{
		}
		else
		{
			if (objElems[i].id != '')
			{
				varName = jQuery(myForm).find('#' + objElems[i].id).attr('name');
				setElementValue(objElems[i], arr[varName]);
			}
		}
	}

	jQuery('#PrevDisasterSerial', myForm).val(jQuery('#DisasterSerial', myForm).val());

	//Set GeographyItem info into hidden fields
	jQuery('#divDatacard .tblGeography select:gt(1)').empty().disable();
	jQuery(arr['GeographyItems']).each(function(key, value) {
		var mySelect = jQuery('#divDatacard .tblGeography #GeoLevel' + key);
		if (parseInt(key) == 0)
		{
			mySelect.val(value.GeographyId);
		}
		else			
		{
			mySelect.append(jQuery('<option>', { value : value.GeographyId }).text(value.GeographyName));
		}
	});
	
	// Enable Edit Button according to Role
	if (jQuery('#desinventarUserRoleValue').val() >= 2)
	{
		disenabutton($('btnDatacardEdit'), false);
	}
} //setDICard

function validateInputDouble(prmValue)
{
	var answer = 1;
	var value = prmValue;
	if (isNaN(value))
	{
		answer = 0;
	}
	if (value == '')
	{
		answer = 0;
	}
	if (answer > 0)
	{
		if (occurrences(value, ',') > 0)
		{
			answer = 0;
		}
	}
	if (answer > 0)
	{
		if (occurrences(value, '.') > 1)
		{
			answer = 0;
		}
	}
	return(answer);
}

function occurrences(string, substring)
{
	var n=0;
	var pos=0;
	while(true)
	{
		pos=string.indexOf(substring,pos);
		if (pos!=-1)
		{
			n++;
			pos+=substring.length;
		}
		else
		{
			break;
		}
	}
	return(n);
}
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyData() {
	jQuery('body').on('click', '.ViewData', function() {
		jQuery(this).toggleClass('highlight');
	});

	jQuery('body').on('click', '.linkGridGotoCard', function() {
		jQuery('body').trigger('cmdDatacardShow');
		var DisasterId = jQuery(this).attr('DisasterId');
		var RecordNumber = jQuery(this).attr('rowindex');
		var RecordCount = jQuery('#prmDataPageRecords').val();
		jQuery('body').trigger('cmdDatacardGoto', [DisasterId, RecordNumber, RecordCount]);
		return false;
	});
	
	// Page Number Fields
	jQuery('body').on('keydown', '#DataCurPage', function(event) {
		if(event.keyCode == 13) {
			doDataDisplayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});
	
	// Navigation Buttons
	jQuery('body').on('click', '#btnGridGotoFirstPage', function() {
		doDataDisplayPage(1);
	});
	jQuery('body').on('click', '#btnGridGotoPrevPage', function() {
		doDataDisplayPage('prev');
	});
	jQuery('body').on('click', '#btnGridGotoNextPage', function() {
		doDataDisplayPage('next');
	});
	jQuery('body').on('click', '#btnGridGotoLastPage', function() {
		doDataDisplayPage(jQuery('#prmDataPageCount').val());
	});

	jQuery('body').on('cmdViewDataParams', function() {
		Ext.getCmp('wndViewDataParams').show();
	});
	jQuery('body').on('cmdViewDataUpdate', function() {
		doDataUpdate();
	});
	jQuery('body').trigger('cmdViewDataUpdate');

	// Initialize
	jQuery('div.ViewDataParams').on('cmdInitialize', function() {
		doDataInitialize();
	});
} //onReadyData()

function doDataInitialize()
{
	var field_list = jQuery('div.ViewDataParams select.FieldsAvailable');
	field_list.find('option').remove();
	jQuery('div.ViewParamFields div.ViewParamFieldAvailable').each(function() {
		field_list.append(jQuery('<option>', { value : 'D.' + jQuery('span.field',this).text() }).text(jQuery('span.label',this).text()));
	});
	jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
		field_list.append(jQuery('<option>', { value : 'E.' + key }).text(value[0]));
	});

	var field_list = jQuery('div.ViewDataParams select.FieldsShow');
	field_list.find('option').remove();
	jQuery('div.ViewParamFields div.ViewParamFieldShow').each(function() {
		field_list.append(jQuery('<option>', { value : 'D.' + jQuery('span.field',this).text() }).text(jQuery('span.label',this).text()));
	});
}

function doDataUpdate()
{
	jQuery('#tblDataRows tr:even').addClass('under');
	jQuery('.GridCellNumber').each(function() {
		jQuery(this).html(jQuery(this).html().replace(/\s/g,'&nbsp;'));
	});
	// Set Number of Records in Current Displayed Page
	jQuery('#prmDataPageRecords').val(jQuery('#tblDataRows tr').size());
} //doDataUpdate();

function doDataDisplayPage(page)
{
	if (parseInt(jQuery('#prmDataPageUpdate').val()) < 1)
	{
		jQuery('#prmDataPageUpdate').val(1);
		var mypag = page;
		var now = parseInt(jQuery('#DataCurPage').val());
		if (page == 'prev')
		{
			mypag = now - 1;
		}
		else if (page == 'next')
		{
			mypag = now + 1;
		}
		var NumberOfPages = jQuery('#prmDataPageCount').val();
		if ((mypag < 1) || (mypag > NumberOfPages))
		{
			// Out of Range Page, do nothing
		}
		else
		{
			jQuery('#DataCurPage').val(mypag);
			var RegionId = jQuery('#desinventarRegionId').val();
			var RecordsPerPage = jQuery('#prmDataPageSize').val();
			var QueryDef = jQuery('#prmDataQueryDef').val();
			var FieldList = jQuery('#prmDataFieldList').val();
			
			jQuery('#tblDataRows').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
			jQuery.post(jQuery('#desinventarURL').val() + '/data.php',
				{'r' : RegionId,
				 'page': mypag,
				 'RecordsPerPage' : RecordsPerPage,
				 'sql'            : QueryDef,
				 'fld'            : FieldList
				},
				function(data)
				{
					jQuery('#tblDataRows').html(data);
					// Reload the jQuery functions on the new DOM elements...
					doDataUpdate();
					jQuery('#prmDataPageNumber').val(mypag);
					// Set Number of Records in Current Displayed Page
					jQuery('#prmDataPageRecords').val(jQuery('#tblDataRows tr').size());
					jQuery('#prmDataPageUpdate').val(0);
				}
			);
		}
	}
} //doDataDisplayPage()

function doUpdateDatabaseListByUser()
{
	jQuery(".contentBlock").hide();
	jQuery("#divRegionList").show();
	// Hide everything at start...
	jQuery('.databaseTitle').hide();
	jQuery('.databaseList').hide();

	jQuery.post(jQuery('#desinventarURL').val() + '/',
		{
			cmd: 'cmdSearchDB',
			searchDBQuery: '', 
			searchDBCountry : 0
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				if (parseInt(data.NoOfDatabases) > 0)
				{
					jQuery('#divDatabaseFindList').show();
					jQuery('#divDatabaseFindError').hide();
					RegionByRole = new Array(5);
					RegionByRole['ADMINREGION'] = new Array();
					RegionByRole['SUPERVISOR'] = new Array();
					RegionByRole['USER'] = new Array();
					RegionByRole['OBSERVER'] = new Array();
					RegionByRole['NONE'] = new Array();

					$RoleList = new Array(5);
					var iCount = 0;
					jQuery('#divDatabaseFindList table.databaseList').each(function() {
						jQuery('tr:gt(0)', this).remove();
						jQuery('tr', this).hide();
					});
					jQuery.each(data.RegionList, function(RegionId, value) {
						jQuery('#divRegionList #title_' + value.Role).show();
						jQuery('#divRegionList #list_' + value.Role).show();
						var list = jQuery('#divRegionList #list_' + value.Role).show();
						var item = jQuery('tr:last', list).clone().show();
						jQuery('td.RegionId', item).text(RegionId);
						jQuery('td span.RegionLabel', item).text(value.RegionLabel);
						jQuery('td a.RegionLink', item).attr('href', jQuery('#desinventarURL').val() + '/#' + RegionId + '/');
						list.append(item);
						iCount++;
					});
					jQuery('#divDatabaseFindList td.RegionDelete').hide();
					if (jQuery('#desinventarUserRoleValue').val() >= 5)
					{
						jQuery('#divDatabaseFindList td.RegionDelete').show();
					}
				}
				else
				{
					jQuery('#divDatabaseFindList').hide();
					jQuery('#divDatabaseFindError').show();
				}
			} //if
		},
		'json' //function
	);
} //doUpdateDatabaseListByUser()

function doGetRegionInfo(RegionId)
{
	jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(jQuery(
		'#desinventarURL').val() + '/',
		{
		  cmd         : 'cmdDatabaseGetInfo', 
		  RegionId    : RegionId,
		  LangIsoCode : jQuery('#desinventarLang').val()
		},
		function(data)
		{
			if (parseInt(data.Status)>0)
			{
				var i = data.RegionInfo;
				jQuery('#divRegionInfo').show();
				jQuery('#divRegionInfo #divRegionLogo').html('<img src="' + jQuery('#desinventarURL').val() + '/?cmd=cmdDatabaseGetLogo&RegionId=' + RegionId + '" alt="" />');
				jQuery('#divRegionInfo #txtRegionLabel').text(i.RegionLabel);
				jQuery('#divRegionInfo #txtRegionPeriod').text(i.PeriodBeginDate + ' - ' + i.PeriodEndDate);
				jQuery('#divRegionInfo #txtRegionNumberOfRecords').text(i.NumberOfRecords);
				jQuery('#divRegionInfo #txtRegionLastUpdate').text(i.RegionLastUpdate);

				jQuery('#divRegionInfo #divInfoGeneral').hide();
				if (i.InfoGeneral != '')
				{
					jQuery('#divRegionInfo #divInfoGeneral #Text').html(i.InfoGeneral);
					jQuery('#divRegionInfo #divInfoGeneral').show();
				}
				jQuery('#divRegionInfo #divInfoCredits').hide();
				if (i.InfoCredits != '')
				{
					jQuery('#divRegionInfo #divInfoCredits #Text').html(i.InfoCredits);
					jQuery('#divRegionInfo #divInfoCredits').show();
				}
				jQuery('#divRegionInfo #divInfoSources').hide();
				if (i.InfoSources != '')
				{
					jQuery('#divRegionInfo #divInfoSources #Text').html(i.InfoSources);
					jQuery('#divRegionInfo #divInfoSources').show();
				}
				jQuery('#divRegionInfo #divInfoSynopsis').hide();
				if (i.InfoSynopsis != '')
				{
					jQuery('#divRegionInfo #divInfoSynopsis #Text').html(i.InfoSynopsis);
					jQuery('#divRegionInfo #divInfoSynopsis').show();
				}
			}
		},
		'json'
	);
} //doGetRegionInfo()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

	var mod = "";
	var opt = "";
	var reg = "";

	function uploadMsg(msg)
	{
		if (mod != '')
		{
			var mydiv = $(mod + 'statusmsg');
			mydiv.innerHTML = msg;
		}
	}
	
	function updateList(div, url, pars, callback)
	{
		jQuery('#' + div).load(url, pars, function(response, status, xhr) {
			// Hide first two columns (EventId,EventPredefined)
			jQuery('td:nth-child(1)','#tblEventListUser,#tblEventListPredef').hide();
			jQuery('td:nth-child(2)','#tblEventListUser,#tblEventListPredef').hide();
			// Hide first two columns (CauseId,CausePredefined)
			jQuery('td:nth-child(1)','#tblCauseListUser,#tblCauseListPredef').hide();
			jQuery('td:nth-child(2)','#tblCauseListUser,#tblCauseListPredef').hide();
		});
	}

	function updateUserBar(url, cmd, user, pass)
	{
		jQuery.post(url,
			{
				cmd      : cmd,
				userid   : user,
				password : pass
			},
			function(data)
			{
				jQuery('#pagecontent').html(data);
				if (cmd == "login")
				{
					updateList('pagecontent', url, 'cmd=welcome');
				}
				else if (cmd == "logout")
				{
					window.location.reload(false);
				}				
			}
		);
	} //function

	function setEveCau(id, name, desc, active, is_pred, module)
	{
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		uploadMsg('');
		if (module == "event")
		{
			$('EventName').value = name;
			$('aEventDesc').value = desc;
			if (active == "1")
			{
				$('aEventActive').checked = true;
			}
			else
			{
				$('aEventActive').checked = false;
			}
			if (is_pred == "1")
			{
				$('aEventDesc').disabled = true;
			}
			else
			{
				$('aEventDesc').disabled = false;
			}
			$('aEventPredefined').value = is_pred;
			$('aEventId').value = id;
		}
		else if (module == "cause")
		{
			$('aCauseName').value = name;
			$('aCauseDesc').value = desc;
			if (active == "1")
			{
				$('aCauseActive').checked = true;
			}
			else
			{
				$('aCauseActive').checked = false;
			}
			if (is_pred == "1")
			{
				$('aCauseDesc').disabled = true;
			}
			else
			{
				$('aCauseDesc').disabled = false;
			}
			$('aCausePredefined').value = is_pred;
			$('aCauseId').value = id;
		}
	}

	function setRolLog (key, val, module)
	{
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		if (module == "role")
		{
			$('UserId').value = key;
			$('AuthAuxValue').value = val;
		}
		else if (module == "log")
		{
			$('DBLogType').value = key;
			$('DBLogNotes').value = val;
		}
	}
	
	function setLevGeo(key, val, val2, val3, ly1, ly2, ly3, module)
	{
		mod = module;
		$(mod + 'addsect').style.display = 'block';
		if (module == "lev")
		{
			$('GeoLevelId').value = key;
			$('GeoLevelName').value = val;
			$('GeoLevelDesc').value = val2;
			$('GeoLevelLayerCode').value = ly2;
			$('GeoLevelLayerName').value = ly3;
		}
		else if (module == "geo")
		{
			$('aGeographyId').value = key;
			$('aGeographyCode').value = val;
			$('aGeographyName').value = val2;
		}
	}

	function setadmingeo(reg, k, l)
	{
		reg = jQuery('#desinventarRegionId').val();
		var v = k.split("|");
		mod = 'geo';
		uploadMsg('');
		if (v[0] == -1)
		{
			setLevGeo('','','',1,'','','','geo');
			if (l == 0)
			{
				$('aGeoParentId').value = '';
			}
			$('alev' + l).style.display = "none";
		}
		else if (v[0] == -2)
		{
			$('geoaddsect').style.display = 'none';
		}
		else
		{
			setLevGeo(v[0],v[1],v[2],v[3],'','','','geo');
			$('aGeoParentId').value = v[0];
			updateList('alev' + l, jQuery('#desinventarURL').val() + '/geography.php', 'r='+ reg +'&cmd=list&GeographyId=' + v[0]);
		}
	} //function
	
	function setUserPA(login, name, email, pass, cnt, city, active)
	{
		mod = "userpa";
		$(mod + 'addsect').style.display = 'block';
		$('UserId').value = login;
		$('UserFullName').value = name;
		$('UserEMail').value = email;
		$('UserPasswd').value = pass;
		$('UserCountry').value = cnt;
		$('UserCity').value = city;
		if (active == "1")
		{
			$('UserActive').checked = true;
		}
		else
		{
			$('UserActive').checked = false;
		}
	}

	var winopt = 'width=1020,height=700,left=0,top=0,screenX=0,screenY=0,resizable=no,scrollbars=no,status=no,toolbar=no';

	function setfocus(a_field_id)
	{
		$(a_field_id).focus();
	}

	function getForm(fobj)
	{
		var str = '';
		var ft = ''; 
		var fv = ''; 
		var fn = ''; 
		var els = ''; 
		for(var i = 0;i < fobj.elements.length;i++)
		{
			els = fobj.elements[i];
			ft = els.title;
			fv = els.value;
			fn = els.name;
			switch(els.type)
			{
				case "text": case "hidden": case "password": case "textarea":
					// is it a required field?
					if (encodeURI(ft) == "required" && encodeURI(fv).length < 1)
					{
						els.focus();
						return false;
					}
					str += fn + "=" + encodeURI(fv) + "&";
				break;   
				case "checkbox":
				case "radio":
					if(els.checked) str += fn + "=" + encodeURI(fv) + "&";
				break;
				case "select-one":
					str += fn + "=" +
					els.options[els.selectedIndex].value + "&";
				break;
			} // switch
		} // for
		str = str.substr(0,(str.length - 1));
		return str;  
	}

	function getGeoItems(reg, geoid, l, lev, src)
	{
		if (src == "DATA")
		{
			div = window.parent.frames['dif'].document.getElementById('lev'+ l);
			ele = window.parent.frames['dif'].document.getElementById('geolev'+ l);
		}
		else
		{
			div = $('lev'+ l);
			ele = $('geolev'+ l);
		}
		geo = geoid.substr(0, (l+1) * 5);
		for (var w=0; w < ele.length; w++)
		{
			if (ele.options[w].value == geo)
				ele.selectedIndex = w ;
		}
		if (l < lev)
		{
			var lsAjax = new Ajax.Updater( div, jQuery('#desinventarURL').val() + '/cards.php',
				{
					method: 'get', 
					parameters: 'r='+ reg +'&cmd=list&GeographyId='+ geo + '&t=' + new Date().getTime(),
					onComplete: function(request) {
						getGeoItems(reg, geoid, l+1, lev, src);
					}
				});
		}
	}

	function showinfo(mydiv)
	{
		if ($(mydiv).style.display == 'none')
		{
			$(mydiv).style.display = 'block';
		}
		else
		{
			$(mydiv).style.display = 'none';
		}
	}

	function CheckIsIE()
	{
		if (navigator.appName.toUpperCase() == 'MICROSOFT INTERNET EXPLORER')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function saveRes(cmd, typ)
	{
		if($('DCRes').value != '')
		{
			switch ($('DCRes').value)
			{
				case 'D':
					$('_D+saveopt').value = typ;
					sendList(cmd);
				break;
				case 'M':
					// SaveMap to PNG Format
					sendMap(cmd);
				break;
				case 'G':
					sendGraphic(cmd);
				break;
				case 'S':
					$('_S+saveopt').value = typ;
					sendStatistic(cmd);
				break;
			} //switch
		}
	} //function
	
	function sendList(cmd)
	{
		if (cmd == 'result')
		{
			jQuery('#prmQueryCommand').val('cmdGridShow');
		}
		else
		{
			jQuery('#prmQueryCommand').val('cmdGridSave');
		}
		if ($('_D+Field[]').length > 0)
		{
			$('_D+cmd').value = cmd;
			selectall('_D+Field[]');
			var ob = $('_D+Field[]');
			var mystr = "";
			for (i=0; i < ob.length; i++)
			{
				mystr += ob[i].value + ",";
			}
			mystr += "D.DisasterId";
			$('_D+FieldH').value = mystr;
			combineForms('frmMainQuery', 'CD');
			Ext.getCmp('westm').show();
			Ext.getCmp('westm').collapse();
			$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/data.php';
			jQuery('#frmMainQuery').attr('target','dcr');
			if (cmd != 'result')
			{
				jQuery('#frmMainQuery').attr('target', 'iframeDownload');
			}
			jQuery('#frmMainQuery').submit();
			//hideMap();
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function sendMap(cmd)
	{
		jQuery('#prmQueryCommand').val('cmdMapShow');
		if ($('_M+Type').length > 0)
		{
			$('_M+cmd').value = cmd;
			if (cmd == "export")
			{
				jQuery('#prmQueryCommand').val('cmdMapSave');

				// to export image save layers and extend..
				var dcr = document.getElementById('dcr');
				var mm = map;
				//var mm = dcr.map;
				var extent = mm.getExtent();
				var layers = mm.layers;
				var activelayers = [];
				for (i in layers)
				{
					if (layers[i].visibility && layers[i].calculateInRange() && !layers[i].isBaseLayer)
					{
						activelayers[activelayers.length] = layers[i].params['LAYERS'];
					}
				}
				
				jQuery('form.MapSave').attr('action', jQuery('#desinventarURL').val() + '/thematicmap.php');
				jQuery('form.MapSave').attr('target', 'iframeDownload');
				jQuery('form.MapSave input.Extent').val([extent.left,extent.bottom,extent.right,extent.top].join(','));
				jQuery('form.MapSave input.Layers').val(activelayers);
				jQuery('form.MapSave input.Id').val(jQuery('#prmMapId').val());
				jQuery('form.MapSave input.Title').val(jQuery('#MapTitle').val());
				jQuery('form.MapSave').trigger('submit');
			}
			else
			{
				combineForms('frmMainQuery', 'CM');
				Ext.getCmp('westm').show();
				Ext.getCmp('westm').collapse();
				$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/thematicmap.php';
				jQuery('#frmMainQuery').attr('target','dcr');
				if (cmd != 'result')
				{
					jQuery('#frmMainQuery').attr('target', 'iframeDownload');
				}
				jQuery('#frmMainQuery').submit();
				//hideMap();
			}
			return true;
		}
		else
		{
			return false;
		}
	} //function
	
	function sendGraphic(cmd)
	{
		if (cmd == 'result')
		{
			jQuery('#prmQueryCommand').val('cmdGraphShow');
		}
		else
		{
			jQuery('#prmQueryCommand').val('cmdGraphSave');
		}
		jQuery('#prmGraphCommand').val(cmd);
		jQuery('#frmGraphParams input.TendencyLabel0').val(jQuery('#frmGraphParams #prmGraphTendency0 option:selected').text());
		jQuery('#frmGraphParams #prmGraphFieldLabel0').val(jQuery('#frmGraphParams #prmGraphField0 option:selected').text());
		jQuery('#frmGraphParams #prmGraphFieldLabel1').val(jQuery('#frmGraphParams #prmGraphField1 option:selected').text());
		
		combineForms('frmMainQuery', 'frmGraphParams');
		Ext.getCmp('westm').show();
		Ext.getCmp('westm').collapse();
		$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/graphic.php';
		jQuery('#frmMainQuery').attr('target','dcr');

		if (cmd != 'result')
		{
			jQuery('#frmMainQuery').attr('target', 'iframeDownload');
		}
		jQuery('#frmMainQuery').submit();
		//hideMap();
	}
	
	function sendStatistic(cmd)
	{
		if (cmd == 'result')
		{
			jQuery('#prmQueryCommand').val('cmdStatShow');
		}
		else
		{
			jQuery('#prmQueryCommand').val('cmdStatSave');
		}
		if ( jQuery('#fldStatParam_FirstLev').val() != "" && $('fldStatFieldSelect').length > 0)
		{
			$('_S+cmd').value = cmd;
			//selectall('fldStatFieldSelect');
			var field      = 'D.DisasterId||';
			var fieldlabel = jQuery('#txtStatRecords').text();
			jQuery('#fldStatFieldSelect option').each(function() {
				field      += ',' + jQuery(this).val();
				fieldlabel += ',' + jQuery(this).text();
			});
			jQuery('#fldStatField').val(field);
			jQuery('#fldStatFieldLabel').val(fieldlabel);
			
			jQuery('#frmStatParams td.StatGroup').each(function() {
				jQuery('input', this).val(jQuery('select option:selected',this).text());
			});
			combineForms('frmMainQuery', 'frmStatParams');
			Ext.getCmp('westm').show();
			Ext.getCmp('westm').collapse();
			$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/statistic.php';
			jQuery('#frmMainQuery').attr('target','dcr');
			if (cmd != 'result')
			{
				jQuery('#frmMainQuery').attr('target', 'iframeDownload');
			}
			jQuery('#frmMainQuery').submit();
			//hideMap();
			return true;
		}
		else
		{
			return false;
		}
	} //function
	
	function saveQuery()
	{
		jQuery('#prmQueryCommand').val('cmdQuerySave');
		selectall('_D+Field[]');
		combineForms('frmMainQuery', 'CD');
		combineForms('frmMainQuery', 'CM');
		combineForms('frmMainQuery', 'frmGraphParams');
		selectall('fldStatFieldSelect');
		combineForms('frmMainQuery', 'frmStatParams');
		jQuery('#_CMD').val('savequery');
		$('frmMainQuery').action=jQuery('#desinventarURL').val() + '/?r=' + jQuery('#desinventarRegionId').val();
		jQuery('#frmMainQuery').attr('target', 'iframeDownload');
		jQuery('#frmMainQuery').submit();
		return true;
	}

	function disab(field)
	{
		if (field != null)
		{
			field.disabled = true;
			field.className = "disabled";
		}
	}
	
	function enab(field)
	{
		if (field != null)
		{
			field.disabled = false;
			field.className = "";
		}
	}
	
	// Effects options
	function showeff(val, x, y)
	{
		if (val == ">=" || val == "<=" || val == "=" || val == "-3")
		{
			$(x).style.display = 'inline';
			if (val == "-3")
			{
				$(y).style.display = 'inline';
			}
			else
			{
				$(y).style.display = 'none';
			}
		}
		if (val == "" || val == "0" || val == "-1" || val == "-2")
		{
			$(x).style.display = 'none';
			$(y).style.display = 'none';
		}
	}
	
	function enadisEff(id, chk)
	{
		if (chk)
		{
			$('o'+ id).style.display = 'inline';
			enab($(id +'[0]'));
			enab($(id +'[1]'));
			enab($(id +'[2]'));
		}
		else
		{
			$('o'+ id).style.display = 'none';
			disab($(id +'[0]'));
			disab($(id +'[1]'));
			disab($(id +'[2]'));
		}
	}
	
	// forms management
	function combineForms(dcf, ref)
	{
		var dc = $(dcf);
		var rf = $(ref).elements;
		var ih = null;
		for (i=0; i < rf.length; i++)
		{
			if (rf[i].disabled == false)
			{
				ih = document.createElement("input");
				ih.type   = "hidden";
				ih.value  = rf[i].value;
				ih.name   = rf[i].name;
				dc.appendChild(ih);
			}
		}
	}
	
	function setAdvQuery(value, ope)
	{
		$('QueryCustom').value += value + ' ';
		switch (ope)
		{
			case 'text':
				disab($('<'));
				disab($('>'));
				enab($('='));  $('=').value = "=''";
				enab($('<>')); $('<>').value = "<>''";
				enab($("LIKE '%%'"));
				disab($('=-1')); disab($('=0')); disab($('=-2'));
			break;
			case 'date':
				enab($('<')); $('<').value = "<''";
				enab($('>')); $('>').value = ">''";
				enab($('=')); $('=').value = "=''";
				enab($('<>')); $('<>').value = "<>''";
				enab($("LIKE '%%'"));
				disab($('=-1')); disab($('=0')); disab($('=-2'));
			break;
			case 'number':
				enab($('<')); $('<').value = "<";
				enab($('>')); $('>').value = ">";
				enab($('=')); $('=').value = "=";
				enab($('<>'));$('<>').value = "<>";
				disab($("LIKE '%%'"));
				enab($('=-1')); enab($('=0')); enab($('=-2'));
			break;
			case 'boolean':
				disab($('<'));
				disab($('>'));
				disab($('='));
				disab($('<>'));
				disab($("LIKE '%%'"));
				enab($('=-1')); enab($('=0')); enab($('=-2'));
			break;
		} //switch
	} //function

	function printRes()
	{
		window.print();
	}
	
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyExtraEffects() {
	jQuery('.msgEEFieldStatus').hide();

	jQuery('#btnEEFieldAdd').unbind('click').click(function() {
		setExtraEff('','','','','','','');
		jQuery('#EEFieldCmd').val('cmdEEFieldInsert');
	});	

	jQuery('#btnEEFieldReset').unbind('click').click(function() {
		jQuery('#extraeffaddsect').hide();
		uploadMsg('');
	});
	
	jQuery('#frmEEFieldEdit').unbind('submit').submit(function() {
		var params = jQuery(this).serialize();
		var bContinue = true;
		if (bContinue) {
			if (jQuery('#EEFieldLabel').val() == '') {
				jQuery('#EEFieldLabel').highlight().focus();
				bContinue = false;
			}
		}
		if (bContinue) {
			if (jQuery('#EEFieldDesc').val() == '') {
				jQuery('#EEFieldDesc').highlight().focus();
				bContinue = false;
			}
		}
		if (bContinue) {
			if (jQuery('#EEFieldType').val() == '') {
				jQuery('#EEFieldType').highlight().focus();
				bContinue = false;
			}
		}
		if (bContinue) {
			jQuery.post(jQuery('#desinventarURL').val() + '/extraeffects.php',
				params,
				function(data) {
					jQuery('.msgEEFieldStatus').hide();
					if (data.Status == 'OK') {
						jQuery('#msgEEFieldStatusOk').show();
						updateList('lst_eef', jQuery('#desinventarURL').val() + '/extraeffects.php', 'cmd=cmdEEFieldList+&RegionId='+ jQuery('#desinventarRegionId').val());
						jQuery('#extraeffaddsect').hide();
					} else {
						jQuery('#msgEEFieldStatusError').show();
					}
				},
				'json'
			);
		}
		return false;
	});
} //function

function setExtraEff (id, label, def, type, size, active, public) {
	// clear highlighted fields...
	jQuery.each(jQuery('.clsValidateField'), function() {
		jQuery(this).unhighlight();
	});

	//jQuery('#EEFieldDesc').unhighlight();
	// hide status msg
	jQuery('.msgEEFieldStatus').hide();

	// Show form
	jQuery('#extraeffaddsect').hide();
	
	mod = "extraeff";
	$(mod + 'addsect').style.display = 'block';
	$('EEFieldId').value = id;
	$('EEFieldLabel').value = label;
	$('EEFieldDesc').value = def;
	$('EEFieldType').value = type;
	$('EEFieldSize').value = size;
	if (active == "1")
		$('EEFieldActive').checked = true;
	else
		$('EEFieldActive').checked = false;
	if (public == "1")
		$('EEFieldPublic').checked = true;
	else
		$('EEFieldPublic').checked = false;
} //function
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyGraphic() {
	// 2010-02-21 (jhcaiced) This jQuery calls ensures that the Period and Stat
	// parameters are not empty at the same time.
	jQuery('#prmGraphPeriod').change(function() {
		var Value = jQuery(this).val();
		if (Value != '') {
			jQuery('#prmGraphStat').val('');
		} else {
			jQuery('#prmGraphStat').val('MONTH');
		}
	});

	jQuery('#prmGraphStat').change(function() {
		var Value = jQuery(this).val();
		if (Value != '') {
			jQuery('#prmGraphPeriod').val('');
		} else {
			jQuery('#prmGraphPeriod').val('YEAR');
		}
	});

	jQuery('#prmGraphField1').change(function() {
		if (jQuery(this).val() != '')
		{
			enab($('prmGraphScale1'));
			enab($('prmGraphData1'));
			enab($('prmGraphMode1'));
		}
		else
		{
			disab($('prmGraphScale1'));
			disab($('prmGraphData1'));
			disab($('prmGraphMode1'));
		}
	});
	
	jQuery('#prmGraphTypeHistogram').change(function() {
		jQuery('#prmGraphType').val('HISTOGRAM');
		var grp = parseInt(jQuery(this).val());
		// Histogram Type
		disab($('_G+K_pie'));
		jQuery('#prmGraphKind').val('BAR');
		enab($('prmGraphPeriod'));
		$('prmGraphPeriod').value = 'YEAR';
		enab($('prmGraphStat'));
		jQuery('#prmGraphScale0').enable();
		if (grp > 0)
		{
			disabAxis2();
			jQuery('#prmGraphMode0').val('NORMAL');
			disab($('prmGraphModeCummulative0'));
			enab($('prmGraphModeStacked0'));
			enab($('prmGraphModeStacked1'));
		}
		else
		{
			enabAxis2();
			jQuery('#prmGraphMode0').val('NORMAL');
			enab($('prmGraphModeCummulative0'));
			disab($('prmGraphModeStacked0'));
			disab($('prmGraphModeStacked1'));
		}
		disab($('_G+D_perc'));
		disab($('_G+D_perc2'));
		if (jQuery('#prmGraphTypeComparative').val() != '') {
			jQuery('#prmGraphTypeComparative').val('');
		}
		jQuery('#prmGraphSubType').val(grp);
	});
	
	jQuery('#prmGraphTypeComparative').change(function() {
		jQuery('#prmGraphType').val('COMPARATIVE');
		var grp = parseInt(jQuery(this).val());
		// Comparatives
		disabAxis2();
		enab($('_G+K_pie'));
		jQuery('#prmGraphKind').val('PIE');
		$('prmGraphPeriod').value = "";
		disab($('prmGraphPeriod'));
		$('prmGraphStat').value = "";
		disab($('prmGraphStat'));
		jQuery('#prmGraphScale0').disable();
		jQuery('#prmGraphMode0').val('NORMAL');
		disab($('prmGraphModeCummulative0'));
		disab($('prmGraphModeStacked0'));
		disab($('prmGraphModeStacked1'));
		enab($('_G+D_perc'));
		enab($('_G+D_perc2'));
		if (jQuery('#prmGraphTypeHistogram').val() != '') {
			jQuery('#prmGraphTypeHistogram').val('');
		}
		jQuery('#prmGraphSubType').val(grp);
	});

	jQuery('#prmGraphKind').change(function() {
		comp = jQuery('#prmGraphTypeComparative').val();
		if (comp != '')
		{
			comp = parseInt(comp);
		}
		else
		{
			comp = 0;
		}
		var kind = jQuery(this).val();
		if (comp > 0)
		{
			if (kind != 'PIE')
			{
				disab($('_G+D_perc'));
				disab($('_G+D_perc2'));
				if (jQuery('#prmGraphData0').val() == 'PERCENT')
				{	
					jQuery('#prmGraphData0').val('NONE');
				}
			}
			else
			{
				enab($('_G+D_perc'));
				enab($('_G+D_perc2'));
			}
		}
		if ( (kind == 'BAR' || kind == 'LINE' || kind == 'PIE') && (comp < 200) )
		{
			 enabAxis2();
			 enab($('prmGraphModeCummulative0'));
			 disab($('prmGraphModeStacked0'));
			 disab($('prmGraphModeStacked1'));
			 jQuery('#prmGraphScale0').enable();
		}
		else
		{
			disabAxis2();
			disab($('prmGraphModeCummulative0'));
			jQuery('#prmGraphScale0').disable();
		}
	});
	
	jQuery('[help_tip]').mouseover(function() {
		showtip(jQuery(this).attr('help_tip'));
	});

	// Initialize Controls on Load
	jQuery('#prmGraphTypeComparative').val('').trigger('change');
	jQuery('#prmGraphTypeHistogram').val('D.DisasterBeginTime').trigger('change');

	jQuery('body').on('cmdViewGraphParams', function() {
		Ext.getCmp('wndViewGraphParams').show();
	});

	jQuery('div.ViewGraphParams').on('cmdInitialize', function(event) {
		doViewGraphParamsInitialize();
	});
} // onReadyGraphic()

function doViewGraphParamsInitialize()
{
	// GraphTypeHistogram - By Geolevels
	var geolevel_list = jQuery('body').data('GeolevelsList');
	var select = jQuery('div.ViewGraphParams select.TypeHistogram');
	jQuery(select).find('option.Geolevel').remove();
	jQuery.each(geolevel_list, function(key, value) {
		var clone = jQuery('<option>', {value: 100 + parseInt(value.GeoLevelId)}).text(value.GeoLevelName + jQuery('div.ViewGraphParams span.HistogramByGeography').text());
		jQuery(clone).addClass('Geolevel');
		jQuery(select).append(clone);
	});

	// GraphTypeComparative - By Geolevels
	var geolevel_list = jQuery('body').data('GeolevelsList');
	var select = jQuery('div.ViewGraphParams select.TypeComparative');
	jQuery(select).find('option.Geolevel').remove();
	jQuery.each(geolevel_list, function(key, value) {
		var clone = jQuery('<option>', {value: 200 + parseInt(value.GeoLevelId)}).text(jQuery('div.ViewGraphParams span.ComparativeByGeography').text() + ' ' + value.GeoLevelName);
		jQuery(clone).addClass('Geolevel');
		jQuery(select).append(clone);
	});
} //doViewGraphParamsInitialize()

function disabAxis2()
{
	jQuery('#divVerticalAxis2').hide();
	jQuery('#prmGraphField1').val('');
} //disabAxis2()

function enabAxis2()
{
	jQuery('#divVerticalAxis2').show();
	//jQuery('#prmGraphField1').val('');
}


/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyImport() {
	jQuery("div.divImport").hide();
	jQuery("div#divImportSelectFile").show();
}

function sendForm() {
	var fr = document.getElementById('iframe2');
	var im = document.getElementById('divDatacardsImport');
	fr.src=jQuery('#desinventarURL').val() + '/images/loading.gif';
	im.submit();
}

function enadisField(lnow, lnext, val) {
	var sour = document.getElementById(lnow);
	if (val)
		sour.disabled = false;
	else {
		sour.disabled = true;
		fillColumn(lnow, lnext, false);
		for (var i = sour.length - 1; i>=0; i--) {
			sour.remove(i);
		}
	}
}

function fillColumn(lnow, lnext, exclude) {
	var sour = document.getElementById(lnow);
	var dest = document.getElementById(lnext);
	// clean dest list
	for (var i = dest.length - 1; i>=0; i--) {
		dest.remove(i);
	}
	for (var i=0; i < sour.length; i++) {
		if (exclude)
			test = !sour[i].selected;
		else
			test = true;
		if (test) {
			var opt = document.createElement('option');
			opt.value = sour[i].value;
			opt.text = sour[i].text;
			var pto = dest.options[i];
			try {
				dest.add(opt, pto);  }
			catch(ex) {
				dest.add(opt, i);    }
		}
	}
}
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
 - General ExtJS functions
*/
function onReadyExtJS()
{
	Ext.BLANK_IMAGE_URL = '/extJS/resources/images/default/s.gif';
	Ext.ns('DesInventar');

	// Hide Loading div...
	jQuery('#loading').hide();
	jQuery('#loading-mask').hide();	

	// 2011-04-29 (jhcaiced) Fix for use of ExtJS in IE9 ?
	if ((typeof Range !== "undefined") && !Range.prototype.createContextualFragment)
	{
		Range.prototype.createContextualFragment = function(html)
		{
			var frag = document.createDocumentFragment(), div = document.createElement("div");
			frag.appendChild(div);
			div.outerHTML = html;
			return frag;
		};
	}

	jQuery('body').on('cmdViewportShow', function(event) {
		doViewportShow();
	});

	doDialogsCreate();
	doMainMenuCreate();
	doViewportCreate();
} //onReadyExtJS()

function doViewportCreate()
{
	DesInventar.WestPanel = Ext.extend(Ext.Panel, {
		initComponent: function() {
			var config = {
				region: 'west',
				border: false,
				split: false,
				layout: 'fit',
				width: 350,
				title: jQuery('#msgQueryDesignTitle').text(),
				autoScroll: true,
				margins:'0 2 0 0',
				contentEl: 'divWestPanel',
				floatable: true
			};
			Ext.apply(this, config);
			Ext.apply(this.initialConfig, config);
			this.defwidth = this.width;
			this.deftitle = this.title;
			DesInventar.WestPanel.superclass.initComponent.call(this);
		}
	});
	DesInventar.Viewport = Ext.extend(Ext.Viewport, {
		initComponent: function() {
			var config = {
				contentEl: 'divViewport',
				layout:'border',
				border: false,
				items:[
					{
						region:'north',
						height: 30,
						border: false,
						contentEl: 'north',
						collapsible: false,
						tbar: new DesInventar.Toolbar({id:'toolbar', MenuHandler: doMainMenuHandler})
					},
					new DesInventar.WestPanel({id:'westm', collapsible: true}),
					{
						region: 'south',
						id: 'southm',
						split: false,
						title: jQuery('#msgHelpTitle').text(),
						height: 80,
						minSize: 100,
						maxSize: 200,
						margins: '0 0 0 0',
						contentEl: 'south',
						collapsible: true
					},
					new Ext.Panel({
						region: 'center',
						id: 'centerm',
						contentEl: 'container',
						autoScroll: true
					})
				]
			};
			Ext.apply(this, config);
			Ext.apply(this.initialConfig, config);
			DesInventar.Viewport.superclass.initComponent.call(this);
		}
	});
	var viewport = new DesInventar.Viewport({id:'viewport'});
	viewport.show();
	jQuery('#westm .x-panel-header-text').attr('title', jQuery('#msgQueryDesignTooltip').text());
	Ext.getCmp('westm').on('expand', function() {
		jQuery('#divRegionInfo').hide();
	});
} // doViewportCreate()

function doViewportShow()
{
	jQuery('body').trigger('cmdMainMenuUpdate');
	jQuery('body').trigger('cmdMainWaitingHide');
	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());
	var RegionId = jQuery('#desinventarRegionId').val();

	var title = jQuery('div.desinventarInfo span.title').text();

	jQuery('.contentBlock').hide();
	if (RegionId != '')
	{
		title = title + ' | ' + jQuery('#desinventarRegionLabel').val();
		if (UserRoleValue > 0)
		{
			Ext.getCmp('westm').show();
			Ext.getCmp('viewport').doLayout();
			Ext.getCmp('westm').expand();
			jQuery('#divQueryResults').show();
			jQuery('body').trigger('cmdQueryResultsButtonHide');
			jQuery('#dcr').hide();
		}
		else
		{
			Ext.getCmp('westm').hide();
			Ext.getCmp('viewport').doLayout();
			jQuery('#divDatabasePrivate').show();
		}
	}
	else
	{
		title = title + ' | ' + jQuery('div.desinventarInfo span.region_list').text();
		Ext.getCmp('westm').hide();
		Ext.getCmp('viewport').doLayout();
		jQuery('#divRegionList').show();
		doUpdateDatabaseListByUser();
	}
	jQuery(document).attr('title',title);
} //doViewportShow()

function doMainChangeLanguage(LangIsoCode)
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdUserLanguageChange',
			LangIsoCode : LangIsoCode
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('body').trigger('cmdWindowReload');
			}
			else
			{
				console.error('cmdUserLanguageChange error : ' + data.Status + ' ' + item.langisocode);
			}
		},
		'json'
	);
} //doMainChangeLanguage()

function doMainMenuHandler(item)
{
	var menuCmd = '';
	if (item.itemid != undefined)
	{
		menuCmd = item.itemid;
	}
	if (item.id != undefined)
	{
		menuCmd = item.id;
	}
	var RegionId = jQuery('#desinventarRegionId').val();
	switch (menuCmd)
	{
		case 'mnuUserLogin':
		case 'mnuUserChangeLogin':
			jQuery('body').trigger('cmdUserLoginShow');
		break;
		case 'mnuFileLogout':
			jQuery('body').trigger('cmdUserLogout');
		break;
		case 'mnuUserChangePasswd':
			jQuery('body').trigger('cmdUserAccountShow');
		break;
		case 'mnuFileLanguageEnglish':
			doMainChangeLanguage('eng');
		break;
		case 'mnuFileLanguageSpanish':
			doMainChangeLanguage('spa');
		break;
		case 'mnuFileLanguagePortuguese':
			doMainChangeLanguage('por');
		break;
		case 'mnuFileLanguageFrench':
			doMainChangeLanguage('fre');
		break;
		// DesConsultar Menu Options
		case 'mnuQueryViewDesign':
			if (jQuery('#desinventarRegionId').val() != '')
			{
				jQuery('.contentBlock').hide();
				jQuery('#divQueryResults').show();
				Ext.getCmp('westm').expand();
			}
		break;
		case 'mnuQueryViewData':
			jQuery('body').trigger('cmdViewDataParams');
		break;
		case 'mnuQueryViewMap':
			jQuery('body').trigger('cmdViewMapParams');
		break;
		case 'mnuQueryViewGraph':
			jQuery('body').trigger('cmdViewGraphParams');
		break;
		case 'mnuQueryViewStd':
			jQuery('body').trigger('cmdViewStdParams');
		break;
		case 'mnuQueryOptionNew':
			// Just reload the current region window...(need a better solution!!)
			window.location = jQuery('#desinventarURL').val() + '/' + RegionId;
		break;
		case 'mnuQueryOptionSave':
			saveQuery();
		break;
		case 'mnuQueryOptionOpen':
			Ext.getCmp('wndQueryOpen').show();
		break;
		case 'mnuQueryResultSave':
			jQuery('#btnResultSave').trigger('click');
		break;
		case 'mnuQueryResultSaveAsXLS':
			jQuery('#btnResultSaveXLS').trigger('click');
		break;
		case 'mnuQueryResultSaveAsCSV':
			jQuery('#btnResultSaveCSV').trigger('click');
		break;
		case 'mnuQueryResultPrint':
			jQuery('#btnResultPrint').trigger('click');
		break;
		case 'mnuFileInfo':
			jQuery('.contentBlock').hide();
			jQuery('#divQueryResults').show();
			jQuery('#dcr').hide();
			jQuery('#divRegionInfo').show();
			doGetRegionInfo(jQuery('#desinventarRegionId').val());
			Ext.getCmp('westm').collapse();
		break;
		case 'mnuFileOpen':
			window.location.hash = '';
			/*
			// Show database list
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			doUpdateDatabaseListByUser();
			*/
		break;
		// Datacards Menu Items
		case 'mnuDatacardEdit':
			jQuery('#cardsRecordNumber').val(0);
			jQuery('#cardsRecordSource').val('');
			jQuery('body').trigger('cmdDatacardShow');
		break;
		case 'mnuDatacardImport':
			hideQueryDesign();
			jQuery('.contentBlock').hide();
			jQuery('#divDatacardsImport').show();
			updateList('divDatacardsImport', jQuery('#desinventarURL').val() + '/import.php', 'r=' + RegionId);
		break;
		case 'mnuFileDownload':
			jQuery('.clsAdminDatabaseExport').hide();
			Ext.getCmp('wndDatabaseExport').show();
			jQuery('body').trigger('cmdDatabaseExport');
		break;
		case 'mnuFileUploadCopy':
			doDatabaseUploadShow('Copy');
		break;
		case 'mnuFileUploadReplace':
			doDatabaseUploadShow('Replace');
		break;
		case 'mnuDatacardSetup':
			hideQueryDesign();
			doMainMenuToggle(false);
			Ext.getCmp('mnuDatacard').enable();
			Ext.getCmp('mnuDatacardEdit').hide();
			Ext.getCmp('mnuDatacardSetup').hide();
			Ext.getCmp('mnuDatacardSetupEnd').show();
			Ext.getCmp('mnuDatacardSetupEnd').enable();
			jQuery('.contentBlock').hide();
			jQuery('.classDBConfig_tabs:first').click();
			jQuery('#divDatabaseConfiguration').show();
			jQuery('#tabDatabaseConfiguration').show();
		break;
		case 'mnuDatacardSetupEnd':
			doMainMenuToggle(true);
			jQuery('body').trigger('cmdMainMenuUpdate');
			jQuery('body').trigger('cmdDatabaseLoadData');
		break;
		case 'mnuFileCreate':
			doDatabaseCreateShow();
		break;
		case 'mnuUserAccountManagement':
			jQuery('div.AdminUsers').trigger('cmdLoadData');
			Ext.getCmp('wndAdminUsers').show();
		break;
		case 'mnuAdminDatabases':
			jQuery('.contentBlock').hide();
			jQuery('#divAdminDatabase').show();
			doAdminDatabaseUpdateList();
		break;
		case 'mnuHelpAbout':
			Ext.getCmp('wndDialog').show();
		break;
		case 'mnuHelpWebsite':
			window.open('http://www.desinventar.org', '', '');
		break;
		case 'mnuHelpMethodology':
			var url = 'http://www.desinventar.org';
			if (jQuery('#desinventarLang').val() == 'spa')
			{
				url = url + '/es/metodologia';
			}
			else
			{
				url = url + '/en/methodology';
			}
			window.open(url, '', '');
		break;
		case 'mnuHelpDocumentation':
			var url = 'http://www.desinventar.org/';
			window.open(url, '', '');
		break;
	} //switch
} //doMainMenuHandler()

function hideQueryDesign()
{
	// Hide Query Design Panel
	Ext.getCmp('westm').collapse();
} //hideQueryDesign()

function doMainMenuCreate()
{
	DesInventar.Toolbar = Ext.extend(Ext.Toolbar, {
		initComponent: function() {
			var config = {
				overflow: 'visible',
				items: []
			};
			Ext.apply(this, config);
			Ext.apply(this.initialConfig, config);
			DesInventar.Toolbar.superclass.initComponent.call(this);
			this.initializeToolbar();
		},
		initializeToolbar: function()
		{
			var mnuFileUpload = new Ext.menu.Menu({
				items: [
					{id:'mnuFileUploadCopy'   , text: jQuery('span#msgFileUploadCopy').text()   , handler: this.MenuHandler },
					{id:'mnuFileUploadReplace', text: jQuery('span#msgFileUploadReplace').text(), handler: this.MenuHandler }
				]
			});
			var mnuFileLanguage = new Ext.menu.Menu({
				items: [
					{id:'mnuFileLanguageEnglish'   , text: jQuery('span#msgFileLanguageEnglish').text()   , handler: this.MenuHandler },
					{id:'mnuFileLanguageSpanish'   , text: jQuery('span#msgFileLanguageSpanish').text()   , handler: this.MenuHandler },
					{id:'mnuFileLanguagePortuguese', text: jQuery('span#msgFileLanguagePortuguese').text(), handler: this.MenuHandler },
					{id:'mnuFileLanguageFrench'    , text: jQuery('span#msgFileLanguageFrench').text()    , handler: this.MenuHandler }
				]
			});
			
			var mnuFile = new Ext.menu.Menu({
				items: [
					{id:'mnuFileCreate'     , text: jQuery('span#msgFileCreate').text()     , handler: this.MenuHandler },
					{id:'mnuFileOpen'       , text: jQuery('span#msgFileOpen').text()       , handler: this.MenuHandler },
					{id:'mnuFileDownload'   , text: jQuery('span#msgFileDownload').text()   , handler: this.MenuHandler },
					{id:'mnuFileUpload'     , text: jQuery('span#msgFileUpload').text()     , menu: mnuFileUpload       },
					'-',
					{id:'mnuFileInfo'       , text: jQuery('span#msgFileInfo').text()       , handler: this.MenuHandler },
					{id:'mnuFileLanguage'   , text: jQuery('span#msgFileLanguage').text()   , menu: mnuFileLanguage     },
					{id:'mnuFileLogout'     , text: jQuery('span#msgFileLogout').text()     , handler: this.MenuHandler }
				]
			});
			
			var mnuUser = new Ext.menu.Menu({
				items: [
					{id: 'mnuUserLogin'            , text: jQuery('span#msgUserLogin').text()            , handler: this.MenuHandler }, 
					{id: 'mnuUserChangeLogin'      , text: jQuery('span#msgUserChangeLogin').text()      , handler: this.MenuHandler },
					{id: 'mnuUserChangePasswd'     , text: jQuery('span#msgUserChangePasswd').text()     , handler: this.MenuHandler },
					{id: 'mnuUserAccountManagement', text: jQuery('span#msgUserAccountManagement').text(), handler: this.MenuHandler }
				]
			});

			var mnuQueryOption = new Ext.menu.Menu({
				items: [
					{id:'mnuQueryOptionNew'  , text: jQuery('span#msgQueryOptionNew').text() , handler: this.MenuHandler },
					{id:'mnuQueryOptionSave' , text: jQuery('span#msgQueryOptionSave').text(), handler: this.MenuHandler },
					{id:'mnuQueryOptionOpen' , text: jQuery('span#msgQueryOptionOpen').text(), handler: this.MenuHandler }
				]
			});

			var mnuQueryResultSaveAs = new Ext.menu.Menu({
				items: [
					{id:'mnuQueryResultSaveAsXLS', text: jQuery('span#msgQueryResultSaveAsXLS').text(), handler: this.MenuHandler },
					{id:'mnuQueryResultSaveAsCSV', text: jQuery('span#msgQueryResultSaveAsCSV').text(), handler: this.MenuHandler }
				]
			});

			var mnuQuery = new Ext.menu.Menu({
				items: [
					{id:'mnuQueryViewDesign'           , text: jQuery('span#msgQueryViewDesign').text()           , handler: this.MenuHandler },
					{id:'mnuQueryViewData'             , text: jQuery('span#msgQueryViewData').text()             , handler: this.MenuHandler },
					{id:'mnuQueryViewMap'              , text: jQuery('span#msgQueryViewMap').text()              , handler: this.MenuHandler },
					{id:'mnuQueryViewGraph'            , text: jQuery('span#msgQueryViewGraph').text()            , handler: this.MenuHandler },
					{id:'mnuQueryViewStd'              , text: jQuery('span#msgQueryViewStd').text()              , handler: this.MenuHandler },
					'-',
					{id:'mnuQueryResultSave'           , text: jQuery('span#msgQueryResultSave').text()           , handler: this.MenuHandler },
					{id:'mnuQueryResultSaveAs'         , text: jQuery('span#msgQueryResultSaveAs').text()         , menu: mnuQueryResultSaveAs },
					{id:'mnuQueryResultPrint'          , text: jQuery('span#msgQueryResultPrint').text()          , handler: this.MenuHandler },
					{id:'mnuQueryOption'               , text: jQuery('span#msgQueryOption').text()               , menu: mnuQueryOption       }
				]
			});
			var mnuDatacard = new Ext.menu.Menu({
				items: [
					{id:'mnuDatacardEdit'    , text: jQuery('span#msgDatacardEdit').text()    , handler: this.MenuHandler },
					{id:'mnuDatacardSetup'   , text: jQuery('span#msgDatacardSetup').text()   , handler: this.MenuHandler },
					{id:'mnuDatacardSetupEnd', text: jQuery('span#msgDatacardSetupEnd').text(), handler: this.MenuHandler }
				]
			});
			var mnuHelp = new Ext.menu.Menu({
				style: { overflow: 'visible' },
				items: [
					{id:'mnuHelpDocumentation' , text: jQuery('span#msgHelpDocumentation').text() , handler: this.MenuHandler },
					{id:'mnuHelpMethodology'   , text: jQuery('span#msgHelpMethodology').text()   , handler: this.MenuHandler },
					'-',
					{id:'mnuHelpWebsite'       , text: jQuery('span#msgHelpWebsite').text()       , handler: this.MenuHandler },
					'-',
					{id:'mnuHelpAbout'         , text: jQuery('span#msgHelpAbout').text()         , handler: this.MenuHandler }
				]
			});
			this.add({id:'mnuFile'     , text: jQuery('span#msgFile').text()    , menu: mnuFile     });
			this.add({id:'mnuUser'     , text: jQuery('span#msgUser').text()    , menu: mnuUser     });
			this.add({id:'mnuQuery'    , text: jQuery('span#msgQuery').text()   , menu: mnuQuery    });
			this.add({id:'mnuDatacard' , text: jQuery('span#msgDatacard').text(), menu: mnuDatacard });
			this.add({id:'mnuHelp'     , text: jQuery('span#msgHelp').text()    , menu: mnuHelp     });
			
			// This elements appear on reverse order on screen (?)
			this.add('->',{id: 'mnuHelpWebsiteLabel', text: '<img src="' + jQuery('#desinventarURL').val() + '/images/di_logo4.png" alt="" />' });
			this.add('->',{id: 'mnuRegionLabel'     , text: '' });
			//this.add('->',{id: 'mnuWaiting'         , text: '<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />', hidden: true });
			this.add('->',{id: 'mnuWaiting'         , text: '' });
		}
	});

	// Attach main events to body
	jQuery('body').on('cmdMainWaitingShow', function() {
		Ext.getCmp('mnuWaiting').show();
	});
	jQuery('body').on('cmdMainWaitingHide', function() {
		Ext.getCmp('mnuWaiting').hide();
	});

	jQuery('body').on('cmdMainMenuUpdate', function() {
		doMainMenuUpdate();
	});
	jQuery('body').on('cmdMainMenuResultButtonsEnable', function() {
		if (jQuery('#DCRes').val() == 'D' || jQuery('#DCRes').val() == 'S')
		{
			Ext.getCmp('mnuQueryResultSave').hide();
			Ext.getCmp('mnuQueryResultSaveAs').show();
			Ext.getCmp('mnuQueryResultSaveAs').enable();
			Ext.getCmp('mnuQueryResultSaveAsXLS').show();
			Ext.getCmp('mnuQueryResultSaveAsXLS').enable();
			Ext.getCmp('mnuQueryResultSaveAsCSV').show();
			Ext.getCmp('mnuQueryResultSaveAsCSV').enable();
		}
		else
		{
			Ext.getCmp('mnuQueryResultSave').show();
			Ext.getCmp('mnuQueryResultSave').enable();
			Ext.getCmp('mnuQueryResultSaveAs').hide();			
			Ext.getCmp('mnuQueryResultSaveAsXLS').hide();
			Ext.getCmp('mnuQueryResultSaveAsCSV').hide();
		}
		Ext.getCmp('mnuQueryResultPrint').show();
		Ext.getCmp('mnuQueryResultPrint').enable();
	});
	jQuery('body').on('cmdMainMenuResultButtonsDisable', function() {
		Ext.getCmp('mnuQueryResultSave').hide();
		Ext.getCmp('mnuQueryResultSaveAs').hide();
		Ext.getCmp('mnuQueryResultSaveAsXLS').hide();
		Ext.getCmp('mnuQueryResultSaveAsCSV').hide();
		Ext.getCmp('mnuQueryResultPrint').hide();
	});
	jQuery('body').on('cmdMainWindowUpdate', function() {
		if (jQuery('#desinventarRegionId').val() != '')
		{
			Ext.getCmp('westm').collapse();
		}
		jQuery('.contentBlock').hide();
		jQuery('#divLoading').show();
		setTimeout(function()
		{
			jQuery('body').trigger('cmdMainMenuUpdate');
			if (jQuery('#desinventarRegionId').val() != '')
			{
				jQuery('body').trigger('cmdMainQueryUpdate');
			}
			jQuery('.contentBlock').hide();
			jQuery('#divLoading').hide();
			doViewportShow();
		}, 2000);
	});
} //doCreateMainMenu()

function doMainMenuToggle(bEnable)
{
	jQuery('#divMainMenu span.menu').each(function() {
		var w = Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu'));
		if (w != undefined)
		{
			if (bEnable)
			{
				w.enable();
			}
			else
			{
				w.disable();
			}
		}
	});
}

function doMainMenuDisable()
{
	jQuery('#divMainMenu span.item').each(function() {
		var id = jQuery(this).attr('id').replace('msg','mnu');
		var w = Ext.getCmp(id);
		if (w != undefined)
		{
			w.disable();
		}
	});
	jQuery('#divMainMenu span.submenu').each(function() {
		var w = Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu'));
		if (w != undefined)
		{
			w.disable();
		}
	});
} //doMainMenuDisable()

function doMainMenuUpdate()
{
	doMainMenuDisable();

	// Menu items that are always enabled
	jQuery('#divMainMenu span.clsMenuAlwaysOn').each(function() {
		Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
	});
	Ext.getCmp('mnuUser').setText(jQuery('span#msgUser').text());

	// Enable menu items when a User is logged in
	if (jQuery('#desinventarUserId').val() == '')
	{
		jQuery('#divMainMenu span.clsMenuWithoutUser').each(function() {
			Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
		});
		Ext.getCmp('mnuUserLogin').show();
		Ext.getCmp('mnuUserChangeLogin').hide();
	}
	else
	{
		jQuery('#divMainMenu span.clsMenuWithUser').each(function() {
			Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
		});
		Ext.getCmp('mnuUserLogin').hide();
		Ext.getCmp('mnuUserChangeLogin').show();
		Ext.getCmp('mnuUser').setText(jQuery('span#msgUser').text() + ' : ' + jQuery('#desinventarUserId').val());
	}

	// Configure which options are visible using RoleValue
	var UserRoleValue = parseInt(jQuery('#desinventarUserRoleValue').val());

	if (UserRoleValue >= 5)
	{
		Ext.getCmp('mnuUserAccountManagement').show();
		Ext.getCmp('mnuUserAccountManagement').enable();
	}
	else
	{
		Ext.getCmp('mnuUserAccountManagement').hide();
	}

	Ext.getCmp('mnuFileUploadReplace').hide();

	Ext.getCmp('mnuDatacard').hide();
	Ext.getCmp('mnuDatacardSetup').hide();
	Ext.getCmp('mnuDatacardSetupEnd').hide();
	Ext.getCmp('mnuDatacardEdit').hide();
	
	// Show some menu items when a Region is Selected
	if (jQuery('#desinventarRegionId').val() == '')
	{
	}
	else
	{
		Ext.getCmp('mnuRegionLabel').setText('[' + jQuery('#desinventarRegionLabel').val() + ']');
		if (UserRoleValue > 0)
		{
			jQuery('#divMainMenu span.clsMenuWithRegion').each(function() {
				Ext.getCmp(jQuery(this).attr('id').replace('msg','mnu')).enable();
			});
			Ext.getCmp('mnuDatacardEdit').hide();

			//2012-03-01 Bug #55 -  Enable download function for any user that can view the database
			Ext.getCmp('mnuFileDownload').enable();
		}
		if (UserRoleValue >= 2) 
		{
			// Edit datacards instead of only view them
			Ext.getCmp('mnuDatacard').show();
			Ext.getCmp('mnuDatacardEdit').show();
			Ext.getCmp('mnuDatacardEdit').enable();

			if (UserRoleValue >= 4)
			{
				Ext.getCmp('mnuFileUploadReplace').show();
				Ext.getCmp('mnuFileUploadReplace').enable();
				Ext.getCmp('mnuDatacardSetup').show();
				Ext.getCmp('mnuDatacardSetup').enable();
			}
		}		
	}
	jQuery('body').trigger('cmdMainMenuResultButtonsDisable');
} //doMainMenuUpdate()

function doDialogsCreate()
{
	var w;
	// Query Open Window
	w = new Ext.Window({id:'wndQueryOpen',
		el:'qry-win', layout:'fit', width:300, height:200,
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'qry-cfg', autoScroll: true }),
		buttons:
		[
			{
				text: jQuery('#msgQueryOpenButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndQueryOpen').hide();
				}
			}
		]
	});

	// Database List - Database Search Window
	w = new Ext.Window({id:'wndAdminUsers',
		el:'divAdminUsersWin', layout:'fit', x:200, y:100, width:600, height:450, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({ contentEl: 'divAdminUsersContent', autoScroll: true })
	});
	w = new Ext.Window({id:'wndDialog',
		el:'dlg-win', layout:'fit', x:350, y:200, width:300, height:150, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({ contentEl: 'dlg', autoScroll: true })
	});
	// Datacard View/Edit Window
	w = new Ext.Window({id:'wndDatacard',
		el:'divDatacardWindow', layout:'fit', 
		width:960, height:638, //x: 65, y: 0, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({ contentEl: 'divDatacardContent', autoScroll: true })
	});
	w.on('hide',function() {
		jQuery('#divDatacardWindow').hide();
		showtip('');					
	});

	w = new Ext.Window({id:'wndViewDataParams', 
		el:'divViewDataParamsWindow', layout:'fit',
		width:600, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'divViewDataParamsContent', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewDataButtonClear').text(),
				handler: function()
				{
					$('CD').reset();
				} //handler
			},
			{
				text: jQuery('#msgViewDataButtonSend').text(),
				handler: function()
				{
					if (sendList("result"))
					{
						$('DCRes').value = "D";
						jQuery('body').trigger('cmdQueryResultsButtonShow');
						Ext.getCmp('wndViewDataParams').hide();
					}
					else
					{
						console.debug('Error while executing function ViewData');
					}
				} //handler
			},
			{
				text: jQuery('#msgViewDataButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewDataParams').hide();
				} //handler
			}
		] //button
	});

	w = new Ext.Window({id:'wndViewMapParams',
		el:'map-win',  layout:'fit',  width:650, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'map-cfg', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewMapButtonClear').text(),
				handler: function()
				{
					$('CM').reset();
				}
			},
			{
				text: jQuery('#msgViewMapButtonSend').text(),
				handler: function()
				{
					if (sendMap("result"))
					{
						$('DCRes').value = "M";
						Ext.getCmp('wndViewMapParams').hide();
						jQuery('body').trigger('cmdQueryResultsButtonShow');
					}
					else
					{
						console.debug('Error while executing function ViewMap');
					}
				}
			},
			{
				text: jQuery('#msgViewMapButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewMapParams').hide();
				}
			}
		]
	});

	w = new Ext.Window({id:'wndViewGraphParams',
		el:'divGraphParameters',  layout:'fit',  width:750, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'grp-cfg', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewGraphButtonClear').text(),
				handler: function()
				{
					$('frmGraphParams').reset();
					jQuery('#prmGraphTypeHistogram').change();
				}
			},
			{
				text: jQuery('#msgViewGraphButtonSend').text(),
				handler: function()
				{
					sendGraphic('result');
					$('DCRes').value = "G";
					Ext.getCmp('wndViewGraphParams').hide();
					jQuery('body').trigger('cmdQueryResultsButtonShow');
				}
			},
			{
				text: jQuery('#msgViewGraphButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewGraphParams').hide();
				}
			}
		]
	});

	w = new Ext.Window({id:'wndViewStdParams',
		el:'std-win',  layout:'fit',  width:600, height:420, 
		closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
		items: new Ext.Panel({contentEl: 'std-cfg', autoScroll: true }),
		buttons: [
			{
				text: jQuery('#msgViewStdButtonClear').text(),
				handler: function()
				{
					$('frmStatParams').reset();
				}
			},
			{
				text: jQuery('#msgViewStdButtonSend').text(),
				handler: function()
				{
					if (sendStatistic("result"))
					{
						$('DCRes').value = "S";
						Ext.getCmp('wndViewStdParams').hide();
						jQuery('body').trigger('cmdQueryResultsButtonShow');
					}
					else
					{
						console.debug('Error while executing ViewStd function');
					}
				} //handler
			},
			{
				text: jQuery('#msgViewStdButtonClose').text(),
				handler: function()
				{
					Ext.getCmp('wndViewStdParams').hide();
				}
			}
		]
	});

} //doDialogsCreate()


/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyMain()
{
	onReadyDatabaseList();
	onReadyDatabaseUpload();
	onReadyDatabaseCreate();
	onReadyDatabaseUsers();
	onReadyQueryDesign();
	onReadyGeography();
	onReadyGeolevels();
	onReadyDatabaseEvents();
	onReadyDatabaseCauses();
	onReadyAdminUsers();
	onReadyUserPermAdmin();
	onReadyCommon();
	onReadyPrototype();
	onReadyUserLogin();
	onReadyUserAccount();
	onReadyDatacards();
	onReadyAdminDatabase();
	onReadyExtraEffects();
	onReadyQueryResults();
	onReadyData();
	onReadyGraphic();
	onReadyThematicMap();	
	onReadyStatParams();

	jQuery('#frmMainQuery').submit(function() {
		var myURL = jQuery(this).attr('action');
		var myCmd = jQuery('#prmQueryCommand').val();
		if ( (myCmd == 'cmdGridSave') ||
		     (myCmd == 'cmdGraphSave') ||
		     (myCmd == 'cmdMapSave') || 
		     (myCmd == 'cmdStatSave') ||
		     (myCmd == 'cmdQuerySave'))
		{
			return true;
		}
		else
		{
			//jQuery('body').trigger('cmdMainWaitingShow');
			jQuery('#divRegionInfo').hide();
			jQuery('#dcr').show();
			jQuery('#dcr').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
			jQuery.post(myURL,
				jQuery(this).serialize(),
				function(data)
				{
					//jQuery('body').trigger('cmdMainWaitingHide');
					jQuery('#dcr').html(data);
					switch(myCmd)
					{
						case 'cmdGridShow':
							jQuery('body').trigger('cmdViewDataUpdate');
						break;
						case 'cmdMapShow':
							createThematicMap();
						break;
						case 'cmdGraphShow':
						break;
						case 'cmdStatShow':
							onReadyStatistic();
						break;
						default:
						break;
					} //switch
				}
			);
			return false;
		}
	});

	jQuery('#DBConfig_Geolevels').on('show', function() {
		jQuery('body').trigger('cmdGeolevelsShow');
	});
	jQuery('#DBConfig_Geography').on('show', function() {
		jQuery('body').trigger('cmdGeographyShow');
	});

	jQuery('#DBConfig_Events').on('show', function() {
		jQuery('body').trigger('cmdDatabaseEventsShow');
	});

	jQuery('#DBConfig_Causes').on('show', function() {
		jQuery('body').trigger('cmdDatabaseCausesShow');
	});

	jQuery('#DBConfig_Users').on('show', function() {
		jQuery('body').trigger('cmdDatabaseUsersShow');
	});
	
	// Tabs for Database Configuration
	jQuery('#DBConfig_tabs').tabs();
	jQuery('.classDBConfig_tabs').click(function() {
		var me = jQuery(jQuery(this).attr('href'));
		showtip(me.find('.helptext').text());
		var cmd = jQuery(this).attr('cmd');
		if (cmd == '')
		{
			jQuery(me).trigger('show');
		}
		else
		{
			me.find('.content').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
			jQuery.post(
				jQuery(this).data('url'),
				{
					cmd      : cmd,
					RegionId : jQuery('#desinventarRegionId').val(),
					lang     : jQuery('#desinventarLang').val()
				},
				function(data)
				{
					me.find('.content').html(data);
					switch(cmd)
					{
						case 'cmdDBInfoCause':
							onReadyDBConfigCauses();
						break;
						default:
							onReadyExtraEffects();
						break;
					}
				}
			);
		}
		return false;
	});
	jQuery('body').on('cmdDatabaseLoadData', function() {
		doDatabaseLoadData();
	});

	jQuery(window).bind('hashchange', function(e) {
		var url = jQuery.param.fragment();
		var options = url.split('/');
		switch(options[0])
		{
			case '':
				//Nothing to do
				jQuery('#desinventarRegionId').val('');
				jQuery('body').trigger('cmdViewportShow');
			break;
			default:
				var RegionId = options[0];
				jQuery('#desinventarRegionId').val(RegionId);
				jQuery('body').trigger('cmdDatabaseLoadData');
			break;
		}
	});
	jQuery(window).trigger('hashchange');
} //onReadyMain()

function doDatabaseLoadData()
{
	if (jQuery('#desinventarRegionId').val() != '')
	{
		jQuery.post(
			jQuery('#desinventarURL').val() + '/',
			{
				cmd      : 'cmdDatabaseLoadData',
				RegionId : jQuery('#desinventarRegionId').val()
			},
			function(data)
			{
				// Initialize data-* components for body
				jQuery('body').data('RegionId', data.RegionId);
				jQuery('body').data('params', data.params);
				jQuery('body').data('GeolevelsList', data.GeolevelsList);
				jQuery('body').data('EventList', data.EventList);
				jQuery('body').data('CauseList', data.CauseList);
				jQuery('body').data('EEFieldList', data.EEFieldList);
				jQuery('body').data('RecordCount', data.RecordCount);

				//Compatibility with old methods
				jQuery('#desinventarUserId').val(data.params.UserId);
				jQuery('#desinventarUserFullName').val(data.params.UserFullName);
				jQuery('#desinventarUserRole').val(data.params.UserRole);
				jQuery('#desinventarUserRoleValue').val(data.params.UserRoleValue);
				
				var dataItems = jQuery('body').data();
				jQuery.each(dataItems, function(index, value) {
					if (index.substr(0,13) === 'GeographyList')
					{
						jQuery('body').removeData(index);
					}
				});
				jQuery('body').data('GeographyList', data.GeographyList);
				// Trigger event on mainblock components to update them
				jQuery('.mainblock').trigger('cmdInitialize');
				// Info
				jQuery('#desinventarLang').val(data.params.LangIsoCode);
				jQuery('#desinventarRegionId').val(data.params.RegionId);
				jQuery('#desinventarRegionLabel').val(data.params.RegionLabel);
				jQuery('#desinventarNumberOfRecords').val(data.RecordCount);

				jQuery('body').trigger('cmdViewportShow');
			},
			'json'
		);
	}
} //doDatabaseLoadData()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyQueryDesign()
{
	jQuery('div.QueryDesign').on('mouseover','.withHelpOver',function() {
		showtip(jQuery(this).data('help'));
	}).on('focus','.withHelpFocus',function() {
		showtip(jQuery(this).data('help'));
	});

	jQuery('div.QueryDesign div.GeographyList').on('click', 'li.item input:checkbox', function(event) {
		jQuery(this).trigger('GeographyUpdate');
	}).on('click','li.item span.label', function(event) {
		jQuery(this).parent().find('input:checkbox').trigger('click');
		jQuery(this).trigger('GeographyUpdate');
	}).on('GeographyUpdate', 'li.item', function(event) {
		var GeoLevelCount = jQuery('div.QueryDesign div.GeolevelsHeader table tr td').size() - 2;
		var GeographyLevel = jQuery(this).data('GeographyLevel');
		if (GeographyLevel < GeoLevelCount)
		{
			var GeographyId = jQuery(this).data('GeographyId');
			var item = jQuery(this);
			jQuery('ul.list li', item).remove();
			var isChecked = jQuery('input:checkbox',this).prop('checked');
			
			if (isChecked)
			{
				GeographyList = jQuery('body').data('GeographyList-' + GeographyId);
				if (GeographyList == undefined) 
				{
					jQuery.post(
						jQuery('#desinventarURL').val() + '/',
						{
							cmd         : 'cmdGeographyGetItemsById',
							RegionId    : jQuery('#desinventarRegionId').val(),
							GeographyId : GeographyId
						},
						function(data)
						{
							if (parseInt(data.Status) > 0)
							{
								jQuery.each(data.GeographyList, function(key, value) {
									jQuery('body').data('GeographyList' + key, value);
								});
								jQuery.each(data.GeographyList[GeographyId], function(key, value) {
									var clone = jQuery('div.QueryDesign div.GeographyList ul.mainlist li.item:first').clone().show();
									clone.data('GeographyId', key);
									clone.data('GeographyLevel', GeographyLevel + 1);
									jQuery('input:checkbox',clone).attr('value', key);
									jQuery('span.label',clone).text(value.GeographyName);
									jQuery('ul.list:first',item).append(clone);
								});
							}
						},
						'json'
					);
				}
				else
				{
					jQuery.each(GeographyList, function(key, value) {
						var clone = jQuery('div.QueryDesign div.GeographyList ul.mainlist li.item:first').clone().show();
						clone.data('GeographyId', key);
						clone.data('GeographyLevel', GeographyLevel + 1);
						jQuery('input:checkbox',clone).attr('value', key);
						jQuery('span.label',clone).text(value.GeographyName);
						jQuery('ul.list:first',item).append(clone);
					});
				}
			}
		}
		event.stopPropagation();
	});

	jQuery('div.QueryDesign table.EffectList').on('click', 'input:checkbox', function(event) {
		jQuery(this).trigger('EffectUpdate');
	}).on('click','span.label', function(event) {
		var checkbox = jQuery(this).parent().find('input:checkbox');
		checkbox.prop('checked', !checkbox.prop('checked'));
		jQuery(this).trigger('EffectUpdate');
	}).on('change','select.operator', function(event) {
		var value = jQuery(this).val();
		jQuery(this).trigger('HideValues');
		if ((value == '>=') || (value == '<=') || (value == '=') || (value == '-3') )
		{
			jQuery(this).trigger('ShowFirstValue');
			if (value == '-3')
			{
				jQuery(this).trigger('ShowLastValue');
			}
		}
	}).on('EffectUpdate', 'td div', function(event) {
		if (jQuery('input:checkbox',this).prop('checked'))
		{
			jQuery('span.options', this).show();
			jQuery('select.operator',this).enable().change();
		}
		else
		{
			jQuery('span.options',this).hide();
			jQuery('select.operator',this).disable().change();
		}		
	}).on('HideValues', 'td div', function(event) {
		jQuery('span.firstvalue', this).hide();
		jQuery('span.firstvalue input',this).disable();
		jQuery('span.lastvalue', this).hide();
		jQuery('span.lastvalue input',this).disable();
	}).on('ShowFirstValue', 'td div', function(event) {
		jQuery('span.firstvalue', this).show();
		jQuery('span.firstvalue input',this).enable();
	}).on('ShowLastValue', 'td div', function(event) {
		jQuery('span.lastvalue', this).show();
		jQuery('span.lastvalue input', this).enable();
	});

	jQuery('div.QueryDesign').on('click', 'table.QueryCustom div.field', function(event) {
		setAdvQuery(jQuery(this).data('field'), jQuery(this).data('type'));
	});
	
	jQuery('div.QueryDesign').on('cmdInitialize', function() {
		var params = jQuery('body').data('params');

		// Initialize fields
		jQuery('input.RegionId', this).val(jQuery('body').data('RegionId'));
		jQuery('input.MinYear' , this).val(params.MinYear);
		jQuery('input.MaxYear' , this).val(params.MaxYear);
		jQuery('input.queryBeginYear', this).val(params.MinYear);
		jQuery('input.queryEndYear', this).val(params.MaxYear);

		// Load Geolevels List
		var geolevel_list = jQuery('body').data('GeolevelsList');
		jQuery('div.QueryDesign div.GeolevelsHeader table tr td:gt(0)').remove();
		jQuery.each(geolevel_list, function(key, value) {
			var clone = jQuery('div.QueryDesign div.GeolevelsHeader table tr td:last').clone().show();
			jQuery('span',clone).text(value.GeoLevelName);
			jQuery('span',clone).data('help', value.GeoLevelDesc);
			jQuery('div.QueryDesign div.GeolevelsHeader table tr').append(clone);
		});
		// Load Geography List
		var geography_list = jQuery('div.QueryDesign div.GeographyList ul.mainlist');
		geography_list.find('li:gt(0)').remove();
		geography_list.find('li').hide();
		jQuery.each(jQuery('body').data('GeographyList'), function(key, value) {
			var item = geography_list.find('li:last').clone().show();
			jQuery('input:checkbox', item).attr('value', key);
			jQuery('span.label', item).html(value.GeographyName);
			jQuery(item).data('GeographyId', key);
			jQuery(item).data('GeographyLevel', 0);
			geography_list.append(item);
		});
		// Load Event List
		jQuery('div.QueryDesign select.Event').empty();
		jQuery.each(jQuery('body').data('EventList'), function(key, value) {
			if (parseInt(value.EventPredefined) > 0)
			{
				var option = jQuery('<option>', { value : value.EventId }).text(value.EventName);
				option.data('help', value.EventDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Event').append(option);
			}
		});
		var option = jQuery('<option>', { value : '' }).text('---');
		option.attr('disabled','disabled');
		jQuery('div.QueryDesign select.Event').append(option);
		jQuery.each(jQuery('body').data('EventList'), function(key, value) {
			if (parseInt(value.EventPredefined) < 1)
			{
				var option = jQuery('<option>', { value : key }).text(value.EventName);
				option.data('help', value.EventDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Event').append(option);
			}
		});		
		// Load Cause List
		jQuery('div.QueryDesign select.Cause').empty();
		jQuery.each(jQuery('body').data('CauseList'), function(key, value) {
			if (parseInt(value.CausePredefined) > 0)
			{
				var option = jQuery('<option>', { value : key }).text(value.CauseName);
				option.data('help', value.CauseDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Cause').append(option);
			}
		});
		var option = jQuery('<option>', { value : '' }).text('---');
		option.attr('disabled','disabled');
		jQuery('div.QueryDesign select.Cause').append(option);
		jQuery.each(jQuery('body').data('CauseList'), function(key, value) {
			if (parseInt(value.CausePredefined) < 1)
			{
				var option = jQuery('<option>', { value : value.CauseId }).text(value.CauseName);
				option.data('help', value.CauseDesc);
				option.addClass('withHelpOver');
				jQuery('div.QueryDesign select.Cause').append(option);
			}
		});
		// Load EffectPeople List (ef1)
		var effect_list = jQuery('div.QueryDesign table.EffectPeopleList');
		effect_list.find('tr:gt(0)').remove();
		jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('select.operator', clone).attr('name', 'D_' + field + '[0]').disable();
			jQuery('span.firstvalue input', clone).attr('name', 'D_' + field + '[1]');
			jQuery('span.lastvalue input', clone).attr('name', 'D_' + field + '[2]').disable();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectPeople',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});

		// Load EffectSector List (sec)
		var effect_list = jQuery('div.QueryDesign table.EffectSectorList');
		effect_list.find('tr:gt(0)').remove();
		jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('select.operator', clone).attr('name', 'D_' + field + '[0]').disable();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectSector',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});

		// Load EffectLosses2 List (ef3)
		var effect_list = jQuery('div.QueryDesign table.EffectListLosses2');
		effect_list.find('tr:gt(0)').remove();
		jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('select.operator', clone).attr('name', 'D_' + field + '[0]').disable();
			jQuery('span.firstvalue input', clone).attr('name', 'D_' + field + '[1]');
			jQuery('span.lastvalue input', clone).attr('name', 'D_' + field + '[2]').disable();
			jQuery('span.label', clone).text(jQuery('span.label',this).text());
			jQuery('div.EffectLosses2',clone).data('field', jQuery(this).data('field'));
			effect_list.append(clone);
		});
		// Load EffectAdditional List (EEFieldList)
		var effect_list = jQuery('div.QueryDesign table.EffectAdditionalList');
		effect_list.find('tr:gt(0)').remove();
		jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
			var field = key;
			var type = value[2];
			var clone = jQuery('tr:last', effect_list).clone().show();
			jQuery('div.Effect', clone).hide();
			switch(type)
			{
				case 'INTEGER':
				case 'DOUBLE':
				case 'CURRENCY':
					jQuery('select.operator'      , clone).attr('name', 'EEFieldQuery[' + field + '][0]').disable();
					jQuery('span.firstvalue input', clone).attr('name', 'EEFieldQuery[' + field + '][1]');
					jQuery('span.lastvalue input' , clone).attr('name', 'EEFieldQuery[' + field + '][2]').disable();
					jQuery('div.EffectNumeric', clone).show();
				break;
				case 'STRING':
				case 'TEXT':
				case 'DATE':
					jQuery('input.text', clone).attr('name', 'EEFieldQuery[' + field + '][Text]');
					jQuery('div.EffectText', clone).show();
				break;
			}
			jQuery('input.type', clone).attr('name', 'EEFieldQuery[' + field + '][Type]');
			jQuery('input.type', clone).attr('value', type);
			jQuery('span.label', clone).text(value[0]);
			jQuery('div.EffectAdditional',clone).data('field', field);
			effect_list.append(clone);
		});
		// Load QueryCustom field list
		var field_list = jQuery('div.QueryDesign table.QueryCustom div.list');
		field_list.find('div:gt(0)').remove();
		jQuery('div.QueryDesign table.QueryCustom div.defaultlist span').each(function() {
			var field = jQuery(this).data('field');
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', field);
			jQuery(clone).data('type', jQuery(this).data('type'));
			jQuery('input', clone).attr('value', jQuery(this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'number');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'boolean');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'number');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery('div.desinventarInfo div.EffectList div.EffectOther').each(function() {
			var field = jQuery('span.field', this).text();
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', jQuery(this).data('field'));
			jQuery(clone).data('type', 'text');
			jQuery('input', clone).attr('value', jQuery('span.label',this).text());
			field_list.append(clone);
		});
		jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
			var field = key;
			var type = value[2];
			var clone = jQuery('div:last', field_list).clone().show();
			jQuery(clone).data('field', field);
			jQuery(clone).data('type', 'text');
			jQuery('input', clone).attr('value', value[0]);
			field_list.append(clone);
		});
		
	});
} //onReadyQueryDesign()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyQueryResults()
{
	jQuery('#queryBeginYear').blur(function() {
		validateBeginYear();	
	});
	jQuery('#queryEndYear').blur(function() {
		validateEndYear();
	});

	jQuery('body').on('cmdMainQueryUpdate', function() {
		// 2011-02-05 (jhcaiced) Configure RecordStatus field
		if ( (jQuery('#desinventarUserId').val() != '') &&
		     (jQuery('#desinventarUserRoleValue').val() > 1) )
		{
			jQuery('#fldQueryRecordStatus').val(['PUBLISHED','READY']);
			jQuery('#divQueryRecordStatus').show();
		}
		else
		{
			jQuery('#fldQueryRecordStatus').val(['PUBLISHED']);
			jQuery('#divQueryRecordStatus').hide();
		}
	});
	
	jQuery('#btnViewData').click(function() {
		jQuery('body').trigger('cmdViewDataParams');
	});
	jQuery('#btnViewMap').click(function() {
		jQuery('body').trigger('cmdViewMapParams');
	});
	jQuery('#btnViewGraph').click(function() {
		jQuery('body').trigger('cmdViewGraphParams');
	});
	jQuery('#btnViewStd').click(function() {
		jQuery('body').trigger('cmdViewStdParams');
	});

	jQuery('body').on('cmdQueryResultsButtonShow', function() {
		jQuery('#btnResultSave').show();
		jQuery('#btnResultPrint').show();
		jQuery('body').trigger('cmdMainMenuResultButtonsEnable');
	});
	jQuery('body').on('cmdQueryResultsButtonHide', function() {
		jQuery('#btnResultSave').hide();
		jQuery('#btnResultPrint').hide();
		jQuery('body').trigger('cmdMainMenuResultButtonsDisable');
	});

	jQuery('#btnResultSave').click(function() {
		if (jQuery('#DCRes').val() == 'M' || jQuery('#DCRes').val() == 'G')
		{
			saveRes('export', '');
		}
	}).mouseover(function() {
		if (jQuery('#DCRes').val() == 'D' || jQuery('#DCRes').val() == 'S')
		{
			jQuery('#btnResultSaveOptions').show();
			jQuery('#btnResultShow').val(1);
		}
	}).mouseout(function() {
		jQuery('#btnResultShow').val('');
	});
	jQuery('#btnResultSaveOptions').mouseout(function() {
		setTimeout(function() {
			if (jQuery('#btnResultShow').val() != '')
			{
				jQuery('#btnResultSaveOptions').hide();
			}
		}, 4000);
	});

	jQuery('#btnResultSaveXLS').click(function() {
		saveRes('export', 'xls');
	}).mouseover(function() {
		jQuery('#btnResultShow').val(1);
	});
	jQuery('#btnResultSaveCSV').click(function() {
		saveRes('export', 'csv');
	}).mouseover(function() {
		jQuery('#btnResultShow').val(1);
	});
	jQuery('#btnResultPrint').click(function() {
		printRes();
	});

	// Initialize code
	jQuery('body').trigger('cmdMainQueryUpdate');
} //onReadyQueryResults()

function validateQueryDefinition()
{
	var iReturn = 1;
	return iReturn;
} //validateQueryDefinition()

function validateBeginYear()
{
	var prmQueryMinYear = jQuery("#prmQueryMinYear").val();
	var MinYear = jQuery("#queryBeginYear").val();
	if (parseInt(MinYear) != MinYear-0 )
	{
		jQuery("#queryBeginYear").val(prmQueryMinYear);
	}
} //validateBeginYear()

function validateEndYear()
{
	var prmQueryMaxYear = jQuery("#prmQueryMaxYear").val();
	var MaxYear = jQuery("#queryEndYear").val();
	if (parseInt(MaxYear) != MaxYear-0 )
	{
		jQuery("#queryEndYear").val(prmQueryMaxYear);
	}
} //validateEndYear()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyStatistic()
{
	jQuery('#btnStatGotoFirstPage').click(function() {
		doStatDisplayPage(1);
	});
	jQuery('#btnStatGotoPrevPage').click(function() {
		doStatDisplayPage('prev');
	});
	jQuery('#btnStatGotoNextPage').click(function() {
		doStatDisplayPage('next');
	});
	jQuery('#btnStatGotoLastPage').click(function() {
		doStatDisplayPage(jQuery('#prmStatNumberOfPages').val());
	});
	
	jQuery('.linkStatOrderColumn').click(function() {
		doStatOrderByField(jQuery(this).attr('AltField'), jQuery(this).attr('OrderType'));
		return false;
	});

	jQuery('#tblStatRows tr:even').addClass('under');
	
	jQuery('#StatCurPage').keydown(function(event) {
		if(event.keyCode == 13) {
			doStatDisplayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});
} //onReadyStatistic()

function doStatDisplayPage(page)
{
	var mypag = page;
	now = parseInt(jQuery('#StatCurPage').val());
	if (page == 'prev')
	{
		mypag = now - 1;
	}
	else if (page == 'next')
	{
		mypag = now + 1;
	}
	var NumberOfPages = jQuery('#prmStatNumberOfPages').val();
	if ((mypag < 1) || (mypag > NumberOfPages))
	{
		return false;
	}
	jQuery('#StatCurPage').val(mypag);
	var RecordsPerPage = jQuery('#prmStatRecordsPerPage').val();

	jQuery('#tblStatRows').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(
		jQuery('#desinventarURL').val() + '/statistic.php',
		{
			r     : jQuery('#prmStatRegionId').val(),
			 page : mypag,
			 rxp  : RecordsPerPage,
			 sql  : jQuery('#prmStatQueryDef').val(),
			 fld  : jQuery('#prmStatFieldList').val(),
			 geo  : jQuery('#prmStatGeography').val()
		},
		function(data)
		{
			jQuery('#tblStatRows').html(data);
		}
	);
} //doStatDisplayPage()

function doStatOrderByField(field, dir)
{
	jQuery('#tblStatRows').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(
		jQuery('#desinventarURL').val() + '/statistic.php',
		{
			r    : jQuery('#prmStatRegionId').val(),
			page : $('StatCurPage').value,
			rxp  : jQuery('#prmStatRecordsPerPage').val(),
			sql  : jQuery('#prmStatQueryDef').val(),
			fld  : jQuery('#prmStatFieldList').val(),
			ord  : field,
			geo  : jQuery('#prmStatGeography').val(),
			dir  : dir
		},
		function(data)
		{
			jQuery('#tblStatRows').html(data);
		}
	);
} //doStatOrderByField()

function setTotalize(lnow, lnext)
{
	var sour = $(lnow);
	var dest = $(lnext);
	// clean dest list
	for (var i = dest.length - 1; i>=0; i--)
	{
		dest.remove(i);
	}
	for (var i=0; i < sour.length; i++)
	{
		if (!sour[i].selected)
		{
			var opt = document.createElement('option');
			opt.value = sour[i].value;
			opt.text = sour[i].text;
			var pto = dest.options[i];
			try
			{
				dest.add(opt, pto);
			}
			catch(ex)
			{
				dest.add(opt, i);
			}
		}
	} //for
} //setTotalize()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
function onReadyStatParams()
{
	jQuery('#fldStatParam_FirstLev').change(function() {
		setTotalize('fldStatParam_FirstLev', 'fldStatParam_SecondLev');
		jQuery('#fldStatParam_ThirdLev').empty();
	});
	
	jQuery('#fldStatParam_SecondLev').change(function() {
		setTotalize('fldStatParam_SecondLev', 'fldStatParam_ThirdLev');
	});

	jQuery('body').on('cmdViewStdParams', function() {
		Ext.getCmp('wndViewStdParams').show();
		jQuery('#fldStatParam_FirstLev').trigger('change');
	});

	jQuery('div.ViewStatParams').on('cmdInitialize', function(event) {
		doViewStatParamsInitialize();
	});
} //onReadyStatParams()

function doViewStatParamsInitialize()
{
	var statlevel_list = jQuery('div.ViewStatParams select.StatlevelFirst');
	statlevel_list.find('option').remove();
	jQuery.each(jQuery('body').data('GeolevelsList'), function(key, value) {
		statlevel_list.append(jQuery('<option>', { value: value.GeoLevelId + '|D.GeographyId' }).text(value.GeoLevelName));
	});
	statlevel_list.append(jQuery('<option>', { value : '|D.EventId'}).text(jQuery('#ViewStatParamsLabelEvent').text()));
	statlevel_list.append(jQuery('<option>', { value : 'YEAR|D.DisasterBeginTime'}).text(jQuery('#ViewStatParamsLabelYear').text()));
	statlevel_list.append(jQuery('<option>', { value : 'MONTH|D.DisasterBeginTime'}).text(jQuery('#ViewStatParamsLabelMonth').text()));
	statlevel_list.append(jQuery('<option>', { value : '|D.CauseId'}).text(jQuery('#ViewStatParamsLabelCause').text()));
	statlevel_list.val(jQuery('option:first', statlevel_list).val());

	var field_list = jQuery('div.ViewStatParams select.FieldsAvailable');
	field_list.find('option').remove();
	// EffectPeople (ef1)
	jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|S|-1' }).text(jQuery('#StatLabelAuxHave').text() + ' ' + label));
	});	
	// EffectLosses1 List (ef2)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses1').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label));
	});
	// EffectLosses2 List (ef3)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label));
	});	
	// EffectSector (sec)
	jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|S|-1' }).text(jQuery('#StatLabelAuxAffect').text() + ' ' + label));
	});
	field_list.append(jQuery('<option>', { value: '', disabled:'disabled'}).text('---'));
	// EEFieldList
	jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
		var field = key;
		var label = value[0];
		var type  = value[2];
		if ( (type == 'INTEGER') || (type == 'DOUBLE'))
		{
			field_list.append(jQuery('<option>', { value: 'E.' + field + '|>|-1' }).text(label));
		}
	});
	field_list.append(jQuery('<option>', { value : 'D.EventDuration|S|-1'}).text(jQuery('#StatLabelEventDuration').text()));

	var field_list = jQuery('div.ViewStatParams select.FieldsShow');
	field_list.find('option').remove();
	// EffectPeople (ef1)
	jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + 'Q|>|-1' }).text(label));
	});	
	
} //doViewStatParamsInitialize()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
//2011-02-19 (jhcaiced) Do not remove this line, initialize map=null 
// to avoid an error with IE and the maps
var map = null;

function onReadyThematicMap()
{
	jQuery('body').on('cmdViewMapParams', function() {
		Ext.getCmp('wndViewMapParams').show();
	});
	//Initialize
	jQuery('div.ViewMapParams').on('cmdInitialize', function(event) {
		doViewMapParamsInitialize();
	});
} //onReadyThematicMap()

function doViewMapParamsInitialize()
{
	// Level of Representation
	var geolevel_list = jQuery('div.ViewMapParams select.Geolevel');
	geolevel_list.find('option').remove();
	jQuery.each(jQuery('body').data('GeolevelsList'), function(key, value) {
		if (parseInt(value.HasMap) > 0)
		{
			geolevel_list.append(jQuery('<option>', { value: value.GeoLevelId + '|D.GeographyId|' }).text(value.GeoLevelName));
		}
	});
	geolevel_list.val(jQuery('option:first', geolevel_list).val());

	// Variable to be represented
	var field_list = jQuery('div.ViewMapParams select.Field');
	field_list.find('option').remove();
	field_list.append(jQuery('<option>', { value: 'D.DisasterId||' }).text(jQuery('#RepNumLabel').text()));
	// EffectPeople (ef1)
	jQuery('div.desinventarInfo div.EffectList div.EffectPeople').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + 'Q|>|-1' }).text(label));
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|=|-1' }).text(jQuery('#AuxHaveLabel').text() + ' ' + label));
	});	
	// EffectLosses1 List (ef2)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses1').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label));
	});
	// EffectLosses2 List (ef3)
	jQuery('div.desinventarInfo div.EffectList div.EffectLosses2').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|>|-1' }).text(label));
	});	
	// EffectSector (sec)
	jQuery('div.desinventarInfo div.EffectList div.EffectSector').each(function() {
		var field = jQuery('span.field', this).text();
		var label = jQuery('span.label',this).text();
		field_list.append(jQuery('<option>', { value: 'D.' + field + '|=|-1' }).text(jQuery('#AuxAffectLabel').text() + ' ' + label));
	});
	field_list.append(jQuery('<option>', { value: '', disabled:'disabled'}).text('---'));
	// EEFieldList
	jQuery.each(jQuery('body').data('EEFieldList'), function(key, value) {
		var field = key;
		var label = value[0];
		field_list.append(jQuery('<option>', { value: 'E.' + field + '|>|-1' }).text(label));
	});
	field_list.val(jQuery('option:first', field_list).val());
}

function createThematicMap()
{
	var lon = parseFloat(jQuery('#prmMapLat').val());
	var lat = parseFloat(jQuery('#prmMapLon').val());
	var zoom = parseInt(jQuery('#prmMapZoom').val());
	var minx = parseFloat(jQuery('#prmMapMinX').val());
	var maxx = parseFloat(jQuery('#prmMapMaxX').val());
	var miny = parseFloat(jQuery('#prmMapMinY').val());
	var maxy = parseFloat(jQuery('#prmMapMaxY').val());
	OpenLayers.IMAGE_RELOAD_ATTEMPTS = 3;
	OpenLayers.ImgPath = jQuery('#desinventarOpenLayersURL').val() + '/img/';

	OpenLayers.Util.onImageLoadErrorColor = "transparent";
	var prj1 = new OpenLayers.Projection("EPSG:4326");
	var prj2 = new OpenLayers.Projection("EPSG:900913");
	var options = 
	{
		projection        : prj2,
		displayProjection : prj1,
		minResolution     : "auto",
		minExtent         : new OpenLayers.Bounds(-1, -1, 1, 1),
		units             : "m",
		maxResolution     : 156543.0339,
		maxExtent         : new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34,  20037508.34),
		controls          : [],
		numZoomLevels     : 22
	};
	map = new OpenLayers.Map('map', options);
	map.addControl(new OpenLayers.Control.PanZoomBar({zoomStopHeight:3,zoomWorldIcon:true}));
	map.addControl(new OpenLayers.Control.LayerSwitcher({ascending:false}));
	map.addControl(new OpenLayers.Control.MousePosition({displayClass:'MapMousePosition'}));
	map.addControl(new OpenLayers.Control.NavToolbar());
	
	// WMS Local Base Map
	mapServer = jQuery('#prmMapServer').val();
	var base = new OpenLayers.Layer.WMS(
		'Local BaseMap',
		jQuery('#desinventarURL').val() + '/wms/',
		{ },
		{ isBaseLayer:true }
	);
	map.addLayer(base);

	// Add Remote Layers	
	if (parseInt(jQuery('#optionUseRemoteMaps').val()) > 0)
	{
		// Yahoo Maps Base Layer
		var yahoo = new OpenLayers.Layer.Yahoo( "Yahoo Maps", { 'sphericalMercator': true });
		map.addLayer(yahoo);
		// Google Layers
		var gphy = new OpenLayers.Layer.Google("Google Physical" , {type: google.maps.MapTypeId.TERRAIN});
		map.addLayer(gphy);
		var gmap = new OpenLayers.Layer.Google("Google Basic"    , {numZoomLevels: 20 });
		map.addLayer(gmap);
		var ghyb = new OpenLayers.Layer.Google("Google Hybrid"   , {type: google.maps.MapTypeId.HYBRID, numZoomLevels:20});
		map.addLayer(ghyb);
		var gsat = new OpenLayers.Layer.Google("Google Satellite", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels:22});
		map.addLayer(gsat);

		// Metacarta Basic Base Layer (not working with OpenLayers 3...
		//var met1 = new OpenLayers.Layer.WMS("Metacarta Basic", "http://labs.metacarta.com/wms/vmap0", {'layers': 'basic', 'transparent': true}, {'isBaseLayer':true});
		//met1.setVisibility(false);
		//map.addLayer(met1);
	} //if
	
	// Effects and Admin layer(s)
	jQuery('#MapEffectLayers div').each(function() {
		var MapFile = jQuery(this).find(':eq(1)').text();
		var LayerName = jQuery(this).find(':eq(2)').text().trim();
		var layer = new OpenLayers.Layer.WMS(
			'DesInventar/' + jQuery(this).find(':eq(0)').text(),
			jQuery('#desinventarURL').val() + '/wms/' + jQuery('#prmMapId').val() + '/effects/',
			{
				format : 'png',
				layers : LayerName
			},
			{
				isBaseLayer :false
			}
		);
		map.addLayer(layer);
		jQuery('#MapAdminLayers div').each(function() {
			var layername = jQuery(this).find(':eq(1)').text().trim();
			var layer = new OpenLayers.Layer.WMS(
				jQuery(this).find(':eq(0)').text(),
				jQuery('#desinventarURL').val() + '/wms/' + jQuery('#prmMapId').val() + '/' + layername + '/',
				{
					format : 'png',
					layers : layername
				},
				{
					isBaseLayer : false
				}
			);
			layer.setVisibility(false);
			map.addLayer(layer);
		});
	});

	// 2009-08-07 (jhcaiced) Calculate Zoom of Area an Show Map Centered
	var pt1 = new OpenLayers.LonLat(minx, miny);
	var pt2 = new OpenLayers.LonLat(maxx, maxy);
	var bounds = new OpenLayers.Bounds();
	pt1.transform(prj1, map.getProjectionObject());
	pt2.transform(prj1, map.getProjectionObject());
	bounds.extend(pt1);
	bounds.extend(pt2);
	zoom = base.getZoomForExtent(bounds);
	map.setCenter(bounds.getCenterLonLat(), zoom);
	if (lon == 0 && lat == 0) {
		map.zoomToMaxExtent();
	}
	
	jQuery('#MapTitle').val(jQuery('#defaultMapTitle').text());
	jQuery('#linkRestoreMapTitle').click(function() {
		jQuery('#MapTitle').val(jQuery('#defaultMapTitle').text());
	});
}

function addRowToTable() {
	var tbl = $('tbl_range');
	var lastRow = tbl.rows.length;
	// if there's no header row in the table, then iteration = lastRow + 1
	var iteration = lastRow;
	var row = tbl.insertRow(lastRow);
	var cellBeg = row.insertCell(0);
	var textNode = document.createTextNode(iteration + 1);
	cellBeg.appendChild(textNode);
	// left cell
	var cellLeft = row.insertCell(1);
	var lim = document.createElement("input");
	lim.setAttribute('type', 'text');
	lim.setAttribute('size', '5');
	lim.setAttribute('class', 'line');
	lim.setAttribute('name', '_M+limit['+ iteration +']');
	lim.setAttribute('id', '_M+limit['+ iteration +']');
	lim.setAttribute('onBlur', "miv=parseInt($('_M+limit["+ iteration -1+"]').value)+1; $('_M+legend["+ iteration +"]').value='{-#mbetween#-} '+ miv +' - '+ this.value;");
	cellLeft.appendChild(lim);
	// center cell
	var cellCenter = row.insertCell(2);
	var leg = document.createElement('input');
	leg.setAttribute('type', 'text');
	leg.setAttribute('size', '20');
	leg.setAttribute('class', 'line');
	leg.setAttribute('name', '_M+legend['+ iteration +']');
	leg.setAttribute('id', '_M+legend['+ iteration +']');
	cellCenter.appendChild(leg);
	// right cell
	var cellRight = row.insertCell(3);
	var ic = document.createElement('input');
	ic.setAttribute('type', 'text');
	ic.setAttribute('size', '3');
	ic.setAttribute('class', 'line');
	ic.setAttribute('id', '_M+ic['+ iteration +']');
	ic.setAttribute('style', 'background:#00ff00;');
	ic.setAttribute('onClick', "showColorGrid2('_M+color["+ iteration +"]','_M+ic["+ iteration +"]');");
	cellRight.appendChild(ic);
	var col = document.createElement('input');
	col.setAttribute('type', 'hidden');
	col.setAttribute('name', '_M+color['+ iteration +']');
	col.setAttribute('id', '_M+color['+ iteration +']');
	col.setAttribute('value', '00ff00;');
	cellRight.appendChild(col);
}

function removeRowFromTable() {
	var tbl = $('tbl_range');
	var lastRow = tbl.rows.length;
	if (lastRow > 2)
		tbl.deleteRow(lastRow - 1);
}

function dechex(dec) {
	var Char_hexadecimales = "0123456789ABCDEF";
	var low = dec % 16;
	var high = (dec - low)/16;
	hex = "" + Char_hexadecimales.charAt(high) + Char_hexadecimales.charAt(low);
	return hex;
}

function hexdec(hex) {
	return parseInt(hex,16);
}

function genColors() {
	var tbl = $('tbl_range');
	var cnt = tbl.rows.length - 2;
	var a = $('_M+color[0]').value;
	var z = $('_M+color['+ cnt +']').value;
	var a1 = hexdec(a.substring(1,3));	var z1 = hexdec(z.substring(1,3));
	var a2 = hexdec(a.substring(3,5));	var z2 = hexdec(z.substring(3,5));
	var a3 = hexdec(a.substring(5,7));	var z3 = hexdec(z.substring(5,7));
	var m1 = ((z1 - a1) / cnt);
	var m2 = ((z2 - a2) / cnt);
	var m3 = ((z3 - a3) / cnt);
	for (i=0; i <= cnt; i++)
	{
		h1 = dechex(a1 + (m1 * i));
		h2 = dechex(a2 + (m2 * i));
		h3 = dechex(a3 + (m3 * i));
		val = "#" + h1 + h2 + h3;
		$('_M+color['+ i + ']').value = val;
		$('_M+ic['+ i + ']').style.backgroundColor = val;
	} //for
}

	/*
	// Microsoft Virtual Earth Base Layer
	var virtualearth = new OpenLayers.Layer.VirtualEarth("Microsoft Virtual Earth", { 'sphericalMercator': true });
	map.addLayer(virtualearth);
	*/


	/*
	// Use a Global Risk Data Platform (http://preview.grid.unep.ch/) WMS..
	var bk1 = new OpenLayers.Layer.WMS("Flood Risk..",
		"http://preview.grid.unep.ch:8080/geoserver/wms",
		{'layers': 'preview:fl_risk', 'transparent': true},
		{'isBaseLayer':true});
	bk1.setVisibility(false);
	map.addLayer(bk1);
	*/

	/*
	// 2009-07-11 (jhcaiced) This layer doesn't work with Spherical Mercator projection (????)
	var bk2 = new OpenLayers.Layer.WMS("GRDP - Population 2007",
		"http://metafunctions.grid.unep.ch/cgi-bin/mapserv",
		{ map:'/www/preview/previewims/etc/preview_ims.map', 'transparent':true, 'format':'png', 
			 'sphericalMercator': true, layers:'popdens'},
		{'isBaseLayer':true});
	bk2.setVisibility(false);
	map.addLayer(bk2);
	*/
	
	/* Metacarta Base Layers			
	// 2009-02-06 (jhcaiced) Metacarta Satellite doesn't work with Spherical Mercator, this needs to be fixed !!
	var met2 = new OpenLayers.Layer.WMS("** Metacarta Satellite",
			"http://labs.metacarta.com/wms-c/Basic.py", {layers:'satellite', 'transparent':true, 'format':'png' },
			{'isBaseLayer':true });
	met2.setVisibility(false);
	map.addLayer(met2);
	*/
	
	/* 
	// Layers from georiesgo/geosemantica
	// Change map
	vlayer = new OpenLayers.Layer.Vector("{-#tdrawpoint#-}");
	map.addLayer(vlayer);
	map.addControl(new OpenLayers.Control.EditingToolbar(vlayer));
	var pegeo = new OpenLayers.Layer.WMS("** Geología Perú",
			"http://per.geosemantica.net/services/mapserv.exe?MAP=2c1bc078-13e6-4734-863a-5636442a2e30_wms.map", 
			{'layers':'geoutm_shp', 'transparent':true, 'format':'png' }, {'isBaseLayer':false});
	pegeo.setVisibility(false);
	map.addLayer(pegeo);
	var cosis = new OpenLayers.Layer.WMS("** Amenaza Sísmica Colombia",
			"http://col.geosemantica.net/services/mapserv.exe?map=8ec9a29a-f170-4023-9fe0-bf3d38c5ee2e.map",
			{'layers':'aisacel_1_shp', 'transparent':true, 'format':'png' }, {'isBaseLayer':false});
	cosis.setVisibility(false);
	map.addLayer(cosis);
	var queryTarget = $('queryOut');
	WMSToolbar = new OpenLayers.Control.WMSToolbar({queryTarget: queryTarget});
	map.addControl(WMSToolbar);
	//parent.document.getElementById('frmwait').innerHTML='';
	
	*/
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyUserAccount()
{
	jQuery('form.UserChangePasswd .status').hide();

	jQuery('form.UserChangePasswd .btnSubmit').click(function() {
		jQuery('form.UserChangePasswd').trigger('submit');
	});
	
	jQuery('form.UserChangePasswd .btnCancel').click(function() {
		jQuery('body').trigger('cmdUserAccountHide');
	});

	jQuery('form.UserChangePasswd').submit(function() {
		var form = jQuery(this);
		var UserPasswd = jQuery('.UserPasswd', form).val();
		var UserPasswd2 = jQuery('.UserPasswd2', form).val();
		var UserPasswd3 = jQuery('.UserPasswd3', form).val();
		var bContinue = true;
		jQuery('.status', form).hide();
		if (UserPasswd == '' || UserPasswd2 == '' || UserPasswd3 == '')
		{
			jQuery('#msgEmptyFields', form).show();
			bContinue = false;
		}
		
		if (bContinue && (UserPasswd2 != UserPasswd3) )
		{
			jQuery('#msgPasswdDoNotMatch', form).show();
			bContinue = false;
		}
		
		if (bContinue)
		{
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd         : 'cmdUserPasswdUpdate',
					UserId      : jQuery('#desinventarUserId').val(),
					UserPasswd  : hex_md5(UserPasswd),
					UserPasswd2 : hex_md5(UserPasswd2)
				},
			    function(data)
			    {
			    	jQuery('.status', form).hide();
			    	if (parseInt(data.Status) > 0)
			    	{
						doUserAccountReset();
						jQuery('#msgPasswdUpdated', form).show();
					}
					else
					{
						jQuery('#msgInvalidPasswd', form).show();
					}
					setTimeout(function() {
						jQuery('.status',form).hide();
						if (parseInt(data.Status) > 0)
						{
							jQuery('body').trigger('cmdUserAccountHide');
						}
					}, 2500);
				},
				'json'
			);
		}
		else
		{
			setTimeout(function() {
				jQuery('.status',form).hide();
			}, 2500);
		}
		return false;
	});

	jQuery('body').on('cmdUserAccountShow', function() {
		doUserAccountReset();
		var w = Ext.getCmp('wndUserAccount');
		if (w != undefined)
		{
			w.show();
		}
	});

	jQuery('body').on('cmdUserAccountHide', function() {
		var w = Ext.getCmp('wndUserAccount');
		if (w != undefined)
		{
			w.hide();
		}
	});

	doUserAccountReset();
	doUserAccountCreate();
}

function doUserAccountReset()
{
	var form = jQuery('form.UserChangePasswd');
	jQuery('.status', form).hide();
	jQuery('.UserPasswd', form).val('');
	jQuery('.UserPasswd2', form).val('');
	jQuery('.UserPasswd3', form).val('');
}

function doUserAccountCreate()
{
	jQuery('#divUserAccountWindow').each(function() {
		var w = new Ext.Window({id:'wndUserAccount',
			el:'divUserAccountWindow', layout:'fit', x:200, y:100, width:400, height:200, 
			closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
			items: new Ext.Panel({ contentEl: 'divUserAccountContent', autoScroll: true })
		});
	});
}
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyUserLogin()
{
	// hide all status messages on start
	doUserLoginUpdateMsg('');

	jQuery('body').on('cmdUserLoginShow', function() {
		doUserLoginShow();
	});

	jQuery('body').on('cmdUserLogout', function() {
		doUserLogout();
	});

	jQuery('body').on('UserLoggedIn',function() {
		jQuery('body').trigger('cmdWindowReload');
	});

	jQuery('body').on('UserLoggedOut',function() {
		jQuery('body').trigger('cmdWindowReload');
	});
	
	jQuery("#btnUserLoginSend").click(function() {
		jQuery('#frmUserLogin').trigger('submit');
		return false;
	});

	jQuery('#fldUserPasswd').keypress(function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13)
		{
			jQuery('#btnUserLoginSend').trigger('click');
		}
	});

	jQuery('#frmUserLogin').submit(function() {
		var UserId     = jQuery('#fldUserId').val();
		var UserPasswd = jQuery("#fldUserPasswd").val();

		doUserLoginUpdateMsg('');

		if (UserId == '' || UserPasswd == '')
		{
			doUserLoginUpdateMsg('#msgEmptyFields');
		}
		else
		{
			jQuery.post(jQuery('#desinventarURL').val() + '/',
				{
					'cmd'        : 'cmdUserLogin',
					'RegionId'   : jQuery('#desinventarRegionId').val(),
					'UserId'     : UserId,
					'UserPasswd' : hex_md5(UserPasswd)
			    },
				function(data)
				{
					if (parseInt(data.Status) > 0)
					{
						doUserLoginUpdateMsg('#msgUserLoggedIn');
						jQuery('#fldUserId').val('');
						jQuery("#fldUserPasswd").val('');

						// Update UserInfo Fields...
						doUserUpdateInfo(data.User);

						Ext.getCmp('wndUserLogin').hide();
						// Trigger Event and Update User Menu etc.
						jQuery('body').trigger('cmdMainWindowUpdate');
					}
					else
					{
						doUserLoginUpdateMsg("#msgInvalidPasswd");
					}
				},
				'json'
			);
		}
		return false;
	});
	//Initialization code
	doUserLoginCreate();
} //onReadyUserLogin()

function doUserLoginCreate()
{
	// User Login Window
	jQuery('#divUserLoginWindow').each(function() {
		var w = new Ext.Window({id:'wndUserLogin',
			el:'divUserLoginWindow', layout:'fit', x:300, y:100, width:500, height:300, 
			closeAction:'hide', plain: true, animCollapse: false, constrainHeader: true,
			items: new Ext.Panel({ contentEl: 'divUserLoginContent', autoScroll: true })
		});
	});
} //doUserLoginCreate()

function doUserUpdateInfo(User)
{
	jQuery('#desinventarUserId').val(User.Id);
	jQuery('#desinventarUserFullName').val(User.FullName);
	jQuery('#desinventarUserRole').val(User.Role);
	jQuery('#desinventarUserRoleValue').val(User.RoleValue);
}

function doUserLogout()
{
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			'cmd'        : 'cmdUserLogout',
			'RegionId'   : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				doUserLoginUpdateMsg("#msgUserLoggedOut");
				// After login, clear passwd field
				jQuery('#fldUserId').val('');
				jQuery('#fldUserPasswd').val('');
				doUserUpdateInfo(data.User);
				// Trigger Event, used to update menu or reload page...
				jQuery('body').trigger('cmdMainWindowUpdate');
			}
			else
			{
				doUserLoginUpdateMsg("#msgInvalidLogout");
			}
		},
		'json'
	);
} //doUserLogout()

function doUserLoginUpdateMsg(msgId)
{
	// Hide all status Msgs (class="status")
	jQuery('div.UserLogin .status').hide();
	if (msgId != '')
	{
		// Show specified message(s)
		jQuery('div.UserLogin ' + msgId).show();
	}
	return(true);
} //doUserLoginUpdateMsg()

function doUserLoginShow()
{
	doUserLoginUpdateMsg();
	jQuery('#fldUserId').val('');
	jQuery('#fldUserPasswd').val('');
	var w = Ext.getCmp('wndUserLogin');
	if (w != undefined)
	{
		w.show();
	}
} //doUserLoginShow()
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyUserPermAdmin()
{
	doUserPermAdminSetup();
} //onReadyUserPermAdmin()

function doUserPermAdminSetup()
{
	// Database Create
	var w = new Ext.Window({id:'wndUserPermAdmin', 
		el: 'divUserPermAdminWin', layout:'fit', 
		width:500, height:250, modal:false,
		closeAction:'hide', plain: false, animCollapse: true, constrainHeader: true,
		items: new Ext.Panel({
			contentEl: 'divUserPermAdminContent',
			autoScroll: true
		}),
		buttons: [
		] //buttons
	});

	// Cancel Button - Hide Window and do nothing
	jQuery('#btnUserPermAdminCancel').click(function() {
		Ext.getCmp('wndUserPermAdmin').hide();
		return false;
	});

	// Send Button - Validate data and send command to backend
	jQuery('#btnUserPermAdminSend').click(function() {
		iReturn = doUserPermAdminValidate();
		if (iReturn > 0)
		{
			jQuery('#btnUserPermAdminSend').attr('readonly', true);
			jQuery.post(
				jQuery('#desinventarURL').val() + '/',
				{
					cmd : 'cmdDatabaseSetUserAdmin',
					RegionId : jQuery('#desinventarRegionId').val(),
					UserId   : jQuery('#fldUserPermAdmin_UserId').val()
				},
				function(data)
				{
					jQuery('.clsUserPermAdminStatus').hide();
					if (parseInt(data.Status) > 0)
					{
						doUserPermAdminUpdateUserAdmin(data.UserAdmin);
						jQuery('#btnUserPermAdminSend').attr('readonly', false);
						jQuery('#txtUserPermAdminOk').show();
					}
					else
					{
						jQuery('#txtUserPermAdminError').show();
					}
					setTimeout(function() {
						jQuery('.clsUserPermAdminStatus').hide();
					}, 2500);
				},
				'json'
			);
		}
		else
		{
			jQuery('#txtUserPermAdminFormError').show();
			setTimeout(function() {
				jQuery('.clsUserPermAdminStatus').hide();
			}, 2500);
		}
		return false;
	});

	// Hide Send button until the combobox has been populated
	jQuery('#btnUserPermAdminSend').hide();
} //doUserPermAdminSetup()

function doUserPermAdminShow()
{
	jQuery('.clsUserPermAdminStatus').hide();

	// In this windows, always update information
	doUserPermAdminPopulateLists();

	Ext.getCmp('wndUserPermAdmin').show();
	jQuery('#fldUserPermAdmin_UserId').focus();
} //doUserPermAdminShow()

function doUserPermAdminValidate()
{
	var iReturn = 1;
	if ( (iReturn > 0) && (jQuery('#fldUserPermAdmin_UserId').val() == '') )
	{
		iReturn = -1;
	}
	return iReturn;
}

function doUserPermAdminUpdateUserAdmin(UserAdmin)
{
	jQuery('#fldUserPermAdmin_UserId').val(UserAdmin.UserId);
	var txtUserAdmin = UserAdmin.UserFullName;
	if (UserAdmin.UserEMail != '')
	{	
		txtUserAdmin = txtUserAdmin + '<br />' + UserAdmin.UserEMail;
	}
	jQuery('#txtUserPermAdminCurrent').html(txtUserAdmin);
}

function doUserPermAdminPopulateLists()
{
	// async UserList field
	jQuery.post(
		jQuery('#desinventarURL').val() + '/',
		{
			cmd : 'cmdGetUserPermList',
			RegionId : jQuery('#desinventarRegionId').val()
		},
		function(data)
		{
			if (parseInt(data.Status) > 0)
			{
				jQuery('#fldUserPermAdmin_UserId').empty();
				jQuery.each(data.UserList, function(key, value) {
					jQuery('#fldUserPermAdmin_UserId').append(
						jQuery('<option>', { value : key }).text(value)
					);
				});
				doUserPermAdminUpdateUserAdmin(data.UserAdmin);
				jQuery('#btnUserPermAdminSend').show();
			}
		},
		'json'
	);
} //doUserPermAdminPopulateLists()

