<script language="php">
/*
 **********************************************
 DesInventar - http://www.desinventar.org  
 (c) 1999-2009 Corporacion OSSO
 **********************************************
*/

define('CONST_REGIONACTIVE', 1);
define('CONST_REGIONPUBLIC', 2);
define('FALSE', 0);
define('TRUE' , 1);

// Database File Locations
// core.db - Users, Regions, Auths.. 
define('CONST_DBCORE', VAR_DIR ."/core.db");
// base.db - DI's Basic database, predefined events etc.
define('CONST_DBBASE', VAR_DIR ."/base.db");
// region.db - Emtpy database skeleton for Regions
define('CONST_DBREGION', VAR_DIR . "/desinventar.db");

// dicore objects
define ("DI_EVENT",			1);
define ("DI_CAUSE",			2);
define ("DI_GEOLEVEL",	3);
define ("DI_GEOGRAPHY",	4);
define ("DI_DISASTER",	5);
define ("DI_DBINFO",		6);
define ("DI_DBLOG",			7);
define ("DI_USER",			8);
define ("DI_REGION",		9);
define ("DI_EEFIELD",	 10);
define ("DI_EEDATA",	 11);

// dicore command
define ("CMD_NEW",			1);
define ("CMD_UPDATE",		2);
define ("CMD_DELETE",		3);

// Error Codes
define ("ERR_NO_ERROR",          1);
define ("ERR_DEFAULT_ERROR",    -1);
define ("ERR_UNKNOWN_ERROR",    -1);
define ("ERR_INVALID_COMMAND",  -2);
define ("ERR_OBJECT_EXISTS",    -3);
define ("ERR_NO_DATABASE",      -4);
define ("ERR_INVALID_PASSWD",   -5);
define ("ERR_ACCESS_DENIED",    -6);
define ("ERR_OBJECT_NOT_FOUND", -7);
define ("ERR_CONSTRAINT_FAIL",  -8);
define ("ERR_FILE_NOT_FOUND",   -9);
define ("ERR_NO_CONNECTION",    -99);

</script>
