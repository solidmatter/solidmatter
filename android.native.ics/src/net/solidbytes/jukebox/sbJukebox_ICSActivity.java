package net.solidbytes.jukebox;

import net.solidbytes.tools.App;
import net.solidbytes.solidmatter.sbConnection;
import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.Uri;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageButton;

public class sbJukebox_ICSActivity extends Activity {
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
    }
    
  /*  @Override
	public void onCreate(Bundle savedInstanceState) {
		
		Log.i("sbJukebox", "sbJukebox ist starting...");
		
		super.onCreate(savedInstanceState);
		setContentView(R.layout.main);
		
		App.setContext(this);
	    
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
	    
	    ImageButton btFavorites = (ImageButton) findViewById(R.id.menu_favorites);
	    btFavorites.setOnClickListener(new View.OnClickListener() {
	        public void onClick(View view) {
	        	String sStreamURL = "http://ollomulder.dyndns.org/play/51c85b830a49400a984210b30b14eb2b/e8de8545eb2543259b83246e56f8fb9f/the_pixies_where_is_my_mind_bassnectar_rmx.mp3";
//	        	String sStreamURL = "http://ice.jukebox.th.gotdns.org/946390f9119148d68687d73ef4f02890/details/getM3U/playlist.m3u?sid=ubdeeejsip18rpjvhle8jlqcm7";
//	        	String sStreamURL = sbConnection.getDomain() + "/play/" + this.getProperty("uuid") + "/" + sToken
	        	
	        	Intent i = new Intent(android.content.Intent.ACTION_VIEW);
    			i.setDataAndType(Uri.parse(sStreamURL), "audio/*");
    			App.Context.startActivity(i);
	        }
        });
	   
	}
	
	public void onResume() {
		super.onResume();
		if (sbConnection.connect(true)) {
			Log.i("sbJukebox", "login to " + sbConnection.getDomain() + " successful");
		} else {
			Log.i("sbJukebox", "login failed: " + sbConnection.sError);
		}
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
        	
        	AlertDialog.Builder alert = new AlertDialog.Builder(this);

			alert.setTitle(R.string.option_search);
			// alert.setMessage("Message");

			// Set an EditText view to get user input
			final EditText input = new EditText(this);
			input.setLines(1);
			alert.setView(input);

			alert.setPositiveButton(R.string.action_search, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int whichButton) {
					
					String sSearchString = input.getText().toString();
					
					Intent myIntent = new Intent(App.Context, Activity_Jukebox_Library_Search.class);
					myIntent.putExtra("searchstring", sSearchString);
		            startActivityForResult(myIntent, 0);
					
				}
			});

			alert.setNegativeButton(R.string.dialog_cancel, new DialogInterface.OnClickListener() {
				public void onClick(DialogInterface dialog, int whichButton) {
					// Canceled.
				}
			});

        	alert.show();
        	
//        	CharSequence text = "Search not available";
//        	int duration = Toast.LENGTH_SHORT;
//
//        	Toast toast = Toast.makeText(context, text, duration);
//        	toast.show();
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
    }*/
    
}