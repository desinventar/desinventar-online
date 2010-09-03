
function validateQueryDefinition() {
	var iReturn = 1;
	var prmQueryMinYear = jQuery("#prmQueryMinYear").val();
	var prmQueryMaxYear = jQuery("#prmQueryMaxYear").val();
	var MinYear = jQuery("#queryBeginYear").val();
	var MaxYear = jQuery("#queryEndYear").val();
	if ( (MinYear == '') || (MinYear < prmQueryMinYear) ) {
		jQuery("#queryBeginYear").val(prmQueryMinYear);
	}
	if ( (MaxYear == '') || (MaxYear > prmQueryMaxYear) ) {
		jQuery("#queryEndYear").val(prmQueryMaxYear);
	}
	return iReturn;
};
