package net.solidbytes.jukebox.nodes;

import java.util.*;

import net.solidbytes.jukebox.R;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbNode;
import net.solidbytes.tools.App;

import org.w3c.dom.*;

import android.content.Intent;
import android.app.Activity;
import android.net.Uri;
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
	
	protected int getIconID() {
		return R.drawable.ic_type_track;
	}
	
	public View getView(int iViewVariant) {
		
		switch (iViewVariant) {
		
		case 1:
			
			Activity activity = (Activity) App.Activity;
			LayoutInflater inflater = activity.getLayoutInflater();

			// Inflate the views from XML
			View vRow = inflater.inflate(R.layout.listentry_track, null);
			TextView vArtist = (TextView) vRow.findViewById(R.id.TrackArtist);
			TextView vTitle = (TextView) vRow.findViewById(R.id.TrackTitle);
			TextView vLength = (TextView) vRow.findViewById(R.id.TrackLength);
			
			String sLabel = this.getProperty("label");
			String[] aLabel = sLabel.split(" - ", 2);
			String sArtist = aLabel[0];
			String sTitle = aLabel[1];
			
			vArtist.setText(sArtist);
			vTitle.setText(sTitle);
			vLength.setText(this.getProperty("info_playtime"));
			
			vRow.setTag(R.id.sbNode, this);
		
			
			// Inflate the views from XML
//			View viewRow = inflater.inflate(R.layout.listentry_track_numbered, null);
//			TextView viewIndex = (TextView) viewRow.findViewById(R.id.TrackIndex);
//			TextView viewTitle = (TextView) viewRow.findViewById(R.id.TrackTitle);
//			
//			String sNumber = this.getProperty("info_index");
//			String sTitle = this.getProperty("info_title");
//			viewIndex.setText(sNumber);
//			viewTitle.setText(sTitle);
			
			return vRow;
			
		default:
			return super.getView(iViewVariant);
		
		}
		
	}
	
	public void play() {
//		String sStreamURL = sbConnection.getDomain() + "/play/" + this.getProperty("uuid") + "/" + sToken
//		Intent i = new Intent(android.content.Intent.ACTION_VIEW);
//		i.setDataAndType(Uri.parse(url), "audio/*");
//		App.Context.startActivity(i);
	}
	
}
