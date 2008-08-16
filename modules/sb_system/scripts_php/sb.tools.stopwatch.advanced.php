<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

require_once('sb.tools.stopwatch.php');

//------------------------------------------------------------------------------
/**
 * TODO: change Description and other PHPDoc stuff
* 
*/
class AdvancedStopwatch {	
	
	private $aTaskTimes = array();
	private $aTaskGroupTimes = array();
	
	private $mtGroupMarkertime = NULL;
	
	public function __construct() {
		//parent::__construct();
		$this->mtStarttime = microtime(TRUE);
		$this->mtMarkertime = $this->mtStarttime;
		$this->mtGroupMarkertime = $this->mtStarttime;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function check($sTask, $sGroup = NULL) {
		$mtNow = microtime(TRUE);
		$mtDifference = $mtNow - $this->mtMarkertime;
		$this->mtMarkertime = microtime(TRUE);
		if (!isset($this->aTaskTimes[$sTask])) {
			$this->aTaskTimes[$sTask] = $mtDifference;
		} else {
			$this->aTaskTimes[$sTask] += $mtDifference;
		}
		if ($sGroup != NULL) {
			$this->checkGroup($sGroup);
		}
		return ($mtDifference);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function checkGroup($sTaskGroup) {
		$mtNow = microtime(TRUE);
		$mtDifference = $mtNow - $this->mtGroupMarkertime;
		$this->mtGroupMarkertime = microtime(TRUE);
		if (!isset($this->aTaskGroupTimes[$sTaskGroup])) {
			$this->aTaskGroupTimes[$sTaskGroup] = $mtDifference;
		} else {
			$this->aTaskGroupTimes[$sTaskGroup] += $mtDifference;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access public
	* @param 
	* @return 
	*/
	public function stop($sTask) {
		$this->mtStoptime = microtime(TRUE);
		$mtTotalTime = $this->mtStoptime - $this->mtStarttime;
		if (isset($this->aTaskTimes[$sTask])) {
			throw new Exception(__CLASS__.': stop time has to be stored seperately, "'.$sTask.'" already exists');
		}
		$this->aTaskTimes[$sTask] = $mtTotalTime;
		return ($mtTotalTime);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function resetTimer() {
		parent::resetTimer();
		$this->aTaskTimes = array();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTaskTimes($sSpecificTask = NULL) {
		
		//var_dumpp($this->aTaskGroupTimes);
		//return ($this->aTaskTimes);
		$aTimes = array_merge($this->aTaskTimes, $this->aTaskGroupTimes);
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