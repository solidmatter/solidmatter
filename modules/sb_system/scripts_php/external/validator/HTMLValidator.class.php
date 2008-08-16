<?php

function callback ($buffer) {
	return $_REQUEST['theValidator_pq1hj9sb8f3']->render($buffer);
}


/**
 * Author Eric Jansson
 *
 * This code is made avialable according to the GNU Lesser General Public License
 * http://www.gnu.org/copyleft/lesser.html
 *
 * A PHP Class for validating dynamic pages against the HTML DTD.  Uses the
 * W3C validator athttp://validator.w3.org
 */
class HTMLValidator {


	// the URL for the online validation service  
	var $_validatorUrl = "http://validator.w3.org/check?uri=";
	// url for the physical path 
	var $_fileExtension = ".validate.html";

	// the directory where validator files should be placed;
	// defaults to the same directory where the script is run
	var $_workDir = null;		 
	// the url of that working directory				
	var $_workDirUrl = null;	

	// these will be determined based on the tag context; these could be defined 
	// local to methods, but generating them all at once saves reprocessing of CGI 
	// info
	var $_filePath = "";	 	// physical path to the HTTP request written to disk
	var $_fileUrl = "";		// url for the HTTP request written to disk

	// HTML validation issues returned from the validator
	var $_messages = array();
	
	// the buffered HTTP response output
	var $_buffer;		
	
	// whether or not a valid response was received from the W3C validator
	var $_hasValidated = false;


	/**
	 * constructor
	 */
	function HTMLValidator() {
	}	

	// gettors and settors
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
	
	function setFileExtension($s) {
		$this->_fileExtension = $s;
	}
    	function getFileExtension() { 
		return $this->_fileExtension;
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
		$this->_buffer = $buffer;		

		if ($this->_workDir == null || $this->_workDirUrl == null) {
			$this->_filePath = $_SERVER['PATH_TRANSLATED'] . $this->_fileExtension;
			$this->_fileUrl = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'] . $this->_fileExtension;			
		} else {
			$path_parts = pathinfo($_SERVER['PHP_SELF']);
			$this->_filePath = $this->_workDir . $path_parts["basename"] . $this->_fileExtension;
			$this->_fileUrl = $this->_workDirUrl . $path_parts["basename"] . $this->_fileExtension;
		}

		// make the request and write it to disk as a file
		$this->writeToDisk();	

		// validate the page
		$results = $this->validate();

		// delete the file on disk
		unlink($this->_filePath);

		// insert our validation results into the page
		// if there is no <body> tag, place at end of output
		//if (strpos($this->_buffer,"</body>") <= 0) {
			$this->_buffer .= $results;
		//} else { // if there is a <body> tag, place just before
		//	$page = substr($this->_buffer,0,strpos($this->_buffer,"</body>") - 1);
		//	$page .= "<br/>" . $results;
		//	$page .= substr($this->_buffer,strpos($this->_buffer,"</body>") - 1);
		//	$this->_buffer = $page;
		//}
		
		return $this->_buffer;
	}


