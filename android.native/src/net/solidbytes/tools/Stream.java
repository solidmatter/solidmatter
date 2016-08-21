package net.solidbytes.tools;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import net.solidbytes.jukebox.AsyncTask_DownloadAlbum;

import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.util.Log;

public class Stream {
	
	public static void transferCompleteStream(InputStream is, OutputStream os) throws IOException {
		
		String sLastSize = "";
		long lReadBytes = 0;
		int iBufferSize = 4096;
		int iBufferFilled;
		byte[] binBuffer = new byte[iBufferSize];
		while (-1 != (iBufferFilled = is.read(binBuffer))) {
			if (iBufferFilled < iBufferSize) {
				os.write(binBuffer, 0, iBufferFilled);
			} else {
				os.write(binBuffer);
			}
			lReadBytes += iBufferFilled;
			// TODO: implement sensible logging
			if (!sLastSize.equals(Filesystem.formatFilesize(lReadBytes, 0))) {
				sLastSize = Filesystem.formatFilesize(lReadBytes, 0);
				Log.v("sbTools", "stream transfer: read " + sLastSize);
			}
		}
		
		Log.v("sbTools", "stream transfer complete");
		
		if (os != null) {
			try {
				os.close();
			} catch (IOException ex) {
				/* ok */
			}
		}
		if (is != null) {
			try {
				is.close();
			} catch (IOException ex) {
				/* ok */
			}
		}
		
	}
	
	public static void transferCompleteStream(InputStream is, OutputStream os, AsyncTask_DownloadAlbum taskTransfer) throws IOException {
		
		String sLastSize = "";
		long lBytesRead = 0;
		int iBufferSize = 4096;
		int iBufferFilled;
		byte[] binBuffer = new byte[iBufferSize];
		Stopwatch swUpdateTimer = new Stopwatch();
		swUpdateTimer.setInterval(250);
		while (-1 != (iBufferFilled = is.read(binBuffer))) {
			if (iBufferFilled < iBufferSize) {
				os.write(binBuffer, 0, iBufferFilled);
			} else {
				os.write(binBuffer);
			}
			lBytesRead += iBufferFilled;
			// TODO: implement sensible logging
			if (!sLastSize.equals(Filesystem.formatFilesize(lBytesRead, 0))) {
				sLastSize = Filesystem.formatFilesize(lBytesRead, 0);
				Log.v("sbTools", "stream transfer: read " + sLastSize);
			}
			if (taskTransfer != null && swUpdateTimer.checkInterval()) {
				taskTransfer.updateReadBytes(lBytesRead);
			}
		}
		
		taskTransfer.updateReadBytes(lBytesRead);
		
		Log.v("sbTools", "stream transfer complete");
		
		if (os != null) {
			try {
				os.close();
			} catch (IOException ex) {
				/* ok */
			}
		}
		if (is != null) {
			try {
				is.close();
			} catch (IOException ex) {
				/* ok */
			}
		}
		
	}

}
