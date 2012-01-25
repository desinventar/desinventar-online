/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

function onReadyData() {
	jQuery('body').on('click', '.linkGridGotoCard', function() {
		var DisasterId = jQuery(this).attr('DisasterId');
		jQuery('body').trigger('cmdDatacardShow');
		jQuery('#cardsRecordSource').val('data');
		jQuery('#cardsRecordCount').val(jQuery('#prmDataPageRecords').val());
		jQuery('#cardsRecordNumber').val(jQuery(this).attr('rowindex'));
		setDICardFromId(jQuery('#desinventarRegionId').val(), DisasterId);
		Ext.getCmp('wndDatacard').show();
		return false;
	});
	
	// Page Number Fields
	jQuery('#DataCurPage').keydown(function(event) {
		if(event.keyCode == 13) {
			doDataDisplayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});
	
	// Navigation Buttons
	jQuery('#btnGridGotoFirstPage').click(function() {
		doDataDisplayPage(1);
	});
	jQuery('#btnGridGotoPrevPage').click(function() {
		doDataDisplayPage('prev');
	});
	jQuery('#btnGridGotoNextPage').click(function() {
		doDataDisplayPage('next');
	});
	jQuery('#btnGridGotoLastPage').click(function() {
		doDataDisplayPage(jQuery('#prmDataPageCount').val());
	});

	jQuery('body').on('cmdViewDataParams', function() {
		Ext.getCmp('wndViewDataParams').show();
	});
	jQuery('body').on('cmdViewDataUpdate', function() {
		doDataUpdate();
	});
	jQuery('body').trigger('cmdViewDataUpdate');

}

function doDataUpdate()
{
	jQuery('#tblDataRows tr:even').addClass('under');
	// Set Number of Records in Current Displayed Page
	jQuery('#prmDataPageRecords').val(jQuery('#tblDataRows tr').size());
}

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
} //function