	/**
	 * Submits the file to the validator, and returns results
	 * @return the results, formatted as HTML
	 */
	function validate() {		
		$results = "";
		$url = $this->_validatorUrl . $this->_fileUrl . ";output=xml";

		// grab the validation results in XML;
		// results are returned from the validator as follows:
		// <result>
		//   <messages>
		//      <msg line="" col="" offset=""></msg>
		//   </messages>
		// </result>

		$data = "";
		if (!($fp = fopen($url, "r"))) {
			$results .= "<div id='xhtmlvalidator'>";
			$results .= "<p>Could not conenct to the W3C Validator: url is $url</p>"; 
			$results .= "<p>Please check the following:</p>";
			$results .= "<ul>";
			$results .= "<li>This server is not behind a firewall that would prevent the W3C Validation Service for accessing this URL: $url</li>";
			$results .= "</ul>";
			$results .= "</div>";
			return $results;
		}
		while ($line = fread($fp, 4096)) {
			$data .= $line;
		};

		// create a parser and parse the URL
		$this->_parser = xml_parser_create();
		xml_set_object($this->_parser, $this);
		xml_set_element_handler($this->_parser, "_startElement", "_endElement");
		xml_set_character_data_handler($this->_parser, "_characterData");
		if (!xml_parse($this->_parser, $data)) {
			return(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($this->_parser)),
				xml_get_current_line_number($this->_parser)));
		}
		xml_parser_free($this->_parser);

		$results .= "<div id='xhtmlvalidator'>";
		
		
		if (!$this->_hasValidated) {
			$results .= "<p>The W3C validator could not be contacted, or there was another problem.  Please check the following:</p>";
			$results .= "<ul>";
			$results .= "<li>The webserver account has permissions to write to this current directory</li>";
			$results .= "<li>The temporary file has an extension indicating it is HTML (current extension is '".$this->_fileExtension ."'--the W3C Validation service will verify the file extension)</li>";
			$results .= "<li>This server is not behind a firewall that would prevent the W3C Validation Service for accessing this URL: $url</li>";
			$results .= "</ul>";
		}
		
		
		if (count($this->_messages) > 0) {
			$lines = explode("\n",$this->_buffer);	// split content into lines	
		
			$results .= "<p>The following validation errors were found:</p>";

			$results .= "<table>";
			$results .= "<tr><th>Line #</th><th>Char offset</th><th>Error message</th><th>Source</th></tr>";

			for ($i=0; $i<count($this->_messages); $i++) {
				$line = $this->_messages[$i]['line'];
				$col = $this->_messages[$i]['col'];
				$offset = $this->_messages[$i]['offset'];
				$text = $this->_messages[$i]['text'];

				// prepare the source which contains the error
				$source = $lines[$line-1];
				
				// if line is too long, show a chuck of content instead 
				if (strlen($source) > 80) {
					$begin = (($offset - 40) < 0) ? 0 : ($offset - 40);
					$end = (($offset + 40) > strlen($this->_buffer)) ? strlen($this->_buffer) : ($offset + 40);
					$source = substr($this->_buffer,$begin,$end - $begin);
				}
				$source = str_replace(">","&gt;",$source);
				$source = str_replace("<","&lt;",$source);

				$results .= "<tr>";
				$results .= "<td>" . $line . "</td>";
				$results .= "<td>" . $offset . "</td>";
				$results .= "<td>" . $text . "</td>";
				$results .= "<td>" . $source . "</td>";
				$results .= "</tr>";
			}
			$results .= "</table>";
			
		} else { // no errors found
			$results .= "<p>No validation problems were found.</p>";
		}
		$results .= "</div>";
		return $results;
	}


	/**
	 * Writes the buffer to a file
	 */
	function writeToDisk()  {

		if (file_exists($this->_filePath)) {
			unlink($filePath);
		}
		$fileHandler = fopen ($this->_filePath, "w");
		fwrite ($fileHandler, $this->_buffer);
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


	// XML parsing functions
	var $_tempLine;
	var $_tempCol;
	var $_tempOffset;
	var $_tempCharData;

	function _startElement($parser, $name, $attrs) {
		if (strtoupper($name) == "RESULT") {
			$this->_hasValidated = true;
		}
	
		if (strtoupper($name) == "MSG") {
			foreach($attrs as $n => $v) {
				if (strtoupper($n) == "LINE") {
					$this->_tempLine = $v;
				} else if (strtoupper($n) == "COL") {
					$this->_tempCol = $v;
				} else if (strtoupper($n) == "OFFSET") {
					$this->_tempOffset = $v;
				}
			}
		}
	}

	function _endElement($parser, $name) {
		if (strtoupper($name) == "MSG") {
			$msg = array();
			$msg['line'] = $this->_tempLine;
			$msg['col'] = $this->_tempCol;
			$msg['offset'] = $this->_tempOffset;
			$msg['text'] = $this->_tempCharData;;
			array_push($this->_messages,$msg);

			$this->_tempAttrs = null;
			$this->_tempCharData = null;
		}
		$this->_tempCharData = null;

	}

	function _characterData($parser, $data) {
		$this->_tempCharData .= $data;
	}


/**
 * Exam if a remote file or folder exists
 *
 * This function is adopted from setec's version.
 * + What's new:
 * Error code with descriptive string.
 * More accurate status code handling.
 * Redirection tracing supported.
 *
 * @retval true resource exists
 * @retval false resource doesn't exist
 * @retval "1 Invalid URL host"
 * @retval "2 Unable to connect to remote host"
 * @retval "3 Status Code not supported: {STATUS_CODE REASON}"
 */
function remote_file_exists($url)
{
   $head = '';
   $url_p = parse_url ($url);

   if (isset ($url_p['host']))
   { $host = $url_p['host']; }
   else
   {
       return '1 Invalid URL host';
   }

   if (isset ($url_p['path']))
   { $path = $url_p['path']; }
   else
   { $path = ''; }

   $fp = fsockopen ($host, 80, $errno, $errstr, 20);
   if (!$fp)
   {
       return '2 Unable to connect to remote host';
   }
   else
   {
       $parse = parse_url($url);
       $host = $parse['host'];

       fputs($fp, 'HEAD '.$url." HTTP/1.1\r\n");
       fputs($fp, 'HOST: '.$host."\r\n");
       fputs($fp, "Connection: close\r\n\r\n");
       $headers = '';
       while (!feof ($fp))
       { $headers .= fgets ($fp, 128); }
   }
   fclose ($fp);
  
   // for debug
   //echo nl2br($headers);
  
   $arr_headers = explode("\n", $headers);
   if (isset ($arr_headers[0]))    {
       if(strpos ($arr_headers[0], '200') !== false)
       { return true; }
       if( (strpos ($arr_headers[0], '404') !== false) ||
           (strpos ($arr_headers[0], '410') !== false))
       { return false; }
       if( (strpos ($arr_headers[0], '301') !== false) ||
           (strpos ($arr_headers[0], '302') !== false))
       {
           preg_match("/Location:\s*(.+)\r/i", $headers, $matches);
           if(!isset($matches[1]))
               return false;
           $nextloc = $matches[1];
           return remote_file_exists($nextloc);
       }
   }
	preg_match('/HTTP.*(\d\d\d.*)\r/i', $headers, $matches);
	return '3 Status Code not supported'. (isset($matches[1])?": $matches[1]":'');
}

}