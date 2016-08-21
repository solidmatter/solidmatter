package net.solidbytes.jukebox;

import java.util.ArrayList;
import java.util.List;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import net.solidbytes.jukebox.nodes.Node_Album;
import net.solidbytes.jukebox.nodes.Node_Artist;
import net.solidbytes.jukebox.nodes.Node_Jukebox;
import net.solidbytes.jukebox.nodes.Node_Track;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbDOMResponse;
import net.solidbytes.tools.SimpleMenuEntry;

import android.app.Activity;
import android.app.ListActivity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ScrollView;
import android.widget.TableLayout;
import android.widget.TableRow;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;


public class Activity_Artists extends sbJukeboxGridActivity {
	
	List<SimpleMenuEntry> lEntries = new ArrayList<SimpleMenuEntry>();
	
	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		
		title.setText(R.string.labels_artists);
		icon.setBackgroundResource(R.drawable.ic_header_artists);
		
		lEntries.add(new SimpleMenuEntry("?", null));
		lEntries.add(new SimpleMenuEntry("#", "0-9"));
		lEntries.add(new SimpleMenuEntry("A", "A"));
		lEntries.add(new SimpleMenuEntry("B", "B"));
		lEntries.add(new SimpleMenuEntry("C", "C"));
		lEntries.add(new SimpleMenuEntry("D", "D"));
		lEntries.add(new SimpleMenuEntry("E", "E"));
		lEntries.add(new SimpleMenuEntry("F", "F"));
		lEntries.add(new SimpleMenuEntry("G", "G"));
		lEntries.add(new SimpleMenuEntry("H", "H"));
		lEntries.add(new SimpleMenuEntry("I", "I"));
		lEntries.add(new SimpleMenuEntry("J", "J"));
		lEntries.add(new SimpleMenuEntry("K", "K"));
		lEntries.add(new SimpleMenuEntry("L", "L"));
		lEntries.add(new SimpleMenuEntry("M", "M"));
		lEntries.add(new SimpleMenuEntry("N", "N"));
		lEntries.add(new SimpleMenuEntry("O", "O"));
		lEntries.add(new SimpleMenuEntry("P", "P"));
		lEntries.add(new SimpleMenuEntry("Q", "Q"));
		lEntries.add(new SimpleMenuEntry("R", "R"));
		lEntries.add(new SimpleMenuEntry("S", "S"));
		lEntries.add(new SimpleMenuEntry("T", "T"));
		lEntries.add(new SimpleMenuEntry("U", "U"));
		lEntries.add(new SimpleMenuEntry("V", "V"));
		lEntries.add(new SimpleMenuEntry("W", "W"));
		lEntries.add(new SimpleMenuEntry("X", "X"));
		lEntries.add(new SimpleMenuEntry("Y", "Y"));
		lEntries.add(new SimpleMenuEntry("Z", "Z"));
		
		
		
		
		try {
			
			GridView gridview = (GridView) findViewById(R.id.grid);
			
			gridview.setAdapter(new LA_GridMenu(this, lEntries));

		    gridview.setOnItemClickListener(new OnItemClickListener() {
		        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
		        	
		        	Intent myIntent = new Intent(view.getContext(), Activity_Artists_List.class);
					myIntent.putExtra("show", lEntries.get(position).sSearch);
					
					startActivityForResult(myIntent, 0);
					
		        }
		    });
			
			
			/*setListAdapter(new LA_SimpleMenu(this, this.lEntries));
			
			ListView lv = getListView();
			lv.setTextFilterEnabled(true);
			
			lv.setOnItemClickListener(new OnItemClickListener() {

				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
					Intent myIntent = new Intent(view.getContext(), Activity_Artists_List.class);
					myIntent.putExtra("show", lEntries.get(position).sSearch);
					startActivityForResult(myIntent, 0);
				}

			});*/
			
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error rendering artist list >> " + e.getMessage() + " // " + e.toString());
            //throw new RuntimeException(e);
			
		}
			
		

	}
    
}

/*public class Activity_Artists extends Activity {
    
	List<Artist> lArtists = new ArrayList<Artist>();
	
	
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);
		
		setContentView(R.layout.jukebox_artists);
		
//		ScrollView vScroller = (ScrollView) this.findViewById(R.id.ArtistListScroller);
//		vScroller.
		
//		lv.setOnItemClickListener(new OnItemClickListener() {
//
//			@Override
//			public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
//				Intent myIntent = new Intent(view.getContext(), Activity_RSSFeed.class);
//				myIntent.putExtra("feed_link", lFeeds.get(position).getLink());
//				myIntent.putExtra("feed_title", lFeeds.get(position).getTitle());
//				startActivityForResult(myIntent, 0);
//			}
//
//		});

	}
	
	@Override
	public void onResume() {
		
		super.onResume();
		
		try {
			
			lArtists = Jukebox.getArtists(null);
			
			LinearLayout ArtistList = (LinearLayout) this.findViewById(R.id.ArtistList);
			ArtistList.removeAllViews();
			
			for (int i=0; i<lArtists.size(); i++) {
				
				LinearLayout trArtist = (LinearLayout) lArtists.get(i).getView(Artist.ROW);
				
				if (i % 2 == 1) {
					trArtist.setBackgroundResource(R.color.odd);
				} else {
					trArtist.setBackgroundResource(R.color.even);
				}
				
				trArtist.setOnClickListener(lArtists.get(i).getNavigationOnClickListener());
				
//				trArtist.setOnClickListener(new OnClickListener() {
//					public void onClick(View view) {
//						view.setBackgroundColor(R.color.focused);
////						Intent myIntent = new Intent(view.getContext(), Activity_Album_Details.class);
////						myIntent.putExtra("album_uuid", lArtists.get(i).getProperty("uuid"));
////						startActivityForResult(myIntent, 0);
//					}
//				});
				
				ArtistList.addView(trArtist);
			}
		
		} catch (Exception e) {
			
			Log.e("sbJukebox", "Error rendering artist list >> " + e.getMessage() + " // " + e.toString());
			//throw new RuntimeException(e);
		}
		
	}
	
	
}*/