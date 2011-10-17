package net.solidbytes.tools.connection;

import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpression;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.*;

import android.util.Log;

public class sbDOMResponse {
	
	protected Document domDoc;
	protected XPath xpCurrent;
	
	public sbDOMResponse(Document domDoc) {
		
		this.domDoc = domDoc;
		
		XPathFactory xpFactory = XPathFactory.newInstance();
        xpCurrent = xpFactory.newXPath();
		
	}
	
	public NodeList getElementsByXPath(String sXPath) {
		
		Log.d("sbTools", "sbDOMResponse XPath selection: " + sXPath);
		
		try {
	        XPathExpression expr = xpCurrent.compile(sXPath);
	        NodeList result = (NodeList) expr.evaluate(this.domDoc, XPathConstants.NODESET);
	        Log.d("sbTools", "sbDOMResponse XPath selection: " + result.getLength() + " matching nodes");
	        return  result;
		} catch (Exception e) {
			Log.e("sbTools", "sbDOMResponse XPath parsing error" + e.toString());
			return null;
		}
		
	}
	
	public String getSessionID() {
		
		Node eSessionID= getElementsByXPath("/response/metadata/system/sessionid").item(0);
		return (eSessionID.getTextContent());
		
	}
	
	public String getUserID() {
		
		Node eUserID= getElementsByXPath("/response/metadata/system/userid").item(0);
		return (eUserID.getTextContent());
		
	}
	
	
}
