/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import java.util.Iterator;
import java.util.Hashtable;
import java.util.Set;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import java.sql.PreparedStatement;
import java.sql.DriverManager;
import org.desinventar.core.Util;
import java.util.logging.*;

public class Region {
	
	public static String getRegionInformation(String sSessionUUID, String sFileName) {
		Statement stmt, stmt2;
		PreparedStatement pstmt;
		ResultSet rs = null;
		Connection conn = null;
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		String sRegionUUID  = s.sRegionUUID;
		
		DIServer.logger.finer("Call getRegionInformation : " + sSessionUUID);
		
		try {
			conn = DIServer.getDbConnection(sSessionUUID);
			if (conn != null) {
				stmt = conn.createStatement();
				Class.forName("org.sqlite.JDBC").newInstance();
				Connection conn2 = null;
				conn2 = DriverManager.getConnection("jdbc:sqlite:"+ sFileName);
				stmt2 = conn2.createStatement();
				/* Export Event List */
				stmt2.executeUpdate("DROP TABLE IF EXISTS Event");
				stmt2.executeUpdate("CREATE TABLE IF NOT EXISTS Event ( " +
				                    " EventId VARCHAR(50) NOT NULL, " +
				                    " EventLocalName VARCHAR(50) NULL, " + 
				                    " EventLocalDesc TEXT NULL, " +
				                    " EventActive BOOL, " +
				                    " EventPreDefined BOOL, " +
				                    " PRIMARY KEY(EventId))");
				rs = stmt.executeQuery("SELECT * FROM " + sRegionUUID + "_Event");
				stmt2.executeUpdate("BEGIN TRANSACTION");
				while (rs.next()) {
					stmt2.executeUpdate("INSERT INTO Event values(" + 
					    "'" + Util.getStringValueFromRecordSet(rs,"EventId") +"'," + 
					    "'" + Util.getStringValueFromRecordSet(rs,"EventLocalName") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"EventLocalDesc") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"EventActive") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"EventPreDefined") + "')"
					);
				} // while
				rs.close();
				stmt2.executeUpdate("END TRANSACTION");
				
