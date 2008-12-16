/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

public class DatacardLockList extends java.util.Hashtable {
	private static final long serialVersionUID = 1; // Needed for Serialization

	public String addDatacardLock(String sSessionUUID, String sDatacardUUID) {
		String sReturn;
		sReturn = "";
		if (! isDatacardLocked(sSessionUUID, sDatacardUUID)) {
			put(sSessionUUID, sDatacardUUID);
			DIServer.logger.fine("addDatacardLock : " + sSessionUUID + " Datacard : " + sDatacardUUID);
			sReturn = sDatacardUUID;
		}
		return sReturn;
	}

	public int removeDatacardLock(String sSessionUUID, String sDatacardUUID) {
		if (containsKey(sDatacardUUID)) {
			remove(sSessionUUID);
			DIServer.logger.fine("removeDatacardLock : " + sSessionUUID + " Datacard : " + sDatacardUUID + " Total : " + size());
		}
		return 0;
	}
	
	public boolean isDatacardLocked(String sSessionUUID, String sDatacardUUID) {
		boolean bAnswer = false;
		bAnswer = containsValue(sDatacardUUID);
		if (bAnswer) {
			if (containsKey(sSessionUUID)) {
				String sMyUUID = get(sSessionUUID).toString();
				bAnswer = ! sMyUUID.equals(sDatacardUUID);
			}
		}
		return bAnswer;
	}
	
	public int removeDatacardLockBySession(String sMySessionUUID) {
		if (containsKey(sMySessionUUID)) {
			//String sDatacardUUID = get(sMySessionUUID).toString();
			remove(sMySessionUUID);
		}
		return 0;
	}
} // DatacardLockList
