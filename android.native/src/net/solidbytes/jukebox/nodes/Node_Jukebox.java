package net.solidbytes.jukebox.nodes;

import java.util.ArrayList;
import java.util.List;

import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbDOMResponse;
import net.solidbytes.solidmatter.sbNode;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.util.Log;

public class Node_Jukebox extends sbNode {
	
	public Node_Jukebox(Element eJukebox) {
		
		hmProperties.put("uuid", "");
		hmProperties.put("name", "");
		hmProperties.put("label", "");
		
		super.fillByElement(eJukebox);
		
	}
	
	
	
	public static List<Node_Artist> getArtists(String sSearch) throws Exception {
		
		List<Node_Artist> lArtists = new ArrayList<Node_Artist>();
		NodeList nodes = null;
		
		if (sSearch == null) {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/artists");
			nodes = domResponse.getElementsByXPath("/response/content/artists/row");
			Log.d("sbJukebox", "extracted " + nodes.getLength() + " artists from XML");
			
		} else {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/artists/-/?show=" + sSearch);
			nodes = domResponse.getElementsByXPath("/response/content/artists/row");
			Log.d("sbJukebox", "extracted " + nodes.getLength() + " artists from XML");
			
		}
		
		for (int i = 0; i < nodes.getLength(); i++) {
        	Element eCurrent = (Element) nodes.item(i);
        	lArtists.add(new Node_Artist(eCurrent));
        }
		
		return (lArtists);
		
	}
	
	public static List<Node_Album> getAlbums(String sSearch) throws Exception {
		
		List<Node_Album> lAlbums = new ArrayList<Node_Album>();
		NodeList nodes = null;
		
		if (sSearch == null) {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/albums");
			nodes = domResponse.getElementsByXPath("/response/content/albums/row");
			Log.d("sbJukebox", "extracted " + nodes.getLength() + " albums from XML");
			
		} else {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/albums/-/?show=" + sSearch);
			nodes = domResponse.getElementsByXPath("/response/content/albums/row");
			Log.d("sbJukebox", "extracted " + nodes.getLength() + " albums from XML");
			
		}
		
		for (int i = 0; i < nodes.getLength(); i++) {
        	Element eCurrent = (Element) nodes.item(i);
        	lAlbums.add(new Node_Album(eCurrent));
        }
		
		return (lAlbums);
		
	}
	
	public static List<sbNode> search(String sSearch) throws Exception {
		
		List<sbNode> lMatches = new ArrayList<sbNode>();
		NodeList nodes = null;
		
		sbDOMResponse domResponse = sbConnection.sendRequest("/-/library/search/?searchstring=" + sSearch);
		nodes = domResponse.getElementsByXPath("/response/content/searchresult/resultset/row");
		Log.d("sbJukebox", "extracted " + nodes.getLength() + " nodes from XML");
		
		for (int i = 0; i < nodes.getLength(); i++) {
        	Element eCurrent = (Element) nodes.item(i);
//        	Log.d("sbJukebox", "found node " + eCurrent.getAttribute("label") + " of type " + eCurrent.getAttribute("nodetype"));
        	if (eCurrent.getAttribute("nodetype").contentEquals("sbJukebox:Artist")) {
        		lMatches.add(new Node_Artist(eCurrent));
        	}
        }
		for (int i = 0; i < nodes.getLength(); i++) {
        	Element eCurrent = (Element) nodes.item(i);
        	if (eCurrent.getAttribute("nodetype").contentEquals("sbJukebox:Album")) {
        		lMatches.add(new Node_Album(eCurrent));
        	}
        }
		for (int i = 0; i < nodes.getLength(); i++) {
        	Element eCurrent = (Element) nodes.item(i);
        	if (eCurrent.getAttribute("nodetype").contentEquals("sbJukebox:Track")) {
        		lMatches.add(new Node_Track(eCurrent));
        	}
        }
		
		Log.d("sbJukebox", "added " + lMatches.size() + " nodes to list");
		
		return (lMatches);
		
	}
	
	
}
