package net.solidbytes.jukebox;

import java.util.ArrayList;
import java.util.List;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import net.solidbytes.jukebox.connection.sbConnection;
import net.solidbytes.jukebox.connection.sbDOMResponse;
import net.solidbytes.jukebox.nodes.Album;
import net.solidbytes.jukebox.nodes.Artist;
import net.solidbytes.jukebox.nodes.Track;
import net.solidbytes.tools.SpinnerDialog;

import android.app.Activity;
import android.app.ListActivity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TableLayout;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;

public class Activity_Artist_Details extends sbJukeboxListActivity {

	protected Artist nodeArtist;
	List<Album>	lAlbums	= new ArrayList<Album>();
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		
		this.icon.setBackgroundResource(R.drawable.ic_header_artists);
		
		String sUUID = "";
		Bundle extras = getIntent().getExtras();
		if(extras !=null) {
			sUUID = extras.getString("artist_uuid");
		}
		
//		SpinnerDialog sdWait = new SpinnerDialog(this);
//		sdWait.show();
		
		try {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/" + sUUID);
			NodeList nodes = domResponse.getElementsByXPath("//sbnode[@master='true']");
			
			nodeArtist = new Artist((Element) nodes.item(0));
			lAlbums = nodeArtist.getAlbums();
			
			this.title.setText(nodeArtist.getProperty("label") + " - Alben");
	        
//	        if (lAlbums.isEmpty()) {
//				setContentView(R.layout.no_content);
//				return;
//			}
	        
	        setListAdapter(new LA_Albums(this, lAlbums));

			ListView lv = getListView();
			lv.setTextFilterEnabled(true);
			
			lv.setOnItemClickListener(new OnItemClickListener() {
				
				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					Intent myIntent = new Intent(view.getContext(), Activity_Album_Details.class);
					myIntent.putExtra("album_uuid", lAlbums.get(position).getProperty("uuid"));
					startActivityForResult(myIntent, 0);
				}

			});

			registerForContextMenu(lv);
	        
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error rendering artist >> " + e.getMessage() + " // " + e.toString());
            //throw new RuntimeException(e);
			
		}
		
//		sdWait.hide();
		
	}
	



}