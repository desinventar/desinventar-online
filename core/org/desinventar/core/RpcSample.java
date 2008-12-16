/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.util.Hashtable;
import com.Ostermiller.util.Base64;
import org.desinventar.core.Util;

public class RpcSample {
	/* Sample XMLRPC Methods */
	public String getVersion() {
		return "8.0.0";
	}
	
	public Hashtable getServerInfo() {
		Hashtable result = new Hashtable();
		result.put("Version", "8.0.0");
		result.put("URL", "http://www.desinventar.org");
		return result;
	}
	
	public String getDB() {
		String encLabel = "";
		String sDemo = "DesInventar";
		encLabel = Base64.encodeToString(sDemo.getBytes());
		return encLabel;
	}
	public String getFile() {
		return Util.encodeFile("/tmp/logo.gif");
	}
	
	public int add(int i1, int i2) {
		return i1 + i2;
	}
	public int subtract(int i1, int i2) {
		return i1 - i2;
	}
} // RpcSample
                                                                                                                        