package net.solidbytes.jukebox.objects;

import net.solidbytes.jukebox.Activity_Album_Details;
import net.solidbytes.jukebox.R;
import net.solidbytes.jukebox.connection.sbConnection;

import org.w3c.dom.Element;

import android.app.Activity;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;

public class Artist extends sbNode {
	
	public static final int ROW = 1;
	
	public Artist(Element eArtist) {
		
		hmProperties.put("uuid", "");
		hmProperties.put("name", "");
		hmProperties.put("label", "");
		
		super.fillByElement(eArtist);
		
	}
	
	public View getView(int iViewVariant) {
		
		Activity activity = (Activity) sbConnection.getContext();
		LayoutInflater inflater = activity.getLayoutInflater();
		
		String sArtist = this.getProperty("label");
		
		switch (iViewVariant) {
		
		case 1: // ROW
			
			// Inflate the views from XML
			View vRow = inflater.inflate(R.layout.listentry_artist, null);
			TextView tvName = (TextView) vRow.findViewWithTag("ArtistName");
			
			tvName.setText(sArtist);
			
			return vRow;
			
		}
		
		return null;
		
	}
	
}
