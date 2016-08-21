package net.solidbytes.tools;

import java.util.ArrayList;
import java.util.Collections;

public class Arrays {
	
	public static ArrayList<String> createStringList(String ... values)	{
	    ArrayList<String> results = new ArrayList<String>();
	    Collections.addAll(results, values);
	    return results;
	}
	
	public static String[] convertToStringArray(ArrayList list)	{
	    return (String[]) list.toArray(new String[0]);
	}
	
	public static String[] filterStringArray(String[] aStrings, String sFilterText)	{
		ArrayList<String> aResults = new ArrayList<String>();
		for (int i=0; i<aStrings.length; i++) {
    		if (aStrings[i].contains(sFilterText)) {
    			aResults.add(aStrings[i]);
    		}
    	}
		return convertToStringArray(aResults);
	}
	

}
