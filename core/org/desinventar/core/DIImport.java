/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Set;
import java.io.*;
import java.sql.*;
import com.Ostermiller.util.*;
import java.util.logging.*;

public class DIImport {

	public static Hashtable validateFromCSV(String sSessionUUID,
	                                        String sFileName, int iDataType) {
		return processFromCSV(sSessionUUID, sFileName, iDataType, false);
	}
	public static Hashtable importFromCSV(String sSessionUUID,
	                                      String sFileName, int iDataType) {
		return processFromCSV(sSessionUUID, sFileName, iDataType, true); 
	}
	public static Hashtable processFromCSV(String sSessionUUID,
	                                      String sFileName, int iDataType, 
	                                      boolean doImport) {
		int iReturn = Constants.ERR_NO_ERROR;
		String sId = "";
		String sErrorInfo = "";
		String sOutFile = "/tmp/di8import_" + sSessionUUID + ".csv";
		int iError;
		int iErrorType = Constants.ERRTYPE_NONE;
		int iLineNumber = 0;
		int iErrorCount = 0;
		DIObject o = null;
		DIDisaster myDIDisaster = null;
		DIEEData myDIEEData = null;
		ExcelCSVParser parser = null;
		File f = null;
		FileInputStream fis = null;
		String[] arrValues;
		try {
			FileWriter fOut = new FileWriter(sOutFile);
			Connection conn = DIServer.getDbConnection(sSessionUUID);
			if (conn != null ) {
				DIGeography g = new DIGeography(sSessionUUID);
				f = new java.io.File(sFileName);
				fis = new java.io.FileInputStream(f);
				// Charsets  UTF-8 Cp1252 ISO-8859-1
				parser = new ExcelCSVParser(new InputStreamReader(fis, "ISO-8859-1"));

				if ((iDataType == Constants.DI_DISASTER) ||
				    (iDataType == Constants.DI_EEDATA) ) {
					// 2008-09-20 First line contains csv version
					arrValues = parser.getLine();
					// 2008-09-18 First line contains column headers...
					arrValues = parser.getLine();
				}
				
				iLineNumber = 0;
				iErrorCount = 0;
				String sDisasterGeographyCode = "";
				while ((arrValues = parser.getLine()) != null) {
					iError = Constants.ERR_NO_ERROR;
					iLineNumber++;
					if (iDataType == Constants.DI_EVENT) {
						o = new DIEvent(sSessionUUID, arrValues[1],arrValues[2]);
					}
					if (iDataType == Constants.DI_CAUSE) {
						o = new DICause(sSessionUUID, arrValues[1]);
					}
					if (iDataType == Constants.DI_GEOLEVEL) {
						o = new DIGeoLevel(sSessionUUID, Integer.valueOf(arrValues[0]).intValue(),arrValues[1]);
					}
					if (iDataType == Constants.DI_GEOGRAPHY) {
						o = new DIGeography(sSessionUUID, Integer.valueOf(arrValues[0]).intValue(),
						                    arrValues[1], arrValues[2],arrValues[3]);
					}

					if (iDataType == Constants.DI_DISASTER) {
						myDIDisaster = new DIDisaster(sSessionUUID);
						//  0 - DisasterId
						myDIDisaster.set("DisasterId", arrValues[0]);
						
						//  1 - DisasterSerial
						myDIDisaster.set("DisasterSerial", arrValues[1]);
						sId = myDIDisaster.getString("DisasterSerial");
						
						//  2 - DisasterBeginTime
						//myDIDisaster.sDisasterBeginTime    = arrValues[2];
						myDIDisaster.set("DisasterBeginTime", DIUtil.getDateDisaster(arrValues[2]));
						
						//  3 - DisasterGeographyId
						sDisasterGeographyCode = arrValues[3];
						if (g.readFromDBByCode(conn, sDisasterGeographyCode)) {
							myDIDisaster.set("DisasterGeographyId", g.getString("GeographyId"));
						} else {
							myDIDisaster.set("DisasterGeographyId", "");
							iError = Constants.ERR_NO_GEOGRAPHY;
						}
						
						//  4 - DisasterSiteNotes
						myDIDisaster.set("DisasterSiteNotes", arrValues[4]);
						//  5 - DisasterSource
						myDIDisaster.set("DisasterSource", arrValues[5]);
						//  6 - DisasterLongitude
						myDIDisaster.set("DisasterLongitude", new Double(0.0));
						//  7 - DisasterLatitude
						myDIDisaster.set("DisasterLatitude", new Double(0.0));
						
						// Record Fields
						//  8 - RecordAuthor
						myDIDisaster.set("RecordAuthor", arrValues[8]);
						//  9 - RecordCreation
						//String sMyRecordCreation = DIUtil.getDateDisaster(arrValues[9]);
						myDIDisaster.set("RecordLastUpdate", Util.getNowDateTimeString());
												
						if (myDIDisaster.getString("RecordCreation").length() == 0) {
							myDIDisaster.set("RecordCreation", myDIDisaster.getString("RecordLastUpdate"));
						}
						
						// 10 - RecordStatus
						myDIDisaster.set("RecordStatus", arrValues[10]);
						myDIDisaster.set("RecordStatus", "PUBLISHED");
						
						// 11 - EventId
						// 12 - EventDuration
						// 13 - EventMagnitude
						// 14 - EventNotes
						DIEvent e = new DIEvent(sSessionUUID, arrValues[11], "");
						myDIDisaster.set("EventId", e.getString("EventId"));
						myDIDisaster.set("EventDuration", getIntValueFromString(arrValues[12]));
						myDIDisaster.set("EventMagnitude", arrValues[13]);
						myDIDisaster.set("EventNotes", arrValues[14]);

						// 2008-04-23 (jhcaiced/mayandar) Assing 'OTHER' when none defined
						if ( (myDIDisaster.getString("EventId").length() < 1) ||
						     (myDIDisaster.getString("EventId").equals("NULL")) ||
						     (myDIDisaster.getString("EventId").equals("null")) ) {
							myDIDisaster.set("EventId", "OTHER");
						}
						
						// 15 - Cause
						DICause c = new DICause(sSessionUUID, arrValues[15]);
						myDIDisaster.set("CauseId", c.getString("CauseId"));
						// 16 - Cause Notes
						myDIDisaster.set("CauseNotes", arrValues[16]);
						// 2007-03-06 (jhcaiced/mayandar) Assing 'UNKNOWN' when none defined
						if ( (myDIDisaster.getString("CauseId").length() < 1) ||
						     (myDIDisaster.getString("CauseId").equals("NULL")) ||
						     (myDIDisaster.getString("CauseId").equals("null")) ) {
							myDIDisaster.set("CauseId", "UNKNOWN");
						}
						
						// 17 - 23 Effects on Persons
						myDIDisaster.set("EffectPeopleDead", getIntValueFromString(arrValues[17]));
						myDIDisaster.set("EffectPeopleMissing", getIntValueFromString(arrValues[18]));
						myDIDisaster.set("EffectPeopleInjured", getIntValueFromString(arrValues[19]));
						myDIDisaster.set("EffectPeopleHarmed", getIntValueFromString(arrValues[20]));
						myDIDisaster.set("EffectPeopleAffected", getIntValueFromString(arrValues[21]));
						myDIDisaster.set("EffectPeopleEvacuated", getIntValueFromString(arrValues[22]));
						myDIDisaster.set("EffectPeopleRelocated", getIntValueFromString(arrValues[23]));
						
						// 24 - 25 Effects on Houses
						myDIDisaster.set("EffectHousesDestroyed", getIntValueFromString(arrValues[24]));
						myDIDisaster.set("EffectHousesAffected", getIntValueFromString(arrValues[25]));
						/*
						// Calculate Stat Fields
						myDIDisaster.iEffectPeopleDeadStat      = myDIDisaster.iEffectPeopleDead;
						if (myDIDisaster.iEffectPeopleDeadStat < 0) { myDIDisaster.iEffectPeopleDeadStat = 0; };
						myDIDisaster.iEffectPeopleMissingStat   = myDIDisaster.iEffectPeopleMissing;
						if (myDIDisaster.iEffectPeopleMissingStat < 0) { myDIDisaster.iEffectPeopleMissingStat = 0;};
						myDIDisaster.iEffectPeopleInjuredStat   = myDIDisaster.iEffectPeopleInjured;
						if (myDIDisaster.iEffectPeopleInjuredStat < 0) { myDIDisaster.iEffectPeopleInjuredStat = 0;};
						myDIDisaster.iEffectPeopleHarmedStat    = myDIDisaster.iEffectPeopleHarmed;
						if (myDIDisaster.iEffectPeopleHarmedStat < 0) { myDIDisaster.iEffectPeopleHarmedStat = 0;};
						myDIDisaster.iEffectPeopleAffectedStat  = myDIDisaster.iEffectPeopleAffected;
						if (myDIDisaster.iEffectPeopleAffectedStat < 0) { myDIDisaster.iEffectPeopleAffectedStat = 0;};
						myDIDisaster.iEffectPeopleEvacuatedStat = myDIDisaster.iEffectPeopleEvacuated;
						if (myDIDisaster.iEffectPeopleEvacuatedStat < 0) { myDIDisaster.iEffectPeopleEvacuatedStat = 0;};
						myDIDisaster.iEffectPeopleRelocatedStat = myDIDisaster.iEffectPeopleRelocated;
						if (myDIDisaster.iEffectPeopleRelocatedStat < 0) { myDIDisaster.iEffectPeopleRelocatedStat = 0;};
						myDIDisaster.iEffectHousesDestroyedStat = myDIDisaster.iEffectHousesDestroyed;
						if (myDIDisaster.iEffectHousesDestroyedStat < 0) { myDIDisaster.iEffectHousesDestroyedStat = 0;};
						myDIDisaster.iEffectHousesAffectedStat  = myDIDisaster.iEffectHousesAffected;
						if (myDIDisaster.iEffectHousesAffectedStat < 0) { myDIDisaster.iEffectHousesAffectedStat = 0;};
						*/

												
						// 26 - 32 Effects
						myDIDisaster.set("EffectLossesValueLocal", getDoubleValueFromString(arrValues[26]));
						myDIDisaster.set("EffectLossesValueUSD", getDoubleValueFromString(arrValues[27]));
						myDIDisaster.set("EffectRoads", getDoubleValueFromString(arrValues[28]));
						myDIDisaster.set("EffectFarmingAndForest", getDoubleValueFromString(arrValues[29]));
						myDIDisaster.set("EffectLiveStock", getIntValueFromString(arrValues[30]));
						myDIDisaster.set("EffectEducationCenters", getIntValueFromString(arrValues[31]));
						myDIDisaster.set("EffectMedicalCenters", getIntValueFromString(arrValues[32]));
						
						// 33 EffectOtherLosses
						myDIDisaster.set("EffectOtherLosses", arrValues[33]);
						
						// 34 EffectNotes
						myDIDisaster.set("EffectNotes", arrValues[34]);
						
						// 35 - 45 Sectors Affected
						myDIDisaster.set("SectorTransport", getIntValueFromString(arrValues[35]));
						myDIDisaster.set("SectorCommunications", getIntValueFromString(arrValues[36]));
						myDIDisaster.set("SectorRelief", getIntValueFromString(arrValues[37]));
						myDIDisaster.set("SectorAgricultural", getIntValueFromString(arrValues[38]));
						myDIDisaster.set("SectorWaterSupply", getIntValueFromString(arrValues[39]));
						myDIDisaster.set("SectorSewerage", getIntValueFromString(arrValues[40]));
						myDIDisaster.set("SectorEducation", getIntValueFromString(arrValues[41]));
						myDIDisaster.set("SectorPower", getIntValueFromString(arrValues[42]));
						myDIDisaster.set("SectorIndustry", getIntValueFromString(arrValues[43]));
						myDIDisaster.set("SectorHealth", getIntValueFromString(arrValues[44]));
						myDIDisaster.set("SectorOther", getIntValueFromString(arrValues[45]));
						
						// Validar UniqueID (i.e. DisasterSerial)
						iError = Constants.ERR_NO_ERROR;
						iErrorType = Constants.ERRTYPE_NONE;						
						if (myDIDisaster.validateUniqueId() == false) {
							iErrorType = Constants.ERRTYPE_WARNING;
							iError     = Constants.ERR_DISASTER_DUPLICATED_SERIAL;
						} else {
							iError = myDIDisaster.validateInsert();
						}
						sErrorInfo = "()";
						myDIDisaster.set("RecordStatus", "PUBLISHED");
						switch (iError) {
						case Constants.ERR_DISASTER_DUPLICATED_SERIAL:
							iErrorType = Constants.ERRTYPE_WARNING;
							sErrorInfo = "(" + myDIDisaster.getString("DisasterSerial") + ")";
							break;
						case Constants.ERR_DISASTER_NO_EVENT:
							iErrorType = Constants.ERRTYPE_ERROR;
							sErrorInfo = "(" + myDIDisaster.getString("EventId") + ")";
							break;
						case Constants.ERR_DISASTER_NO_CAUSE:
							iErrorType = Constants.ERRTYPE_ERROR;
							sErrorInfo = "(" + myDIDisaster.getString("CauseId") + ")";
							break;
						case Constants.ERR_DISASTER_NO_GEOGRAPHY:
							iErrorType = Constants.ERRTYPE_ERROR;
							sErrorInfo = "(" + sDisasterGeographyCode + ")";
							break;
						case Constants.ERR_DISASTER_NULL_SOURCE:
							myDIDisaster.set("RecordStatus", "DRAFT");
							iErrorType = Constants.ERRTYPE_WARNING;
							sErrorInfo = "()";
							break;
						case Constants.ERR_DISASTER_NO_EFFECTS:
							myDIDisaster.set("RecordStatus", "DRAFT");
							iErrorType = Constants.ERRTYPE_WARNING;
							sErrorInfo = "(NO EFFECTS)";
							break;
						}
						o = myDIDisaster;
					} // DIDisaster
					if (iDataType == Constants.DI_EEDATA) {
						myDIEEData = new DIEEData(sSessionUUID);
						myDIEEData.set("DisasterId", arrValues[0]);
						Set set = myDIEEData.oField.keySet();
						Iterator iter = set.iterator();
						String sKey;
						//String sFieldType;
						int i;
						for (i=1; i < arrValues.length; i++) {
							if (iter.hasNext()) {
								sKey = (String)iter.next();
								//sFieldType = (String)myDIEEData.oFieldType.get(sKey);
								myDIEEData.set(sKey, arrValues[i]);
							} /* if */
						} /* for */
						o = myDIEEData;
					}
					
					arrValues = null;
					// Validar UniqueID (i.e. DisasterSerial)
					/*					
					if (o.validateUniqueId() == false) {
						// Do not do insert, only update...
						if (doImport) {
							o.saveToDB();
							o = null;
						}
					}
					*/
					if (iError == Constants.ERR_NO_ERROR) {
						iError = o.validateInsert();
					}
					String sErrorTypeMsg = "";
					boolean bImportRecord = true;
					switch (iErrorType) {
					case Constants.ERRTYPE_NONE:
						bImportRecord = true;
						break;
					case Constants.ERRTYPE_ERROR:
						iReturn = Constants.ERR_IMPORT_ERROR;
						iErrorCount++;
						sErrorTypeMsg = "ERROR";
						bImportRecord = false;
						break;
					case Constants.ERRTYPE_WARNING:
						sErrorTypeMsg = "WARNING";
						bImportRecord = true;
						break;
					}
					String sErrorMsg = sErrorTypeMsg + "," +
									   iLineNumber + "," + 
									   iError + "," +
									   sId + "," +
									   DIUtil.getErrMessage(iError) + " - " +
									   sErrorInfo;
					if (iError < 0) {
						DIServer.logger.severe(sErrorMsg);
						fOut.write(sErrorMsg + "\n");
					}
					if (bImportRecord) {
						if (doImport) {
							if (iDataType != Constants.DI_EEDATA) {
								o.insertIntoDB();
							}
							o.saveToDB();
							o = null;
							if (iDataType == Constants.DI_DISASTER) {
								myDIEEData = new DIEEData(sSessionUUID, myDIDisaster.getString("DisasterId"));
								myDIEEData.insertIntoDB();
							}
						}
					}
					myDIDisaster = null;
					myDIEEData = null;
				} // while
			} // if
			fOut.close();
		} catch (java.io.IOException e) {
			DIServer.logger.log(Level.SEVERE, "ERROR: processFromCSV", e);
		}
		
		if (doImport) {
			// Update RegionStructLastUpdate
			UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
			s.updateRegionStructLastUpdate();
		}

		// Send Results in a Hashtable
		Hashtable H = new Hashtable();
		H.put("Status"    , new Integer(iReturn));
		H.put("FileName"  , sOutFile);
		H.put("ErrorCount", new Integer(iErrorCount));
		return H;
	} //importFromCSV
	
	public static Double getDoubleValueFromString(String myValue) {
		String sValue;
		double dValue;
		sValue = myValue;
		if (sValue == "") {
			sValue = "0";
		}
		dValue = Double.valueOf(sValue).doubleValue();
		sValue = null;
		return new Double(dValue);
	}
	public static Integer getIntValueFromString(String myValue) {
		String sValue;
		int iValue;
		sValue = myValue;
		if ( (sValue.equals("")) || 
		     (sValue.equals("null")) || 
		     (sValue.equals("NULL")) ){
			sValue = "0";
		}
		iValue = Integer.valueOf(sValue).intValue();
		sValue = null;
		return new Integer(iValue);
	}
} //class

