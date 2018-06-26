<?php

abstract class CONFIG {
	
	// storage information for solidMatter config XMLs
	const DIR = '_config/';
	const INTERFACE = 'interface.xml';
	const CONTROLLER = 'controller.xml';
	const REPOSITORIES = 'repositories.xml';
	
	// directory for temporary files (e.g. uploads)
	const TEMPDIR = '_temp/';
	
	// directory for various logs
	const LOGDIR = '_logs/';
	
	// debugging flags
	const DEBUG = array(
		'ENABLED' 		=> TRUE,
		'LOG_ALL' 		=> FALSE,
		'BASIC'			=> TRUE,
		'CLIENT'		=> FALSE,
		'IMPORT'		=> FALSE,
		'SESSION'		=> FALSE,
		'REQUEST'		=> FALSE,
		'HANDLER'		=> FALSE,
		'NODE'			=> FALSE,
		'REDIRECT'		=> FALSE,
		'EXCEPTIONS'	=> TRUE,
		'PDO'			=> TRUE,
	);
	
	// API key for the Songkick webservice
	const KEY_SONGKICK = 'jhDU4U3JHdui256Fs';
	
}

?>