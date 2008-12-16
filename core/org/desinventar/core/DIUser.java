/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.*;
import java.util.logging.*;

public class DIUser extends DIObject {
	public String  sUserName;
	public String  sUserEMail;
	public String  sUserPasswd;
	public String  sUserFullName;
	public String  sUserLangCode;
	public String  sUserCountry;
	public String  sUserCity;
	public String  sUserCreationDate;
	public boolean bUserActive;
	
	public DIUser(String sSessionUUID) {
		setSession(sSessionUUID, true);
		sUserName = "";
		sUserEMail = "";
		sUserPasswd = "";
		sUserFullName = "";
		sUserLangCode = "";
		sUserCountry = "";
		sUserCity = "";
		sUserCreationDate = Util.getNowDateString();
		bUserActive = false;
	} // constructor

	public DIUser(String sMySessionUUID, String sMyUserName) {
		this(sMySessionUUID);
		sUserName     = sMyUserName;
	}
	
	public DIUser(String sMyUserName, String sMyUserFullName, String sMyUserEMail) {
		this(sMyUserName);
		sUserFullName = sMyUserFullName;
		sUserEMail    = sMyUserEMail;
	}
	
	public String getSelectQuery() {
		return "SELECT * FROM Users WHERE UserName='" + sUserName + "'";
	}
	
	public String getInsertQuery() {
		return "INSERT INTO Users (UserName, UserEMail, UserPasswd, UserFullName, UserLangCode, UserCountry, UserCity, UserCreationDate, UserActive) " +
		  " VALUES ('" + sUserName					+ "','" +
										 sUserEMail					+ "','" +
										 sUserPasswd				+ "','" +
										 sUserFullName			+ "','" +
										 sUserLangCode			+ "','" +
										 sUserCountry				+ "','" +
										 sUserCity					+ "','" +
										 sUserCreationDate	+ "'," +
										 bUserActive				+ ")";
	}
	
	public String getDeleteQuery() {
		// Don't delete, just mark it as deleted
		return "UPDATE Users SET UserActive=false WHERE UserName='" + sUserName + "'";
	}
	
	public String getUpdateQuery() {
		String sQuery = "UPDATE Users SET " +
		       " UserEMail='"         + sUserEMail         + "', " +
		       " UserPasswd='"        + sUserPasswd        + "', " +
		       " UserFullName='"      + sUserFullName      + "', " +
		       " UserCountry='"       + sUserCountry       + "', " +
		       " UserCity='"          + sUserCity          + "', " +
		       " UserActive="         + bUserActive        +
		       " WHERE UserName = '" 	+ sUserName + "'";
		//DIServer.logger.finest(sQuery);
		return sQuery;       
	}
	
	public Hashtable toHashtable() {
		Hashtable result = new Hashtable();
		result.put("UserName",          sUserName);
		result.put("UserEMail",         sUserEMail);
		result.put("UserPasswd",        sUserPasswd);
		result.put("UserFullName",      sUserFullName);
		result.put("UserLangCode",      sUserLangCode);
		result.put("UserCountry",       sUserCountry);
		result.put("UserCity",          sUserCity);
		result.put("UserCreationDate",  sUserCreationDate);
		result.put("UserActive",        new Boolean(bUserActive));
		return result;		
	} // toHashtable
	
	public static DIUser fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIUser e;
		e = new DIUser(sMySessionUUID);
		e.sUserName           = (String)oMyData.get("UserName");
		e.sUserEMail          = (String)oMyData.get("UserEMail");
		e.sUserPasswd         = (String)oMyData.get("UserPasswd");
		e.sUserFullName       = (String)oMyData.get("UserFullName");
		e.sUserLangCode       = (String)oMyData.get("UserLangCode");
		e.sUserCountry        = (String)oMyData.get("UserCountry");
		e.sUserCity           = (String)oMyData.get("UserCity");
		e.sUserCreationDate   = (String)oMyData.get("UserCreationDate");
		e.bUserActive         = ((Boolean)oMyData.get("UserActive")).booleanValue();
		return e;
	}

	public int setFields(ResultSet rs) {
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			sUserName          = Util.getStringValueFromRecordSet(rs,"UserName");
			sUserEMail         = Util.getStringValueFromRecordSet(rs,"UserEMail");
			sUserPasswd        = Util.getStringValueFromRecordSet(rs,"UserPasswd");
			sUserFullName      = Util.getStringValueFromRecordSet(rs,"UserFullName");
			sUserLangCode      = Util.getStringValueFromRecordSet(rs,"UserLangCode");
			sUserCountry       = Util.getStringValueFromRecordSet(rs,"UserCountry");
			sUserCity          = Util.getStringValueFromRecordSet(rs,"UserCity");
			sUserCreationDate  = Util.getStringValueFromRecordSet(rs,"UserCreationDate");
			bUserActive        = rs.getBoolean("UserActive");
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIUser::setFields : ERROR : " + sUserName, e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		return iReturn;
	} 

	public String getPermPrefix() {
		return "USER";
	}

}
