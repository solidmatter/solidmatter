package net.solidbytes.jukebox;

import net.solidbytes.tools.App;
import net.solidbytes.tools.Filesystem;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.DialogInterface;
import android.os.Bundle;
import android.preference.Preference;
import android.preference.Preference.OnPreferenceClickListener;
import android.preference.PreferenceActivity;
import android.util.Log;

public class Activity_Preferences extends PreferenceActivity {
	
	static final int DIALOG_CLEAR_CACHE = 0;

	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		
		super.onCreate(savedInstanceState);
		
		addPreferencesFromResource(R.xml.preferences);
		
		// Get the custom preference
		/*Preference customPref = (Preference) findPreference("customPref");
		customPref.setOnPreferenceClickListener(new OnPreferenceClickListener() {
			
			public boolean onPreferenceClick(Preference preference) {
				Toast.makeText(getBaseContext(), "The custom preference has been clicked", Toast.LENGTH_LONG).show();
				SharedPreferences customSharedPreference = getSharedPreferences("myCustomSharedPrefs", Activity.MODE_PRIVATE);
				SharedPreferences.Editor editor = customSharedPreference.edit();
				editor.putString("myCustomPref", "The preference has been clicked");
				editor.commit();
				return true;
			}

		});*/
		
		
		
		Preference myPref = findPreference("clear_cache");
		myPref.setOnPreferenceClickListener(new OnPreferenceClickListener() {

	        @Override
	        public boolean onPreferenceClick(Preference preference) {
	        	showDialog(DIALOG_CLEAR_CACHE);
				return false;
	        }
	        
	    });

		
	    
	}
	
	protected void onResume() {
		
		super.onResume();
		
		updateInfos();
		
	}
	
	protected void updateInfos() {
		
		// show cache size
		Preference prefCache = findPreference("clear_cache");
		long lCacheSize = Filesystem.getFileSize(App.getExternalCacheDir());
		Log.d("sbJukebox", "cache size is " + lCacheSize + " bytes");
		prefCache.setSummary(Filesystem.formatFilesize(lCacheSize));
		
		// show download directory
		Preference prefDir = findPreference("downloads_directory");
		String sDownloadDir = App.Prefs.getString("downloads_directory", "not specified");
		Log.d("sbJukebox", "download directory is " + sDownloadDir);
		prefDir.setSummary(sDownloadDir);
		
	}
	
	protected Dialog onCreateDialog(int id) {
	    Dialog dialog;
	    switch(id) {
	    case DIALOG_CLEAR_CACHE:
	    	AlertDialog.Builder builder = new AlertDialog.Builder(this);
	    	builder.setMessage(R.string.prefs_clear_cache_message);
	    	builder.setCancelable(true);
	    	builder.setPositiveButton(R.string.dialog_ok, new DialogInterface.OnClickListener() {
	    	           public void onClick(DialogInterface dialog, int id) {
	    	        	   Filesystem.deleteRecursive(App.getExternalCacheDir());
	    	        	   updateInfos();
	    	           }
	    	       });
	    	builder.setNegativeButton(R.string.dialog_cancel, new DialogInterface.OnClickListener() {
	    	           public void onClick(DialogInterface dialog, int id) {
	    	                dialog.cancel();
	    	           }
	    	       });
	    	dialog = builder.create();
	        break;
	    default:
	        dialog = null;
	    }
	    return dialog;
	}
	
}