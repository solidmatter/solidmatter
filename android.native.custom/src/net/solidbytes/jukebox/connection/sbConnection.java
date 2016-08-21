package net.solidbytes.jukebox.connection;

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
	
	private static String sSessionID;
	
	private static Context AppContext;
	private static SharedPreferences AppPrefs;
	
	private static String sCookie = "";
	public static boolean bConnected = false;
	public static String sError = "";
	
	public static void setContext(Context ctxCurrent) {
		AppContext = ctxCurrent;
		AppPrefs = PreferenceManager.getDefaultSharedPreferences(AppContext);
	}
	
	public static Context getContext() {
		return AppContext;
	}
	
	public static boolean connect() {
		
		// don't reconnect if already connected
		if (isConnected()) {
			return true;
		}
		
		boolean bSuccess = login();
		
		if (!bSuccess) {
			Toast toast = Toast.makeText(AppContext, "Could not connect to server. \n" + sbConnection.sError, Toast.LENGTH_LONG);
			toast.show();
		} else {
			Toast toast = Toast.makeText(AppContext, "Connected to " + AppPrefs.getString("server_domain", ""), Toast.LENGTH_SHORT);
			toast.show();
		}
		
		return bSuccess;
		
	}
	
	public static boolean login() {
		
		// connect to server
		//SharedPreferences prefs = PreferenceManager.getDefaultSharedPreferences(getBaseContext());
		String sDomain = AppPrefs.getString("server_domain", "");
		String sUser = AppPrefs.getString("server_username", "");
		String sPass = AppPrefs.getString("server_password", "");
		
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
			
//			Log.d("sbConnection", domResponse.toString());
			
			
		} catch (Exception e) {
			
			bSuccess = false;
			Log.e("sbConnection", "\nError: " + e);
			sbConnection.sError = "\nError: " + e;
			
		}
		
		return bSuccess;
		
	}
	
	public static sbDOMResponse sendRequest(String sAction) throws Exception {
		return sendRequest(sAction, "");
	}
	
	
	public static sbDOMResponse sendRequest(String sAction, String sPostData) throws Exception {
		
		String sDomain = AppPrefs.getString("server_domain", "");
		
		OutputStream os = null;
		InputStream  is = null;
		
		String sURL = "http://" + sDomain + "/api" + sAction;
		
		Stopwatch tCon = new Stopwatch();
		Log.d("sbConnection", "sending request: " + sURL);
		
		boolean bSuccess = true;
		
		try {
			
			// Connection:
			URL urlRequest = new URL(sURL);
			
			URLConnection con = urlRequest.openConnection();
			
			// add session cookie if logged in
			if (isConnected()) {
				String sCookie = "PHPSESSID=" + sSessionID;
				con.setRequestProperty("Cookie", sCookie);
			}
			
			con.setConnectTimeout(2000);
			if (!(con instanceof HttpURLConnection)) {
				throw new Exception( "Error: Only HTTP allowed." );
			}
			((HttpURLConnection) con).setRequestMethod("POST");
			con.setDoOutput(true);
			
			// Send data:
			
			os = con.getOutputStream();
			os.write(sPostData.getBytes());
			os.flush();
			
			is = con.getInputStream();
			
			
			// Read response:
			
//			int len;
//			byte[] buff = new byte[4096];
//			while( -1 != (len = is.read( buff )) ) {
//				//System.out.print( new String( buff, 0, len ) );
//				Log.d("sbConnection", new String( buff, 0, len ));
//
//			}
			//System.out.println();
			
			// recieve response document
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
			
		} catch (Exception e) {
			
			//System.out.println( "\nError: " + ex );
			Log.e("sbConnection", "\nError: " + e);
			sbConnection.sError = "\nError: " + e;
			//throw e;
			return null;
			
		} finally {
			
			if( os != null ) try { os.close(); } catch( IOException ex ) {/*ok*/}
			if( is != null ) try { is.close(); } catch( IOException ex ) {/*ok*/}
			
		}
		
		
		
	}
	
	public static InputStream getStream(String sAction) throws Exception {
		
		OutputStream os = null;
		InputStream  is = null;
		
		//String sURL = "http://" + AppPrefs.getString("server_domain", "") + "/api" + sAction;
		String sURL = "http://dckorean.net/cheditor4/example/sample.jpg";
		
		Stopwatch tCon = new Stopwatch();
		Log.d("sbConnection", "opening stream ressource: " + sURL);
		
		try {
			
			// Connection:
			URL urlRequest = new URL(sURL);
			
			URLConnection con = urlRequest.openConnection();
			
			// add session cookie if logged in
			if (isConnected()) {
				String sCookie = "PHPSESSID=" + sSessionID;
				con.setRequestProperty("Cookie", sCookie);
			}
			
			con.setConnectTimeout(2000);
			if (!(con instanceof HttpURLConnection)) {
				throw new Exception( "Error: Only HTTP allowed." );
			}
			((HttpURLConnection) con).setRequestMethod("POST");
			con.setDoOutput(true);
			
			// Send data:
			os = con.getOutputStream();
			os.flush();
			con.connect();
			is = con.getInputStream();
			
//			URL url = new URL(strURL);
//			URLConnection conn = url.openConnection();
//			conn.connect();
//			InputStream instream = conn.getInputStream();
//			bmp = BitmapFactory.decodeStream(instream);

//			int len;
//			byte[] buff = new byte[4096];
//			while( -1 != (len = is.read( buff )) ) {
//				//System.out.print( new String( buff, 0, len ) );
//				Log.v("sbConnection", new String( buff, 0, len ));
//
//			}
			
			return (is);
			
		} catch (Exception e) {
			
			//System.out.println( "\nError: " + ex );
			Log.e("sbConnection", "\nError: " + e);
			sbConnection.sError = "\nError: " + e;
			throw e;
			
		} finally {
			
			if( os != null ) try { os.close(); } catch( IOException ex ) {/*ok*/}
			if( is != null ) try { is.close(); } catch( IOException ex ) {/*ok*/}
			
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

