package net.solidbytes.jukebox.connection;

import net.solidbytes.jukebox.App;
import net.solidbytes.jukebox.R;
import net.solidbytes.tools.Stopwatch;

import java.io.*;
import java.net.*;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.w3c.dom.Document;

import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import android.util.Log;
import android.widget.Toast;

public abstract class sbConnection {
	
	private static int iTimeout = 2000;
	
	private static String sDomain;
	private static String sUser;
	private static String sPass;
	
	private static String sSessionID;
	
	private static String sCookie = "";
	
	public static boolean bConnected = false;
	public static String sError = "";
	
	public static boolean connect() {
		
		sDomain = App.Prefs.getString("server_domain", "ollomulder.dyndns.org");
		sUser = App.Prefs.getString("server_username", "ollo");
		sPass = App.Prefs.getString("server_password", "test");
		
		sDomain = "ollomulder.dyndns.org";
		//sDomain = "192.168.100.33";
		//sDomain = "10.7.12.138";
				
		// don't reconnect if already connected
		if (isConnected()) {
			return true;
		}
		
		boolean bSuccess = login();
		
		if (!bSuccess) {
			Toast toast = Toast.makeText(App.Context, "Could not connect to server. \n" + sbConnection.sError, Toast.LENGTH_LONG);
			toast.show();
		} else {
			Toast toast = Toast.makeText(App.Context, "Connected to " + App.Prefs.getString("server_domain", ""), Toast.LENGTH_SHORT);
			toast.show();
		}
		
		return bSuccess;
		
	}
	
	public static String getDomain() {
		return sDomain;
	}
	
	public static boolean login() {
		
		// connect to server
		//SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
		
		
		boolean bSuccess = true;
		
		try {
		
			String sAction = "/-/login/login/";
			String sPost = "login=" + sUser + "&password=" + sPass;
			
			if (sDomain == "") {
				throw new Exception("no server domain specified");
			}
			
			Log.i("sbConnection", "attempting login at " + sDomain);
			
			sbDOMResponse domResponse = sendRequest(sAction, sPost);
			
			if (domResponse.getUserID() == null) {
				bSuccess = false;
				Log.i("sbConnection", "Login failed, no UserID returned");
			} else {
				sSessionID = domResponse.getSessionID();
				Log.i("sbConnection", "Login successful, SessionID: " + sSessionID);
			}
			
		} catch (UnknownHostException e) {
			
			bSuccess = false;
			sbConnection.sError = "\nError: " + R.string.error_unknown_host + "(" + sDomain + ")";
		
		} catch (Exception e) {
			
			bSuccess = false;
			Log.e("sbConnection", "\nError: " + e);
			StackTraceElement[] es = e.getStackTrace();
			for (int i=0; i<es.length; i++) {
				Log.v("sbConnection", es[i].toString());
			}
			sbConnection.sError = "\nError: " + e;
			
		}
		
		return bSuccess;
		
	}
	
