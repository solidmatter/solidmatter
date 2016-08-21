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

import net.solidbytes.jukebox.nodes.Node_Album;
import net.solidbytes.jukebox.nodes.Node_Jukebox;
import net.solidbytes.solidmatter.sbConnection;
import net.solidbytes.solidmatter.sbDOMResponse;
import net.solidbytes.solidmatter.sbNode;
import net.solidbytes.tools.Logg;
import android.app.Activity;
import android.app.Dialog;
import android.app.ListActivity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.res.Resources;
import android.os.Bundle;
import android.util.Log;
import android.view.ContextMenu;
import android.view.ContextMenu.ContextMenuInfo;
import android.view.MenuItem;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.AdapterContextMenuInfo;
import android.widget.AdapterView.OnItemLongClickListener;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;

public class Activity_Jukebox_Library_Search extends sbJukeboxListActivity {

	List<sbNode>	lMatches	= new ArrayList<sbNode>();

	/** Called when the activity is first created. */
	@Override
	public void onCreate(Bundle savedInstanceState) {

		super.onCreate(savedInstanceState);

//		this.icon.setBackgroundResource(R.drawable.ic_header_albums);
		
		String sSearchString = "";
		Bundle extras = getIntent().getExtras();
		if (extras != null) {
			sSearchString = extras.getString("searchstring");
		}

		try {
			
			this.title.setText(getString(R.string.labels_search_for) + " " + sSearchString);
			
			lMatches = Node_Jukebox.search(sSearchString);
			setListAdapter(new LA_Various(this, lMatches));
			
			ListView lv = getListView();
			lv.setTextFilterEnabled(true);

//			lv.setOnItemClickListener(new OnItemClickListener() {
//
//				public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
//					Intent myIntent = new Intent(view.getContext(), Activity_Album_Details.class);
//					myIntent.putExtra("album_uuid", lAlbums.get(position).getProperty("uuid"));
//					startActivityForResult(myIntent, 0);
//				}
//
//			});
//
//			registerForContextMenu(lv);
			
			
			
			

		} catch (Exception e) {

			Logg.e("sbJukebox", e);

		}

	}

//	@Override
//	public void onCreateContextMenu(ContextMenu menu, View v, ContextMenuInfo menuInfo) {
//
//		super.onCreateContextMenu(menu, v, menuInfo);
//
//		AdapterContextMenuInfo info = (AdapterContextMenuInfo) menuInfo;
//
//		sbNode nodeAlbum = (sbNode) info.targetView.getTag(R.id.sbNode);
//
//		menu.setHeaderTitle(nodeAlbum.getProperty("label"));
//		menu.add(0, R.id.download, 0, "Download");
//
//	}
//
//	@Override
//	public boolean onContextItemSelected(MenuItem item) {
//		
//		AdapterContextMenuInfo info = (AdapterContextMenuInfo) item.getMenuInfo();
//		
//		Album nodeAlbum = (Album) info.targetView.getTag(R.id.sbNode);
//		
//		switch (item.getItemId()) {
//		case R.id.download:
//			new AsyncTask_DownloadAlbum((Activity) this).execute(nodeAlbum);
//			//nodeAlbum.download();
//			return true;
//		default:
//			return super.onContextItemSelected(item);
//		}
//	}

}