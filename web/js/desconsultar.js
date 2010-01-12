
function validateQueryDefinition() {
	var iReturn = 1;
	var prmMinYear;
	var MinYear;
	var prmMaxYear;
	var MaxYear;
	prmMinYear = jQuery("#prmMinYear").val();
	prmMaxYear = jQuery("#prmMaxYear").val();
	MinYear = jQuery("#queryBeginYear").val();
	MaxYear = jQuery("#queryEndYear").val();
	if ( (MinYear == '') || (MinYear < prmMinYear) ) {
		jQuery("#queryBeginYear").val(prmMinYear);
	}
	if ( (MaxYear == '') || (MaxYear > prmMaxYear) ) {
		jQuery("#queryEndYear").val(prmMaxYear);
	}
	return iReturn;
};

