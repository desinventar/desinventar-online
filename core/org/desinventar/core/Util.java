/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.text.SimpleDateFormat;
import java.util.Hashtable;
import java.sql.*;
import com.Ostermiller.util.Base64;
import java.util.logging.*;
import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.InputStream;

public class Util {
	public static int decodeFile(String sFileName, String sData) {
		return Constants.ERR_NO_ERROR;
	}
	
	public static String encodeFile(String sFileName) {
		String encLabel = "";
		byte[] myBytes;
		java.io.File f = null;
		java.io.FileInputStream fis = null;
		try {
			f = new java.io.File(sFileName);
			fis = new java.io.FileInputStream(f);
			myBytes = new byte[(int)f.length()];
			fis.read(myBytes, 0, (int)f.length());
			fis.close();
			encLabel = Base64.encodeToString(myBytes);
		} catch (java.io.IOException e) {
			DIServer.logger.log(Level.SEVERE, "encodeFile : Error while encoding file : " + sFileName, e);
			e.printStackTrace();
		}
		return encLabel;
	}

	public static String getStringValueFromRecordSet(ResultSet rs, 
	                                                 String sField) {
		String sValue = "";
		try {
			sValue = rs.getString(sField);
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "getStringValueFromRecordSet : " + sField, e);
			e.printStackTrace();
		}
		if (sValue == null) {
			sValue = "";
		}		
		if (sValue.equalsIgnoreCase("null")) {
			sValue = "";
		}
		return sValue;
	}
	public static int getIntValueFromRecordSet(ResultSet rs, 
	                                           String sField) {
		Integer iValue = null;
		try {
			iValue = new Integer(rs.getInt(sField));
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "getIntValueFromRecordSet : " + sField, e);
			e.printStackTrace();
		}
		return iValue.intValue();
	}
	public static Double getDoubleValueFromRecordSet(ResultSet rs, 
	                                                 String sField) {
		Double iValue = null;
		try {
			iValue = new Double(rs.getDouble(sField));
		} catch (Exception e) {
			DIServer.logger.log(Level.SEVERE, "getDoubleValueFromRecordSet : " + sField, e);
			e.printStackTrace();
		}
		return iValue;
	}
	
	public static String getDateString(long iTime) {
		SimpleDateFormat df;
		df = new SimpleDateFormat("yyyy-MM-dd");
		return df.format(new Date(iTime));
	}

	public static String getNowDateString() {
		long iTime;
		iTime = (new java.util.Date()).getTime();
		return getDateString(iTime);
	}

	public static String getDateTimeString(long iTime) {
		SimpleDateFormat df;
		df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		return df.format(new Date(iTime));
	}

	public static String getNowDateTimeString() {
		long iTime;
		iTime = (new java.util.Date()).getTime();
		return getDateTimeString(iTime);
	}
	
	
	public static String getStringFromHashtable(Hashtable oMyData, String sKey) {
		String sDefault = "";
		String s = (String)oMyData.get(sKey);
		
		if (s != null) {
			if ((s.length() < 1) ||
			    (s.equals("null"))){
				s = sDefault;
			}
		} else {
			s = sDefault;
		}
		return s;	
	}
	
	public static String getDateFromHashtable(Hashtable oMyData, String sKey, String sDefault) {
		String s = (String)oMyData.get(sKey);
		if (s != null) {
			if ((s.length() < 1) ||
			    (s.equals("null"))){
				s = sDefault;
			}
		} else {
			s = sDefault;
		}
		return s;
	}

	public static Double getDoubleFromHashtable(Hashtable oMyData, String sKey) {
		Double sDefault = null;
		Double s = sDefault;
		try {
			s = (Double)oMyData.get(sKey);
		} catch (Exception e) {
			String s2 = (String)oMyData.get(sKey);
			if (s2 != null) {
				if ((s2.length() < 1) ||
				    (s2.equals("null")) ) {
					s = sDefault;
				} else {
					//s = Double.valueOf(s2).doubleValue();
					s = new Double(s2);
				}				
			} else {
				s = sDefault;
			}
		}		
		return s;	
	}

	public static void executeMySQLScript(Connection conn, String sQuery) {
		//String output = "";
		try {
			Statement stmt = conn.createStatement();
			stmt.executeUpdate(sQuery);
			stmt.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	public static String executeMySQLScript2(String sDbName, String sQuery) {
		String output = "";
		try {
			String[] cmd = new String[] {"mysql", sDbName,
			  "--user=dicore","--password=dicore","-e", 
			  sQuery };
			Process proc = Runtime.getRuntime().exec(cmd);
			InputStream inputstream = proc.getErrorStream();
			InputStreamReader inputstreamreader = new InputStreamReader(inputstream);
			BufferedReader bufferedreader = new BufferedReader(inputstreamreader);
			String line;
			while ((line = bufferedreader.readLine()) != null) {
				output = output + "\n" + line;
			}
			// check for failure
			try {
				if (proc.waitFor() != 0) {
					System.err.println("exit value = " + proc.exitValue());
				}
			} catch (InterruptedException e) {
				System.err.println(e);
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return output;
	}
	
	public static String padString(String sValue, int iLength) {
		while (sValue.length() < iLength) {
			sValue = sValue + ' ';
		}
		return sValue;
	}
} // Util
