<div class="divBlock divBlockSouthAmerica" style="display:none;">
	<table>
		<tr>
			<td align="right">
				<a href="#" id="btnMainWindow" target="_blank" rel="noopener">
					<img id="btnMainWindow2" src="{-$desinventarURLPortal-}/images/b_desinventar3.jpg" border="0" width="150" height="44" alt="" />
				</a>
			</td>
		</tr>
		<tr>
			<td>
				{-assign var="imgCANMap" value="subreg_can_$lang.png"-}
				<img id="imgCAN" src="{-$desinventarURLPortal-}/images/{-$imgCANMap-}" alt="" usemap="#srcan" style="border-style:none" />
				<map id="srcan" name="srcan">
					<area shape="poly" coords="173,39,175,41,155,62,155,69,193,105,216,105,215,142,221,152,219,153,214,145,189,145,189,152,193,152,197,156,186,157,186,162,190,167,190,200,180,200,186,192,186,184,161,184,138,161,113,161,98,145,114,129,114,97,105,86,116,74,121,78,137,62,137,55,144,55,145,51,130,36,75,37,75,24,127,25,145,47,162,48"
						href="#" alt="COL" title="{-#tcountryCOL#-}" />
					<area shape="poly"  coords="176,42,171,49,172,59,166,67,174,74,182,66,177,63,177,53,190,40,205,56,225,55,243,37,243,30,301,30,301,41,297,42,244,42,227,58,230,63,241,63,245,56,265,58,284,77,284,92,274,102,279,108,263,125,251,126,242,116,238,121,245,127,245,137,253,137,232,157,226,157,215,143,216,105,193,105,155,70,155,62"
						href="#" alt="VEN" title="{-#tcountryVEN#-}" />
					<area shape="poly" coords="105,154,94,164,72,141,27,140,27,153,73,153,89,167,84,172,84,188,97,188,82,202,93,213,97,215,104,207,104,197,111,197,134,175,134,161,113,161"
						href="#" alt="ECU" title="{-#tcountryECU#-}" />
					<area shape="poly" coords="86,207,79,214,94,231,94,246,95,249,77,266,47,265,47,277,75,277,75,274,97,252,137,321,163,321,185,343,199,330,199,282,193,271,181,271,182,254,160,255,150,240,188,200,178,200,186,192,185,185,161,184,138,161,134,162,134,175,111,196,104,198,104,208,96,215"
						href="#" alt="PER" title="{-#tcountryPER#-}" />
					<area shape="poly" coords="195,271,211,272,224,258,238,258,238,278,247,287,259,288,270,299,283,298,283,325,307,326,306,336,312,343,313,355,306,361,299,354,277,354,264,367,264,386,246,387,245,392,227,381,215,395,208,395,208,373,200,365,206,359,197,350,161,386,124,386,124,373,159,373,163,377,194,347,188,341,199,330,200,282"
						href="#" alt="BOL" title="{-#tcountryBOL#-}" />
					<area shape="default" nohref="nohref" alt="" />
				</map>
			</td>
		</tr>
	</table>
	{-*
	<hr size="1" />
	<a href="#" id="linkPortalGAR2011">GAR 2011</a>
	<br />
	{-assign var="myLang" value="$lang"-}
	{-if $lang == 'fre'-}
		{-assign var="myLang" value="eng"-}
	{-/if-}
	{-assign var="myFile" value="block_gar2011_1_$myLang.tpl"-}
	{-include file="$myFile"-}
	*-}
</div>
