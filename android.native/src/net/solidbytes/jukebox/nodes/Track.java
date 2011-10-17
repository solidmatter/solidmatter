package net.solidbytes.jukebox.nodes;

import java.util.*;

import net.solidbytes.jukebox.R;
import net.solidbytes.tools.connection.sbConnection;

import org.w3c.dom.*;

import android.app.Activity;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;


public class Track extends sbNode {
	
	public static final int ROW_NUMBERED = 1;
	
	public Track(Element eTrack) {
		
		hmProperties.put("uuid", "");
		hmProperties.put("name", "");
		hmProperties.put("label", "");
		hmProperties.put("info_index", "");
		hmProperties.put("info_title", "");
		hmProperties.put("info_artist", "");
		hmProperties.put("info_playtime", "");
		hmProperties.put("info_lyrics", "");
		hmProperties.put("enc_bitrate", "");
		hmProperties.put("enc_playtime", "");
		
		super.fillByElement(eTrack);
		
		eNode = eTrack;
		
	}
	
	
	
//	public View getView(int iViewVariant) {
//		
//		switch (iViewVariant) {
//		
//		case 1:
//			
//			Activity activity = (Activity) sbConnection.getContext();
//			LayoutInflater inflater = activity.getLayoutInflater();
//			
//			// Inflate the views from XML
//			View viewRow = inflater.inflate(R.layout.listentry_track_numbered, null);
//			TextView viewIndex = (TextView) viewRow.findViewById(R.id.TrackIndex);
//			TextView viewTitle = (TextView) viewRow.findViewById(R.id.TrackTitle);
//			
//			String sNumber = this.getProperty("info_index");
//			String sTitle = this.getProperty("info_title");
//			viewIndex.setText(sNumber);
//			viewTitle.setText(sTitle);
//			
//			return viewRow;
//		
//		}
//		
//		return null;
//		
//	}
	
}
