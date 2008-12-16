/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.util.Hashtable;
import org.desinventar.core.Util;

public class RpcQueryOperations {
	public String buildQuickDisasterSearchSQL(String sSessionUUID, Hashtable oMyData) {
		String sQuery;
		sQuery = Region.buildQuickDisasterSearchSQL(sSessionUUID, oMyData);
		return sQuery;
	}
	public String doQuickDisasterSearch(String sSessionUUID, Hashtable oMyData) {
		String sFileName;
		sFileName = "/tmp/di8queryresult_" + sSessionUUID + ".db3";
		Region.doQuickDisasterSearch(sSessionUUID, oMyData, sFileName);
		return Util.encodeFile(sFileName);
	}
} // RpcQueryOperations
