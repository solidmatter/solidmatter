<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
* @param 
* @return 
*/
class sbException extends Exception {
	// TODO: log exceptions
}

// system exceptions

class LazyBastardException extends sbException { }

class SecurityException extends sbException { }
class FileNotFoundException extends sbException { }
class LibraryNotFoundException extends FileNotFoundException { }
class LocaleNotFoundException extends FileNotFoundException { }
class MessageInvalidException extends sbException { }
class RequestInvalidException extends MessageInvalidException { }
class ResponseInvalidException extends MessageInvalidException { }
class NodeNotFoundException extends sbException { }
class QueryNotFoundException extends sbException { }
class InputNotFoundException extends sbException { }
class UnknownNodetypeException extends sbException { }
class ImageProcessingException extends sbException { }
class ParameterException extends sbException { }
class MissingParameterException extends sbException { }
class InvalidFormDataException extends sbException { }
class ViewUndefinedException extends sbException { }
class ActionUndefinedException extends sbException { }
class SessionTimeoutException extends sbException { }

class ImportException extends sbException { }
class ExportException extends sbException { }

class NestedTransactionException extends sbException { }

// JSR exceptions -----------------------------------------------------------

class JCRException extends sbException { }

class AccessDeniedException extends JCRException { }
class ConstraintViolationException extends JCRException { }
class InvalidItemStateException extends JCRException { }
class InvalidQueryException extends JCRException { }
class InvalidSerializedDataException extends JCRException { }
class InvalidLifecycleTransitionException extends JCRException { }
class ItemExistsException extends JCRException { }
class ItemNotFoundException extends JCRException { }
class LockException extends JCRException { }
class LoginException extends JCRException { }
class NamespaceException extends JCRException { }
class NoSuchElementException extends JCRException { }
class NoSuchNodeTypeException extends JCRException { }
class NoSuchWorkspaceException extends JCRException { }
class PathNotFoundException extends JCRException { }
class ReferentialIntegrityException extends JCRException { }
class RepositoryException extends JCRException { }
class UnsupportedRepositoryOperationException extends JCRException { }
class ValueFormatException extends JCRException { }
class VersionException extends JCRException { }


 


 

 
 
 
  
  



/*function convertException2XML($e) {
	
	$domException = new sbDOMDocument();
	$elemException = $domException->createElement('exception');
	$elemException->setAttribute('message', str_replace('/', '/ ', $e->getMessage()));
	$elemException->setAttribute('code', $e->getCode());
	$elemException->setAttribute('file', $e->getFile());
	$elemException->setAttribute('line', $e->getLine());
	$elemException->setAttribute('type', get_class($e));
	
	$elemTrace = $domException->convertArrayToElement('trace', $e->getTrace());
	$elemException->appendChild($elemTrace);
	$domException->appendChild($elemException);
	
	return ($domException->firstChild);
	
}

/*function handle_error($iErrNo, $sErrStr, $sErrFile, $iErrLine) {
	
	global $_RESPONSE;
	if ($_RESPONSE != NULL) {
		$_RESPONSE->addError($iErrNo, $sErrStr, $sErrFile, $iErrLine);
	}
	
}

set_error_handler('handle_error');*/




?>