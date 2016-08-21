package net.solidbytes.jukebox;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.*;

import net.solidbytes.jukebox.nodes.Node_Album;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.tools.App;
import net.solidbytes.tools.Arrays;
import net.solidbytes.tools.Stopwatch;
import net.solidbytes.tools.Stream;
import net.solidbytes.tools.Zip;

import android.R;
import android.app.Activity;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.media.MediaScannerConnection;
import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;

public class AsyncTask_DownloadAlbum extends AsyncTask <Node_Album, Integer, Long> {
    
	Activity actContext;
	
	NotificationManager nmManager;
	Notification nDownload;
	
	private static int iLastNotificationID = 0;
	int iNotificationID;
	
	String sAlbumTitle;
	long lContentLength;
	Integer iProgress = -1;
	
	public AsyncTask_DownloadAlbum(Activity actCurrent) {
		actContext = actCurrent;
	}
	
	protected void onPreExecute() {
		
		// To create a status notification:
		// Get a reference to the NotificationManager: 
		String ns = Context.NOTIFICATION_SERVICE;
		nmManager = (NotificationManager) actContext.getSystemService(ns);
		
		// Instantiate the Notification:
		int icon = R.drawable.stat_sys_download;
		long when = System.currentTimeMillis();
		nDownload = new Notification(icon, "sbJukebox Download starting...", when);
		
		// Define the notification's message and PendingIntent:
		Context context = actContext.getApplicationContext();
		CharSequence contentTitle = actContext.getString(net.solidbytes.jukebox.R.string.status_downloading);
		CharSequence contentText = actContext.getString(net.solidbytes.jukebox.R.string.status_connecting);
		Intent notificationIntent = new Intent(android.content.Intent.ACTION_VIEW);
		// no Intent for now
		//PendingIntent contentIntent = PendingIntent.getActivity(actContext, 0, notificationIntent, 0);
		nDownload.setLatestEventInfo(context, contentTitle, contentText, null);
		
		// set flag so user can't dismiss the notification
		//nDownload.flags = nDownload.flags |= Notification.FLAG_FOREGROUND_SERVICE;
		
		// Pass the Notification to the NotificationManager:
		iNotificationID = iLastNotificationID + 1;
		iLastNotificationID = iLastNotificationID + 1;
		nmManager.notify(iNotificationID, nDownload);
		
	}

