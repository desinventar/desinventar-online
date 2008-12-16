/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Hashtable;
import java.sql.*;
import java.util.logging.Level;

public class DIEvent extends DIObject {
	public DIEvent() {
		sTableName   = "Event";
		sPermPrefix  = "EVENT";
		sFieldKeyDef ="EventId/STRING";
		sFieldDef    ="EventLocalName/STRING," +
		              "EventLocalDesc/STRING," +
		              "EventActive/BOOLEAN," +
		              "EventPreDefined/BOOLEAN," +
		              "EventCreationDate/DATETIME";
		createFields(sFieldKeyDef,sFieldDef);
		set("EventId", "");
		set("EventLocalName", "");
		set("EventLocalDesc", "");
		set("EventPreDefined", new Boolean(false));
		set("EventActive"    , new Boolean(true));
		set("EventCreationDate", Util.getNowDateString());
	} // constructor
	
	public DIEvent(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	} // constructor
	
	public DIEvent(String sMySessionUUID, String sMyId) {
		this(sMySessionUUID);
		set("EventId", sMyId);
	}
	
	public DIEvent(String sMySessionUUID, String sMyLocalName, String sMyLocalDesc) {
		this(sMySessionUUID);
		set("EventLocalName", sMyLocalName);
		set("EventLocalDesc", sMyLocalDesc);
		setIdByLocalName();
	}
	
	public void setIdByLocalName() {
		try {
			int iCount = 0;
			String sQuery = "SELECT * FROM " + sRegionUUID + "_" + sTableName +  
			 " WHERE EventLocalName='" + (String)get("EventLocalName") + "'";
			Statement st = conn.createStatement();
			st.executeUpdate("SET NAMES utf8");
			ResultSet rs = st.executeQuery(sQuery);
			rs.last();
			iCount = rs.getRow();
			if (iCount > 0) {
				rs.beforeFirst();
				while (rs.next()) {
					set("EventId", Util.getStringValueFromRecordSet(rs,"EventId"));
					set("EventPreDefined", new Boolean(rs.getBoolean("EventPreDefined")));
					set("EventCreationDate", rs.getString("EventCreationDate"));
				}
			} else {
				// Search Predefined Events (dicore.Event)
				String sLangCode = Region.getLangCode(sSessionUUID,sRegionUUID);
				sQuery = "SELECT * FROM DIEvent WHERE " + 
							"EventLangCode='" + sLangCode + "' AND (" +
				            "EventLocalName='" + (String)get("EventLocalName") + "'" + " OR " +
				            "EventDI6Name='" + (String)get("EventLocalName") + "')";
				st = DIServer.dicoreconn.createStatement();
				rs = st.executeQuery(sQuery);
				rs.last();
				iCount = rs.getRow();
				if (iCount > 0) {
					rs.beforeFirst();
					while (rs.next()) {
						set("EventId", Util.getStringValueFromRecordSet(rs,"EventId"));
						set("EventLocalName", Util.getStringValueFromRecordSet(rs,"EventLocalName"));
						set("EventLocalDesc", Util.getStringValueFromRecordSet(rs,"EventLocalDesc"));
						set("EventPreDefined", new Boolean(true));
						set("EventCreationDate", rs.getString("EventCreationDate"));
					}
				}
			}
			rs.close();
			st.close();
		} catch (SQLException e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DIEvent::setIdByLocalName()", e);
		}
		if (((String)get("EventId")).length() > 0) {
			//System.out.print("PRED  : ");
		} else {
			set("EventId", get("EventLocalName"));
			//System.out.print("LOCAL : ");
		}
	}

	public String getDeleteQuery() {
		return "UPDATE " + sRegionUUID + "_" + sTableName + " SET EventActive=false " +
		  " WHERE " + getWhereSubQuery();
	}
	
	public static DIEvent fromHashtable(String sMySessionUUID, Hashtable oMyData) {
		DIEvent e;
		e = new DIEvent(sMySessionUUID);
		e.copyFromHashtable(oMyData);		
		return e;
	}

	public int validateInsert() {
		int iReturn = Constants.ERR_NO_ERROR;
		iReturn = validateNotNullStr(iReturn, Constants.ERR_EVENT_NULL_ID, (String)get("EventId"));
		iReturn = validateUniqueStr(iReturn, Constants.ERR_EVENT_DUPLICATED_ID, "EventId", (String)get("EventId"));
		return iReturn;
	}
	public int validateDelete() {
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			String sQuery = "SELECT * FROM " + sRegionUUID + "_Disaster WHERE EventId='" + (String)get("EventId") + "'";
			Statement st = conn.createStatement();
			ResultSet rs = st.executeQuery(sQuery);
			if (rs.next()) {
				iReturn = Constants.ERR_EVENT_CANNOT_DELETE;
				DIServer.logger.warning("DIEvent::validateDelete() : Can't Delete Event : " + (String)get("EventId"));
			}
			rs.close();
			st.close();
		} catch (SQLException e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DIEvent::validateDelete()", e);
		}
		return iReturn;
	} // validateDelete()
}
