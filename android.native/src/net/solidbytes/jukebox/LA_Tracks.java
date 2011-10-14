package net.solidbytes.jukebox;

import java.util.List;

import net.solidbytes.jukebox.nodes.Album;
import net.solidbytes.jukebox.nodes.Artist;
import net.solidbytes.jukebox.nodes.Track;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListAdapter;
import android.widget.TextView;

public class LA_Tracks extends ArrayAdapter<Track> {
	
	public LA_Tracks(Activity activity, List<Track> lTracks) {
		super(activity, 0, lTracks);
	}
	
	@Override
	public View getView(int position, View convertView, ViewGroup parent) {

		Activity activity = (Activity) getContext();
		LayoutInflater inflater = activity.getLayoutInflater();

		// Inflate the views from XML
		View vRow = inflater.inflate(R.layout.listentry_track, null);
		TextView vArtist = (TextView) vRow.findViewById(R.id.TrackArtist);
		TextView vTitle = (TextView) vRow.findViewById(R.id.TrackTitle);
		TextView vLength = (TextView) vRow.findViewById(R.id.TrackLength);
		
		Track oCurrent = getItem(position);
		
		String sLabel = oCurrent.getProperty("label");
		String[] aLabel = sLabel.split(" - ", 2);
		String sArtist = aLabel[0];
		String sTitle = aLabel[1];
		
		vArtist.setText(sArtist);
		vTitle.setText(sTitle);
		vLength.setText(oCurrent.getProperty("info_playtime"));
		
		return vRow;

	}
	
}
