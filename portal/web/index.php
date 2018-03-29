<?php
/*
 DesInventar - http://www.desinventar.org
 (c) Corporación OSSO
*/
require_once('../include/loader.php');
$portaltype = getParameter('portaltype', 'desinventar');
$t->assign('desinventarPortalType', $portaltype);
$t->display('index.tpl');
