/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;
import java.sql.*;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Set;
import java.util.logging.*;

public class DIObject {
	// Variables
	public    String     sSessionUUID;
	public    String     sRegionUUID;
	protected Connection conn;
	// Dynamic Objects
	public String sTableName;
	public String sPermPrefix;
	public String sFieldKeyDef;
	public String sFieldDef;
	public Hashtable oField;
	public Hashtable oFieldType;

	public DIObject() {
		sSessionUUID = "";
		sRegionUUID  = "empty";
		sTableName = "";
		sPermPrefix = "OBJECT";
		sFieldKeyDef = "";
		sFieldDef    = "";
		oField = new Hashtable();
		oFieldType = new Hashtable();
	}
	
	public DIObject(String sMySessionUUID) {
		this();
		setSession(sMySessionUUID, false);
	}
	
	public DIObject(String sMySessionUUID,
	                String sMyFieldKeyDef,
	                String sMyFieldDef) {
		this(sMySessionUUID);
		sFieldKeyDef = sMyFieldKeyDef;
		sFieldDef    = sMyFieldDef;
		createFields(sFieldKeyDef,sFieldDef);
	}
	
	public void createFields(String sMyFieldKeyDef, String sMyFieldDef) {
		String sAllFields = sMyFieldKeyDef + "," + sMyFieldDef;
		String[] sFields = sAllFields.split(",");
		String[] sItem = null;
		String sFieldName, sFieldType;
		for (int i = 0; i < sFields.length; i++) {
			sItem = sFields[i].split("/");
			sFieldName = sItem[0];
			sFieldType = sItem[1];
			oFieldType.put(sFieldName, sFieldType);
			if (sFieldType.equals("STRING")) {
				oField.put(sFieldName, "");
			}
			if (sFieldType.equals("TEXT")) {
				oField.put(sFieldName, "");
			}
			if (sFieldType.equals("DATETIME")) {
				oField.put(sFieldName, Util.getNowDateString());
			}
			if (sFieldType.equals("INTEGER")) {
				oField.put(sFieldName, new Integer(-1));
			}
			if (sFieldType.equals("DOUBLE")) {
				oField.put(sFieldName, new Double(0.0));
			}
			if (sFieldType.equals("BOOLEAN")) {
				oField.put(sFieldName, new Boolean(true));
			}
		}
	}

