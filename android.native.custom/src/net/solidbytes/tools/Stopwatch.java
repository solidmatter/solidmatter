package net.solidbytes.tools;

import java.util.*;

public class Stopwatch {
	
	private long lStartTime;
	
	public Stopwatch() {
		lStartTime = new Date().getTime();
	}
	
	public long Stop() {
		long lStopTime = new Date().getTime();
		return (lStopTime - lStartTime);
	}
	
	
}
