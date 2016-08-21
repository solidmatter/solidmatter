package net.solidbytes.jukebox;

import net.solidbytes.solidmatter.sbConnection;
import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.Window;
import android.widget.ImageView;
import android.widget.TextView;

public class sbJukeboxActivity extends Activity {

    protected TextView title;
    protected ImageView icon;
    
    @Override
    public void onCreate(Bundle savedInstanceState) {
    	
        super.onCreate(savedInstanceState);
 
        requestWindowFeature(Window.FEATURE_CUSTOM_TITLE);
        getWindow().setFeatureInt(Window.FEATURE_CUSTOM_TITLE, R.layout.custom_window_title);
 
        title = (TextView) findViewById(R.id.header_title);
        icon  = (ImageView) findViewById(R.id.header_icon);
        
    }
    
    @Override
    public void onResume() {
		super.onResume();
		sbConnection.connect();
	}
	
}
