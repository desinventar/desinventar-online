/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

public class RpcSessionOperations {

    public int existSession(String sMySessionUUID) {
    	int iReturn = Constants.ERR_NO_ERROR;
    	UserSession s = (UserSession)DIServer.SessionList.get(sMySessionUUID);
    	if (s == null) {
    		iReturn = Constants.ERR_NO_SESSION;
		}
    	return iReturn;
	}
	
} // RpcSessionOperations
