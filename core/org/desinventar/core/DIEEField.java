/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.sql.*;
import java.util.Hashtable;
import java.util.logging.Level;

public class DIEEField extends DIObject {
	public String  sEEFieldId;
	public String  sEEGroupId;
	public String  sEEFieldLabel;
	public String  sEEFieldDesc;
	public String  sEEFieldType;
	public int     iEEFieldSize;
	public int     iEEFieldOrder;
	public boolean bEEFieldActive;
	public boolean bEEFieldPublic;
	
	public DIEEField() {
		sEEFieldId     = "";
		sEEGroupId     = "";
		sEEFieldLabel  = "";
		sEEFieldDesc   = "";
		sEEFieldType   = "";
		iEEFieldSize   = 0;
		iEEFieldOrder  = -1;
		bEEFieldActive = true;
		bEEFieldPublic = true;
	}
	
	public DIEEField(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	}
	
	public DIEEField(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		sEEFieldId = sMyId;
	}
	
	public String getSelectQuery() {
		return "SELECT * FROM " + sRegionUUID + "_EEField WHERE EEFieldId='" + sEEFieldId + "'";
	}
	
	public String getInsertQuery() {
		return "INSERT INTO " + sRegionUUID + "_EEField (EEFieldId) VALUES ('" + sEEFieldId + "')";
	}
	
	public String getDeleteQuery() {
		return "DELETE FROM " + sRegionUUID + "_EEField WHERE EEFieldId='" + sEEFieldId + "'";
	}
	
	public String getUpdateQuery() {
		return "UPDATE " + sRegionUUID + "_EEField SET " +
		       " EEGroupId='"      + sEEGroupId       + "', " +
		       " EEFieldLabel='"   + sEEFieldLabel    + "', " +
		       " EEFieldDesc='"    + sEEFieldDesc     + "', " +
		       " EEFieldType='"    + sEEFieldType     + "', " +
		       " EEFieldSize="     + iEEFieldSize     + " , " +
		       " EEFieldOrder="    + iEEFieldOrder    + " , " +
		       " EEFieldActive="   + bEEFieldActive   + " , " +
		       " EEFieldPublic="   + bEEFieldPublic   + "   " +
		       " WHERE EEFieldId = '" + sEEFieldId + "'";
	}

	public PreparedStatement buildUpdateQuery() {
		PreparedStatement ps=null;
		try {
			String sQuery = "UPDATE " + sRegionUUID + "_EEField SET " +
			                " EEGroupId       =?, " +
			                " EEFieldLabel    =?, " +
			                " EEFieldDesc     =?, " +
			                " EEFieldType     =?, " +
			                " EEFieldSize     =?, " +
			                " EEFieldOrder    =?, " +
			                " EEFieldActive   =?, " + 
			                " EEFieldPublic   =?  " +
			                " WHERE EEFieldId =?";
			ps = conn.prepareStatement(sQuery);
			ps.setString( 1, sEEGroupId);
			ps.setString( 2, sEEFieldLabel);
			ps.setString( 3, sEEFieldDesc);
			ps.setString( 4, sEEFieldType);
			ps.setInt(    5, iEEFieldSize);
			ps.setInt(    6, iEEFieldOrder);
			ps.setBoolean(7, bEEFieldActive);
			ps.setBoolean(8, bEEFieldPublic);
			ps.setString( 9, sEEFieldId);
		} catch (SQLException e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : buildUpdateQuery() => ", e);
		}
		return ps;
	}

	public Hashtable toHashtable() {
		Hashtable result = new Hashtable();
		result.put("EEGroupId"    , sEEGroupId);
		result.put("EEFieldId"    , sEEFieldId);
		result.put("EEFieldLabel" , sEEFieldLabel);
		result.put("EEFieldDesc"  , sEEFieldDesc);
		result.put("EEFieldType"  , sEEFieldType);
		result.put("EEFieldSize"  , new Integer(iEEFieldSize));
		result.put("EEFieldOrder" , new Integer(iEEFieldOrder));
		result.put("EEFieldActive", new Boolean(bEEFieldActive));
		result.put("EEFieldPublic", new Boolean(bEEFieldPublic));
		return result;		
	} // toHashtable

	public static DIEEField fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIEEField o;
		o = new DIEEField(sMySessionUUID);
		o.sEEFieldId     = (String)oMyData.get("EEFieldId");
		o.sEEGroupId     = (String)oMyData.get("EEGroupId");
		o.sEEFieldLabel  = (String)oMyData.get("EEFieldLabel");
		o.sEEFieldDesc   = (String)oMyData.get("EEFieldDesc");
		o.sEEFieldType   = (String)oMyData.get("EEFieldType");
		o.iEEFieldSize   = ((Integer)oMyData.get("EEFieldSize")).intValue();
		o.iEEFieldOrder  = ((Integer)oMyData.get("EEFieldOrder")).intValue();
		o.bEEFieldActive = ((Boolean)oMyData.get("EEFieldActive")).booleanValue();
		o.bEEFieldPublic = ((Boolean)oMyData.get("EEFieldPublic")).booleanValue();
		return o;
	}
	public int setFields(ResultSet rs) {
		int iReturn = 1;
		try {
			while (rs.next()) {
				sEEFieldId     = Util.getStringValueFromRecordSet(rs,"EEFieldId");
				sEEGroupId     = Util.getStringValueFromRecordSet(rs,"EEGroupId");
				sEEFieldLabel  = Util.getStringValueFromRecordSet(rs,"EEFieldlabel");
				sEEFieldDesc   = Util.getStringValueFromRecordSet(rs,"EEFieldDesc");
				sEEFieldType   = Util.getStringValueFromRecordSet(rs,"EEFieldType");
				iEEFieldSize   = rs.getInt("EEFieldSize");
				iEEFieldOrder  = rs.getInt("EEFieldOrder");
				bEEFieldActive = rs.getBoolean("EEFieldActive");
				bEEFieldPublic = rs.getBoolean("EEFieldPublic");
			} 
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIEEFields::setFields => " + sEEFieldId, e);
			iReturn = Constants.ERR_NO_ERROR; // Error
		}
		return iReturn;
	} 

	public String getPermPrefix() {
		return "EEFIELD";
	}
	public String getTableSuffix() {
		return "EEField";
	}
	
	public int afterCreate() {
		int iReturn = Constants.ERR_NO_ERROR;
		/* Create COLUMN in _EEData Table */
		PreparedStatement ps=null;
		try {
			String sDataType = "";
			if ((sEEFieldType.equals("STRING")) ||
			    (sEEFieldType.equals(Constants.EE_TEXT)) ) {
				sDataType = "TEXT";
			}
			if (sEEFieldType.equals(Constants.EE_DATE)) {
				sDataType = "DATETIME";
			}
			if (sEEFieldType.equals(Constants.EE_INTEGER)) {
				sDataType = "NUMERIC(30,0)";
			}
			if (sEEFieldType.equals(Constants.EE_FLOAT)) {
				sDataType = "NUMERIC(30,4)";
			}
			if (sEEFieldType.equals(Constants.EE_CURRENCY)) {
				sDataType = "NUMERIC(30,4)";
			}
			String sQuery = "ALTER TABLE " + sRegionUUID + "_EEData " +
			                " ADD COLUMN (" + sEEFieldId + " " + sDataType + ")";
			ps = conn.prepareStatement(sQuery);
			ps.executeUpdate();
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : buildUpdateQuery() => ", e);
		}
		return iReturn;
	}
}
