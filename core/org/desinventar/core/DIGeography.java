/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.sql.Connection;
import java.sql.Statement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Hashtable;
import java.util.logging.Level;

public class DIGeography extends DIObject {
	public static final int READBYCODE = 1;
	public static final int READBYID   = 2;
	
	public DIGeography() {
		sTableName = "Geography";
		sPermPrefix = "GEOGRAPHY";
		sFieldKeyDef = "GeographyId/STRING";
		sFieldDef    = "GeographyCode/STRING," +
		               "GeographyName/STRING," +
		               "GeographyLevel/INTEGER," +
		               "GeographyActive/BOOLEAN";
		createFields(sFieldKeyDef,sFieldDef);
	}
	
	public DIGeography(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	}
	
	public DIGeography(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		set("GeographyId", sMyId);
	}
	
	public DIGeography(String sMySessionUUID, int myLevel, 
	                   String myCode, String myName) {
		this(sMySessionUUID, "");
		set("GeographyLevel", new Integer(myLevel));
		set("GeographyCode", myCode);
		set("GeographyName", myName);
	}

	public DIGeography(String sMySessionUUID, 
	                   int myLevel, 
	                   String myCode, 
	                   String myName, 
	                   String myParentCode) {
		this(sMySessionUUID, myLevel, myCode, myName);
		set("GeographyId", buildGeographyId(sMySessionUUID, myCode, myParentCode, myLevel));
	}

	public String getDeleteQuery() {
		//return "DELETE FROM " + sRegionUUID + "_Geography WHERE GeographyId='" + sGeographyId + "'";
		return "UPDATE " + sRegionUUID + "_" + sTableName + 
		  " SET GeographyActive=false WHERE GeographyId='" + getString("GeographyId") + "'";
	}
	
	public static DIGeography fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIGeography o;
		o = new DIGeography(sMySessionUUID);
		o.copyFromHashtable(oMyData);
		return o;
	}

	public boolean readFromDB(Connection conn, String sMyValue, int iMyType) {
		boolean bReturn;
		String sQuery;
		
		bReturn = true;
		try {
			Statement stmt = conn.createStatement();
			sQuery = "SELECT * FROM " + sRegionUUID + "_" + sTableName + " WHERE ";
			if (iMyType == READBYCODE) {
				sQuery = sQuery + " GeographyCode='" + sMyValue + "'";
			}
			if (iMyType == READBYID) {
				sQuery = sQuery + " GeographyId='" + sMyValue + "'";
			}
			ResultSet rs = stmt.executeQuery(sQuery);
			bReturn = false;
			while (rs.next()) {
				set("GeographyId"    , Util.getStringValueFromRecordSet(rs,"GeographyId"));
				set("GeographyCode"  , Util.getStringValueFromRecordSet(rs,"GeographyCode"));
				set("GeographyName"  , Util.getStringValueFromRecordSet(rs,"GeographyName"));
				set("GeographyLevel" , new Integer(rs.getInt("GeographyLevel")));
				set("GeographyActive", new Boolean(rs.getBoolean("GeographyActive")));
				bReturn = true;
			}
			rs.close();
			stmt.close();
			rs = null;
			stmt = null;
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIGeography::readFromDB => " + sMyValue, e);
			bReturn = false;
		}
		sQuery = null;
		return bReturn;
	} // readFromDB

	public boolean readFromDBByCode(Connection conn, String sMyCode) {
		return readFromDB(conn, sMyCode, READBYCODE);
	} // loadFromDBByCode

	public boolean readFromDBById(Connection conn, String sMyId) {
		return readFromDB(conn, sMyId, READBYID);
	} // loadFromDBByCode

	public String buildGeographyId(String sMySessionUUID,
	                                      String sMyCode, 
	                                      String sMyParentCode, 
	                                      int iMyLevel) {
		String sId = "";		
		boolean bError;
		String sParentId = "";
		int iId;
		DIGeography oParent;
		DIGeography oChild;
		Statement stmt;
		String sQuery;
		Connection conn = DIServer.getDbConnection(sMySessionUUID);

		bError = false;

		oChild = new DIGeography(sMySessionUUID);
		if (oChild.readFromDBByCode(conn, sMyCode)) {
			DIServer.logger.warning("DIGeography : Geography Code already in database => " + sMyCode);
			bError = true;
		}
		
		if ((!bError) && (sMyParentCode.length() > 0)) {
			oParent = new DIGeography(sMySessionUUID);
			if (oParent.readFromDBByCode(conn, sMyParentCode)) {
				sParentId = oParent.getString("GeographyId");
			} else {
				DIServer.logger.warning("DIGeography : ParentCode doesn't exist : " + sMyCode + " Parent : " + sMyParentCode);
				sParentId = "";
				bError = true;
			}
		}

		if (!bError) {
			sQuery = "SELECT COUNT(*) AS COUNT FROM " + sRegionUUID + "_Geography WHERE GeographyLevel=" + iMyLevel + "";
			if (sParentId != "") {
				sQuery += " AND GeographyId LIKE '" + sParentId + "%'";
			}
			try {
				stmt = conn.createStatement();
				ResultSet rs = stmt.executeQuery(sQuery);
				rs.next();
				iId = rs.getInt("COUNT");
				iId++;
				rs.close();
				stmt.close();
				sId = padNumber(iId, 5);
			} catch (Exception e) {
				DIServer.logger.warning("DIGeography => " + sMyCode);
				e.printStackTrace();
				bError = true;
			}
		}
		sId = sParentId + sId;
		if (!bError) {
			if (oChild.readFromDBById(conn, sId)) {
				DIServer.logger.warning("DIGeography : GeographyId already exists in database : " + sId);
				sId = "";
			}
		}
		return sId;
	}

	public static String padNumber(int iNumber, int iLen) {
		String sNumber;
		sNumber = new Integer(iNumber).toString();
		while (sNumber.length() < iLen) {
			sNumber = "0" + sNumber;
		}
		return sNumber;
	}	

	public int validateInsert() {
		int iReturn = Constants.ERR_NO_ERROR;
		
		iReturn = validateNotNullStr(iReturn, Constants.ERR_GEOGRAPHY_NULL_ID, getString("GeographyCode"));
		iReturn = validateUniqueStr(iReturn, Constants.ERR_GEOGRAPHY_DUPLICATED_ID, "GeographyCode", getString("GeographyCode"));
		iReturn = validateNotNullStr(iReturn, Constants.ERR_GEOGRAPHY_NULL_NAME, getString("GeographyName"));
		iReturn = validateNotNullInt(iReturn, Constants.ERR_GEOGRAPHY_NULL_LEVEL, getInteger("GeographyLevel"));
		return iReturn;
	}

	public int validateDelete() {
		int iReturn = Constants.ERR_NO_ERROR;
		String sGeographyId = getString("GeographyId");
		try {
			String sQuery = "SELECT * FROM " + sRegionUUID + "_Disaster WHERE GeographyId='" + sGeographyId + "'";
			Statement st = conn.createStatement();
			ResultSet rs = st.executeQuery(sQuery);
			if (rs.next()) {
				iReturn = Constants.ERR_CONSTRAINT_FAIL;
				DIServer.logger.warning("DIGeography::validateDelete() : Can't Delete : " + sGeographyId);
			}
			rs.close();
			st.close();
		} catch (SQLException e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DIGeography::validateDelete()", e);
		}
		return iReturn;
	} // validateDelete()
}

