package net.solidbytes.tools.archive;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.zip.ZipEntry;
import java.util.zip.ZipFile;

import net.solidbytes.tools.Arrays;

import android.util.Log;

public class Zip {

	public static String[] unzip(File fileSource, File dirDestination) {

		ArrayList<String> aFilenames = new ArrayList<String>();
		
		try {
			
//			File fSourceZip = new File(strZipFile);
//			String zipPath = strZipFile.substring(0, strZipFile.length()-4);
			dirDestination.mkdir();
			
			ZipFile zipFile = new ZipFile(fileSource);
			Enumeration e = zipFile.entries();

			while (e.hasMoreElements()) {
				
				ZipEntry entry = (ZipEntry) e.nextElement();
				File destinationFilePath = new File(dirDestination, entry.getName());

				// create directories if required.
				destinationFilePath.getParentFile().mkdirs();

				// if the entry is directory, leave it. Otherwise extract it.
				if (entry.isDirectory()) {
					
					continue;
					
				} else {
					
					Log.d("sbTools", "Extracting " + destinationFilePath);

					/*
					 * Get the InputStream for current entry of the zip file
					 * using
					 * 
					 * InputStream getInputStream(Entry entry) method.
					 */
					BufferedInputStream bis = new BufferedInputStream(zipFile.getInputStream(entry));

					int b;
					byte buffer[] = new byte[1024];

					/*
					 * read the current entry from the zip file, extract it and
					 * write the extracted file.
					 */
					FileOutputStream fos = new FileOutputStream(destinationFilePath);
					BufferedOutputStream bos = new BufferedOutputStream(fos, 1024);

					while ((b = bis.read(buffer, 0, 1024)) != -1) {
						bos.write(buffer, 0, b);
					}

					// flush the output stream and close it.
					bos.flush();
					bos.close();

					// close the input stream.
					bis.close();
					
					aFilenames.add(destinationFilePath.getCanonicalPath());
					
				}
			}
		} catch (IOException ioe) {
			Log.e("sbTools", "Zip extracting failed: " + ioe);
		}
		
		return Arrays.convertToStringArray(aFilenames);

	}
}
