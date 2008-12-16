/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.sql.*;
import java.util.Hashtable;
import java.util.logging.Level;

public class DICause extends DIObject {
	public DICause() {
		sTableName = "Cause";
		sPermPrefix = "CAUSE";
		sFieldKeyDef = "CauseId/STRING";
		sFieldDef    = "CauseLocalName/STRING" + "," + 
					   "CauseLocalDesc/STRING" + "," +
					   "CauseActive/BOOLEAN" + "," +
					   "CausePreDefined/BOOLEAN" + "," +
					   "CauseCreationDate/DATETIME";
		createFields(sFieldKeyDef,sFieldDef);
		set("CauseId", "");
		set("CauseLocalName", "");
		set("CauseLocalDesc", "");
		set("CauseActive", new Boolean(true));
		set("CausePreDefined", new Boolean(false));
		set("CauseCreationDate", Util.getNowDateString());
	} // constructor
	
	public DICause(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	} // constructor
	
	public DICause(String sMySessionUUID, String sMyLocalName) {
		this(sMySessionUUID);
		set("CauseLocalName", sMyLocalName);
		setIdByLocalName();
	}
	
	public void setIdByLocalName() {
		try {
			int iCount = 0;
			Statement st = conn.createStatement();
			st.executeUpdate("SET NAMES utf8");
			String sQuery = "SELECT * FROM " + sRegionUUID + "_" + sTableName +
                " WHERE CauseLocalName='" + (String)get("CauseLocalName") + "'";
			ResultSet rs = st.executeQuery(sQuery);
			rs.last();
			iCount = rs.getRow();
			if (iCount > 0) {
				rs.beforeFirst();
				while (rs.next()) {
					set("CauseId", Util.getStringValueFromRecordSet(rs,"CauseId"));
					set("CausePreDefined", new Boolean(rs.getBoolean("CausePreDefined")));
					set("CauseCreationDate", rs.getString("CauseCreationDate"));
				}
			} else {
				// Search in Predefined Causes (dicore.Cause)
				String sLangCode = Region.getLangCode(sSessionUUID,sRegionUUID);
				sQuery = "SELECT * FROM DICause WHERE " + 
				            " CauseLangCode='" + sLangCode + "'" + " AND ( " +
				            " CauseLocalName='" + (String)get("CauseLocalName") + "'" + " OR " +
				            " CauseDI6Name='" + (String)get("CauseLocalName") + "')";
				st = DIServer.dicoreconn.createStatement();
				rs = st.executeQuery(sQuery);
				rs.last();
				iCount = rs.getRow();
				if (iCount > 0) {
					rs.beforeFirst();
					while (rs.next()) {
						set("CauseId", Util.getStringValueFromRecordSet(rs,"CauseId"));
						set("CauseLocalName", Util.getStringValueFromRecordSet(rs,"CauseLocalName"));
						set("CauseLocalDesc", Util.getStringValueFromRecordSet(rs,"CauseLocalDesc"));
						set("CausePreDefined", new Boolean(true));
						set("CauseCreationDate", rs.getString("CauseCreationDate"));
					}
				}
			}
			rs.close();
			st.close();
		} catch (SQLException e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DICause::setdByLocalName()", e);
		}
		if (((String)get("CauseId")).length() > 0) {
		} else {
			set("CauseId", get("CauseLocalName"));
		}
	}
	
	public String getDeleteQuery() {
		return "UPDATE " + sRegionUUID + "_" + sTableName + " SET CauseActive=false " +
		  " WHERE " + getWhereSubQuery();
	}
	
	public static DICause fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DICause o;
		o = new DICause(sMySessionUUID);
		o.copyFromHashtable(oMyData);
		return o;
	}

	public int validateInsert() {
		int iReturn = Constants.ERR_NO_ERROR;
		iReturn = validateNotNullStr(iReturn, Constants.ERR_CAUSE_NULL_ID, (String)get("CauseId"));
		iReturn = validateUniqueStr(iReturn, Constants.ERR_CAUSE_DUPLICATED_ID, "CauseId", (String)get("CauseId"));
		return iReturn;
	}
	
	public int validateDelete() {
		int iReturn = Constants.ERR_NO_ERROR;
		String sCauseId = (String)get("CauseId");
		try {
			String sQuery = "SELECT * FROM " + sRegionUUID + "_Disaster WHERE CauseId='" + sCauseId + "'";
			Statement st = conn.createStatement();
			ResultSet rs = st.executeQuery(sQuery);
			if (rs.next()) {
				iReturn = Constants.ERR_CAUSE_CANNOT_DELETE;
				DIServer.logger.warning("DICause::validateDelete() : Can't Delete Cause : " + sCauseId);
			}
			rs.close();
			st.close();
		} catch (SQLException e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DICause::validateDelete()", e);
		}
		return iReturn;
	} // validateDelete()
} // DICause
