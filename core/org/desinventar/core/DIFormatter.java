/************************************************
 DesInventar8
 http://www.desinventar.org
 (c) 1999-2008 Corporacion OSSO
 ***********************************************/
/* Implementation of RPC Calls for DI Server Information */
package org.desinventar.core;

import java.text.SimpleDateFormat;
import java.util.TimeZone;
import java.util.Date;
import java.util.logging.Formatter;
import java.util.logging.*;

class DIFormatter extends Formatter {
	private SimpleDateFormat df;
	public DIFormatter() {
		df = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
		df.setTimeZone(TimeZone.getTimeZone("UTC"));
	}
	// This method is called for every log records	
	public String format(LogRecord rec) {		
		StringBuffer buf = new StringBuffer(1000);
		buf.append(Util.padString(rec.getLevel().toString(), 7));
		buf.append(' ');
		buf.append(df.format(new Date(rec.getMillis())));
		buf.append(' ');
		buf.append(formatMessage(rec));
		buf.append('\n');
		return buf.toString();
	} //format
} // DIFormatter
