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
* A Stopwatch is used to determine the time that has passed between two points 
* in time.
* Counting is started at the creation via 'new Stopwatch' and stops
* when the method Stop() is called. When called, Stop() also prints out the 
* timedifference in seconds if needed or returns the time in seconds.
*/
class Stopwatch {
	
	//--------------------------------------------------------------------------
	/** 
	* The microtime-timestamp of the creation's moment.
	* @var integer
	*/
	protected $mtStarttime;
	/** 
	* The microtime-timestamp for caching intermediate check calls. 
	* @var integer
	*/
	protected $mtMarkertime;
	/** 
	* The microtime-timestamp of the moment the method 'stop' was called.
	* @var integer
	*/
	protected $mtStoptime;
	
	//--------------------------------------------------------------------------
	/** 
	* Determines if the result should be printed out instantly when stopping 
	* the watch via Stop().
	* @var integer
	*/
	public $bQuiet = TRUE;
	
	//--------------------------------------------------------------------------
	//##########################################################################
	//--------------------------------------------------------------------------
	/**
	* Constructor, starts counting the time.
	* @access public
	*/
	public function __construct($bQuiet = TRUE) {
		$this->bQuiet = $bQuiet;
		$this->mtStarttime = microtime(TRUE);
		$this->mtMarkertime = $this->mtStarttime;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Stops counting and prints out the passed time in seconds passed 
	* since creation if necessary.
	*/
	public function stop() {
		$this->mtStoptime = microtime(TRUE);
		$sTotaltime = $this->getTimeString($this->mtStoptime - $this->mtStarttime);
		if (!$this->bQuiet) {
			Stopwatch::output($sTotaltime);
		}
		return ($sTotaltime);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function check($bSetMarker = FALSE) {
		$mtNow = microtime(TRUE);
		$sTime = $this->getTimeString($mtNow - $this->mtMarkertime);
		if($bSetMarker) {
			$this->mtMarkertime = microtime(TRUE);
		}
		if (!$this->bQuiet) {
			$this->output($sTime);
		}
		return ($sTime);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function resetTimer() {
		$this->mtStarttime = microtime(TRUE);
		$this->mtMarkertime = $this->mtStarttime;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTimeString($mtSubject) {
		$flTime = 1000 * $mtSubject;
		$sTime = number_format($flTime, 3, '.', '');
		return ($sTime);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function output($sTotaltime) {
		echo '<div align="center" style="font-size:10px;">Action took '.$sTotaltime.' Milliseconds...<br></div>';
	}
	
}

?>