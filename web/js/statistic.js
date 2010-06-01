function onReadyStatistic() {
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

	jQuery('#lst_dis tr:even').addClass('under');
	
	jQuery('#StatCurPage').keydown(function(event) {
		if(event.keyCode == 13) {
			doStatDisplayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});
}

function doStatDisplayPage(page) {
	var mypag = page;
	now = parseInt($('StatCurPage').value);
	if (page == 'prev') {
		mypag = now - 1;
	} else if (page == 'next') {
		mypag = now + 1;
	}
	var NumberOfPages = jQuery('#prmStatNumberOfPages').val();
	if (mypag < 1 || mypag > NumberOfPages) {
		return false;
	}
	$('StatCurPage').value = mypag ;
	var RecordsPerPage = jQuery('#prmStatRecordsPerPage').val();
	var lsAjax = new Ajax.Updater('lst_dis', 'statistic.php', {
		method: 'post', parameters: 'r=' + jQuery('#prmStatRegionId').val + '&page='+ mypag +'&rxp=' + RecordsPerPage +'&sql=' + jQuery('#prmStatQueryDef').val() + '&fld=' + jQuery('#prmStatFieldList').val() + '&geo=' + jQuery('#prmStatGeography').val(),
		onLoading: function(request) {
			$(div).innerHTML = "<img src='loading.gif>";
		}
	} );
}

function doStatOrderByField(field, dir) {
	var lsAjax = new Ajax.Updater('lst_dis', 'statistic.php', {
		method: 'post', 
		parameters: 'r=' + jQuery('#prmStatRegionId').val() + '&page='+ $('StatCurPage').value +'&rxp=' + jQuery('#prmStatRecordsPerPage').val() + '&sql=' + jQuery('#prmStatQueryDef').val() + '&fld=' + jQuery('#prmStatFieldList').val() + '&ord='+ field + '&geo=' + jQuery('#prmStatGeography').val() + '&dir='+ dir,
		onLoading: function(request) {
			$(div).innerHTML = "<img src='loading.gif>";
		}
	} );
}

/*
window.onload = function() {
	var qrydet = parent.document.getElementById('querydetails');
	var qdet = "";
	{-foreach key=k item=i from=$qdet-}
		{-if $k == "GEO"-}qdet += "<b>{-#geo#-}:</b> {-$i-}";{-/if-}
		{-if $k == "EVE"-}qdet += "<b>{-#eve#-}:</b> {-$i-}";{-/if-}
		{-if $k == "CAU"-}qdet += "<b>{-#cau#-}:</b> {-$i-}";{-/if-}
		{-if $k == "EFF"-}qdet += "<b>{-#eff#-}:</b> {-$i-}";{-/if-}
		{-if $k == "BEG"-}qdet += "<b>{-#beg#-}:</b> {-$i-}";{-/if-}
		{-if $k == "END"-}qdet += "<b>{-#end#-}:</b> {-$i-}";{-/if-}
		{-if $k == "SOU"-}qdet += "<b>{-#sou#-}:</b> {-$i-}";{-/if-}
		{-if $k == "SER"-}qdet += "<b>{-#ser#-}:</b> {-$i-}";{-/if-}
	{-/foreach-}
	qrydet.innerHTML = qdet;
}
*/
