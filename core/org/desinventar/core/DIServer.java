/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
package org.desinventar.core;

import org.apache.xmlrpc.server.PropertyHandlerMapping;
import org.apache.xmlrpc.server.XmlRpcServer;
import org.apache.xmlrpc.server.XmlRpcServerConfigImpl;
import org.apache.xmlrpc.webserver.WebServer;
import java.sql.*;
import java.io.File;
import java.io.IOException;
import java.util.logging.*;
//import java.util.concurrent.Semaphore;
import edu.emory.mathcs.backport.java.util.concurrent.Semaphore;

public class DIServer implements Runnable {
	private static final int port = 8081;
	public static String sConfigConfDir = "/etc/desinventar";
	public static String sConfigDataDir = "/tmp";
	public static String sConfigSkelDir = "/var/desinventar/skel";
	public static String sConfigLogFile = "/tmp/dicore.log";
	public static Connection dicoreconn = null;
	public static UserSessionList SessionList = null;
	public static DatacardLockList myDatacardLockList = null;
	public static Logger logger = null;
	public static int iThreadCount = 0;
	public static boolean bRunServer = false;
	private final Semaphore sem = new Semaphore(1);
	
	public static void main(String[] args) throws Exception {
		logger = Logger.getLogger("org.desinventar.core");
		try {
			// Create a file handler that write log record to a file called my.log
			FileHandler handler = new FileHandler(sConfigLogFile, true);
			//handler.setFormatter(new SimpleFormatter());
			handler.setFormatter(new DIFormatter());
			// Add to the desired logger
			logger.addHandler(handler);
		} catch (IOException e) {
			System.err.println("ERROR : Can't create log file");
		}
		logger.setLevel(Level.FINEST);
		logger.info("Starting DIServer");

		openDICOREConnection();
		SessionList = new UserSessionList();
		myDatacardLockList = new DatacardLockList();
		bRunServer = (args.length == 0);
		if (bRunServer) {
			// Create and Start Threads...
			DIServer thr1 = new DIServer();
			DIServer thr2 = new DIServer();
			new Thread(thr1).start();
			new Thread(thr2).start();
		} else {
			runTest();
		}
	}

	public synchronized void addThread() {
		iThreadCount++;
	}

	public synchronized void removeThread() {
		iThreadCount--;
	}
	
	public synchronized int getThreadCount() {
		return iThreadCount;
	}

	// DEBUG : This is not working !!!
	protected void finalize() throws Exception {
		logger.fine("Closing DICORE Database Connection");
		//closeDbConnection();
		//closeDICOREConnection();
    }

	public static void openDICOREConnection() {
		try {
			// This database is made using the mysql root user, that
			// should allow to create new databases and set permissions
			// on them.
			// create database XXX;
			// grant all on XXX.* to dicore@localhost;
			// flush privileges;
			String url = "jdbc:mysql://localhost/di8db?user=di8db&password=di8db";
			Class.forName ("com.mysql.jdbc.Driver").newInstance();
			dicoreconn = DriverManager.getConnection (url);
			logger.finest("DICORE connection established");
		} catch (Exception e) {
			logger.log(Level.SEVERE, "Cannot connect to DICORE database", e);
		}
	}

	public static void closeDICOREConnection() {
		if (dicoreconn != null) {
			try {
				dicoreconn.close ();
				logger.finest("DICORE connection terminated");
			} catch (Exception e) { /* ignore close errors */ }
		} // if
	} // finally
	
	public static Connection getDbConnection(String sSessionUUID) {
		return dicoreconn;
	}

	public void run() {
		int iThread;
		try {
			sem.acquire();
			addThread();
			iThread = getThreadCount();
			sem.release();
			
			if (iThread == 1) {
				runServer();
				/*
				if (bRunServer) {
					runServer();
				} else {
					runTest();
				} // bRunServer
				*/
			} // ThreadCount == 1

			if (iThread == 2) {
				// This process search unused sessions and close them
				runSessionMonitor();
			}
			sem.acquire();
			removeThread();
			if (getThreadCount() == 0) {
				closeDICOREConnection();
			}
			sem.release();
		} catch (Throwable t) { }
	} //run
	
