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
import android.view.View;
import android.widget.ImageButton;
import android.widget.TabHost;
import android.widget.Toast;

public class Activity_sbJukebox extends Activity {

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
	    
		// add onclick events to the buttons
		ImageButton btArtists = (ImageButton) findViewById(R.id.menu_artists);
		btArtists.setOnClickListener(new View.OnClickListener() {
	    	 public void onClick(View view) {
	            Intent myIntent = new Intent(view.getContext(), Activity_Artists.class);
	            startActivityForResult(myIntent, 0);
	        }
        });
	    ImageButton btAlbums = (ImageButton) findViewById(R.id.menu_albums);
	    btAlbums.setOnClickListener(new View.OnClickListener() {
	        public void onClick(View view) {
	            Intent myIntent = new Intent(view.getContext(), Activity_Albums.class);
	            startActivityForResult(myIntent, 0);
	        }
        });
	   
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