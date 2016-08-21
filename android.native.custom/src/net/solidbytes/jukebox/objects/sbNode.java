package net.solidbytes.jukebox.objects;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Set;

import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import net.solidbytes.jukebox.Activity_Album_Details;
import net.solidbytes.tools.NavigationOnClickListener;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.content.Context;
import android.content.Intent;
import android.util.Log;
import android.view.View.OnClickListener;

public class sbNode {
	
	protected HashMap<String, String> hmProperties = new HashMap<String, String>();
	protected Element eNode = null;
	
	protected void fillByElement(Element eCurrent) {
		
		Set<String> setProperties = hmProperties.keySet();
		Iterator<String> iter = setProperties.iterator();
	    
		while (iter.hasNext()) {
	    	
	    	String sProperty = iter.next();
	    	String sValue = eCurrent.getAttribute(sProperty).toString();
	    	hmProperties.put(sProperty, sValue);
	      	
	    	Log.v("sbJukebox", "set property '" + sProperty + "' to '" + sValue + "'");
	    	
	    }
		
		eNode = eCurrent;
		
	}
	
	public String getProperty(String sProperty) {
		
		return hmProperties.get(sProperty);
		
	}
	
	public NodeList getNodesByXPath(String sXPath) {
		
		try {
			
			XPathFactory xpFactory = XPathFactory.newInstance();
	        XPath xpCurrent = xpFactory.newXPath();
	        XPathExpression xpeCurrent = xpCurrent.compile(sXPath);
	        
	        NodeList nlNodes = (NodeList) xpeCurrent.evaluate(eNode, XPathConstants.NODESET);
	        
	        return nlNodes;
		
		} catch (Exception e) {
			
			Log.e("sbTools", "XPath parsing error on Node: " + e.toString());
			return null;
			
		}
		
	}
	
	public OnClickListener getNavigationOnClickListener() {
		return new NavigationOnClickListener(this);
	}
	
	public Intent getIntent(Context c) {
		return new Intent(c, Activity_Album_Details.class);
	}
	
}
