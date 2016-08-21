package net.solidbytes.jukebox;

import java.util.List;

import net.solidbytes.jukebox.objects.Album;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class LA_Albums extends ArrayAdapter<Album> {

	public LA_Albums(Activity activity, List<Album> lAlbums) {
		super(activity, 0, lAlbums);
	}
	
	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		
//		Activity activity = (Activity) getContext();
//		LayoutInflater inflater = activity.getLayoutInflater();
//		
//		// Inflate the views from XML
//		View rowView = inflater.inflate(R.layout.listentry_album, null);
//		TextView textViewArtist = (TextView) rowView.findViewById(R.id.album_artist);
//		TextView textViewTitle = (TextView) rowView.findViewById(R.id.album_title);
//		ImageView imageView = (ImageView) rowView.findViewById(R.id.album_cover);
//		
//		Album oCurrent = getItem(position);
//		String sLabel = oCurrent.getProperty("label");
//		String[] aLabel = sLabel.split(" - ", 2);
//		textViewArtist.setText(aLabel[0]);
//		textViewTitle.setText(aLabel[1]);
//		//imageView.setImageBitmap(oCurrent.getCover(50));
//		
//		return rowView;
		
		Album nodeCurrent = getItem(position);
		return (nodeCurrent.getView(Album.ROW));

	}
	
	
}
