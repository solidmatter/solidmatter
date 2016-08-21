package net.solidbytes.solidmatter;

import java.util.HashMap;
import java.util.Iterator;
import java.util.Set;

import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

//import net.solidbytes.jukebox.Activity_Album_Details;
import net.solidbytes.jukebox.R;
import net.solidbytes.tools.App;
import net.solidbytes.tools.NavigationOnClickListener;

import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

import android.content.Context;
import android.content.Intent;
import android.graphics.drawable.Drawable;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;

public class sbNode {
	
	public static final int VIEW_ROW_SIMPLE = 8;
	
	protected HashMap<String, String> hmProperties = new HashMap<String, String>();
	protected Element eNode = null;
	
	protected void fillByElement(Element eCurrent) {
		
		Set<String> setProperties = hmProperties.keySet();
		Iterator<String> iter = setProperties.iterator();
	    
		while (iter.hasNext()) {
	    	
	    	String sProperty = iter.next();
	    	String sValue = eCurrent.getAttribute(sProperty).toString();
	    	hmProperties.put(sProperty, sValue);
	      	
	    	Log.v("sbTools", "set property '" + sProperty + "' to '" + sValue + "'");
	    	
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
	
	/*public Intent getIntent(Context c) {
		return new Intent(c, Activity_Album_Details.class);
	}
	
	public View getView(int iViewVariant) {
		
		String sLabel = this.getProperty("label");
		
		switch (iViewVariant) {
		
		case 8: // ROW
			
			// Inflate the views from XML
			View vRow = App.inflate(R.layout.listentry_various, null);
			TextView tvLabel = (TextView) vRow.findViewById(R.id.NodeLabel);
			ImageView ivIcon = (ImageView) vRow.findViewById(R.id.NodeIcon);
			
			tvLabel.setText(sLabel);
			ivIcon.setBackgroundResource(this.getIconID());
			
			return vRow;
			
		}
		
		Log.e("sbJukebox", "sbNode.getView() could not recognize required view with id " + iViewVariant);
		return null;
		
	}*/
	
	protected int getIconID() {
		return R.drawable.ic_type_dummy;
	}
	
}
