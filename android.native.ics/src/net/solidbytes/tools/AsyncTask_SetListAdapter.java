package net.solidbytes.tools;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.*;

import net.solidbytes.jukebox.nodes.Album;
import net.solidbytes.solidmatter.sbConnection;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.util.Log;

public class AsyncTask_SetListAdapter extends AsyncTask <Album, Void, Long> {
    
	Context actCurrent;
	ProgressDialog pdDownload;
	
	public AsyncTask_SetListAdapter(Activity actStarter) {
		actCurrent = actStarter;
	}
	
	protected void onPreExecute() {
		pdDownload = new ProgressDialog(actCurrent);
		pdDownload.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL);
		pdDownload.setMessage("Loading...");
		pdDownload.setCancelable(false);
		pdDownload.show();
	}

	protected Long doInBackground(Album... aAlbums) {
		
		
		
		
		
		return null;

	}

//	protected void onProgressUpdate(Integer... progress) {
//		pdDownload.setProgress(progress[0]);
//		Log.d("sbTools", "progress is " + progress[0] + "%");
//	}

	protected void onPostExecute(Long result) {
		pdDownload.dismiss();
	}
    
}