<?php
/*
 DesInventar - http://www.desinventar.org
 (c) 1998-2012 Corporacion OSSO
*/

require_once('include/loader.php');
require_once('include/query.class.php');
require_once('include/maps.class.php');

require_once 'include/geolevel_operations.php';

use DesInventar\Legacy\Model\GeographyCarto;
use DesInventar\Legacy\Model\GeographyLevel;

$post = $_POST;
$get = $_GET;
$cmd = getParameter('cmd', '');
$RegionId = getParameter('_REG', getParameter('RegionId', getParameter('r', '')));

if ($RegionId == '') {
    exit();
}

$data = form2data($post, $RegionId);
$us->open($RegionId);

switch ($cmd) {
    case 'insert':
        // Create new GeoLevel and GeoCarto Objects
        $o = new GeographyLevel($us);
        $o->setFromArray($data);
        $levid = $o->getMaxGeoLevel();
        if ($levid >= 0) {
            $levid ++;
        }
        $o->set('GeoLevelId', $levid);
        $c = new GeographyCarto($us);
        $c->setFromArray($data);
        $c->set('GeoLevelId', $o->get('GeoLevelId'));
        // Save to database
        $gl = $o->insert();
        $gl = $c->insert();
        if (!iserror($gl)) {
            $t->assign('ctl_msginslev', true);
        } else {
            $t->assign('ctl_errinslev', true);
            $t->assign('insstatlev', $gl);
            if ($gl == ERR_OBJECT_EXISTS) {
                $t->assign('ctl_chkname', true);
                $t->assign('chkname', true);
            } //if
        } //else
        break;
    case 'update':
        $o = new GeographyLevel($us);
        $c = new GeographyCarto($us);
        // Set primary key values
        $o->set('GeoLevelId', $data['GeoLevelId']);
        $o->load();
        $c->set('GeoLevelId', $data['GeoLevelId']);
        $c->load();
        // Update with data from FORM
        $o->setFromArray($data);
        $c->setFromArray($data);
        // Save to database
        $gl = $o->update();
        $gl = $c->update();
        if (!iserror($gl)) {
            $t->assign('ctl_msgupdlev', true);
        } else {
            $t->assign('ctl_errupdlev', true);
            $t->assign('updstatlev', $gl);
            if ($gl == ERR_OBJECT_EXISTS) {
                $t->assign('ctl_chkname', true);
                $t->assign('chkname', true);
            }
        }
        break;
    case 'chkname':
        $t->assign('ctl_chkname', true);
        if ($us->q->isvalidObjectName($get['GeoLevelId'], $get['GeoLevelName'], DI_GEOLEVEL)) {
            $t->assign('chkname', true);
        }
        break;
    case 'list':
        $t->assign('ctl_levlist', true);
        $t->assign('levl', $us->q->loadGeoLevels('', -1, false));
        break;
    case 'cmdDBInfoGeoLevel':
        $t->assign('ctl_admingeo', true);
        $t->assign('ctl_levlist', true);
        $t->assign('levl', $us->q->loadGeoLevels('', -1, false));
        break;
    default:
        break;
} //switch
$t->assign('reg', $RegionId);
$t->assign('dic', $us->q->queryLabelsFromGroup('DB', $lg));
$t->force_compile   = true;
$t->display('geolevel.tpl');
