package net.solidbytes.jukebox.nodes;

import java.util.ArrayList;
import java.util.List;

import net.solidbytes.jukebox.Activity_Album_Details;
import net.solidbytes.jukebox.R;
import net.solidbytes.tools.App;
import net.solidbytes.tools.connection.sbConnection;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.app.Activity;
import android.content.Intent;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;

public class Artist extends sbNode {
	
	public static final int ROW = 1;
	
	protected List<Album>				lAlbums	= new ArrayList<Album>();
	
	public Artist(Element eArtist) {
		
		hmProperties.put("uuid", "");
		hmProperties.put("name", "");
		hmProperties.put("label", "");
		
		super.fillByElement(eArtist);
		
	}
	
	public View getView(int iViewVariant) {
		
		String sArtist = this.getProperty("label");
		
		switch (iViewVariant) {
		
		case 1: // ROW
			
			// Inflate the views from XML
			View vRow = App.inflate(R.layout.listentry_artist, null);
			TextView tvName = (TextView) vRow.findViewWithTag("ArtistName");
			
			tvName.setText(sArtist);
			
			return vRow;
			
		}
		
		return null;
		
	}
	
	
	/**
	 * @return
	 */
	public List<Album> getAlbums() {
		
		NodeList nlTracks = super.getNodesByXPath("children[@mode='albums']/sbnode");

		Log.d("sbJukebox", "found " + nlTracks.getLength() + " Tracks");

		for (int i = 0; i < nlTracks.getLength(); i++) {
			Element eCurrent = (Element) nlTracks.item(i);
			Album aCurrent = new Album(eCurrent);
			lAlbums.add(aCurrent);
			Log.d("sbJukebox", "found album: " + aCurrent.getProperty("label"));
		}

		return lAlbums;

	}
	
}
