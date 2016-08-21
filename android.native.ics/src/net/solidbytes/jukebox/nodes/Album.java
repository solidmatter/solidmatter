package net.solidbytes.jukebox.nodes;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.List;

import net.solidbytes.jukebox.R;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbNode;
import net.solidbytes.tools.App;
import net.solidbytes.tools.Filesystem;
import net.solidbytes.tools.Stopwatch;
import net.solidbytes.tools.Stream;
import net.solidbytes.tools.Zip;
import net.solidbytes.tools.AsyncTask_DownloadAlbum;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.Drawable;
import android.media.MediaScannerConnection;
import android.net.Uri;
import android.os.Environment;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

/**
 * @author ollo
 * 
 */
public class Album extends sbNode {

	public final static int	ROW				= 1;
	public final static int	DETAILS			= 2;

	List<Track>				lTracks			= new ArrayList<Track>();


	public Album(Element eAlbum) {

		hmProperties.put("uuid", "");
		hmProperties.put("name", "");
		hmProperties.put("label", "");
		hmProperties.put("info_artist", "");
		hmProperties.put("info_title", "");
		hmProperties.put("info_published", "");

		super.fillByElement(eAlbum);

		eNode = eAlbum;

	}
	
	protected int getIconID() {
		return R.drawable.ic_type_album;
	}
	
	/**
	 * @param iViewVariant
	 * @return
	 */
	public View getView(int iViewVariant) {

		String sLabel = this.getProperty("label");
		String[] aLabel = sLabel.split(" - ", 2);
		String sArtist = aLabel[0];
		String sTitle = aLabel[1];

		switch (iViewVariant) {

		case 1: // ROW

			// Inflate the views from XML
			View vRow = App.inflate(R.layout.listentry_album, null);
			TextView vArtist = (TextView) vRow.findViewById(R.id.album_artist);
			TextView vTitle = (TextView) vRow.findViewById(R.id.album_title);
			ImageView vCover = (ImageView) vRow.findViewById(R.id.album_cover);

			vRow.setTag(R.id.sbNode, this);

			vArtist.setText(sArtist);
			vTitle.setText(sTitle);
			this.attachCoverLoader(vCover, 100);

			return vRow;

		case 2: // DETAILS

			View vDetails = App.inflate(R.layout.album_details, null);

			TextView vAlbumTitle = (TextView) vDetails.findViewById(R.id.AlbumTitle);
			TextView vArtist1 = (TextView) vDetails.findViewById(R.id.AlbumArtist);
			ImageView vCover1 = (ImageView) vDetails.findViewById(R.id.AlbumCover);
			
			vArtist1.setText(sArtist);
			vAlbumTitle.setText(sTitle);
			this.attachCoverLoader(vCover1, 144);

			TableLayout vTracks = (TableLayout) vDetails.findViewById(R.id.TrackList);
			List<Track> lTracks = getTracks();
			for (int i = 0; i < lTracks.size(); i++) {
				TableRow vTrack = (TableRow) App.inflate(R.layout.listentry_track_numbered, null);
				TextView vTrackIndex = (TextView) vTrack.findViewWithTag("TrackIndex");
				TextView vTrackTitle = (TextView) vTrack.findViewWithTag("TrackTitle");
				Track nodeTrack = lTracks.get(i);
				vTrackIndex.setText(nodeTrack.getProperty("info_index"));
				vTrackTitle.setText(nodeTrack.getProperty("info_title"));
				if (i % 2 == 1) {
					vTrack.setBackgroundResource(R.color.odd);
				} else {
					vTrack.setBackgroundResource(R.color.even);
				}
				vTracks.addView(vTrack);
			}

			return vDetails;
			
			default:
				return super.getView(iViewVariant);
			
		}

	}

	/**
	 * @return
	 */
	public List<Track> getTracks() {

		NodeList nlTracks = super.getNodesByXPath("children[@mode='tracks']/sbnode");

		Log.d("sbJukebox", "found " + nlTracks.getLength() + " Tracks");

		for (int i = 0; i < nlTracks.getLength(); i++) {
			Element eCurrent = (Element) nlTracks.item(i);
			Track tCurrent = new Track(eCurrent);
			lTracks.add(tCurrent);
			Log.d("sbJukebox", "found track: " + tCurrent.getProperty("label"));
		}

		return lTracks;

	}
	
	/**
	 * @param iSize
	 * @return
	 */
	public Drawable getCover(int iSize) {

		try {

			InputStream is = null;

			File dirCache = App.getExternalCacheFile("/covers/" + iSize + "/");
			dirCache.mkdirs();
			File fileCache = new File(dirCache, hmProperties.get("uuid") + ".jpg");

			if (!fileCache.exists()) {
				
				Log.d("sbJukebox", "saving new cover under " + fileCache.getAbsolutePath());

				is = sbConnection.getStream("/" + hmProperties.get("uuid") + "/details/getCover/?size=" + iSize, null);
				OutputStream os = new FileOutputStream(fileCache);
				
				Stream.transferCompleteStream(is, os);

			}

			Log.d("sbJukebox", "loading cover from " + fileCache.getAbsolutePath());

			is = new FileInputStream(fileCache);
			Drawable d = Drawable.createFromStream(is, "src");
			return d;
			
			// Bitmap img = BitmapFactory.decodeStream(is);
			// return img;
			
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error retrieving album cover >> " + e.getMessage() + " // " + e.toString());
			return null;
			
		}

	}
	
	/**
	 * @param vCurrent
	 * @param iSize
	 */
	public void attachCoverLoader(final View vCurrent, final int iSize) {

		final Handler handler = new Handler() {
			@Override
			public void handleMessage(Message message) {
				vCurrent.setBackgroundDrawable((Drawable) message.obj);
			}
		};

		Thread thread = new Thread() {
			@Override
			public void run() {
				// TODO : set imageView to a "pending" image
				Message message = handler.obtainMessage(1, getCover(iSize));
				handler.sendMessage(message);
			}
		};
		thread.start();

	}
	
	/**
	 * @param iSize
	 * @return
	 */
	public String getPlaylist() {

		try {

			InputStream is = null;

			File dirCache = App.getExternalCacheFile("/playlists/");
			dirCache.mkdirs();
			File fileCache = new File(dirCache, hmProperties.get("uuid") + ".m3u");

			if (!fileCache.exists()) {
				
				Log.d("sbJukebox", "saving new playlist under " + fileCache.getAbsolutePath());

				is = sbConnection.getStream("/" + hmProperties.get("uuid") + "/details/getM3U/", null);
				OutputStream os = new FileOutputStream(fileCache);
				
				Stream.transferCompleteStream(is, os);

			}

			Log.d("sbJukebox", "playlist URI is file://" + fileCache.getAbsolutePath());

			return "file://" + fileCache.getAbsolutePath();
			
			// Bitmap img = BitmapFactory.decodeStream(is);
			// return img;
			
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error retrieving album cover >> " + e.getMessage() + " // " + e.toString());
			return null;
			
		}

	}
	
	
	/**
	 * @param 
	 * @return
	 */
//	public void download() {
//		
//		new AsyncTask_DownloadAlbum().execute(this);
//
//	}

}