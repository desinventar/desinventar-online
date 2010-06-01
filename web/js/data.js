function onReadyData() {
	/*
	window.onload = function() {
		var qrydet = parent.document.getElementById('querydetails');
		var qdet = "";
		{-foreach key=k item=i from=$qdet-}
			{-if $k == "GEO"-}qdet += "<b>{-#geo#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "EVE"-}qdet += "<b>{-#eve#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "CAU"-}qdet += "<b>{-#cau#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "EFF"-}qdet += "<b>{-#eff#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "BEG"-}qdet += "<b>{-#beg#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "END"-}qdet += "<b>{-#end#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "SOU"-}qdet += "<b>{-#sou#-}:</b> {-$i-} ";{-/if-}
			{-if $k == "SER"-}qdet += "<b>{-#ser#-}:</b> {-$i-} ";{-/if-}
		{-/foreach-}
		qrydet.innerHTML = qdet;
		onReadyData();
	} */

	jQuery('.linkGridGotoCard').click(function() {
		var DisasterId = jQuery(this).attr('DisasterId');
		setDIForm(DisasterId);
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
		doDataDisplayPage(jQuery('#prmDataNumberOfPages').val());
	});
}

function setDIForm(prmDisasterId) {
	//parent.w.collapse();
	difw.show();
	setDICardFromId(jQuery('#prmDataRegionId').val(), prmDisasterId, '');
}

function doDataDisplayPage(page) {
	var mypag = page;
	var now = parseInt(jQuery('#DataCurPage').val());
	if (page == 'prev') {
		mypag = now - 1;
	} else if (page == 'next') {
		mypag = now + 1;
	}
	var NumberOfPages = jQuery('#prmDataNumberOfPages').val();
	if ((mypag < 1) || (mypag > NumberOfPages)) {
		// Out of Range Page
	} else {
		jQuery('#DataCurPage').val(mypag);
		var RegionId = jQuery('#prmDataRegionId').val();
		var RecordsPerPage = jQuery('#prmDataRecordsPerPage').val();
		var QueryDef = jQuery('#prmDataQueryDef').val();
		var FieldList = jQuery('#prmDataFieldList').val();
		var lsAjax = new Ajax.Updater('lst_dis', 'data.php', {
			method: 'post', parameters: 'r=' + RegionId + '&page='+ mypag +'&RecordsPerPage=' + RecordsPerPage + '&sql=' + QueryDef + '&fld=' + FieldList,
			onLoading: function(request) {
				$(div).innerHTML = "<img src='loading.gif>";
			},
			onComplete: function(request) {
				// Reload the jQuery functions on the new DOM elements...
				onReadyData();
			}
		});
	}
} //function

