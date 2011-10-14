package net.solidbytes.jukebox;

import java.io.File;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;
import android.preference.PreferenceManager;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

public class App {
	
	public static Activity Activity;
	public static Context Context;
	public static SharedPreferences Prefs;
	
	public static void setContext(Activity actMain) {
		Activity = actMain;
		Context = (Context) actMain;
		Prefs = PreferenceManager.getDefaultSharedPreferences(Context);
	}
	
	public static LayoutInflater getLayoutInflater() {
		return (Activity.getLayoutInflater());
	}
	
	public static View inflate(int iResourceID, ViewGroup vRoot) {
		return (getLayoutInflater().inflate(iResourceID, vRoot));
	}
	
	public static File getExternalCacheDir() {
		return (Context.getExternalCacheDir());
	}
	
	public static File getExternalCacheFile(String sPath) {
		File dirCache = Context.getExternalCacheDir();
		File fileImage = new File(dirCache.getAbsolutePath() + sPath);
		return (fileImage);
	}
	
}
