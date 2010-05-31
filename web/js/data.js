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
	jQuery('#pp').keydown(function(event) {
		if(event.keyCode == 13) {
			displayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});
	
	// Navigation Buttons
	jQuery('#btnGridGotoFirstPage').click(function() {
		displayPage(1);
	});
	jQuery('#btnGridGotoPrevPage').click(function() {
		displayPage('prev');
	});
	jQuery('#btnGridGotoNextPage').click(function() {
		displayPage('next');
	});
	jQuery('#btnGridGotoLastPage').click(function() {
		displayPage(jQuery('#prmNumberOfPages').val());
	});
}

function setDIForm(prmDisasterId) {
	//parent.w.collapse();
	difw.show();
	setDICardFromId(jQuery('#prmRegionId').val(), prmDisasterId, '');
}

function displayPage(page) {
	var mypag = page;
	now = parseInt($('pp').value);
	if (page == 'prev') {
		mypag = now - 1;
	} else if (page == 'next') {
		mypag = now + 1;
	}
	var NumberOfPages = jQuery('#prmNumberOfPages').val();
	if (mypag < 1 || mypag > NumberOfPages) {
		return false;
	}
	$('pp').value = mypag ;
	var RegionId = jQuery('#prmRegionId').val();
	var RecordsPerPage = jQuery('#prmRecordsPerPage').val();
	var QueryDef = jQuery('#prmQueryDef').val();
	var FieldList = jQuery('#prmFieldList').val();
	var lsAjax = new Ajax.Updater('lst_dis', 'data.php', {
		method: 'post', parameters: 'r=' + RegionId + '&page='+ mypag +'&RecordsPerPage=' + RecordsPerPage + '&sql=' + QueryDef + '&fld=' + FieldList,
		onLoading: function(request) {
			$(div).innerHTML = "<img src='loading.gif>";
		}
	} );
}
