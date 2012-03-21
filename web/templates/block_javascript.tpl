<script type="text/javascript">
	/*
	var	w;
	var	s;
	*/

	// Layout, buttons and internal windows - UI DesConsultar module
	//Ext.onReady(function()
	function initializeExt()
	{
		/*
		// ==> Results Configuration Windows
		// Data
		var datw;
		var datb = Ext.get('dat-btn');
		if (datb != null)
		{
			datb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!datw) {
					}
					datw.show(this);
				}
			}); // data window
		}
		// Statistics
		var stdw;
		var stdb = Ext.get('std-btn');
		if (stdb != null)
		{
			stdb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!stdw) {
					}
					stdw.show(this);
				}
			}); // statistics
		}
		// Graphic
		var grpw;
		var grpb = Ext.get('grp-btn');
		if (grpb != null)
		{
			grpb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!grpw) {
					}
					grpw.show(this);
				}
			}); // Graphics
		}
		// Map
		var map; // Map Object
		var mapw;
		var mapb = Ext.get('map-btn');
		if (mapb != null)
		{
			mapb.on('click', function() {
				if (validateQueryDefinition()) {
					if (!mapw) {
					}
					mapw.show(this);
				}
			}); // Map
		}

		*/

	}
	//); // Ext.onReady()
	// end ExtJS object
	
	// Find all Effects fields enable by saved query
	window.onload = function()
	{
		// select optimal height in results frame
		//varhgt = screen.height * 360 / 600;
		//$('dcr').style = "height:"+ hgt + "px;"
		{-foreach name=ef1 key=k item=i from=$ef1-}
			{-assign var="ff" value="D_$k"-}
			{-if $qd.$ff[0] != ''-}
				enadisEff('{-$k-}', true);
				showeff('{-$qd.$ff[0]-}', 'x{-$k-}', 'y{-$k-}');
			{-/if-}
		{-/foreach-}
		{-foreach name=sec key=k item=i from=$sec-}
			{-assign var="sc" value="D_$k"-}
			{-if $qd.$sc[0] != ''-}
				{-foreach name=sc2 key=k2 item=i2 from=$i[3]-}
					{-assign var="ff" value="D_$k2"-}
					{-if $qd.$ff[0] != ''-}
						enadisEff('{-$k2-}', true);
						showeff('{-$qd.$ff[0]-}', 'x{-$k2-}', 'y{-$k2-}');
					{-/if-}
				{-/foreach-}
				enadisEff('{-$k-}', true);
			{-/if-}
		{-/foreach-}
		{-foreach name=ef3 key=k item=i from=$ef3-}
			{-assign var="ff" value="D_$k"-}
			{-if $qd.$ff[0] != ''-}
				enadisEff('{-$k-}', true);
				showeff('{-$qd.$ff[0]-}', 'x{-$k-}', 'y{-$k-}');
			{-/if-}
		{-/foreach-}
	} //function
	//var geotree = new CheckTree('geotree');
</script>
