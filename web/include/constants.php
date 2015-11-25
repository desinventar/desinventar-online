<script language="php">
/*
 **********************************************
 DesInventar - http://www.desinventar.org  
 (c) 1998-2012 Corporación OSSO
 **********************************************
*/
define('CONST_REGIONACTIVE', 1);
define('CONST_REGIONPUBLIC', 2);
define('FALSE', 0);
define('TRUE' , 1);

define('ERROR'  , 0);
define('WARNING', 1);

// base.db - DI's Basic database, predefined events etc.
define('CONST_DBBASE', VAR_DIR .'/main/base.db');
// region.db - Emtpy database skeleton for Regions
define('CONST_DBNEWREGION', VAR_DIR . '/main/desinventar.db');

// database directory
define('CONST_DBREGIONDIR', VAR_DIR . '/database');

// dicore objects
define('DI_EVENT',     1);
define('DI_CAUSE',     2);
define('DI_GEOLEVEL',	3);
define('DI_GEOGRAPHY',	4);
define('DI_DISASTER',	5);
define('DI_DBINFO',    6);
define('DI_DBLOG',     7);
define('DI_USER',      8);
define('DI_REGION',    9);
define('DI_EEFIELD',  10);
define('DI_EEDATA',   11);

// dicore command
define('CMD_NEW',			1);
define('CMD_UPDATE',		2);
define('CMD_DELETE',		3);

// Normal Answer Codes
define('STATUS_NO'  , 0);
define('STATUS_YES' , 1);
define('STATUS_OK'  , 1);

// Error Codes
define('ERR_NO_ERROR'         ,  1);
define('ERR_DEFAULT_ERROR'    , -1);
define('ERR_UNKNOWN_ERROR'    , -1);
define('ERR_INVALID_COMMAND'  , -2);
define('ERR_OBJECT_EXISTS'    , -3);
define('ERR_NO_DATABASE'      , -4);
define('ERR_INVALID_PASSWD'   , -5);
define('ERR_ACCESS_DENIED'    , -6);
define('ERR_OBJECT_NOT_FOUND' , -7);
define('ERR_CONSTRAINT_FAIL'  , -8);
define('ERR_FILE_NOT_FOUND'   , -9);
define('ERR_TABLE_LOCKED'     , -10);
define('ERR_UPLOAD_FAILED'    , -11);
define('ERR_NO_CONNECTION'    , -99);
define('ERR_USER_DUPLICATE_ID' , -100);
define('ERR_WITH_WARNINGS'     , -101);
define('ERR_LANGUAGE_NO_CHANGE', -120);
define('ERR_LANGUAGE_INVALID'  , -121);
define('ERR_INVALID_ZIPFILE'   , -130);

// Graph Types
define('GRAPH_HISTOGRAM_TEMPORAL'   , 0);
define('GRAPH_HISTOGRAM_EVENT'      , 1);
define('GRAPH_HISTOGRAM_CAUSE'      , 2);
define('GRAPH_HISTOGRAM_GEOGRAPHY'  , 100);
define('GRAPH_COMPARATIVE_EVENT'    , 3);
define('GRAPH_COMPARATIVE_CAUSE'    , 4);
define('GRAPH_COMPARATIVE_GEOGRAPHY', 200);

define('GEOGRAPHY_ONLY_ACTIVE', true);
define('GEOGRAPHY_ALL', false);
</script>
