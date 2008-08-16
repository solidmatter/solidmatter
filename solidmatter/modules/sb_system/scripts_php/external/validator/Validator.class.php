<?php

function callback ($buffer) {
	print "callback";
	return $_REQUEST['theValidator_pq1hj9sb8f3']->render($buffer);
}



/**
 * Author Eric Jansson
 *
 * This code is made available according to the GNU Lesser General Public License
 * http://www.gnu.org/copyleft/lesser.html
 *
 * A PHP Class for validating dyncmic pages against validation services
 * Writes HTTP requests to a file and then submits these to a URL where 
 * that service is offered
 */
class Validator {
	
	// CONSTANTS
	
	// INSTANCE VARIABLES
	
	// these will be determined based on the tag context; these could be defined 
	// local to methods, but generating them all at once saves reprocessing of CGI 
	// info
	var $_filePath = "";	 	// physical path to the HTTP request written to disk
	var $_fileUrl = "";		// url for the HTTP request written to disk
	
	// these properties must be defined, and have constants
	
	// the file extension to attach to file written to disk;
	// name is this script name + this extension
	var $_fileExtension = ".validate.html";
	// browser window target for validation results
	var $_targetWindow = "newWindow";
	// options applied on opening that window
	var $_windowOptions = "scrollbars=yes,location=yes,menubar=yes,titlebar=yes,resizable=yes,width=600,height=450";	
	// text for the button written to the browser
	var $_buttonText = "validate";
	// the URLs for the default validator
	// these have the marker '###URL###' where the url or the resource 
	// to validate will be placed
	var $_validatorUrl = "http://validator.w3.org/check?uri=###URL###";	//the URL for the online validation service
	
	// these properties may or may not be defined
	var $_buttonMarker = null;	// marker in the HTML where the validate button should be placed

	// both of the properties below should be defined, or both left NULL
	var $_workDir = null;		// the directory where validator files should be placed; 
					// defaults to the same directory where the script is run
	var $_workDirUrl = null;	// the url of that working directory
	
	// in case more than  1 validation service is to be used, you need to add the 
	// buttons separately
	var $_validationButtons = array();
	
	/**
	 * constructor
	 */
	function Validator() {
	}	
	
	// gettors and settors	

	function setFileExtension($s) {
		$this->_fileExtension = $s;
	}
    function getFileExtension() { 
		return $this->_fileExtension;
	}    
	
	function setTargetWindow($s) {
		$this->_targetWindow = $s;
	}
	function getTargetWindow() {
		return $this->_targetWindow;
	}
	
	function setWindowOptions($s) {
		$this->_windowOptions = $s;
	}
	function getWindowOptions() {
		return $this->_windowOptions;
	}		
	
	function setButtonText($s) {
		$this->_buttonText = $s;
	}
	function getButtonText() {
		return $this->_buttonText;
	}		
	
	function setButtonMarker($s) {
		$this->_buttonMarker= $s;
	}
	function getButtonMarker() {
		return $this->_buttonMarker;
	}		
	
	function setValidatorUrl($s) {
		$this->_validatorUrl = $s;
	}
	function getValidatorUrl() {
		return $this->_validatorUrl;
	}

	function setWorkDir($s) {
		$this->_workDir = $s;
	}
    	function getWorkDir() { 
		return $this->_workDir;
	} 

	function setWorkDirUrl($s) {
		$this->_workDirUrl = $s;
	}
    	function getWorkDirUrl() { 
		return $this->_workDirUrl;
	} 			
	
	
	
	/**
	* Adds a ValidationService to the Validator
	* @param a ValidationService object
	*/
	function addValidationButton(& $vb) {
		array_push($this->_validationButtons,$vb);
	}
	
	/**
	 * Starts the buffering process
	 */	
	function execute() {	
		// store a ref to this object in the request array
		$_REQUEST['theValidator_pq1hj9sb8f3'] =& $this;
		ob_start("callback");
	}

