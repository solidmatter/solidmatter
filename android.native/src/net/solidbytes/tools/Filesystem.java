package net.solidbytes.tools;

import java.io.File;
import java.io.IOException;
import java.text.NumberFormat;
import java.util.HashSet;
import java.util.Set;

import android.util.Log;

public class Filesystem {
	
	static final int BYTES = 0;
	static final int KILOBYTES = 1;
	static final int MEGABYTES = 2;
	static final int GIGABYTES = 3;
	
	public static long getFileSize(File dirFolder) {
		return (getFileSize(dirFolder, new HashSet<String>()));
	}

	public static long getFileSize(File folder, Set<String> history) {
		
		long foldersize = 0;
		
		try {
			
			if (folder.isDirectory()) {
				
				Log.d("sbTools", "getting directory size for " + folder.getCanonicalPath());
				
				File[] filelist = folder.listFiles();
				
				Log.d("sbTools", "found " + filelist.length + " files/dirs in " + folder.getCanonicalPath());
				
				for (int i = 0; i < filelist.length; i++) {
					
					boolean inHistory = history.contains(filelist[i].getCanonicalPath());
					
					history.add(filelist[i].getCanonicalPath());
					if (inHistory) {
						Log.e("sbTools", folder.getCanonicalPath() + " found in history");
					} else {
						foldersize += getFileSize(filelist[i], history);
					}
					
				}
			
			} else if (folder.isFile()) {
				
				Log.e("sbTools", folder.getCanonicalPath() + " is a " + folder.length() + " bytes file");
				return folder.length();
				
			} else {
				
				Log.e("sbTools", folder.getCanonicalPath() + " is neither a file nor a directory");
				
			}
		
		} catch (IOException e) {
			
			Log.e("sbTools", "Error while calculation diretory size for " + folder.getAbsolutePath() + ">> " + e.getMessage() + " // " + e.toString());
			
		}
		
		return foldersize;
		
	}
	
	public static String formatFilesize(long lFilesize) {
		return formatFilesize(lFilesize, -1);
	}
	
	/**
	 * TODO: allow forced units and think over the forced fraction digits
	 * @param lFilesize
	 * @param iNumFractionDigits
	 * @return
	 */
	public static String formatFilesize(long lFilesize, int iNumFractionDigits) {
		
		String sFilesize = "";
		float flFilesize = (float) lFilesize;
		NumberFormat nfCrap = NumberFormat.getInstance();
		int iFractionKB = 0;
		int iFractionMB = 2;
		int iFractionGB = 2;
		if (iNumFractionDigits != -1) {
			iFractionKB = iNumFractionDigits;
			iFractionMB = iNumFractionDigits;
			iFractionGB = iNumFractionDigits;
		}
		
		if (lFilesize < 1024) {
			sFilesize = lFilesize + " B";
		} else if (lFilesize < 1048576) {
			nfCrap.setMaximumFractionDigits(iFractionKB);
			nfCrap.setMinimumFractionDigits(iFractionKB);
			flFilesize = flFilesize / 1024;
			sFilesize = nfCrap.format(flFilesize) + " KB";
		} else if (lFilesize < 1073741824) {
			nfCrap.setMaximumFractionDigits(iFractionMB);
			nfCrap.setMinimumFractionDigits(iFractionMB);
			flFilesize = flFilesize / 1048576;
			sFilesize = nfCrap.format(flFilesize) + " MB";
		} else {
			nfCrap.setMaximumFractionDigits(iFractionGB);
			nfCrap.setMinimumFractionDigits(iFractionGB);
			flFilesize = flFilesize / 1073741824;
			sFilesize = nfCrap.format(flFilesize) + " GB";
			
		}
		
		return (sFilesize);

	}
	
	public static void deleteRecursive(File fileOrDirectory) {
		if (fileOrDirectory.isDirectory()) {
			for (File child : fileOrDirectory.listFiles()) {
				deleteRecursive(child);
			}
		}
		fileOrDirectory.delete();
	}
	
}
