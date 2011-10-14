package net.solidbytes.tools;

import java.net.*;

import android.os.AsyncTask;

class AsyncDownloadTask extends AsyncTask <URL, Integer, Long> {
    
	protected Long doInBackground(URL... urls) {
        int count = urls.length;
        long totalSize = 0;
        for (int i = 0; i < count; i++) {
        	//totalSize += Downloader.downloadFile(urls[i], "dummy");
        	  publishProgress((int) ((i / (float) count) * 100));
        }
        return totalSize;
    }

    protected void onProgressUpdate(Integer... progress) {
        //setProgressPercent(progress[0]);
    }

    protected void onPostExecute(Long result) {
        //showDialog("Downloaded " + result + " bytes");
    }
    
}