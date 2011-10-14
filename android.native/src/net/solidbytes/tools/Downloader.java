package net.solidbytes.tools;

import java.io.File;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import android.os.Environment;
import android.util.Log;

public class Downloader {
	
	public void downloadStream(InputStream is, String sDirectory, String fileName) {
    	
        try {
        	
            File sdcard = Environment.getExternalStorageDirectory();
            File dir = new File (sdcard.getAbsolutePath() + sDirectory); // sDirectory = "/dir1/dir2"
            dir.mkdirs();
            //File f = new File(dir, "filename");
            FileOutputStream f = new FileOutputStream(new File(sdcard, fileName));
            
            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = is.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            
        } catch (Exception e) {
        	
            Log.e("sbTools", "Exception in Downloader.downloadStream() (" + e.getMessage() + ")");
            
        }

    }
	
    public void downloadFile(String fileURL, String fileName) {
    	
        try {
        	
            File root = Environment.getExternalStorageDirectory();
            URL u = new URL(fileURL);
            HttpURLConnection c = (HttpURLConnection) u.openConnection();
            c.setRequestMethod("GET");
            c.setDoOutput(true);
            c.connect();
            FileOutputStream f = new FileOutputStream(new File(root, fileName));

            InputStream in = c.getInputStream();

            byte[] buffer = new byte[1024];
            int len1 = 0;
            while ((len1 = in.read(buffer)) > 0) {
                f.write(buffer, 0, len1);
            }
            f.close();
            
        } catch (Exception e) {
        	
        	Log.e("sbTools", "Exception in Downloader.downloadFile() (" + e.getMessage() + ")");
            
        }

    }
    
}
