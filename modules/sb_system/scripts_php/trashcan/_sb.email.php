<?php

//------------------------------------------------------------------------------
/**
*	@package solidBrickz
*	@author	()((() [Oliver Müller]
*	@version 0.80.00
*/
//------------------------------------------------------------------------------

if (!defined('SMTP'))		define('SMTP',		320);
if (!defined('MAIL'))		define('MAIL',		321);
if (!defined('SENDMAIL'))	define('SENDMAIL',	322);

if (!defined('TO'))			define('TO',		340);
if (!defined('CC'))			define('CC',		341);
if (!defined('BCC'))		define('BCC',		342);
if (!defined('REPLYTO'))	define('REPLYTO',	343);

//------------------------------------------------------------------------------

load_library('/external/phpmailer/class.phpmailer');
//require_once($PATH_GLOBALSCRIPTS.'/external/phpmailer/class.smtp.php');

//------------------------------------------------------------------------------
/**
* 
*/
class EMail {
	
	var $pmMail			= NULL;
	
	var $bError			= FALSE;
	var $sErrorMessage	= '';
	
	function EMail($iMethod = SMTP, $sSubject = '', $bIsHTML = FALSE) {
		
		global $PATH_GLOBALSCRIPTS;
		
		$this->pmMail = new PHPMailer();
		
		$this->pmMail->Subject = $sSubject;
		$this->pmMail->IsHTML($bIsHTML);
		$this->pmMail->SetLanguage('en', $PATH_GLOBALSCRIPTS.'/external/phpmailer/language/');
		
		if ($iMethod == SMTP) {
			$this->pmMail->IsSMTP();
		}
		//print_r($this->pmMail);
	}
	
	function SetMethod() {
		
	}
	
	function SetSMTPOptions($sHost, $iPort, $bUseAuth = FALSE, $sLogin = '', $sPassword = '') {
	
		$this->pmMail->Host     = $sHost;
		$this->pmMail->Port     = $iPort;
		
		if ($bUseAuth) {
			
			$this->pmMail->SMTPAuth		= TRUE;
			$this->pmMail->Username		= $sLogin;
			$this->pmMail->Password		= $sPassword;
			
		}
	
	}
	
	function SetFrom($sAddress, $sName = '') {
		
		$this->pmMail->From     = $sAddress;
		$this->pmMail->FromName = $sName;
		
	}
	
	function AddRecipient($eMode, $sAddress, $sName = '') {
		
		switch ($eMode) {
			
			case TO:
				//print_r($this);
				//print_r($this->pmMail);
				$this->pmMail->AddAddress($sAddress, $sName);
				break;
				
			case CC:
				
				break;
			
		    case BCC:
				
				break;
		
			case REPLYTO:
				$this->pmMail->AddReplyTo($sAddress, $sName);
				break;
		
		}
	
	
	}
	
	function SetBody($sBody) {
		$this->pmMail->Body = $sBody;
	}
	
	function SetAltbody($sAltBody) {
		$this->pmMail->AltBody = $sAltBody;
	}
	
	function SetSubject($sSubject) {
		$this->pmMail->Subject = $sSubject;
	}
	
	function SetCharset($sEncoding) {
		$this->pmMail->CharSet = $sEncoding;
	}
	
	function SetWordWrap($iNumChars) {
		$this->pmMail->WordWrap = $iNumChars;
	}
	
	function Send() {
		
		error_reporting(0);
		
		if (!$this->pmMail->Send()) {
			$this->bError = TRUE;
			$this->sErrorMessage = $this->pmMail->ErrorInfo;
			error_reporting(E_ALL);
			return (FALSE);
			
		} else {
			error_reporting(E_ALL);
			return (TRUE);
		}
		
		
	}
	
}

?>