package net.solidbytes.jukebox;

import java.util.ArrayList;
import java.util.List;

import net.solidbytes.jukebox.nodes.Node_Album;
import net.solidbytes.jukebox.nodes.Node_Artist;
import net.solidbytes.jukebox.nodes.Node_Jukebox;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbDOMResponse;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.app.ListActivity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.AdapterView.OnItemClickListener;

public class Activity_Artists_List extends sbJukeboxListActivity {
	
	List<Node_Artist> lArtists = new ArrayList<Node_Artist>();
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {
		
		super.onCreate(savedInstanceState);
		
		this.icon.setBackgroundResource(R.drawable.ic_header_artists);
		
		
		String sShow = "";
		Bundle extras = getIntent().getExtras();
		if(extras !=null) {
			sShow = extras.getString("show");
		}
		
		try {
			
			if (sShow == "" || sShow == null) {
				lArtists = Node_Jukebox.getArtists(null);
				this.title.setText(R.string.labels_random_artists);
			} else {
				lArtists = Node_Jukebox.getArtists(sShow);
				this.title.setText(R.string.labels_artists_beginning_with + sShow);
			}
			
			if (lArtists.isEmpty()) {
				setContentView(R.layout.no_content);
				return;
			}
			
			
			setListAdapter(new LA_Artists(this, this.lArtists));
			
			ListView lv = getListView();
			lv.setTextFilterEnabled(true);
		
			lv.setOnItemClickListener(new OnItemClickListener() {

				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					Intent myIntent = new Intent(view.getContext(), Activity_Artist_Details.class);
					myIntent.putExtra("artist_uuid", lArtists.get(position).getProperty("uuid"));
					startActivityForResult(myIntent, 0);
				}

			});
			
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error rendering album list >> " + e.getMessage() + " // " + e.toString());
            //throw new RuntimeException(e);
			
		}
			
		

	}

}
