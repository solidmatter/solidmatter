package net.solidbytes.jukebox;

import java.util.ArrayList;
import java.util.List;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import net.solidbytes.jukebox.nodes.Node_Album;
import net.solidbytes.jukebox.nodes.Node_Jukebox;
import net.solidbytes.jukebox.nodes.Node_Track;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbDOMResponse;
import net.solidbytes.solidmatter.sbNode;
import net.solidbytes.tools.SpinnerDialog;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.ContextMenu;
import android.view.LayoutInflater;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.view.ContextMenu.ContextMenuInfo;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TableLayout;
import android.widget.TextView;
import android.widget.AdapterView.AdapterContextMenuInfo;

public class Activity_Album_Details extends sbJukeboxListActivity {

	protected Node_Album nodeAlbum;
	protected List<Node_Track> lTracks = new ArrayList<Node_Track>();
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		
		this.icon.setBackgroundResource(R.drawable.ic_header_albums);
		
//		SpinnerDialog sdWait = new SpinnerDialog(this);
//		sdWait.show();
		
		
		String sUUID = "";
		Bundle extras = getIntent().getExtras();
		if(extras !=null) {
			sUUID = extras.getString("album_uuid");
		}
		
		try {
			
			sbDOMResponse domResponse = sbConnection.sendRequest("/" + sUUID);
			NodeList nodes = domResponse.getElementsByXPath("//sbnode[@master='true']");
			
	        nodeAlbum = new Node_Album((Element) nodes.item(0));
			
	        title.setText(nodeAlbum.getProperty("label"));
			
	        lTracks = nodeAlbum.getTracks();
			
			setListAdapter(new LA_Tracks(this, lTracks));

			ListView lv = getListView();
			lv.setTextFilterEnabled(true);
	        
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error rendering album >> " + e.getMessage() + " // " + e.toString());
            throw new RuntimeException(e);
			
		}
		
//		sdWait.hide();
		
	}

	@Override
	public void onCreateContextMenu(ContextMenu menu, View v, ContextMenuInfo menuInfo) {

		super.onCreateContextMenu(menu, v, menuInfo);

		AdapterContextMenuInfo info = (AdapterContextMenuInfo) menuInfo;

		sbNode nodeTrack = (sbNode) info.targetView.getTag(R.id.sbNode);

		menu.setHeaderTitle(nodeTrack.getProperty("label"));
		menu.add(0, R.id.play, 0, "Play");

	}

	@Override
	public boolean onContextItemSelected(MenuItem item) {
		
		AdapterContextMenuInfo info = (AdapterContextMenuInfo) item.getMenuInfo();
		
		Node_Track nodeTrack = (Node_Track) info.targetView.getTag(R.id.sbNode);
		
		switch (item.getItemId()) {
		case R.id.play:
			nodeTrack.play();
			return true;
		default:
			return super.onContextItemSelected(item);
		}
	}
}