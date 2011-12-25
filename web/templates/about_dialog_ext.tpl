{-config_load file="$lg.conf" section="grpAboutDialog"-}
<!-- Show Dialog window -->
<div id="divAboutDialogWin" class="x-hidden">
	<div class="x-window-header">
	</div>
	<div id="divAboutDialogContent">
		<table border="0">
			<tr>
				<td>
					<img src="{-$desinventarURL-}/images/di_logo.png">
				</td>
				<td>
					<p style="font-size: 16pt;" align="center">DesInventar {-$desinventarVersion-}</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					{-#msgAboutDialogEMail#-}
					<hr />
					{-#msgAboutDialogCopyright#-}
				</td>
			</tr>
		</table>
	</div>
</div>
