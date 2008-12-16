/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.util.Hashtable;
import java.util.UUID;

public class RpcDIServer {
	public String getVersion() {
		return "8.1.8";
	}
	
	public int doCmdServer(int iMyCmd) {
		switch (iMyCmd) {
			case Constants.SERVER_SHUTDOWN:
				DIServer.logger.info("I am going to exit now !!");
				DIServer.SessionList.saveSessionList(DIServer.sConfigDataDir + "/di8sessionlist.txt");
				System.exit(0);
				break;
			case Constants.SERVER_SAVESESSIONS:
				DIServer.SessionList.saveSessionList(DIServer.sConfigDataDir + "/di8sessionlist.txt");
				break;
			case Constants.SERVER_LOADSESSIONS:
				DIServer.SessionList.loadSessionList(DIServer.sConfigDataDir + "/di8sessionlist.txt");
				break;
			case Constants.SERVER_AWAKECONNECTIONS:
				DIServer.SessionList.awakeConnections();
		}
		return 0;
	}
	
	/*public int removeUnusedSessions(int iMyTimeout) {
		return DIServer.SessionList.removeUnusedSessions(iMyTimeout, iMyTimeout * 1000);
	}*/

	/*	
	public Boolean getReadObjectPerm(String sSessionUUID, int iObjectId) {
		Boolean bAuthValue = null;
		switch (iObjectId) {
			case Constants.DI_DISASTER:
				bAuthValue = Auth.getBooleanPerm(sSessionUUID, "DISASTER_SELECT");
				break;
			default:
				// By default always can read other objects
				bAuthValue = true;
				break;
		} // switch
		return bAuthValue;
	}
	*/

	public Hashtable readDIObject(String sSessionUUID, int iObjectId, String sMyId) {
		Hashtable ht = null;
		DIObject o = null;
		int iReturn = Constants.ERR_NO_ERROR;
		DIServer.logger.finer("readDIObject : " + iObjectId + " " + sMyId);
		
		//if (bAuthValue) {
			switch (iObjectId) {
				case Constants.DI_GEOLEVEL:
					o = new DIGeoLevel(sSessionUUID,  Integer.valueOf(sMyId).intValue());
					break;
				case Constants.DI_EVENT:
					o = new DIEvent(sSessionUUID, sMyId);
					break;
				case Constants.DI_CAUSE:
					o = new DICause(sSessionUUID, sMyId);
					break;
				case Constants.DI_GEOGRAPHY:
					o = new DIGeography(sSessionUUID, sMyId);
					break;
				case Constants.DI_DISASTER:
					o = new DIDisaster(sSessionUUID, sMyId);
					break;
				case Constants.DI_DBINFO:
					o = new DIDatabaseInfo(sSessionUUID, sMyId);
					break;
				case Constants.DI_DBLOG:
					o = new DIDatabaseLog(sSessionUUID, sMyId);
					break;
				case Constants.DI_USER:
					o = new DIUser(sSessionUUID, sMyId);
					break;
				case Constants.DI_REGION:
					o = new DIRegionInfo(sSessionUUID, sMyId);
					break;
				case Constants.DI_EEFIELD:
					o = new DIEEField(sSessionUUID, sMyId);
					break;
				case Constants.DI_EEDATA:
					o = new DIEEData(sSessionUUID, sMyId);
					break;
				default:
					o = null;
					iReturn = Constants.ERR_UNKNOWN_ERROR;
					break;
			} // switch
			if (o != null) {
				if (o.loadFromDB(sMyId) > 0) {
					ht = o.toHashtable();
				} else {
					iReturn = Constants.ERR_UNKNOWN_ERROR;
				}	
			} // if
		//} else {
		//	iReturn = Constants.ERR_ACCESS_DENIED;
		//}
		// Add the Status of this call
		if (ht == null) {
			ht = new Hashtable();
		} // if
		ht.put("Status", new Integer(iReturn));
		return ht;
	}

	public int saveDIObject(String sSessionUUID, 
	                        int iObjectId, int iMyCmd, 
	                        Hashtable oMyData) {
		int iReturn;
		DIObject o = null;
		DIDisaster d = null;
		iReturn = Constants.ERR_NO_ERROR;
		boolean bUpdateStruct = false;

		switch (iObjectId) {
		case Constants.DI_GEOLEVEL:
			o = DIGeoLevel.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_EVENT:
			o = DIEvent.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_CAUSE:
			o = DICause.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_GEOGRAPHY:
			o = DIGeography.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_DISASTER:
			d = DIDisaster.fromHashtable(sSessionUUID, oMyData);
			String sId = UUID.randomUUID().toString();
			if (d.getString("DisasterId").length() < 1) {
				d.set("DisasterId", sId);
			}
			o = d;
			
			bUpdateStruct = false;
			break;
		case Constants.DI_DBINFO:
			o = DIDatabaseInfo.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_DBLOG:
			o = DIDatabaseLog.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = false;
			break;
		case Constants.DI_USER:
			o = DIUser.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = false;
			break;
		case Constants.DI_REGION:
			o = DIRegionInfo.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_EEFIELD:
			o = DIEEField.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = true;
			break;
		case Constants.DI_EEDATA:
			o = DIEEData.fromHashtable(sSessionUUID, oMyData);
			bUpdateStruct = false;
			break;
		default :
			iReturn = Constants.ERR_UNKNOWN_ERROR;
		}
				
		if (iReturn > 0) {
			switch (iMyCmd) {
			case Constants.CMD_NEW :
				iReturn = o.insertIntoDB();
				if (iReturn > 0) {
					o.saveToDB();
					o.afterCreate();
					// 2008-09-04 (jhcaiced) When creating a Disaster, 
					// also create record in EEData
					if (iObjectId == Constants.DI_DISASTER) {
						String sMyId = d.getString("DisasterId");
						o = new DIEEData(sSessionUUID, sMyId);
						o.insertIntoDB();
					}
				} else {
					// Object already exists, cannot insert another
					iReturn = Constants.ERR_OBJECT_EXISTS;
				}
				break;
			case Constants.CMD_UPDATE:
				iReturn = o.saveToDB();
				break;
			case Constants.CMD_DELETE:
				iReturn = o.deleteFromDB();
				// 2008-09-04 (jhcaiced) When creating a Disaster, 
				// also create record in EEData
				if (iObjectId == Constants.DI_DISASTER) {
					String sMyId = d.getString("DisasterId");
					o = new DIEEData(sSessionUUID, sMyId);
					o.deleteFromDB();
				}
				break;
			default : 
				// Unknown Command...
				iReturn = Constants.ERR_INVALID_COMMAND;
				break;
			}
		}
		
		if (bUpdateStruct) {
			UserSession s = (UserSession)DIServer.SessionList.get(sSessionUUID);
			s.updateRegionStructLastUpdate();
		}
		return iReturn;
	} // saveDIObject

	// Get and specific error message
	public static String getErrMessage(int iErrorCode) {
		return DIUtil.getErrMessage(iErrorCode);
	}
} // RpcDIServer
