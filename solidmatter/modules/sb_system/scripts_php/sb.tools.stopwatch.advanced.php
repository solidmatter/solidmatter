<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
 * TODO: change Description and other PHPDoc stuff
* 
*/
class Stopwatch {
	
	private static $mtStarttime = NULL;
	private static $mtStoptime = NULL;
	private static $mtMarkertime = NULL;
	private static $mtGroupMarkertime = NULL;
	
	private static $aTaskTimes = array();
	private static $aTaskGroupTimes = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function start() {
		self::$mtStarttime = microtime(TRUE);
		self::$mtMarkertime = microtime(TRUE);
		self::$mtGroupMarkertime = microtime(TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function check($sTask, $sGroup = NULL) {
		$mtNow = microtime(TRUE);
		$mtDifference = $mtNow - self::$mtMarkertime;
		self::$mtMarkertime = microtime(TRUE);
		if (!isset(self::$aTaskTimes[$sTask])) {
			self::$aTaskTimes[$sTask] = $mtDifference;
		} else {
			self::$aTaskTimes[$sTask] += $mtDifference;
		}
		if ($sGroup != NULL) {
			self::checkGroup($sGroup);
		}
		return ($mtDifference);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function checkGroup($sTaskGroup) {
		$mtNow = microtime(TRUE);
		$mtDifference = $mtNow - self::$mtGroupMarkertime;
		self::$mtGroupMarkertime = microtime(TRUE);
		if (!isset(self::$aTaskGroupTimes[$sTaskGroup])) {
			self::$aTaskGroupTimes[$sTaskGroup] = $mtDifference;
		} else {
			self::$aTaskGroupTimes[$sTaskGroup] += $mtDifference;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access public
	* @param 
	* @return 
	*/
	public static function stop($sTask) {
		self::$mtStoptime = microtime(TRUE);
		$mtTotalTime = self::$mtStoptime - self::$mtStarttime;
		if (isset(self::$aTaskTimes[$sTask])) {
			throw new Exception('stop time has to be stored seperately, "'.$sTask.'" already exists');
		}
		self::$aTaskTimes[$sTask] = $mtTotalTime;
		return ($mtTotalTime);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function resetTimer() {
		self::$aTaskTimes = array();
		self::$aTaskGroupTimes = array();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getTaskTimes($sSpecificTask = NULL) {
		$aTimes = array_merge(self::$aTaskTimes, self::$aTaskGroupTimes);
		if ($sSpecificTask != NULL) {
			return (sprintf('%01.2f', $aTimes[$sSpecificTask] * 1000));
		}
		foreach ($aTimes as $sTime => $iTime) {
			$aResult[$sTime] = sprintf('%01.2f', $iTime * 1000);	
		}
		return ($aResult);
	}
	
}

?>