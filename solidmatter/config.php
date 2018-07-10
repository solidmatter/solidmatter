<?php

//ini_set('opcache.enable', 0);

abstract class CONFIG {
	
	// storage information for solidMatter config XMLs
	const DIR = '_config/';
	const INTERFACE = 'interface.xml';
	const CONTROLLER = 'controller.xml';
	const REPOSITORIES = 'repositories.xml';
	
	// directory for temporary files (e.g. uploads)
	const TEMPDIR = '_temp/';
	
	// directory for various logs (relative to solidMatter root or absolute path, see LOGDIR_ABS)
	const LOGDIR = '_logs/';
	// true if LOGDIR is an absolute path (ToDo: find more elegant solution, error_log expects absolute path)
	const LOGDIR_ABS = FALSE;
	
	// debugging flags
	const DEBUG = array(
		'ENABLED' 		=> FALSE,
		'LOG_ALL' 		=> FALSE,
		'BASIC'			=> TRUE,
		'CLIENT'		=> FALSE,
		'IMPORT'		=> FALSE,
		'SESSION'		=> FALSE,
		'REQUEST'		=> TRUE,
		'HANDLER'		=> FALSE,
		'NODE'			=> FALSE,
		'REDIRECT'		=> FALSE,
		'EXCEPTIONS'	=> TRUE,
		'PDO'			=> FALSE,
	);
	
	// output prettyprinted XML
	const PRETTYPRINT = TRUE;
	
	// sbSystem Registry Cache, activates necessary change detection
	const USE_REGISTRYCACHE = TRUE;
	
	// API key for the Songkick webservice
	const KEY_SONGKICK = 'jhDU4U3JHdui256Fs';
	
}

?>