	// Methods
	public int setSession(String sMySessionUUID, boolean bCore) {
		int iReturn = Constants.ERR_NO_ERROR;
		sSessionUUID = sMySessionUUID;
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		sRegionUUID  = s.sRegionUUID;
		try {
			conn = DIServer.dicoreconn;
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::setSession() => " + sSessionUUID, e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		return iReturn;
	}
	
	public String strReplace(String str, String pattern, String replace) {
		int s = 0;
		int e = 0;
		StringBuffer result = new StringBuffer();
		while ((e = str.indexOf(pattern, s)) >= 0) {
			result.append(str.substring(s,e));
			result.append(replace);
			s = e + pattern.length();
		}
		result.append(str.substring(s));
		return result.toString();
	}
	
	public String sanitizeQuery(String sQuery) {
		String s;
		s = sQuery;
		return s;
	}
	
	public int exist() {
		int iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		Statement stmt;
		String sQuery = getSelectQuery();
		if (sQuery != null) {
			try {
				stmt = conn.createStatement();
				ResultSet rs = stmt.executeQuery(sanitizeQuery(sQuery));
				while (rs.next()) {
					iReturn = Constants.ERR_NO_ERROR; // Ok
				}
				rs.close();
				stmt.close();
				rs = null;
			} catch (Exception e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::exist() => " + sQuery, e);
				iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
			}
		}
		sQuery = null;
		return iReturn;		
	}

	public int loadFromDB(String sMyId) {
		int iReturn = Constants.ERR_NO_ERROR;
		boolean bFound = false;
		if (getPerm() > 0) {
			String sQuery = getSelectQuery();
			if ( (conn != null) && (sQuery != null)) {
				DIServer.logger.finest("DIObject::loadFromDB => " + sQuery);
				try {
					Statement stmt = conn.createStatement();
					ResultSet rs = stmt.executeQuery(sanitizeQuery(sQuery));
					while (rs.next()) {
						setFields(rs);
						bFound = true;
					}
					rs.close();
					stmt.close();
					if (bFound) {
						iReturn = Constants.ERR_NO_ERROR;
					} else {
						iReturn = Constants.ERR_OBJECT_NOT_FOUND;
					}
				} catch (Exception e) {
					DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::loadFromDB() => " + sMyId, e);
					iReturn = 0; // Error
				}
			}
		} else {
			iReturn = Constants.ERR_ACCESS_DENIED;
		}
		return iReturn;
	} // loadFromDB
	
	public int setFields(ResultSet rs) {
		int iReturn = Constants.ERR_NO_ERROR;
		try {
			Set set = oField.keySet();
			Iterator iter = set.iterator();
			String sKey;
			String sFieldType;
			while (iter.hasNext()) {
				sKey = (String)iter.next();
				sFieldType = (String)oFieldType.get(sKey);
				if (sFieldType.equals("STRING")) {
					set(sKey, Util.getStringValueFromRecordSet(rs, sKey));
				}
				if (sFieldType.equals("TEXT")) {
					set(sKey, Util.getStringValueFromRecordSet(rs, sKey));
				}
				if (sFieldType.equals("DATETIME")) {
					set(sKey, Util.getStringValueFromRecordSet(rs, sKey));
				}
				if (sFieldType.equals("INTEGER")) {
					set(sKey, new Integer(rs.getInt(sKey)));
				}
				if (sFieldType.equals("DOUBLE")) {
					set(sKey, new Double(rs.getDouble(sKey)));
				}
				if (sFieldType.equals("BOOLEAN")) {
					set(sKey, new Boolean(rs.getBoolean(sKey)));
				}
			}
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIObject::setFields : ERROR : ", e);
			iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
		}
		return iReturn;
	}

	/*
	public void printFields() {
		try {
			Set set = oField.keySet();
			Iterator iter = set.iterator();
			String sKey;
			String sFieldType;
			String sValue;
			System.out.println("printFields");
			while (iter.hasNext()) {
				sKey = (String)iter.next();
				sFieldType = (String)oFieldType.get(sKey);
				sValue = oField.get(sKey);
				System.out.println(sKey + " " + sFieldType + " " + sValue);
			}
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "DIObject::setFields : ERROR : ", e);
		}
	}
	*/

	public void copyFromHashtable(Hashtable oMyData) {
		Set set = oMyData.keySet();
		Iterator iter = set.iterator();
		String sKey;
		while (iter.hasNext()) {
			sKey = (String)iter.next();
			if (findField(sKey)) {
				oField.put(sKey, oMyData.get(sKey));
			}
		}
	}

	public int getPerm() {
		int iValue = 0;
		String sPermKey = getPermPrefix();
		String sPermValue = Auth.getPerm(sSessionUUID, sPermKey);
		String[] sPermArr = sPermValue.split("/",2);
		if (sPermArr.length > 0) {
			sPermValue = sPermArr[0];
			if (sPermValue.length() > 0) {
				iValue = (Integer.valueOf(sPermValue)).intValue();
			}
		}
		return iValue;
	}
	
	public int insertIntoDB() {
		String sQuery;
		Statement stmt;
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		sQuery = getInsertQuery();
		if (getPerm()>=3) {
			if (sQuery != null) {
				try {
					if (validate() > 0) {
						stmt = conn.createStatement();
						if (exist() < 0) {
							stmt.executeUpdate(sanitizeQuery(sQuery));
							iReturn = Constants.ERR_NO_ERROR;
					}
						stmt.close();
					}
				} catch (Exception e) {
					DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::insertIntoDB() => " + sQuery, e);
					iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
				}
			}
		}
		// Free memory
		stmt = null;
		sQuery = null;
		return iReturn;
	}

	public int deleteFromDB() {
		String sQuery = getDeleteQuery();
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		DIServer.logger.fine("DIObject::deleteFromDB = " + sQuery);
		if (getPerm() >= Constants.PERM_DELETE) {
			if (sQuery != null) {
				try {
					if (validateDelete()>0) {
						Statement stmt = conn.createStatement();
						stmt.executeUpdate(sanitizeQuery(sQuery));
						stmt.close();
						iReturn = Constants.ERR_NO_ERROR;
					} else {
						iReturn = Constants.ERR_CONSTRAINT_FAIL;
					}
				} catch (Exception e) {
					DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::deleteFromDB() => " + sQuery, e);
					iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
				}
			}
		}
		return iReturn;
	}
	
	public PreparedStatement buildUpdateQuery() {
		PreparedStatement ps=null;
		try {
			String sQuery = getUpdateQuery();
			ps = conn.prepareStatement(sQuery);
		} catch (SQLException e) {
			DIServer.logger.log(Level.SEVERE, "ERROR : buildUpdateQuery() => ", e);
		}
		return ps;
	}

	public int saveToDB() {
		//String sQuery = getUpdateQuery();
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		if (getPerm()>=2) {
			//if (sQuery != null) {
				try {
					if (validate() > 0) {
						//int j = exist();
						if (exist() > 0) {
							PreparedStatement ps = buildUpdateQuery();
							ps.executeUpdate();
							ps.close();
							//Statement stmt = conn.createStatement();
							//stmt.executeUpdate(sanitizeQuery(sQuery));
							//stmt.close();
							iReturn = Constants.ERR_NO_ERROR;
						}
					}
				} catch (Exception e) {
					//DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::saveToDB() => " + sQuery, e);
					iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
				}
			//}
		}
		return iReturn;
	}
	
	public int insertNewIntoDB(Connection oMyConn) {
		String sQuery = "";
		Statement stmt;
		int iReturn = Constants.ERR_UNKNOWN_ERROR;
		if (validate() > 0) {
			try {
				stmt = oMyConn.createStatement();
				sQuery = getInsertQuery();
				if (sQuery.length() > 0) {
					stmt.executeUpdate(sanitizeQuery(sQuery));
					iReturn = Constants.ERR_NO_ERROR;
				}
				sQuery = getUpdateQuery();
				if (sQuery.length() > 0) {
					stmt.executeUpdate(sanitizeQuery(sQuery));
					iReturn = Constants.ERR_NO_ERROR;
				}
				stmt.close();
			} catch (Exception e) {
				DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::insertNewIntoDB() => " + sQuery, e);
				iReturn = Constants.ERR_UNKNOWN_ERROR; // Error
			} //catch
		} // validate
		// Free memory
		stmt = null;
		sQuery = null;
		return iReturn;
	}

	// Utility Functions - Reduce Duplicated Code
	public int validateNotNullStr(int iCurReturn, int iErrorCode, String sValue) {
		int iReturn = Constants.ERR_NO_ERROR;
		if (iCurReturn > 0) {
			if ((sValue==null) || (sValue.length() < 1)) {
				iReturn = iErrorCode;
			}
		} else {
			iReturn = iCurReturn;
		}
		return iReturn;
	}

	public int validateNotNullInt(int iCurReturn, int iErrorCode, int iValue) {
		int iReturn = Constants.ERR_NO_ERROR;
		if (iCurReturn > 0) {
			if (iValue < 0) {
				iReturn = iErrorCode;
			}
		} else {
			iReturn = iCurReturn;
		}
		return iReturn;
	}
	public int validateUniqueInt(int iCurReturn,
	                             int iErrorCode, 
	                             String sFieldName, 
	                             int iValue) {
		int iReturn = Constants.ERR_NO_ERROR;
		if (iCurReturn > 0) {
			try {
				String sQuery = "SELECT * FROM " + getTableName() + " WHERE " + sFieldName + "=" + iValue;
				Statement st = conn.createStatement();
				ResultSet rs = st.executeQuery(sQuery);
				if (rs.next()) {
					iReturn = iErrorCode;
					DIServer.logger.warning("DIObject::validateUniqueInt() : Not Unique Id : " + iValue);
				}
				rs.close();
				st.close();
			} catch (SQLException e) {
					DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::validateUniqueInt()", e);
			}
		} else {
			iReturn = iCurReturn;
		}
		return iReturn;
	} //validateUniqueInt

	public int validateUniqueStr(int iCurReturn, 
	                             int iErrorCode,
	                             String sFieldName, 
	                             String sValue) {
		int iReturn = Constants.ERR_NO_ERROR;
		if (iCurReturn > 0) {
			try {
				String sQuery = "SELECT * FROM " + getTableName() + " WHERE " + sFieldName + "='" + sValue + "'";
				Statement st = conn.createStatement();
				ResultSet rs = st.executeQuery(sQuery);
				if (rs.next()) {
					iReturn = iErrorCode;
					DIServer.logger.warning("DIObject::validateUniqueStr() : Not Unique Id : " + sValue);
				}
				rs.close();
				st.close();
			} catch (SQLException e) {
					DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::validateUniqueStr()", e);
			}
		} else {
			iReturn = iCurReturn;
		}
		return iReturn;
	} //validateUniqueStr

	public int validateRefStr(int iCurReturn, 
	                          int iErrorCode,
	                          String sTableSuffix,
	                          String sFieldName, 
	                          String sValue) {
		int iReturn = Constants.ERR_NO_ERROR;
		if (iCurReturn > 0) {
			try {
				String sQuery = "SELECT * FROM " + sRegionUUID + "_" + sTableSuffix +
				 " WHERE " + sFieldName + "='" + sValue + "'";
				Statement st = conn.createStatement();
				ResultSet rs = st.executeQuery(sQuery);
				if (rs.next()) {
					iReturn = Constants.ERR_NO_ERROR;
				} else {
					iReturn = iErrorCode;
					DIServer.logger.warning("DIObject::validateRefStr() : Invalid Reference to " + sTableSuffix + " : " + sValue);
				}
				rs.close();
				st.close();
			} catch (SQLException e) {
					DIServer.logger.log(Level.SEVERE, "ERROR : DIObject::validateUniqueStr()", e);
			}
		} else {
			iReturn = iCurReturn;
		}
		return iReturn;
	} //validateUniqueStr
		
	// Validate Function, should verify that a record is complete
	// and sound before any attempt to save it into the database	
	public int validate() {
		return Constants.ERR_NO_ERROR;
	}

	public int validateInsert() {
		DIServer.logger.warning("Please implement validateInsert()");
		return Constants.ERR_NO_ERROR;
	}

	public int validateDelete() {
		return Constants.ERR_NO_ERROR;
	}

	public String getWhereSubQuery() {
		String sQuery;
		String[] sFields = sFieldKeyDef.split(",");
		String[] sItem = null;
		String sFieldName, sFieldType;
		sQuery = "(";		
		for (int i = 0; i < sFields.length; i++) {
			sItem = sFields[i].split("/");
			sFieldName = sItem[0];
			sFieldType = sItem[1];
			if (i > 0) {
				sQuery = sQuery + " AND ";
			}
			sQuery = sQuery + sFieldName + "=";
			if (sFieldType.equals("STRING")) {
				sQuery = sQuery + "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("TEXT")) {
				sQuery = sQuery + "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("DATETIME")) {
				sQuery = sQuery + "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("INTEGER")) {
				sQuery = sQuery + getIntegerValue(oField, sFieldName);
			}
			if (sFieldType.equals("DOUBLE")) {
				sQuery = sQuery + getDoubleValue(oField, sFieldName);
			}
			if (sFieldType.equals("BOOLEAN")) {
				sQuery = sQuery + getBooleanValue(oField, sFieldName);
			}
		} // for
		sQuery = sQuery + ")";
		return sQuery;
	}
	public String getSelectQuery() {
		String sQuery;
		sQuery = "SELECT * FROM " + getTableName();
		sQuery = sQuery + " WHERE " + getWhereSubQuery();
		return sQuery;
	} // getSelectQuey
	
	public String getInsertQuery() {
		String sQuery;
		String sQueryFields;
		String sQueryValues;
		String[] sFields = sFieldKeyDef.split(",");
		String[] sItem = null;
		String sFieldName, sFieldType;
		sQueryFields = "(";
		sQueryValues = "(";
		for (int i = 0; i < sFields.length; i++) {
			sItem = sFields[i].split("/");
			sFieldName = sItem[0];
			sFieldType = sItem[1];
			if (i > 0) {
				sQueryFields = sQueryFields + ",";
				sQueryValues = sQueryValues + ",";
			}
			sQueryFields = sQueryFields + sFieldName;
			if (sFieldType.equals("STRING")) {
				sQueryValues = sQueryValues + "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("TEXT")) {
				sQueryValues = sQueryValues + "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("DATETIME")) {
				sQueryValues = sQueryValues + "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("INTEGER")) {
				sQueryValues = sQueryValues + getIntegerValue(oField, sFieldName);
			}
			if (sFieldType.equals("DOUBLE")) {
				sQueryValues = sQueryValues + getDoubleValue(oField, sFieldName);
			}
			if (sFieldType.equals("BOOLEAN")) {
				sQueryValues = sQueryValues + getBooleanValue(oField, sFieldName);
			}
		} // for
		sQueryFields = sQueryFields + ")";
		sQueryValues = sQueryValues + ")";
		
		sQuery = "INSERT INTO " + getTableName() +
		 sQueryFields + " VALUES " + sQueryValues;
		return sQuery;
	} // getInsertQuery
	
	public String getDeleteQuery() {
		String sQuery = null;
		return sQuery;
	} // getDeleteQuery
	
	public String getUpdateQuery() {
		String sQuery;
		sQuery = "UPDATE " + getTableName() + " SET ";
		String[] sFields = sFieldDef.split(",");
		String[] sItem = null;
		String sFieldName, sFieldType, sFieldValue;
		for (int i = 0; i < sFields.length; i++) {
			sItem = sFields[i].split("/");
			sFieldName  = sItem[0];
			sFieldType  = sItem[1];
			sFieldValue = "";
			
			if (i > 0) {
				sQuery = sQuery + ",";
			}
			sQuery = sQuery + sFieldName + "=";
			if (sFieldType.equals("STRING")) {
				sFieldValue = "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("TEXT")) {
				sFieldValue = "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("DATETIME")) {
				sFieldValue = "'" + getStringValue(oField, sFieldName) + "'";
			}
			if (sFieldType.equals("INTEGER")) {
				sFieldValue = (new Integer(getIntegerValue(oField, sFieldName))).toString();
			}
			if (sFieldType.equals("DOUBLE")) {
				sFieldValue = (new Double(getDoubleValue(oField, sFieldName))).toString();
			}
			if (sFieldType.equals("BOOLEAN")) {
				sFieldValue = (new Boolean(getBooleanValue(oField, sFieldName))).toString();
			}
			sQuery = sQuery + sFieldValue;
		} // for
		sQuery = sQuery + " WHERE " + getWhereSubQuery();
		return sQuery;       
	} // getUpdateQuery

	public boolean findField(String sMyFieldName) {
		Set set = oField.keySet();
		Iterator iter = set.iterator();
		String sKey;
		boolean bFound = false;
		while ((bFound==false) && (iter.hasNext())) {
			sKey = (String)iter.next();
			bFound = sKey.equals(sMyFieldName);
		} // while
		return bFound;
	}
	
	public int set(String sMyFieldName, Object oValue) {
		int iReturn = Constants.ERR_NO_ERROR;
		String sFieldName, sFieldType;
		if (findField(sMyFieldName)) {
			sFieldName = sMyFieldName;
			sFieldType = (String)oFieldType.get(sFieldName);
			if (sFieldType.equals("STRING")) {
				oField.put(sFieldName, (String)oValue);
			}
			if (sFieldType.equals("TEXT")) {
				oField.put(sFieldName, (String)oValue);
			}
			if (sFieldType.equals("DATETIME")) {
				oField.put(sFieldName, (String)oValue);
			}
			if (sFieldType.equals("INTEGER")) {
				oField.put(sFieldName, Integer.valueOf(oValue.toString()));
			}
			if (sFieldType.equals("DOUBLE")) {
				oField.put(sFieldName, Double.valueOf(oValue.toString()));
			}
			if (sFieldType.equals("BOOLEAN")) {
				oField.put(sFieldName, (Boolean)oValue);
			}
		} //if
		return iReturn;
	}
	
	public Object get(String sMyFieldName) {
		Object o = null;
		if (findField(sMyFieldName)) {
			o = oField.get(sMyFieldName);
		}
		return o;
	}
	
	public Hashtable toHashtable() {
		return oField;
	} // toHashtable
	
	public String getPermPrefix() {
		return sPermPrefix;
	}
	
	public String getTableSuffix() {
		return sTableName;
	}
	
	public String getTableName() {
		return sRegionUUID + "_" + getTableSuffix();
	}
	
	public int afterCreate() {
		return Constants.ERR_NO_ERROR;
	}
	
	public boolean validateUniqueId() {
		return true;
	}

	public static String getStringValue(Hashtable ht, String sKey) {
		String sValue = "";
		if (ht.containsKey(sKey)) {
			sValue = (String)ht.get(sKey);
			if (sValue == null) {
				sValue = "";
			}
			if (sValue.equals("null")) {
				sValue = "";
			}
		}
		return sValue;
	}
	public static double getDoubleValue(Hashtable ht, String sKey) {
		Double iReturn = new Double(0);		
		if (ht.containsKey(sKey)) {
			Object oValue;
			oValue = (Object)ht.get(sKey);
			if (oValue instanceof String) {
				if (((String)oValue).equals("")) {
					iReturn = new Double(0.0);
				} else {
					iReturn = Double.valueOf((String)oValue);
				}
			}
			if (oValue instanceof Double) {
				iReturn = (Double)oValue;
			}
			if (oValue == null) {
				iReturn = new Double(0.0);
			}
		}
		return iReturn.doubleValue();
	}
	public static int getIntegerValue(Hashtable ht, String sKey) {
		Integer iReturn = new Integer(0);
		if (ht.containsKey(sKey)) {
			Object oValue;
			oValue = (Object)ht.get(sKey);
			if (oValue instanceof String) {
				if (((String)oValue).equals("")) {
					iReturn = new Integer(0);
				} else {
					iReturn = Integer.valueOf((String)oValue);
				}
			}
			if (oValue instanceof Integer) {
				iReturn = (Integer)oValue;
			}
			if (oValue == null) {
				iReturn = new Integer(0);
			}
		}
		return iReturn.intValue();
	}
	public static boolean getBooleanValue(Hashtable ht, String sKey) {
		Boolean oValue = new Boolean(false);
		String sValue = "";
		if (ht.containsKey(sKey)) {
			//sValue = (String)ht.get(sKey);
			sValue = "false";
			if (sValue.length() > 0) {
				oValue = (Boolean)ht.get(sKey);
				if (oValue == null) {
					oValue = new Boolean(false);
				}
			} else {
				oValue = new Boolean(false);
			}
		}
		return oValue.booleanValue();
	}
	
	public String getString(String sKey) {
		return (String)get(sKey);
	}
	
	public int getInteger(String sKey) {
		return ((Integer)get(sKey)).intValue();
	}
	
	public double getDouble(String sKey) {
		return ((Double)get(sKey)).doubleValue();
	}
	
	public boolean getBoolean(String sKey) {
		return ((Boolean)get(sKey)).booleanValue();
	}
	
} // class