				/* Export Cause List */
				stmt2.executeUpdate("DROP TABLE IF EXISTS Cause");
				stmt2.executeUpdate("CREATE TABLE IF NOT EXISTS Cause ( " +
				                    " CauseId VARCHAR(50) NOT NULL, " +
				                    " CauseLocalName VARCHAR(50) NULL, " + 
				                    " CauseLocalDesc TEXT NULL, " +
				                    " CauseActive BOOL, " +
				                    " CausePreDefined BOOL, " +
				                    " PRIMARY KEY(CauseId))");
				rs = stmt.executeQuery("SELECT * FROM " + sRegionUUID + "_Cause");
				stmt2.executeUpdate("BEGIN TRANSACTION");
				while (rs.next()) {
					stmt2.executeUpdate("INSERT INTO Cause values(" + 
					    "'" + Util.getStringValueFromRecordSet(rs,"CauseId") +"'," + 
					    "'" + Util.getStringValueFromRecordSet(rs,"CauseLocalName") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"CauseLocalDesc") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"CauseActive") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"CausePreDefined") + "')"
					);
				} // while
				rs.close();
				stmt2.executeUpdate("END TRANSACTION");

				/* Export GeoLevel List */
				stmt2.executeUpdate("DROP TABLE IF EXISTS GeoLevel");
				stmt2.executeUpdate("CREATE TABLE IF NOT EXISTS GeoLevel ( " +
				                    " GeoLevelId INTEGER, " +
				                    " GeoLevelName VARCHAR(50), " + 
				                    " GeoLevelDesc TEXT NULL, " +
				                    " GeoLevelLayerFile VARCHAR(50), " +
				                    " GeoLevelLayerCode VARCHAR(50), " +
				                    " GeoLevelLayerName VARCHAR(50), " +
				                    " PRIMARY KEY(GeoLevelId))");
				rs = stmt.executeQuery("SELECT * FROM " + sRegionUUID + "_GeoLevel");
				stmt2.executeUpdate("BEGIN TRANSACTION");
				while (rs.next()) {
					stmt2.executeUpdate("INSERT INTO GeoLevel values(" + 
					    "'" + Util.getStringValueFromRecordSet(rs,"GeoLevelId") +"'," + 
					    "'" + Util.getStringValueFromRecordSet(rs,"GeoLevelName") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"GeoLevelDesc") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"GeoLevelLayerFile") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"GeoLevelLayerCode") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"GeoLevelLayerName") + "'" +
					")");
				} // while
				rs.close();
				stmt2.executeUpdate("END TRANSACTION");

				/* Export GeoGraphy List */
				stmt2.executeUpdate("DROP TABLE IF EXISTS Geography");
				stmt2.executeUpdate("CREATE TABLE IF NOT EXISTS Geography ( " +
				                    " GeographyId VARCHAR(60) UNIQUE NOT NULL, " +
				                    " GeographyCode VARCHAR(60), " + 
				                    " GeographyName VARCHAR(100), " +
				                    " GeographyLevel INTEGER, " +
				                    " GeographyActive BOOL, " +
				                    " PRIMARY KEY(GeographyId))");
				rs = stmt.executeQuery("SELECT * FROM " + sRegionUUID + "_Geography");
				//stmt2.executeUpdate("BEGIN TRANSACTION");
				pstmt = conn2.prepareStatement("INSERT INTO Geography VALUES(?,?,?,?,?)");
				stmt2.executeUpdate("BEGIN TRANSACTION");				
				while (rs.next()) {
					pstmt.setString(1, Util.getStringValueFromRecordSet(rs,"GeographyId"));
					pstmt.setString(2, Util.getStringValueFromRecordSet(rs,"GeographyCode"));
					pstmt.setString(3, Util.getStringValueFromRecordSet(rs,"GeographyName"));
					pstmt.setString(4, Util.getStringValueFromRecordSet(rs,"GeographyLevel"));
					pstmt.setString(5, Util.getStringValueFromRecordSet(rs,"GeographyActive"));
					pstmt.executeUpdate();
				} // while
				stmt2.executeUpdate("END TRANSACTION");
				pstmt.close();

				/* Export Database Info */
				stmt2.executeUpdate("DROP TABLE IF EXISTS DatabaseInfo");
				stmt2.executeUpdate("CREATE TABLE IF NOT EXISTS DatabaseInfo ( " +
				                    " RegionUUID             VARCHAR(50) UNIQUE NOT NULL, " +
				                    " RegionLabel            VARCHAR(50) NOT NULL, " +
				                    " RegionDesc             TEXT, " +
				                    " RegionDescEN           TEXT, " +
				                    " RegionLangCode         VARCHAR(10), " +
				                    " RegionStructLastUpdate DATETIME, " +
				                    " PeriodBeginDate        DATETIME, " +
				                    " PeriodEndDate          DATETIME, " +
				                    " OptionAdminURL         VARCHAR(100), " +
				                    " OptionOutOfPeriod      INT, " +
				                    " GeoLimitMinX					 DOUBLE, " +
				                    " GeoLimitMinY					 DOUBLE, " +
				                    " GeoLimitMaxX					 DOUBLE, " +
				                    " GeoLimitMaxY					 DOUBLE" +
				                    " )");
				rs = stmt.executeQuery("SELECT * FROM Region WHERE RegionUUID='" + sRegionUUID + "'");
				stmt2.executeUpdate("BEGIN TRANSACTION");
				while (rs.next()) {
					stmt2.executeUpdate("INSERT INTO DatabaseInfo VALUES (" + 
					    "'" + Util.getStringValueFromRecordSet(rs,"RegionUUID")             +"'," + 
					    "'" + Util.getStringValueFromRecordSet(rs,"RegionLabel")            + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"RegionDesc")             + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"RegionDescEN")           + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"RegionLangCode")         + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"RegionStructLastUpdate") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"PeriodBeginDate")        + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"PeriodEndDate")          + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"OptionAdminURL")         + "'," +
					    		+ rs.getInt("OptionOutOfPeriod")                                + ","  +
									+ rs.getDouble("GeoLimitMinX")                                  + ","  +
									+ rs.getDouble("GeoLimitMinY")                                  + ","  +
									+ rs.getDouble("GeoLimitMaxX")                                  + ","  +
									+ rs.getDouble("GeoLimitMaxY")                                  +	")");
				} // while
				stmt2.executeUpdate("END TRANSACTION");
				
				/* ExtraEffects List */
				stmt2.executeUpdate("DROP TABLE IF EXISTS EEField");
				stmt2.executeUpdate("CREATE TABLE IF NOT EXISTS EEField ( " +
				                    " EEFieldId VARCHAR(50), " +
				                    " EEFieldLabel VARCHAR(50), " + 
				                    " EEFieldDesc TEXT NULL, " +
				                    " EEFieldType VARCHAR(50), " +
				                    " EEFieldSize INT, " +
				                    " EEFieldOrder INT, " +
				                    " EEFieldActive INT, " +
				                    " EEFieldPublic INT, " +
				                    " PRIMARY KEY(EEFieldId))");
				rs = stmt.executeQuery("SELECT * FROM " + sRegionUUID + "_EEField");
				stmt2.executeUpdate("BEGIN TRANSACTION");
				while (rs.next()) {
					stmt2.executeUpdate("INSERT INTO EEField values(" + 
					    "'" + Util.getStringValueFromRecordSet(rs,"EEFieldId") +"'," + 
					    "'" + Util.getStringValueFromRecordSet(rs,"EEFieldLabel") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"EEFieldDesc") + "'," +
					    "'" + Util.getStringValueFromRecordSet(rs,"EEFieldType") + "',"
					        + Util.getStringValueFromRecordSet(rs,"EEFieldSize") + ","
					        + Util.getStringValueFromRecordSet(rs,"EEFieldOrder") + ","
					        + Util.getStringValueFromRecordSet(rs,"EEFieldActive") + ","
					        + Util.getStringValueFromRecordSet(rs,"EEFieldPublic") + ")");
				} // while
				rs.close();
				stmt2.executeUpdate("END TRANSACTION");

				rs.close();
				stmt.close();
				stmt2.close();
				conn2.close();
			} // if conn != null
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "getRegionInformation : Error in Database", e);
			e.printStackTrace();
		}
		return sFileName;
	} // getRegionInformation
	
	public static String buildQuickDisasterSearchSQL(String sSessionUUID, 
	                                        Hashtable oMyQueryParams) {
		String sQuery;
		sQuery = "";
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		String sRegionUUID  = s.sRegionUUID;
		if (oMyQueryParams != null) {
			sQuery = "SELECT * FROM " + sRegionUUID + "_Disaster ";
			Set set = oMyQueryParams.keySet();
			Iterator iter = set.iterator();
			String sKey;
			String sValue;
			boolean bFound = false;
			while (iter.hasNext()) {
				sKey = (String)iter.next();
				sValue = (String)oMyQueryParams.get(sKey);
				if (oMyQueryParams.containsKey(sKey)) {
					if (sValue.length() > 0) {
						if (bFound == false) {
							sQuery = sQuery + " WHERE ";
							bFound = true;
						} else {
							sQuery = sQuery + " AND ";
						}
						if (sKey.equalsIgnoreCase("DisasterSerial")) {
							sQuery = sQuery + "(" + sKey + " LIKE '" + sValue + "')";
						}
						if ((sKey.equalsIgnoreCase("DisasterBeginTime")) ||
						    (sKey.equalsIgnoreCase("DisasterGeographyId"))
						   ) {
							sQuery = sQuery + "(" + sKey + " LIKE '" + sValue + "%')";
							}
						if (sKey.equalsIgnoreCase("EventId"))  {
							sQuery = sQuery + "(" + sKey + "='" + sValue + "')";
						}
					}
				}
			} // while iterator
		} // if
		return sQuery;
	}

	public static int doQuickDisasterSearch(String sSessionUUID, 
	                                        Hashtable oMyQueryParams,
	                                        String sFileName) {
		int iReturn = 0;
		Statement stmt, stmt2;
		ResultSet rs = null;
		Connection conn = null;
		UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
		String sRegionUUID  = s.sRegionUUID;
		
		DIServer.logger.finer("doQuickDisasterSearch : " + sSessionUUID);
		
		try {
			conn = DIServer.getDbConnection(sSessionUUID);
			if (conn != null) {
				stmt = conn.createStatement();
				Class.forName("org.sqlite.JDBC").newInstance();
				Connection conn2 = null;
				conn2 = DriverManager.getConnection("jdbc:sqlite:"+ sFileName);
				stmt2 = conn2.createStatement();

				String sQuery = "SELECT * FROM " + sRegionUUID + "_Disaster ";
				if (oMyQueryParams != null) {
					Set set = oMyQueryParams.keySet();
					Iterator iter = set.iterator();
					String sKey;
					String sValue;
					boolean bFound = false;
					while (iter.hasNext()) {
						sKey = (String)iter.next();
						sValue = (String)oMyQueryParams.get(sKey);
						if (oMyQueryParams.containsKey(sKey)) {
							if (sValue.length() > 0) {
								if (bFound == false) {
									sQuery = sQuery + " WHERE ";
									bFound = true;
								} else {
									sQuery = sQuery + " AND ";
								}
								if (sKey.equalsIgnoreCase("DisasterSerial")) {
									sQuery = sQuery + "(" + sKey + " LIKE '" + sValue + "')";
								}
								if ((sKey.equalsIgnoreCase("DisasterBeginTime")) ||
								    (sKey.equalsIgnoreCase("DisasterGeographyId"))
								   ) {
									sQuery = sQuery + "(" + sKey + " LIKE '" + sValue + "%')";
								}
								if (sKey.equalsIgnoreCase("EventId"))  {
									sQuery = sQuery + "(" + sKey + "='" + sValue + "')";
								}
							}
						}
					} // while iterator
				} // if
				DIServer.logger.finest("doQuickDisasterSearch : " + sSessionUUID + " Query : " + sQuery);

				/* Export Disaster List */
				stmt2.executeUpdate("DROP TABLE IF EXISTS Disaster");
				stmt2.executeUpdate(getCreateTableQuery(Constants.DI_DISASTER));
				stmt2.executeUpdate("BEGIN TRANSACTION");
				DIDisaster d = new DIDisaster(sSessionUUID);
				int iCount = 0;
				rs = stmt.executeQuery(sQuery);
				while (rs.next() && iCount<100) {
					d.setFields(rs);
					d.insertNewIntoDB(conn2);
					iCount++;
				} // while
				rs.close();
				stmt2.executeUpdate("END TRANSACTION");

				stmt.close();
				stmt2.close();
				conn2.close();
			} // if conn != null
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "doQuickDisasterSearch : Error in Database", e);
			e.printStackTrace();
		}		
		return iReturn;
	}

	public static String getCreateTableQuery(int iObjectId) {
		String sQuery = "";
		switch(iObjectId) {
		case Constants.DI_DISASTER:
			sQuery = "CREATE TABLE Disaster ( " +
			  " DisasterId              VARCHAR(50) UNIQUE NOT NULL, " +
			  " DisasterSerial          VARCHAR(50) NOT NULL, " +
			  " DisasterBeginTime       VARCHAR(30) NOT NULL, " +
			  " DisasterGeographyId     VARCHAR(60) NOT NULL, " +
			  " DisasterSiteNotes       TEXT, " +
			  " DisasterLatitude        DOUBLE, " +
			  " DisasterLongitude       DOUBLE, " +
			  " DisasterSource          VARCHAR(200), " +
			  " RecordStatus            VARCHAR(20), " +
			  " RecordAuthor            VARCHAR(50), " +
			  " RecordCreation          DATETIME, " +
			  " RecordLastUpdate        DATETIME, " +
			  " EventId                 VARCHAR(20), " +
			  " EventNotes              TEXT, " +
			  " EventDuration           INT, " +
			  " EventMagnitude          VARCHAR(50), " +
			  " CauseId                 VARCHAR(20)," +
			  " CauseNotes              TEXT," +
			  " EffectPeopleDead        INT, " +
			  " EffectPeopleMissing     INT," +
			  " EffectPeopleInjured     INT," +
			  " EffectPeopleHarmed      INT, " +
			  " EffectPeopleAffected    INT," +
			  " EffectPeopleEvacuated   INT, " +
			  " EffectPeopleRelocated   INT," +
			  " EffectHousesDestroyed   INT," +
			  " EffectHousesAffected    INT," +
			  " EffectLossesValueLocal  NUMERIC(30,2)," +
			  " EffectLossesValueUSD    NUMERIC(30,2)," +
			  " EffectRoads             NUMERIC(30,2)," +
			  " EffectFarmingAndForest  NUMERIC(30,2)," +
			  " EffectLiveStock         NUMERIC(10)," +
			  " EffectEducationCenters  NUMERIC(10)," +
			  " EffectMedicalCenters    NUMERIC(10)," +
			  " EffectOtherLosses       TEXT," +
			  " EffectNotes             TEXT," +
			  " SectorTransport         INT," +
			  " SectorCommunications    INT," +
			  " SectorRelief            INT," +
			  " SectorAgricultural      INT," +
			  " SectorWaterSupply       INT," +
			  " SectorSewerage          INT," +
			  " SectorEducation         INT," +
			  " SectorPower             INT," +
			  " SectorIndustry          INT," +
			  " SectorHealth            INT," +
			  " SectorOther             INT " +
			  ")";
		break;
		} // switch
		return sQuery;
	} // getCreateTableQuery

	public static boolean existRegionTables(String sSessionUUID, String sRegionUUID) {
		boolean bAnswer = false;
		Connection conn;
		Statement st;
		conn = DIServer.getDbConnection(sSessionUUID);
		try {
			st = conn.createStatement();
			String sQuery = "SELECT COUNT(*) FROM " + sRegionUUID + "_Disaster";
			ResultSet rs = st.executeQuery(sQuery);
			while (rs.next()) {
				bAnswer = true;
			}
			rs.close();
			st.close();
		} catch (Exception e) {
			//DIServer.logger.log(Level.SEVERE, "existRegionTables : Error in Database", e);
			//e.printStackTrace();
			bAnswer = false;
		}
		return bAnswer;
	}
	public static boolean createRegionTables(String sSessionUUID, String sRegionUUID) {
		boolean bAnswer = true;
		try {
			/*
			String[][] Tables = {{"Event"        ,"EventId"},
								 {"Cause"        ,"CauseId"},
								 {"GeoLevel"     ,"GeoLevelId"},
								 {"Geography"    ,"GeographyId"},
								 {"Disaster"     ,"DisasterId"},
								 {"DatabaseLog"  ,"DBLogDate"},
								 {"EEField"      ,"EEFieldId"},
								 {"EEData"       ,"DisasterId"},
								 {"EEGroup"      ,"EEGroupId"}
								};
			Connection conn;
			Statement st;
			st = conn.createStatement();
			conn = DIServer.getDbConnection(sSessionUUID);
			for(String[] sTable : Tables) {
				st.executeUpdate("DROP TABLE IF EXISTS " + sRegionUUID + "_" + sTable[0]);
				st.executeUpdate("CREATE TABLE " + sRegionUUID + "_" + sTable[0] + 
				                 " AS SELECT * FROM empty_" + sTable[0] +
				                 " WHERE " + sTable[1] + "=''");
				st.executeUpdate("ALTER TABLE " + sRegionUUID + "_" + sTable[0] + 
				                 " ADD PRIMARY KEY (" + sTable[1] + ")");
			}
			*/
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "createRegionTables : Error in Database", e);
			e.printStackTrace();
			bAnswer = false;
		}
		return bAnswer;	
	}
	public static boolean dropRegionTables(String sSessionUUID, String sRegionUUID) {
		boolean bAnswer = true;
		try {
			/*
			Connection conn;
			Statement st;
			String[][] Tables = {{"Event"        ,"EventId"},
								 {"Cause"        ,"CauseId"},
								 {"GeoLevel"     ,"GeoLevelId"},
								 {"Geography"    ,"GeographyId"},
								 {"Disaster"     ,"DisasterId"},
								 {"DatabaseLog"  ,"DBLogDate"},
								 {"EEField"      ,"EEFieldId"},
								 {"EEData"       ,"DisasterId"},
								 {"EEGroup"      ,"EEGroupId"}
								};
			conn = DIServer.getDbConnection(sSessionUUID);
			st = conn.createStatement();
			for(String[] sTable : Tables) {
				st.executeUpdate("DROP TABLE IF EXISTS " + sRegionUUID + "_" + sTable[0]);
			}
			*/
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "dropRegionTables : Error in Database", e);
			e.printStackTrace();
			bAnswer = false;
		}
		return bAnswer;	
	}
	
	public static String getLangCode(String sSessionUUID, String sRegionUUID) {
		String sValue = "en";
		Connection conn = DIServer.getDbConnection(sSessionUUID);
		Statement st;
		try {
			st = conn.createStatement();
			String sQuery = "SELECT * FROM Region WHERE RegionUUID='" + sRegionUUID + "'";
			ResultSet rs = st.executeQuery(sQuery);
			while (rs.next()) {
				sValue = Util.getStringValueFromRecordSet(rs,"RegionLangCode");
			}
			rs.close();
			st.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
		return sValue;
	}

	public static void clearData(String sType, String sSessionUUID, String sRegionUUID) {
		Connection conn = DIServer.getDbConnection(sSessionUUID);
		Statement st;
		try {
			st = conn.createStatement();
			if (sType.equals("EVENT")) {
				st.executeUpdate("DELETE FROM " + sRegionUUID + "_Event");
			}
			if (sType.equals("CAUSE")) {
				st.executeUpdate("DELETE FROM " + sRegionUUID + "_Cause");
			}
			st.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
		
	// Copy data from DIEvent/DICause to XXX_Event, XXX_Cause
	public static boolean copyPreDefinedData(String sSessionUUID, String sRegionUUID) {
		boolean bAnswer = true;
		bAnswer = copyPreDefinedDataGeneric("EVENT", sSessionUUID, sRegionUUID);
		if (bAnswer) {
			bAnswer = copyPreDefinedDataGeneric("CAUSE", sSessionUUID, sRegionUUID);
		}
		return bAnswer;
	}
	
	public static boolean copyPreDefinedDataGeneric(String sType, String sSessionUUID, String sRegionUUID) {
		boolean bAnswer = true;
		Connection conn;
		Statement st;
		String sQuery;
		String sRegionLangCode = Region.getLangCode(sSessionUUID, sRegionUUID);
		conn = DIServer.getDbConnection(sSessionUUID);
		try {
			st = conn.createStatement();
			if (sType.equals("EVENT")) {
				sQuery = "DELETE FROM " + sRegionUUID + "_Event";
				st.executeUpdate(sQuery);
				sQuery = "INSERT INTO " + sRegionUUID + "_Event " +
						 " (EventId,EventLocalName,EventLocalDesc,EventCreationDate) " +
						 " SELECT EventId,EventLocalName,EventLocalDesc,EventCreationDate " +
						 " FROM DIEvent WHERE EventLangCode='" + sRegionLangCode + "'";
				st.executeUpdate(sQuery);
				sQuery = "UPDATE " + sRegionUUID + "_Event SET EventActive=true,EventPreDefined=true";
				st.executeUpdate(sQuery);
			}
			if (sType.equals("CAUSE")) {
				sQuery = "DELETE FROM " + sRegionUUID + "_Cause ";
				st.executeUpdate(sQuery);
				sQuery = "INSERT INTO " + sRegionUUID + "_Cause " +
						 " (CauseId,CauseLocalName,CauseLocalDesc,CauseCreationDate) " +
						 " SELECT CauseId,CauseLocalName,CauseLocalDesc,CauseCreationDate " +
						 " FROM DICause WHERE CauseLangCode='" + sRegionLangCode + "'";
				st.executeUpdate(sQuery);
				sQuery = "UPDATE " + sRegionUUID + "_Cause SET CauseActive=true,CausePreDefined=true";
				st.executeUpdate(sQuery);
			}			
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "copyPreDefinedData : Error in Database", e);
			e.printStackTrace();
			bAnswer = false;
		}
		return bAnswer;
	}

	public static boolean createRegionStructure(String sSessionUUID, String sRegionUUID) {
		boolean bReturn = true;
		bReturn = ! existRegionTables(sSessionUUID, sRegionUUID);
		if (bReturn) {
			bReturn = createRegionTables(sSessionUUID, sRegionUUID);
			bReturn = copyPreDefinedData(sSessionUUID, sRegionUUID);
		}
		return bReturn;
	}
} // Region
