package net.solidbytes.tools;

import net.solidbytes.jukebox.R;
import net.solidbytes.jukebox.connection.sbConnection;
import net.solidbytes.jukebox.nodes.sbNode;
import android.content.Context;
import android.content.Intent;
import android.view.View;
import android.view.View.OnClickListener;

public class NavigationOnClickListener implements OnClickListener {
	
	sbNode nodeSubject;
	
	public NavigationOnClickListener(sbNode nodeCurrent) {
		nodeSubject = nodeCurrent;
	}
	
	public void onClick(View v) {
		
		v.setBackgroundResource(R.color.focused);
		
		Intent myIntent = nodeSubject.getIntent(v.getContext());
		myIntent.putExtra("album_uuid", nodeSubject.getProperty("uuid"));
		//sbConnection.getContext().startActivityForResult(myIntent, 0);
		
	}
	
}
