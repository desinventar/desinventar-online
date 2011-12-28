/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2011 Corporacion OSSO
*/
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

	jQuery('#tblStatRows tr:even').addClass('under');
	
	jQuery('#StatCurPage').keydown(function(event) {
		if(event.keyCode == 13) {
			doStatDisplayPage(jQuery(this).val());
		} else {
			return blockChars(event, jQuery(this).val(), 'integer:');
		}
	});

} //function

function doStatDisplayPage(page) {
	var mypag = page;
	now = parseInt(jQuery('#StatCurPage').val());
	if (page == 'prev') {
		mypag = now - 1;
	} else if (page == 'next') {
		mypag = now + 1;
	}
	var NumberOfPages = jQuery('#prmStatNumberOfPages').val();
	if ((mypag < 1) || (mypag > NumberOfPages)) {
		return false;
	}
	jQuery('#StatCurPage').val(mypag);
	var RecordsPerPage = jQuery('#prmStatRecordsPerPage').val();

	jQuery('#tblStatRows').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(jQuery('#desinventarURL').val() + '/statistic.php',
		{r : jQuery('#prmStatRegionId').val(),
		 page : mypag,
		 rxp  : RecordsPerPage,
		 sql  : jQuery('#prmStatQueryDef').val(),
		 fld  : jQuery('#prmStatFieldList').val(),
		 geo  : jQuery('#prmStatGeography').val()
		},
		function(data) {
			jQuery('#tblStatRows').html(data);
		}
	);
} //function

function doStatOrderByField(field, dir) {
	jQuery('#tblStatRows').html('<img src="' + jQuery('#desinventarURL').val() + '/images/loading.gif" alt="" />');
	jQuery.post(jQuery('#desinventarURL').val() + '/statistic.php',
		{r  : jQuery('#prmStatRegionId').val(),
		 page : $('StatCurPage').value,
		 rxp  : jQuery('#prmStatRecordsPerPage').val(),
		 sql  : jQuery('#prmStatQueryDef').val(),
		 fld  : jQuery('#prmStatFieldList').val(),
		 ord  : field,
		 geo  : jQuery('#prmStatGeography').val(),
		 dir  : dir
		},
		function(data) {
			jQuery('#tblStatRows').html(data);
		}
	);
} //function

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
} //function
