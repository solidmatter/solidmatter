package net.solidbytes.tools;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.*;

import net.solidbytes.jukebox.App;
import net.solidbytes.jukebox.connection.sbConnection;
import net.solidbytes.jukebox.nodes.Album;
import net.solidbytes.tools.archive.Zip;

import android.app.ProgressDialog;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;

public class AsyncTask_DownloadAlbum extends AsyncTask <Album, Integer, Long> {
    
	ProgressDialog pdDownload;
	long lContentLength;

	protected void onPreExecute() {
		pdDownload = new ProgressDialog(App.Context);
		pdDownload.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
		pdDownload.setMessage("Loading...");
		pdDownload.setCancelable(false);
		pdDownload.show();
	}

	protected Long doInBackground(Album... aAlbums) {
		
		Album nodeAlbum = aAlbums[0];
//        int count = urls.length;
//        long totalSize = 0;
//        for (int i = 0; i < count; i++) {
//        	//totalSize += Downloader.downloadFile(urls[i], "dummy");
//        	  publishProgress((int) ((i / (float) count) * 100));
//        }
//        return totalSize;
        
        Log.i("sbJukebox", "starting download of album " + nodeAlbum.getProperty("label"));
		
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
			File fileTemp = new File(dirTemp, nodeAlbum.getProperty("uuid") + ".zip");
			
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
			final File dirDestination = new File("/mnt/sdcard/external_sd/Music");
			final String sDestinationPath = dirDestination.toString();
			Log.d("sbJukebox", "extracting album '" + nodeAlbum.getProperty("label") + "' to " + dirDestination.getAbsolutePath());
			Zip.unzip(fileTemp, dirDestination);
			fileTemp.delete();
			Log.d("sbJukebox", "extracting the zip file took " + swUnzip.Stop() + " ms");
			
			Log.i("sbJukebox", "downloading and extracting album '" + nodeAlbum.getProperty("label") + "' finished");
			
			App.Activity.sendBroadcast(new Intent(Intent.ACTION_MEDIA_MOUNTED, Uri.parse("file://" + sDestinationPath)));
			
			// tell the media library to scan the newly added album
			// TODO: it should only scan the newly created folder
//					final MediaScannerConnection mScanner = new MediaScannerConnection(App.Context, null);
//					new MediaScannerConnection.MediaScannerConnectionClient() {
//				        public void onMediaScannerConnected() {
//				        	mScanner.scanFile(sDestinationPath, null);
//				        }
//				        public void onScanCompleted(String path, Uri uri) {
//			                if (path.equals(sDestinationPath)) {
//			                        mScanner.disconnect();
//			                }
//				        }
//					}; 
//					mScanner.connect(); 
			
			
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
		}
		
		return null;

	}

	protected void onProgressUpdate(Integer... progress) {
		pdDownload.setProgress(progress[0]);
		Log.d("sbTools", "progress is " + progress[0] + "%");
	}

	protected void onPostExecute(Long result) {
		pdDownload.dismiss();
	}
	
	public void updateReadBytes(long lBytesRead) {
		int iProgress = (int) ((float) lBytesRead / (float) lContentLength * 100);
		publishProgress(iProgress);
	}
    
}