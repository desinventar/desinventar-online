/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

public class Constants {
	// Disaster Commands
	public static final int CMD_NEW    = 1;
	public static final int CMD_UPDATE = 2;
	public static final int CMD_DELETE = 3;

	// Object Ids
	public static final int DI_EVENT     =  1;
	public static final int DI_CAUSE     =  2;
	public static final int DI_GEOLEVEL  =  3;
	public static final int DI_GEOGRAPHY =  4;
	public static final int DI_DISASTER  =  5;
	public static final int DI_DBINFO    =  6;
	public static final int DI_DBLOG     =  7;
	public static final int DI_USER      =  8;
	public static final int DI_REGION    =  9;
	public static final int DI_EEFIELD   = 10;
	public static final int DI_EEDATA    = 11;
	
	// Extra Effect Types
	public static final String EE_UNDEFINED_TYPE =  "";
	public static final String EE_TEXT           =  "TEXT";
	public static final String EE_INTEGER        =  "INTEGER";
	public static final String EE_FLOAT          =  "FLOAT";
	public static final String EE_CURRENCY       =  "CURRENCY";
	public static final String EE_DATE           =  "DATE";
	
	// Basic Perms
	public static final int PERM_NONE   = 0;
	public static final int PERM_READ   = 1;
	public static final int PERM_UPDATE = 2;
	public static final int PERM_INSERT = 3;
	public static final int PERM_DELETE = 4;
	public static final int PERM_GRANT  = 5;
	
	// Server Commands
	public static final int SERVER_SHUTDOWN         = 0;
	public static final int SERVER_SAVESESSIONS     = 1;
	public static final int SERVER_LOADSESSIONS     = 2;
	public static final int SERVER_AWAKECONNECTIONS = 3;
	
	// Return Values
	public static final int ERR_NO_ERROR         =   1;
	public static final int ERR_UNKNOWN_ERROR    =  -1;
	public static final int ERR_INVALID_COMMAND  =  -2;
	public static final int ERR_OBJECT_EXISTS    =  -3;
	public static final int ERR_NO_DATABASE      =  -4;
	public static final int ERR_INVALID_PASSWD   =  -5;
	public static final int ERR_ACCESS_DENIED    =  -6;
	public static final int ERR_OBJECT_NOT_FOUND =  -7;
	public static final int ERR_CONSTRAINT_FAIL  =  -8;
	public static final int ERR_IMPORT_ERROR     =  -9;
	public static final int ERR_NO_SESSION       = -10;

	// Import/Validate Error Codes
	public static final int ERR_NO_GEOGRAPHY               = -100;
	public static final int ERR_NULL_ID                    = -101;
	public static final int ERR_DUPLICATED_ID              = -102;
	public static final int ERR_NO_REF                     = -103;
	public static final int ERR_DUPLICATED_NAME            = -104;
	
	public static final int ERR_CAUSE_NULL_ID              = -110;
	public static final int ERR_CAUSE_DUPLICATED_ID        = -111;
	public static final int ERR_CAUSE_CANNOT_DELETE        = -112;
	
	public static final int ERR_EVENT_NULL_ID              = -120;
	public static final int ERR_EVENT_NULL_NAME            = -121;
	public static final int ERR_EVENT_DUPLICATED_ID        = -122;
	public static final int ERR_EVENT_DUPLICATED_NAME      = -123;
	public static final int ERR_EVENT_CANNOT_DELETE        = -124;
	
	public static final int ERR_GEOLEVEL_NULL_ID           = -130;
	public static final int ERR_GEOLEVEL_DUPLICATED_ID     = -131;
	public static final int ERR_GEOLEVEL_NULL_NAME         = -132;
	public static final int ERR_GEOLEVEL_DUPLICATED_NAME   = -133;
	public static final int ERR_GEOLEVEL_DUPLICATED_LEVEL  = -134;
	
	public static final int ERR_GEOGRAPHY_NULL_ID          = -140;
	public static final int ERR_GEOGRAPHY_DUPLICATED_ID    = -141;
	public static final int ERR_GEOGRAPHY_NULL_NAME        = -142;
	public static final int ERR_GEOGRAPHY_NULL_LEVEL       = -143;
	
	public static final int ERR_DISASTER_NULL_ID           = -150;
	public static final int ERR_DISASTER_DUPLICATED_ID     = -151;
	public static final int ERR_DISASTER_NULL_SERIAL       = -152;
	public static final int ERR_DISASTER_DUPLICATED_SERIAL = -153;
	public static final int ERR_DISASTER_NULL_TIME         = -154;
	public static final int ERR_DISASTER_NULL_SOURCE       = -155;
	public static final int ERR_DISASTER_NULL_STATUS       = -156;
	public static final int ERR_DISASTER_NULL_CREATION     = -157;
	public static final int ERR_DISASTER_NULL_LASTUPDATE   = -158;
	public static final int ERR_DISASTER_NO_GEOGRAPHY      = -159;
	public static final int ERR_DISASTER_NO_EVENT          = -160;
	public static final int ERR_DISASTER_NO_CAUSE          = -161;
	public static final int ERR_DISASTER_NO_EFFECTS        = -162;

	public static final int ERRTYPE_NONE                   =    1;
	public static final int ERRTYPE_ERROR                  = -200;
	public static final int ERRTYPE_WARNING                = -201;	
} // Constants

                                                                                                                        