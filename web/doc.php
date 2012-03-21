<script language="php">
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/
	require_once('include/loader.php');
	if (isset($_GET['m']))
	{
		$mod = $_GET['m'];
	}
	else
	{
		$mod = 'DesInventarInfo';
	}
	if (isset($_GET['p']))
	{
		$pag = $_GET['p'];
		if ($pag == 'datacards')
		{
			$t->assign('eff', $us->q->queryLabelsFromGroup('Effect', $lg));
			$t->assign('sec', $us->q->queryLabelsFromGroup('Sector', $lg));
		}
		if ($pag == 'events')
		{
			$t->assign('eve', $us->q->loadEvents('BASE', null, $lg, $us->RegionLangIsoCode));
		}
		if ($pag == 'causes')
		{
			$t->assign('cau', $us->q->loadCauses('BASE', null, $lg, $us->RegionLangIsoCode));
		}
	}
	else
	{
		$pag = 'main';
	}
	$t->assign('ctl_page', $pag);
	$t->assign('ctl_module', $mod);
	if ($mod == 'DesInventarInfo' && $pag == 'intro')
	{
		$show = $us->q->queryLabel('MainPage', 'DesInventar', $lg);
	}
	else
	{
		$show = $us->q->queryLabel($mod, $pag, $lg);
	}
	if (isset($_GET['title']) && $_GET['title'] == 'no')
	{
		$t->assign('title', false);
	}
	else
	{
		$t->assign('title', true);
	}
	if (is_array($show))
	{
		$t->assign('pagetitle', $show['DictTranslation']);
		$t->assign('pagedesc', $show['DictBasDesc']);
		$t->assign('pagefull', $show['DictFullDesc']);
		$t->assign('urlinfo', $show['DictTechHelp']);
	}
	$t->display('doc.tpl');
</script>
