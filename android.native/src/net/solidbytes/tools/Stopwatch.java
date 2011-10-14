package net.solidbytes.tools;

import java.util.*;

public class Stopwatch {
	
	private long lStartTime;
	private long lMarkerTime;
	private int iInterval;
	
	public Stopwatch() {
		lStartTime = new Date().getTime();
		lMarkerTime = lStartTime;
	}
	
	public long Stop() {
		long lStopTime = new Date().getTime();
		return (lStopTime - lStartTime);
	}
	
	public void setInterval(int iMilliseconds) {
		iInterval = iMilliseconds;
	}
	
	public boolean checkInterval() {
		long lCurrentTime = new Date().getTime();
		if (lCurrentTime - lMarkerTime > iInterval) {
			lMarkerTime = lCurrentTime;
			return true;
		} else {
			return false;
		}
	}
	
	
}