	protected Long doInBackground(Node_Album... aAlbums) {
		
		Node_Album nodeAlbum = aAlbums[0];
//        int count = urls.length;
//        long totalSize = 0;
//        for (int i = 0; i < count; i++) {
//        	//totalSize += Downloader.downloadFile(urls[i], "dummy");
//        	  publishProgress((int) ((i / (float) count) * 100));
//        }
//        return totalSize;
		
		sAlbumTitle = nodeAlbum.getProperty("label");
        
        Log.i("sbJukebox", "starting download of album " + sAlbumTitle);
        
        
        File fileTemp = null;
        
		try {

			
			
//			boolean mExternalStorageAvailable = false;
//			boolean mExternalStorageWriteable = false;
//			String state = Environment.getExternalStorageState();
//
//			if (Environment.MEDIA_MOUNTED.equals(state)) {
//			    // We can read and write the media
//			    mExternalStorageAvailable = mExternalStorageWriteable = true;
//			} else if (Environment.MEDIA_MOUNTED_READ_ONLY.equals(state)) {
//			    // We can only read the media
//			    mExternalStorageAvailable = true;
//			    mExternalStorageWriteable = false;
//			} else {
//			    // Something else is wrong. It may be one of many other states, but all we need
//			    //  to know is we can neither read nor write
//			    mExternalStorageAvailable = mExternalStorageWriteable = false;
//			}
			
			// prepare file storage
			InputStream is = null;
			File dirTemp = App.getExternalCacheFile("/downloads");
			dirTemp.mkdirs();
			fileTemp = new File(dirTemp, nodeAlbum.getProperty("uuid") + ".zip");
			
			// save album to temp directory
			Stopwatch swDownload = new Stopwatch();
			Log.d("sbJukebox", "saving album zip under " + fileTemp.getAbsolutePath());
			URLConnection con = sbConnection.getConnection("/" + nodeAlbum.getProperty("uuid") + "/details/download", null);
			lContentLength = Long.parseLong(con.getHeaderField("Content-Length"));
			is = con.getInputStream();
			OutputStream os = new FileOutputStream(fileTemp);
			Stream.transferCompleteStream(is, os, this);
			Log.d("sbJukebox", "download took " + swDownload.Stop() + " ms");
			
			// extract 
			Stopwatch swUnzip = new Stopwatch();
			//final File dirDestination = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_MUSIC);
			String sDestinationDirectory = App.Prefs.getString("downloads_directory", "/mnt/sdcard/Music");
			final File dirDestination = new File(sDestinationDirectory);
			final String sDestinationPath = dirDestination.toString();
			Log.d("sbJukebox", "extracting album '" + nodeAlbum.getProperty("label") + "' to " + dirDestination.getAbsolutePath());
			nDownload.setLatestEventInfo(actContext, sAlbumTitle, "extracting...", nDownload.contentIntent);
			nmManager.notify(iNotificationID, nDownload);
			final String[] aExtractedFiles = Zip.unzip(fileTemp, dirDestination);
			fileTemp.delete();
			Log.d("sbJukebox", "extracting the zip file took " + swUnzip.Stop() + " ms");
			
			// done with everything except scanning
			Log.i("sbJukebox", "downloading and extracting album '" + nodeAlbum.getProperty("label") + "' finished");
			
			final String[] aMP3sToScan = Arrays.filterStringArray(aExtractedFiles, ".mp3");
			
			MediaScannerConnection.scanFile(App.Context,
					aMP3sToScan, null,
			    new MediaScannerConnection.OnScanCompletedListener() {
			    public void onScanCompleted(String path, Uri uri) {
			    	Log.i("sbTools", "Scanned " + path + ":");
			        Log.i("sbTools", "-> uri=" + uri);
			    }
			});

//			App.Activity.sendBroadcast(new Intent(Intent.ACTION_MEDIA_MOUNTED, Uri.parse("file://" + sDestinationPath)));
			
			
			// tell the media library to scan the newly added album
//			final MediaScannerConnection mScanner = new MediaScannerConnection(App.Context, new MediaScannerConnection.MediaScannerConnectionClient() {
//		        public void onMediaScannerConnected() {
//		        	Log.d("sbJukebox", "media scanner connected");
//		        	for (int i=0; i<aExtractedFiles.length; i++) {
//		        		if (aExtractedFiles[i].contains(".mp3")) {
//		        			Log.d("sbJukebox", "scanning extracted file: " + aExtractedFiles[i]);
//			        		mScanner.scanFile(aExtractedFiles[i], null);
//		        		}
//		        	}
//		        }
//		        public void onScanCompleted(String sPath, Uri uri) {
//		        	Log.d("sbJukebox", "scanned extracted file: " + sPath);
////	                if (path.equals(sDestinationPath)) {
////	                    mScanner.disconnect();
////	                }
//		        }
//			}); 
//			mScanner.connect();
			
			
			//MediaScannerConnection.scanFile(App.Context, new String[] { dirDestination.toString() }, null, null);
			
//					MediaScannerConnection.scanFile(this,
//			                new String[] { file.toString() }, null,
//			                new MediaScannerConnection.OnScanCompletedListener() {
//			            public void onScanCompleted(String path, Uri uri) {
//			                Log.i("ExternalStorage", "Scanned " + path + ":");
//			                Log.i("ExternalStorage", "-> uri=" + uri);
//			            }

			
			
		} catch (Exception e) {
			Log.e("sbJukebox", "Error downloading album >> " + e.getMessage() + " // " + e.toString());
			nDownload.flags = nDownload.flags |= Notification.FLAG_AUTO_CANCEL;
			nDownload.setLatestEventInfo(actContext, sAlbumTitle, "Download interrupted!", nDownload.contentIntent);
			nmManager.notify(iNotificationID, nDownload);
		} finally {
			nmManager.cancel(iNotificationID);
			if (fileTemp != null) {
				fileTemp.delete();
			}
		}
		
		return null;

	}

	protected void onProgressUpdate(Integer... progress) {
		if (progress[0] != iProgress) {
			Log.d("sbTools", "progress is " + progress[0] + "%");
			iProgress = progress[0];
			nDownload.setLatestEventInfo(actContext, sAlbumTitle, progress[0] + actContext.getString(net.solidbytes.jukebox.R.string.status_percent_downloaded), nDownload.contentIntent);
			nmManager.notify(iNotificationID, nDownload);
		}
	}

	protected void onPostExecute(Long result) {
//		nDownload.setLatestEventInfo(actContext, "Download finished", "100" + actContext.getString(net.solidbytes.jukebox.R.string.status_percent_downloaded), nDownload.contentIntent);
//		nmManager.notify(iNotificationID, nDownload);
//		
//		nDownload.flags = nDownload.flags |= Notification.FLAG_AUTO_CANCEL;
		nmManager.cancel(iNotificationID);
		
	}
	
	public void updateReadBytes(long lBytesRead) {
		int iProgress = (int) ((float) lBytesRead / (float) lContentLength * 100);
		publishProgress(iProgress);
	}
    
}