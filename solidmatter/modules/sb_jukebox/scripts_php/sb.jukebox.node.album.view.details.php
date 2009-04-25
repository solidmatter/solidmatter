<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');
import('sb.tools.mime');

//------------------------------------------------------------------------------
/**
*/
class sbView_jukebox_album_details extends sbJukeboxView {
	
	protected $aRequiredAuthorisations = array(
		'display' => array('read'),
		'getM3U' => array('read'),
		'download' => array('download'),
		'getCover' => array('read'),
		'buildQuilt' => array('read'),
	);
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		$this->checkRequirements($sAction);
		
		switch ($sAction) {
			
			case 'display':
				
				// return playlist if user agent is a mediaplayer
				// FIXME: doesn't work because winamp expects a *.m3u filename
				/*$sUserAgent = $_REQUEST->getParam('HTTP_USER_AGENT', 'SERVER');
				$sPattern = '/^(Winamp|NSPlayer|Windows-Media-Player)(.*)/';
				DEBUG('sbJukebox:album:display', 'UserAgent='.$sUserAgent);
				if (preg_match($sPattern, $sUserAgent)) {
					$this->execute('getM3U');
				}*/
				
				// forms
				$this->addSearchForm('albums');
				$this->addCommentForm();
				$this->addTagForm();
				$this->addRelateForm();
				
				// data
				$this->addComments();
				$this->nodeSubject->getTags();
				$this->nodeSubject->getVote($this->getPivotUUID());
				$this->nodeSubject->storeRelations();
			
			case 'displayInline':
				
				// add tracks
				$niTracks = $this->nodeSubject->loadChildren('tracks', TRUE, TRUE, TRUE);
				foreach ($niTracks as $nodeTrack) {
					$nodeTrack->getVote($this->getPivotUUID());
				}
				
				// optionally check if files still exist
				if (Registry::getValue('sb.jukebox.validation.missingfiles.indicate')) {
					import('sbJukebox:sb.jukebox.tools');
					foreach ($niTracks as $nodeTrack) {
						if (!$nodeTrack->checkFileExistance()) {
							$nodeTrack->setAttribute('missing', 'TRUE');
						}
					}
				}
				
				$this->nodeSubject->loadProperties();
				
				// save data in element
				$this->nodeSubject->storeChildren();
				return;
				
			case 'getM3U':
				$this->sendPlaylist();
				break;
			
			case 'download':
				import('sbJukebox:sb.jukebox.tools');
				JukeboxTools::sendDownloadArchive($this->nodeSubject);
				break;
			
			case 'getCover':
				import('sbJukebox:sb.jukebox.tools');
				JukeboxTools::sendCover($this->nodeSubject);
				break;
			
			case 'buildQuilt':
				
				$this->addSearchForm('albums');
				
				// basic init
				$nodeJukebox = $this->getJukebox();
				
				// check cache first
				$cacheQuilts = CacheFactory::getInstance('misc');
				$sCacheKey = 'JBQUILT:'.$this->nodeSubject->getIdentifier();
				if ($aQuilt = $cacheQuilts->loadData($sCacheKey)) { // already rendered 
					
					// nothing to do, dom elements are created later
					
				} else { // render based on image
					
					import('sbJukebox:sb.jukebox.tools');
					import('sb.tools.colors');
					
					// init rendering
					$iColumns = 25;
					$iRows = 25;
					$iNumSamples = 100;
					$imgCover = new Image(Image::FROMFILE, JukeboxTools::getCoverFilename($this->nodeSubject));
					
					$aQuilt = array();
					for ($i=0; $i<$iRows; $i++) {
						for ($j=0; $j<$iColumns; $j++) {
							$aHSL = $imgCover->getHSL($iNumSamples, $iColumns, $iRows, $j, $i);
							$stmtFindCover = $this->crSession->prepareKnown('sbJukebox/album/quilt/findCover');
							$stmtFindCover->bindValue(':hue', $aHSL['h'], PDO::PARAM_INT);
							$stmtFindCover->bindValue(':saturation', $aHSL['s'], PDO::PARAM_INT);
							$stmtFindCover->bindValue(':lightness', $aHSL['l'], PDO::PARAM_INT);
							$stmtFindCover->bindValue(':entropy', $aHSL['e'], PDO::PARAM_INT);
							$stmtFindCover->bindValue(':jukebox_mpath', $nodeJukebox->getMPath(), PDO::PARAM_STR);
							$stmtFindCover->execute();
							foreach ($stmtFindCover as $aRow) {
								$aQuilt[$i][$j]['uuid'] = $aRow['uuid'];
								$aQuilt[$i][$j]['label'] = $aRow['label'];
							}
							$aQuilt[$i][$j]['h'] = $aHSL['h'];
							$aQuilt[$i][$j]['s'] = $aHSL['s'];
							$aQuilt[$i][$j]['l'] = $aHSL['l'];
							$aQuilt[$i][$j]['e'] = $aHSL['e'];
							$aTemp['h'] = $aHSL['h'] / 255; 
							$aTemp['s'] = $aHSL['s'] / 255; 
							$aTemp['l'] = $aHSL['l'] / 255; 
							$aRGB = hsl2rgb($aTemp);
							$aQuilt[$i][$j]['r'] = $aRGB['r'];
							$aQuilt[$i][$j]['g'] = $aRGB['g'];
							$aQuilt[$i][$j]['b'] = $aRGB['b'];
						}
					}
					// store in cache
					$cacheQuilts->storeData($sCacheKey, $aQuilt);
				}
				
				// build quilt dom
				$elemQuilt = $_RESPONSE->createElement('quilt');
				foreach ($aQuilt as $aRow) {
					$elemRow = $_RESPONSE->createElement('row');
					foreach ($aRow as $aColumn) {
						$elemColumn = $_RESPONSE->createElement('column');
						$elemColumn->setAttribute('uuid', $aColumn['uuid']);
						$elemColumn->setAttribute('label', $aColumn['label']);
						$elemColumn->setAttribute('hue', $aColumn['h']);
						$elemColumn->setAttribute('saturation', $aColumn['s']);
						$elemColumn->setAttribute('lightness', $aColumn['l']);
						$elemColumn->setAttribute('entropy', $aColumn['e']);
						$elemColumn->setAttribute('red', $aColumn['r']);
						$elemColumn->setAttribute('green', $aColumn['g']);
						$elemColumn->setAttribute('blue', $aColumn['b']);
						$elemRow->appendChild($elemColumn);
					}
					$elemQuilt->appendChild($elemRow);
				}
				
				$_RESPONSE->addData($elemQuilt);
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
		
	}
	
}

?>