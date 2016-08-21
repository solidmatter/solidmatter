package net.solidbytes.jukebox;

import java.io.InputStream;
import java.util.ArrayList;
import java.util.List;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

import net.solidbytes.jukebox.connection.sbConnection;
import net.solidbytes.jukebox.connection.sbDOMResponse;
import net.solidbytes.jukebox.objects.Album;
import android.app.Activity;
import android.app.ListActivity;
import android.content.Intent;
import android.content.res.Resources;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;

public class Activity_Albums extends ListActivity {
	
	List<Album> lAlbums = new ArrayList<Album>();
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		
		try {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/-/albums");
			//NodeList nodes = domResponse.getElementsByXPath("//sbnode[@master]/children[@mode='albums']/sbnode");
			NodeList nodes = domResponse.getElementsByXPath("/response/content/albums/row");
			
	        Log.d("sbJukebox", "extracted " + nodes.getLength() + " albums from XML");
	        
	        for (int i = 0; i < nodes.getLength(); i++) {
	        	Element eCurrent = (Element) nodes.item(i);
	        	lAlbums.add(new Album(eCurrent));
	        }
			
			setListAdapter(new LA_Albums(this, this.lAlbums));
			
			ListView lv = getListView();
			lv.setTextFilterEnabled(true);
		
			lv.setOnItemClickListener(new OnItemClickListener() {

				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					Intent myIntent = new Intent(view.getContext(), Activity_Album_Details.class);
					myIntent.putExtra("album_uuid", lAlbums.get(position).getProperty("uuid"));
					startActivityForResult(myIntent, 0);
				}

			});
			
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error rendering album list >> " + e.getMessage() + " // " + e.toString());
            //throw new RuntimeException(e);
			
		}
			
		

	}
    
}