package net.solidbytes.tools;

import android.util.Log;

public class Logg {
	
	public static void e(String sTag, Exception e) {
		Log.e(sTag, "Error: " + e);
		logStackTrace(sTag, e);
	}
	
	public static void w(String sTag, Exception e) {
		Log.w(sTag, "Error: " + e);
		logStackTrace(sTag, e);
	}
	
	public static void i(String sTag, Exception e) {
		Log.i(sTag, "Error: " + e);
		logStackTrace(sTag, e);
	}
	
	public static void d(String sTag, Exception e) {
		Log.d(sTag, "Error: " + e);
		logStackTrace(sTag, e);
	}
	
	public static void v(String sTag, Exception e) {
		Log.v(sTag, "Error: " + e);
		logStackTrace(sTag, e);
	}
	
	public static void logStackTrace(String sTag, Exception e) {
		
		StackTraceElement[] es = e.getStackTrace();
		for (int i=0; i<es.length; i++) {
			Log.v(sTag, es[i].toString());
		}
		
	}
	
	
}
