package net.solidbytes.jukebox.objects;

import java.util.ArrayList;
import java.util.List;

import net.solidbytes.jukebox.connection.sbConnection;
import net.solidbytes.jukebox.connection.sbDOMResponse;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.util.Log;

public class Jukebox extends sbNode {
	
	public Jukebox(Element eJukebox) {
		
		hmProperties.put("uuid", "");
		hmProperties.put("name", "");
		hmProperties.put("label", "");
		
		super.fillByElement(eJukebox);
		
	}
	
	
	
	public static List<Artist> getArtists(String sSearch) throws Exception {
		
		List<Artist> lArtists = new ArrayList<Artist>();
		NodeList nodes = null;
		
		if (sSearch == null) {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/artists");
			nodes = domResponse.getElementsByXPath("/response/content/random/resultset/row");
			Log.d("sbJukebox", "extracted " + nodes.getLength() + " artists from XML");
			
		} else {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/artists/-/?show=" + sSearch);
			nodes = domResponse.getElementsByXPath("/response/content/search/resultset/row");
			Log.d("sbJukebox", "extracted " + nodes.getLength() + " artists from XML");
			
		}
		
		for (int i = 0; i < nodes.getLength(); i++) {
        	Element eCurrent = (Element) nodes.item(i);
        	lArtists.add(new Artist(eCurrent));
        }
		
		return (lArtists);
		
	}
	
	
}
