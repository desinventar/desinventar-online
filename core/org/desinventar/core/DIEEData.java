/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.sql.*;
import java.util.Hashtable;

public class DIEEData extends DIObject {
	
	public DIEEData(String sMySessionUUID) {
		sTableName="EEData";
		setSession(sMySessionUUID, false);
		sFieldKeyDef = "DisasterId/STRING";
		sFieldDef    = getRegionEEFieldDef(sRegionUUID);
		createFields(sFieldKeyDef, sFieldDef);
	} // constructor
	
	public DIEEData(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		set("DisasterId", sMyId);
	}
	
	public String getRegionEEFieldDef(String sRegionUUID) {
		String sFieldDef = "";
		int i;
		String sQuery = "SELECT * FROM " + sRegionUUID + "_EEField";
		try  {
		Statement st = conn.createStatement();
		ResultSet rs = st.executeQuery(sQuery);
		i = 0;
		while (rs.next()) {
			if (i>0) {
				sFieldDef = sFieldDef + ",";
			}
			sFieldDef = sFieldDef + 
			  Util.getStringValueFromRecordSet(rs,"EEFieldId") + "/" +
			  Util.getStringValueFromRecordSet(rs,"EEFieldType");
			i++;
		}
		rs.close();
		st.close();
		} catch (SQLException e) {
			e.printStackTrace();
		}
		return sFieldDef;
	}
		
	public String getDeleteQuery() {
		return "DELETE FROM " + getTableName() + 
		 " WHERE " + getWhereSubQuery();
	}

	public static DIEEData fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIEEData o;
		o = new DIEEData(sMySessionUUID);
		o.copyFromHashtable(oMyData);
		return o;
	}

	public String getPermPrefix() {
		return "DISASTER";
	}
	public String getTableSuffix() {
		return "EEData";
	}

	public int validateInsert() {
		return Constants.ERR_NO_ERROR;
	}
}