	/**
	 * Does all the operations: save to disk, etc
	 */	
	function render($buffer) {	
		
		if ($this->_workDir == null || $this->_workDirUrl == null) {
			$this->_filePath = $_SERVER['PATH_TRANSLATED'] . $this->_fileExtension;
			$this->_fileUrl = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . $this->_fileExtension;			
		} else {
			$path_parts = pathinfo($_SERVER['PHP_SELF']);
			$this->_filePath = $this->_workDir . $path_parts["basename"] . $this->_fileExtension;
			$this->_fileUrl = $this->_workDirUrl . $path_parts["basename"] . $this->_fileExtension;
		}

		// make the request and write it to disk
		$this->writeToDisk($buffer);		
		
		if (count($this->_validationButtons) == 0) {

			// get button HTML code
			$button = "<a href=\"" . str_replace("###URL###", $this->_fileUrl, $this->_validatorUrl) . "\"";
			$button .= " target='" . $this->_targetWindow . "' onclick=\"window.open(this.href,this.target, '" . $this->_windowOptions . ",'); returntrue\">" . $this->_buttonText . "</a>";					
		
			// write our validation buttons
			// if marker is blank, write out button at end
			if ($this->isBlank($this->_buttonMarker)) {
				$buffer .= $button;
				// else replace the marker w the button
			} else {
				$buffer = str_replace($this->_buttonMarker, $button, $buffer);
			}
		
		} else {
			
			for ($i=0; $i<count($this->_validationButtons); $i++) {
				$vb = $this->_validationButtons[$i];
				
				// get button HTML code
				$button = "<a href=\"" . str_replace("###URL###", $this->_fileUrl, $vb->_validatorUrl) . "\"";
				$button .= " target='" . $vb->_targetWindow . "' onclick=\"window.open(this.href,this.target, '" . $vb->_windowOptions . ",'); returntrue\">" . $vb->_buttonText . "</a>";					
		
				// write our validation buttons
				// if marker is blank, write out button at end
				if ($this->isBlank($vb->_buttonMarker)) {
					$buffer .= $button;
					// else replace the marker w the button
				} else {
					$buffer = str_replace($vb->_buttonMarker, $button, $buffer);
				}				
			}
		}
		
		return $buffer;
	}		
	
	
	/**
	 * Writes the buffer to a file
	 */
	function writeToDisk($buffer)  {
		if (file_exists($this->_filePath)) {
			//unlink($filePath);
		}
		$fileHandler = fopen ($this->_filePath, "w");
		fwrite ($fileHandler, $buffer);
		fclose ($fileHandler);
	}
	
	
	// utility methods
	
	/** 
	* Returns whether a String is blank (has no non-space characters). 
	* @param s the String to examine
	* @return true if the String is null or whitespace; false otherwise
	*/
	function isBlank($s) {
		if (empty($s)) {
			return true;
		}
		return (strlen(trim($s)) == 0);
	}	
	
	
}


class ValidationButton{
	
	// INSTANCE VARIABLES
	
	// these properties must be defined, and have constants
	
	// browser window target for validation results
	var $_targetWindow = "newWindow";
	// options applied on opening that window
	var $_windowOptions = "scrollbars=yes,location=yes,menubar=yes,titlebar=yes,resizable=yes,width=600,height=450";	
	// text for the button written to the browser
	var $_buttonText = "validate";
	// the URLs for the default validator
	// these have the marker '###URL###' where the url or the resource 
	// to validate will be placed
	var $_validatorUrl = "http://validator.w3.org/check?uri=###URL###";	//the URL for the online validation service
	
	// these properties may or may not be defined
	var $_buttonMarker = null;	// marker in the HTML where the validate button should be placed
	
	
	/**
	 * constructor
	 */
	function ValidationButton() {
	}	
	
	// gettors and settors  
	
	function setTargetWindow($s) {
		$this->_targetWindow = $s;
	}
	function getTargetWindow() {
		return $this->_targetWindow;
	}
	
	function setWindowOptions($s) {
		$this->_windowOptions = $s;
	}
	function getWindowOptions() {
		return $this->_windowOptions;
	}		
	
	function setButtonText($s) {
		$this->_buttonText = $s;
	}
	function getButtonText() {
		return $this->_buttonText;
	}		
	
	function setButtonMarker($s) {
		$this->_buttonMarker= $s;
	}
	function getButtonMarker() {
		return $this->_buttonMarker;
	}		
	
	function setValidatorUrl($s) {
		$this->_validatorUrl = $s;
	}
	function getValidatorUrl() {
		return $this->_validatorUrl;
	}
	
}

?>