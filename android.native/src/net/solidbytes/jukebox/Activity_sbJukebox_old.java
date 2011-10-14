package net.solidbytes.jukebox;

import net.solidbytes.jukebox.connection.sbConnection;
import net.solidbytes.tools.SpinnerDialog;

import android.app.Activity;
import android.app.TabActivity;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Resources;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.widget.TabHost;
import android.widget.Toast;

public class Activity_sbJukebox_old extends TabActivity {

	/** Called when the activity is first created. */
	/*@Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
    }*/
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		
		Log.i("sbJukebox", "sbJukebox ist starting...");
		
		super.onCreate(savedInstanceState);
		setContentView(R.layout.main);
		
		App.setContext(this);
		
		if (sbConnection.connect()) {
			Log.i("sbJukebox", "login to " + sbConnection.getDomain() + " successful");
		} else {
			Log.i("sbJukebox", "login failed: " + sbConnection.sError);
		}

		Resources res = getResources(); // Resource object to get Drawables
		TabHost tabHost = getTabHost();  // The activity TabHost
		TabHost.TabSpec spec;  // Resusable TabSpec for each tab
		Intent intent;  // Reusable Intent for each tab

		// Create an Intent to launch an Activity for the tab (to be reused)
		intent = new Intent().setClass(this, Activity_Artists.class);

		// Initialize a TabSpec for each tab and add it to the TabHost
		spec = tabHost.newTabSpec("artists").setIndicator("Artists",
				res.getDrawable(R.drawable.ic_tab_artists))
				.setContent(intent);
		tabHost.addTab(spec);

		// Do the same for the other tabs
		intent.setClass(this, Activity_Albums.class);
		spec = tabHost.newTabSpec("albums").setIndicator("Albums",
				res.getDrawable(R.drawable.ic_tab_albums))
				.setContent(intent);
		tabHost.addTab(spec);

		intent.setClass(this, Activity_Charts.class);
		spec = tabHost.newTabSpec("songs").setIndicator("Songs",
				res.getDrawable(R.drawable.ic_tab_charts))
				.setContent(intent);
		tabHost.addTab(spec);

		tabHost.setCurrentTab(0);
	}
	
	@Override
    public boolean onCreateOptionsMenu(Menu menu) {
        MenuInflater inflater = getMenuInflater();
        inflater.inflate(R.menu.options_main, menu);
        return true;
    }
    
    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
    	
    	Context context = getApplicationContext();
    	
        // Handle item selection
        switch (item.getItemId()) {
        
        case R.id.option_search:
        	
        	CharSequence text = "Search not available";
        	int duration = Toast.LENGTH_SHORT;

        	Toast toast = Toast.makeText(context, text, duration);
        	toast.show();
            return true;
            
        case R.id.option_preferences:
        	
            Intent settingsActivity = new Intent(getBaseContext(), Activity_Preferences.class);
            startActivity(settingsActivity);
            
        	//Toast toast2 = Toast.makeText(context, "Preferences either...", Toast.LENGTH_SHORT);
        	//toast2.show();
            return true;
            
        default:
            return super.onOptionsItemSelected(item);
            
        }
    }

}