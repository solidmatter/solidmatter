package net.solidbytes.jukebox;

import java.util.List;

import net.solidbytes.jukebox.nodes.Node_Album;
import net.solidbytes.solidmatter.sbNode;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class LA_Various extends ArrayAdapter<sbNode> {

	public LA_Various(Activity activity, List<sbNode> lNodes) {
		super(activity, 0, lNodes);
	}
	
	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		
		sbNode nodeCurrent = getItem(position);
		return (nodeCurrent.getView(sbNode.VIEW_ROW_SIMPLE));

	}
	
	
}
