package net.solidbytes.jukebox;

import java.util.List;

import net.solidbytes.jukebox.nodes.Artist;
import net.solidbytes.tools.SimpleMenuEntry;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.TextView;

public class LA_SimpleMenu extends ArrayAdapter<SimpleMenuEntry> {
	
	public LA_SimpleMenu(Activity activity, List<SimpleMenuEntry> lEntries) {
		super(activity, 0, lEntries);
	}
	
	@Override
	public View getView(int position, View convertView, ViewGroup parent) {

		Activity activity = (Activity) getContext();
		LayoutInflater inflater = activity.getLayoutInflater();

		// Inflate the views from XML
		View vRow = inflater.inflate(R.layout.listentry_simplemenu, null);
		TextView tvName = (TextView) vRow.findViewWithTag("MenuEntry");
		
		SimpleMenuEntry oCurrent = getItem(position);
		tvName.setText(oCurrent.sLabel);
		
		return vRow;

	}

}