package net.solidbytes.jukebox.objects;

import java.io.IOException;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.List;

import net.solidbytes.jukebox.R;
import net.solidbytes.jukebox.connection.sbConnection;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.app.Activity;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.Drawable;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;

public class Album extends sbNode {
	
	public final static int ROW = 1;
	public final static int DETAILS = 2;
	
	List<Track> lTracks = new ArrayList<Track>();
	
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
	
	public View getView(int iViewVariant) {
		
		Activity activity = (Activity) sbConnection.getContext();
		LayoutInflater inflater = activity.getLayoutInflater();
		
		String sLabel = this.getProperty("label");
		String[] aLabel = sLabel.split(" - ", 2);
		String sArtist = aLabel[0];
		String sTitle = aLabel[1];
		
		switch (iViewVariant) {
		
		case 1: // ROW
			
			// Inflate the views from XML
			View viewRow = inflater.inflate(R.layout.listentry_album, null);
			TextView viewArtist = (TextView) viewRow.findViewById(R.id.album_artist);
			TextView viewTitle = (TextView) viewRow.findViewById(R.id.album_title);
			ImageView viewCover = (ImageView) viewRow.findViewById(R.id.album_cover);
			
			viewArtist.setText(sArtist);
			viewTitle.setText(sTitle);
			//viewCover.setImageBitmap(oCurrent.getCover(50));
			
			return viewRow;
		
		case 2: // DETAILS
			
			View viewDetails = inflater.inflate(R.layout.album_details, null);
			
			TextView viewAlbumTitle = (TextView) viewDetails.findViewById(R.id.AlbumTitle);
			TextView viewArtist1 = (TextView) viewDetails.findViewById(R.id.AlbumArtist);
			ImageView viewCover1 = (ImageView) viewDetails.findViewById(R.id.AlbumCover);
			
			viewArtist1.setText(sArtist);
			viewAlbumTitle.setText(sTitle);
			// imageView.setImageBitmap(oCurrent.getCover(50));
			
			TableLayout viewTracks = (TableLayout) viewDetails.findViewById(R.id.TrackList);
			List<Track> lTracks = getTracks();
			for (int i=0; i<lTracks.size(); i++) {
				TableRow trTrack = (TableRow) inflater.inflate(R.layout.listentry_track_numbered, null);
				TextView viewTrackIndex = (TextView) trTrack.findViewWithTag("TrackIndex");
				TextView viewTrackTitle = (TextView) trTrack.findViewWithTag("TrackTitle");
				Track nodeTrack = lTracks.get(i);
				viewTrackIndex.setText(nodeTrack.getProperty("info_index"));
				viewTrackTitle.setText(nodeTrack.getProperty("info_title"));
				if (i % 2 == 1) {
					trTrack.setBackgroundResource(R.color.odd);
				} else {
					trTrack.setBackgroundResource(R.color.even);
				}
				viewTracks.addView(trTrack);
			}
			
			return viewDetails;
			
		}
		
		return null;
		
	}
	
	public List<Track> getTracks() {
		
		NodeList nlTracks = super.getNodesByXPath("children[@mode='tracks']/sbnode");
		
		Log.d("sbJukebox", "found " + nlTracks.getLength() + " Tracks");
		
		for (int i = 0; i < nlTracks.getLength(); i++) {
        	Element eCurrent = (Element) nlTracks.item(i);
        	Track tCurrent = new Track(eCurrent);
        	lTracks.add(tCurrent);
        	Log.d("sbJukebox", "found Track: " + tCurrent.getProperty("label"));
        }
		
		return lTracks;
		
	}
	
	public Bitmap getCover(int iSize) {
		
		try {
			InputStream is = sbConnection.getStream("/" + hmProperties.get("uuid") + "/details/getCover/?size=" + iSize);
			Bitmap img = BitmapFactory.decodeStream(is);
			return img;
			//Drawable d = Drawable.createFromStream(is, "src");
			//return d;
		} catch (Exception e) {
			Log.e("sbJukebox", "Error retrieving album cover >> " + e.getMessage() + " // " + e.toString());
			return null;
		}
		
	}
	
}