	public static sbDOMResponse sendRequest(String sAction) throws Exception {
		return sendRequest(sAction, null);
	}
	
	
	public static sbDOMResponse sendRequest(String sAction, String sPostData) throws Exception {
		
		InputStream is = null;
		
		Stopwatch tCon = new Stopwatch();
		Log.d("sbConnection", "sending request: " + sAction);
		
		try {
			
			is = getStream(sAction, sPostData);
			
			// Receive response document
			DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
            DocumentBuilder builder = factory.newDocumentBuilder();
            Document domResponse = builder.parse(is);
			
            Log.d("sbConnection", "HTTP request and parsing took " + tCon.Stop() + " ms");
            
            // output for debugging purposes
            Transformer transformer = TransformerFactory.newInstance().newTransformer();
            transformer.setOutputProperty(OutputKeys.INDENT, "yes");

            //initialize StreamResult with File object to save to file
            StreamResult result = new StreamResult(new StringWriter());
            DOMSource source = new DOMSource(domResponse);
            transformer.transform(source, result);
            
            String xmlString = result.getWriter().toString();
            Log.v("sbConnection", xmlString);
            
            return (new sbDOMResponse(domResponse));
			
		} catch (UnknownHostException e) {
			
			Log.e("sbConnection", "Error: " + e);
			StackTraceElement[] es = e.getStackTrace();
			for (int i=0; i<es.length; i++) {
				Log.v("sbConnection", es[i].toString());
			}
			sbConnection.sError = "Error: unknown host (" + sDomain + ")";
			//throw e;
			return null;
			
			
		} catch (SocketTimeoutException e) {
			
			Log.e("sbConnection", "Error: " + e);
			StackTraceElement[] es = e.getStackTrace();
			for (int i=0; i<es.length; i++) {
				Log.v("sbConnection", es[i].toString());
			}
			sbConnection.sError = "Error: server not responding (" + sDomain + ")";
			//throw e;
			return null;
			
			
		} catch (Exception e) {
			
			//System.out.println( "\nError: " + ex );
			Log.e("sbConnection", "\nError: " + e);
			StackTraceElement[] es = e.getStackTrace();
			for (int i=0; i<es.length; i++) {
				Log.v("sbConnection", es[i].toString());
			}
			sbConnection.sError = "\nError: " + e;
			//throw e;
			return null;
			
		} finally {
			
//			if( os != null ) try { os.close(); } catch( IOException ex ) {/*ok*/}
			if( is != null ) try { is.close(); } catch( IOException ex ) {/*ok*/}
			
		}
		
		
		
	}
	
	public static InputStream getStream(String sAction, String sPostData) throws Exception {
		
		Log.d("sbConnection", "opening stream ressource");
		
		try {
			
			URLConnection con = getConnection(sAction, sPostData);
			con.connect();
			InputStream is = con.getInputStream();
			
			return (is);
			
		} catch (Exception e) {
			
			//System.out.println( "\nError: " + ex );
			Log.e("sbConnection", "\nError: " + e);
			sbConnection.sError = "\nError: " + e;
			throw e;
			
		}
		
		
	}
	
	public static URLConnection getConnection(String sAction, String sPostData) throws Exception {
		
		OutputStream os = null;
		
		String sURL = "http://" + sDomain + "/api" + sAction;
		
		Log.d("sbConnection", "opening connection: " + sURL);
		
		try {
			
			// Connection:
			URL urlRequest = new URL(sURL);
			URLConnection con = urlRequest.openConnection();
			
			// add session cookie if logged in
			if (isConnected()) {
				String sCookie = "PHPSESSID=" + sSessionID;
				con.setRequestProperty("Cookie", sCookie);
			}
			
			con.setConnectTimeout(iTimeout);
			if (!(con instanceof HttpURLConnection)) {
				throw new Exception( "Error: Only HTTP allowed." );
			}
			((HttpURLConnection) con).setRequestMethod("POST");
			con.setDoOutput(true);
			
			// Send data:
			os = con.getOutputStream();
			if (sPostData != null && sPostData != "") {
				os.write(sPostData.getBytes());
			}
			os.flush();
			con.connect();
			
			return (con);
			
		} catch (Exception e) {
			
			//System.out.println( "\nError: " + ex );
			Log.e("sbConnection", "\nError: " + e);
			sbConnection.sError = "\nError: " + e;
			throw e;
			
		} finally {
			
			if( os != null ) try { os.close(); } catch( IOException ex ) {/*ok*/}
			
		}
		
	}
	
	public static boolean isConnected() {
		
		if (sSessionID != null) {
			return true;
		} else {
			return false;
		}
		
	}
	
	
	public static void getArtists() {
		
		if (!sbConnection.bConnected) {
			sbConnection.connect();
		}
		
		
	}
	
	public static void getAlbums() {
		if (!sbConnection.bConnected) {
			sbConnection.connect();
		}
		
	}
	
	public static void getCharts() {
		if (!sbConnection.bConnected) {
			sbConnection.connect();
		}
		
	}
	
}