	public void runServer() {
		try {
			File fSessionList = new File(DIServer.sConfigDataDir + "/di8sessionlist.txt");
			if (fSessionList.exists()) {
				SessionList.loadSessionList(DIServer.sConfigDataDir + "/di8sessionlist.txt");
			}
			
			WebServer webServer = new WebServer(port);
			XmlRpcServer xmlRpcServer = webServer.getXmlRpcServer();
			PropertyHandlerMapping phm = new PropertyHandlerMapping();
			
			/* Specify the handler classes directly */
			phm.addHandler("RpcDIServer", RpcDIServer.class);
			phm.addHandler("RpcUserOperations", RpcUserOperations.class);
			phm.addHandler("RpcRegionOperations", RpcRegionOperations.class);
			phm.addHandler("RpcSessionOperations", RpcSessionOperations.class);
			/* 2008-02-23 (jhcaiced) Disabled RpcQueryOperations, 
			   this module is no longer in use */
			//phm.addHandler("RpcQueryOperations", RpcQueryOperations.class);
			phm.addHandler("RpcDIUtil", DIUtil.class);
			phm.addHandler("RpcSample", RpcSample.class);
			
			//phm.addHandler(Adder.class.getName(), AdderImpl.class);
		
			xmlRpcServer.setHandlerMapping(phm);
			XmlRpcServerConfigImpl serverConfig =
				(XmlRpcServerConfigImpl) xmlRpcServer.getConfig();
			serverConfig.setEnabledForExtensions(true);
			serverConfig.setContentLengthOptional(false);
			webServer.start();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	public void runSessionMonitor() {
		while (1>0) {
			try {
				Thread.sleep(60 * 1000L);
				SessionList.removeUnusedSessions(180,2592000);
				//SessionList.removeUnusedSessions(2,5);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}

	public static void runTest() {
		String sSessionUUID="";
		sSessionUUID = SessionList.openUserSession("root","97ossonp");
		if (sSessionUUID.length() > 0) {
			logger.fine(sSessionUUID + " Session Stablished...");

			// Test dynamic object creation
			/*
			SessionList.openRegion(sSessionUUID, "COLOMBIA");
			DIObject o = new DIObject(sSessionUUID, 
			                          "EventId/STRING,EventLangCode/STRING,EventInt/INTEGER,EventDbl/DOUBLE,EventBool/BOOLEAN",
			                          "EventPreDefined/BOOLEAN");
			o.sTableName = "Event";
			String s;
			s = o.getSelectQuery();
			s = o.getInsertQuery();
			s = o.getDeleteQuery();
			s = o.getUpdateQuery();
			
			DIEvent e = new DIEvent(sSessionUUID, "TestEvent");
			int i;
			i = e.set("EventLocalName","Evento de Prueba");
			s = e.getSelectQuery();
			s = e.getInsertQuery();
			s = e.getDeleteQuery();
			s = e.getUpdateQuery();
			System.out.println(s);
			SessionList.closeRegion(sSessionUUID, "COLOMBIA");
			*/


			/*
			String sRec = DIUtil.getDateDisaster("08-1-3");
			System.out.println("Date : " + sRec);
			*/
			//Region.createRegionStructure(sSessionUUID, "BOLIVIA");
			//Region.createRegionStructure(sSessionUUID, "COLOMBIA");
			//Region.createRegionStructure(sSessionUUID, "ECUADOR");
			//Region.createRegionStructure(sSessionUUID, "PERU");
			//Region.createRegionStructure(sSessionUUID, "VENEZUELA");
			
			//String s = Util.executeScript(new String[] {"/bin/cat","/etc/issue"});
			//String s;
			/*
			s = Util.executeScript(new String[] {
			  "mysql","-u dicore", "--password=dicore","dicore",
			  "-e \"create database test2\" "});
			*/
			//Util.executeMySQLScript(DIServer.dicoreconn, "DROP DATABASE test2");
			//s = Util.executeMySQLScript("dicore", "CREATE DATABASE test2");
			//s = Util.executeMySQLScript("dicore", "GRANT all on test2.* to dicore@localhost");
			//s = Util.executeMySQLScript("dicore", "DROP DATABASE test2");
			/*
			s = Util.executeScript(new String[] {
			  "mysql","-u dicore --password=dicore dicore",
			  "-e \"grant all on test2.* to jhcaiced@localhost\""});
			*/
			//System.out.println(s);
			/*
			// Try to read a DIUser Object
			DIUser u=new DIUser(sSessionUUID, "root");
			u.loadFromDB("root");
			System.out.println("UserName     : " + u.sUserName);
			System.out.println("UserFullName : " + u.sUserFullName);
			
			*/
			
			// Test UTF-8
			//System.out.println("Demo áéíóúÑñüë");
			//SessionList.awakeConnections();
			/*
			try {
				throw new IOException("my exception text");
			} catch ( Throwable e) {
				logger.log(Level.SEVERE, "Uncaught exception", e);
			}
			*/
			
			//Role r = new Role(DIServer.dicoreconn);
			//r.setUserRole("acampos", "PERU", "OBSERVER");
			//r.setUserRole("demo", "COLOMBIA", "ADMINREGION");
			//String s = r.getUserRole("acampos", "PERU");
			//System.out.println("Role : " + s);
			//Hashtable ht;
			//ht = r.getUserRoleByRegion("COLOMBIA");
			//ht = r.getUserRoleByRegion("PERU");
			
			/*
			SessionList.openRegion(sSessionUUID, "COLOMBIA");				
			//Boolean bPerm = Auth.getBooleanPerm(sSessionUUID, "DISASTER_READ");
			//System.out.println("Permission : " + bPerm);
			String sPerm = Auth.getPerm(sSessionUUID, "DISASTER");
			System.out.println("Permission : " + sPerm);
			Auth.getAllPermsByRegion(sSessionUUID);
			SessionList.closeRegion(sSessionUUID, "COLOMBIA");
			*/
			
			/*
			java.util.Date today = new java.util.Date();
			System.out.println(today.getTime());
			System.out.println(Util.getDateString(today.getTime()));
			System.out.println(Util.getDateTimeString(today.getTime()));
			System.out.println(Util.getNowDateString());
			System.out.println(Util.getNowDateTimeString());
			*/
			
			/*				
			Hashtable<String, Object> ht = new Hashtable<String,Object>();
			ht.put("iValue", -2);
			Integer iValue  = (Integer)ht.get("iValue");
			System.out.println("iValue : " + iValue.toString());
			ht = null;
			*/
			
			//SessionList.changeUserPasswd(sSessionUUID, "demo2", "newdemo2");
			
			/*
			SimpleDateFormat df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
			df.setTimeZone(TimeZone.getTimeZone("UTC"));
			df.setLenient(false);
			df.applyPattern("yyyy-MM-dd");
			Date d = df.parse("992000-12-16");
			df.applyPattern("yyyy-MM-dd");
			System.out.println("Now  : " + df.format(new Date()));
			System.out.println("Date : " + df.format(d));
			*/
			
			//System.out.println("DEBUG 1 : " + (new Date()).toString());
			//String s = Region.getRegionInformation(sSessionUUID, "/tmp/di8_" + sSessionUUID + "_dbinfo.db3");
			/*
			Hashtable<String,String> oQuery = new Hashtable<String,String>();
			oQuery.put("DisasterSerial", "00%");
			oQuery.put("DisasterBeginTime", "2007-01-01");
			oQuery.put("EventId", "FLOOD");
			oQuery.put("DisasterGeographyId", "00001");
			int i = Region.doQuickDisasterSearch(sSessionUUID, oQuery, "/tmp/di8queryresult_" + sSessionUUID + ".db3");
			System.out.println("DEBUG 2 : " + (new Date()).toString());
			*/
			//System.out.println("Charset : " + java.nio.charset.Charset.defaultCharset());
			
			//SessionList.openRegion(sSessionUUID, "BOLIVIA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/BO_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/BO_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/BO_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/BO_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/BO_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "BOLIVIA");

			//SessionList.openRegion(sSessionUUID, "COLOMBIA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CO_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CO_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CO_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CO_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CO_disaster.csv", Constants.DI_DISASTER);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CO_eedata.csv", Constants.DI_EEDATA);
			//SessionList.closeRegion(sSessionUUID, "COLOMBIA");

			//SessionList.openRegion(sSessionUUID, "ECUADOR");
			//Region.copyPreDefinedDataGeneric("EVENT", sSessionUUID, "ECUADOR");
			//Region.copyPreDefinedDataGeneric("CAUSE", sSessionUUID, "ECUADOR");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_disaster.csv", Constants.DI_DISASTER);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/data.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "ECUADOR");

			//SessionList.openRegion(sSessionUUID, "ZECUADOR");
			//Region.copyPreDefinedDataGeneric("EVENT", sSessionUUID, "ZECUADOR");
			//Region.copyPreDefinedDataGeneric("CAUSE", sSessionUUID, "ZECUADOR");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/EQ_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/eq.csv", Constants.DI_DISASTER);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/data.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "ZECUADOR");

			//SessionList.openRegion(sSessionUUID, "PERU");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PE_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PE_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PE_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PE_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PE_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "PERU");

			//SessionList.openRegion(sSessionUUID, "PANAMA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PA_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PA_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PA_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PA_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/PA_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "PANAMA");

			//SessionList.openRegion(sSessionUUID, "VENEZUELA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/VE_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/VE_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/VE_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/VE_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/VE_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "VENEZUELA");

			//SessionList.openRegion(sSessionUUID, "SALVADOR");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/SA_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/SA_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/SA_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/SA_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/SA_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "SALVADOR");

			//SessionList.openRegion(sSessionUUID, "INORISSA");
			//Region.clearData("EVENT", sSessionUUID, "INORISSA");
			//Region.copyPreDefinedDataGeneric("EVENT", sSessionUUID, "INORISSA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ORISSA_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ORISSA_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ORISSA_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ORISSA_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ORISSA_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "INORISSA");

			//SessionList.openRegion(sSessionUUID, "NEPAL");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/NP_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/NP_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/NP_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/NP_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/NP_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "NEPAL");

			//SessionList.openRegion(sSessionUUID, "COSTARICA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CR_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CR_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CR_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CR_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/CR_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "COSTARICA");

			//SessionList.openRegion(sSessionUUID, "INTAMILNADU");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/TAMILNADU_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/TAMILNADU_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/TAMILNADU_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/TAMILNADU_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/TAMILNADU_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "INTAMILNADU");

			//SessionList.openRegion(sSessionUUID, "SRILANKA");
			//Region.clearData("EVENT", sSessionUUID, "SRILANKA");
			//Region.copyPreDefinedDataGeneric("EVENT", sSessionUUID, "SRILANKA");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/LK_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/LK_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/LK_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/LK_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/LK_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "SRILANKA");

			//SessionList.openRegion(sSessionUUID, "IRAN");
			//Region.clearData("EVENT", sSessionUUID, "IRAN");
			//Region.copyPreDefinedDataGeneric("EVENT", sSessionUUID, "IRAN");
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ir_event.csv", Constants.DI_EVENT);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ir_cause.csv", Constants.DI_CAUSE);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ir_geolevel.csv", Constants.DI_GEOLEVEL);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ir_geography.csv", Constants.DI_GEOGRAPHY);
			//DIImport.importFromCSV(sSessionUUID, "/tmp/ir_disaster.csv", Constants.DI_DISASTER);
			//SessionList.closeRegion(sSessionUUID, "IRAN");

			// End Session
			SessionList.closeUserSession(sSessionUUID);
		}
	} //runTest()
		
    
} // DIServer
