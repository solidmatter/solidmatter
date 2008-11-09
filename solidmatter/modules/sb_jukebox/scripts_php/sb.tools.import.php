<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbJukebox]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb_jukebox:sb.tools.import.genres');

//------------------------------------------------------------------------------
/**
*/
class JukeboxToolkit {
	
	protected $nodeJukbox = NULL;
	protected $jbGenreLib = NULL;
	
	protected $aArtists = array();
	protected $aTempArtists = array();
	
	// config
	protected $bVerbose = TRUE;
	protected $aVerboseFlags = array(
		'ARTIST_QUERY' => FALSE,
		'DIRNAME_FORMAT' => FALSE,
		'COVER_INFO' => TRUE,
		'TRACK_NAMES' => TRUE,
	);
	protected $aAbortFlags = array(
		'NO_GENRE' => FALSE,
		//'NO_ID3V1' => TRUE,
		'NO_COVER' => TRUE,
		//'DUPLIVATE_ALBUM' => TRUE,
		'NO_TRACKNUMBER' => TRUE,
		'NO_YEAR' => FALSE,
		'DIFFERING_TAGS' => TRUE,
		
	);
	protected $aTagFlags = array(
		'GENRE' => TRUE,
		'YEAR' => TRUE,
		'BITRATE' => TRUE,
		'ENCODING' => TRUE,
		'QUALITY' => TRUE,
		'CUSTOM' => TRUE,
	);
	
	protected $aKnownDirFormats = array(
		'%a - [%y] - %t - {%d}',
		'%a - [%y] - %t',
		'%a - %t [%y]',
		'%a - %y - %t [%v]',
		'%a - %y - %t',
		'%a - %t',
	);
	protected $aKnownCoverFilenames = array(
		'folder.jpg',
		'folder.png',
		'front.png',
		'front.jpg',
		'cover.png',
		'cover.jpg',
	);
	protected $aKnownSpecialArtists = array(
		'VA' => 'Various Artists',
		'ST' => 'Soundtrack',
		'Mixed' => 'Mixed Tracks',
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($nodeSubject) {
		$this->nodeJukebox = $nodeSubject;
		$this->jbGenreLib = new GenreLib();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getCustomTags($dirCurrent) {
		$aCustomTags = array();
		$fileTags = $dirCurrent->getFile('sbTags.txt');
		if ($fileTags) {
			$sTags = $fileTags->getContents();
			$aTags = explode(',', $sTags);
			foreach ($aTags as $sTag) {
				$aCustomTags[] = trim($sTag);
			}
		}
		return ($aCustomTags);
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTempArtists() {
		return ($this->aTempArtists);	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getArtistNode($sArtist) {
		
		$sArtistClean = str2urlsafe($sArtist);
		
		$nodeArtist = NULL;
		if (isset($this->aArtists[$sArtistClean])) {
			$nodeArtist = $this->aArtists[$sArtistClean];
		} elseif (isset($this->aTempArtists[$sArtistClean])) {
			$nodeArtist = $this->aTempArtists[$sArtistClean];
		}
		if ($nodeArtist != NULL) {
			if ($this->aVerboseFlags['ARTIST_QUERY']) {
				$this->echoInfo('info', 'cached Artist - '.$sArtist);
			}
			return ($nodeArtist);
		}
		
		if (!$this->nodeJukebox->hasNode($sArtistClean)) {
			$nodeArtist = $this->nodeJukebox->addNode($sArtistClean, 'sb_jukebox:artist');
			$nodeArtist->setProperty('label', $sArtist);
			if ($this->aVerboseFlags['ARTIST_QUERY']) {
				$this->echoInfo('info', '[note] new Artist: '.$sArtist);
			}
		} else {
			$nodeArtist = $this->nodeJukebox->getNode($sArtistClean);
			if ($this->aVerboseFlags['ARTIST_QUERY']) {
				$this->echoInfo('info', '[note] existing Artist: '.$sArtist);
			}
		}
		
		$this->aTempArtists[$sArtistClean] = $nodeArtist;
		
		return ($nodeArtist);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function dumpArtists() {
		if (count($this->aTempArtists) > 0 && $this->aVerboseFlags['ARTIST_QUERY']) {
			$this->echoInfo('info', '[dumped] '.count($this->aTempArtists).' artists');
		}
		$this->aTempArtists = array();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeArtists() {
		if ($this->aVerboseFlags['ARTIST_QUERY']) {
			$this->echoInfo('info', '[added] '.count($this->aTempArtists).' artists to cache');
		}
		$this->aArtists = array_merge($this->aTempArtists, $this->aArtists);
		$this->aTempArtists = array();
	}
	
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function echoInfo($sMode, $sMessage) {
		if (!$this->bVerbose) {
			if ($sMode == 'info' || $sMode == 'note') {
				return;
			}
		}
		echo '<p class="'.$sMode.'">'.$sMessage.'</p>';
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function importAlbum($dirAlbum) {
		
		// init
		$aInfo = array(
			'nodeAlbum' => NULL,
			'nodeAlbumArtist' => NULL
		);
		
		// get basic info
		try {
			$aAlbumInfo = $this->getAlbumInfo($dirAlbum);
		} catch (ImportException $e) {
			throw $e;
		}
		
		// check artist
		$nodeAlbumArtist = $this->getArtistNode($aAlbumInfo['artist']);
		
		// check if album already exists
		if ($nodeAlbumArtist->hasNode($aAlbumInfo['properties']['name'])) {
			throw new ImportException('[abort] - artist "'.$aAlbumInfo['artist'].'" already has an album '.$aAlbumInfo['properties']['name']);
		}
		
		// build album node
		$nodeAlbum = $nodeAlbumArtist->addNode($aAlbumInfo['properties']['info_title'], 'sb_jukebox:album');
		foreach ($aAlbumInfo['properties'] as $sProperty => $mValue) {
			$nodeAlbum->setProperty($sProperty, $mValue);
		}
		$nodeAlbum->setProperty('info_artist', $nodeAlbumArtist->getProperty('jcr:uuid'));
		
		try {
			
			// init helpers
			$aAlbumTags = array();
			$bTracksPresent = FALSE;
			$aImportedTracks = array();
			
			// add mp3s / cycle tracks
			$dirAlbum->filterFiles('/^.*\.mp3$/i');
			$dirAlbum->sort();
			
			if ($dirAlbum->countFiles() == 0) { // assume CD subdirs
				foreach ($dirAlbum->getDirectories(TRUE) as $dirCD) {
					$dirCD->filterFiles('/^.*\.mp3$/i');
					$dirCD->sort();
					$this->addTracks($nodeAlbum, $dirAlbum, $dirCD);
				}
			} else { // tracks in album directory
				$this->addTracks($nodeAlbum, $dirAlbum, $dirAlbum);
			}
			
			// add tags
			$nodeAlbum->addTags($aAlbumInfo['tags']);
			$nodeAlbumArtist->addTags($nodeAlbum->getTags());
			
			//$aInfo['tags'] = $aAlbumTags;
			$aInfo['nodeAlbum'] = $nodeAlbum;
			$aInfo['nodeAlbumArtist'] = $nodeAlbumArtist;
			return ($aInfo);
			
		} catch (ImportException $e) {
			$nodeAlbum->remove();	
			throw ($e);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAlbumInfo($dirAlbum) {
		
		// init ----------------------------------------------------------------
		
		$aAlbumInfo = array(
			'tags' => array(),
		);
		$aAlbumProps = array(
			'label' => '',
			'name' => '',
			'info_type' => 'LP',
			'info_artist' => '',
			'info_defects' => '',
			'info_relpath' => '',
			'info_cdsinset' => '',
			'info_published' => '',
			'info_coverexists' => 'FALSE',
			'info_coverfilename' => '',
		);
		
		$aDirFormatTokens = array(
			'artist' => '%a',
			'title' => '%t',
			'year' => '%y',
			'defects' => '%d',
			'albumtype' => '%v',
			'[',
			']',
		);
		$aDirFormatReplacements = array(
			'(.+)',
			'(.+)',
			'(\d+)',
			'(.+)',
			'(.+)',
			'\[',
			'\]',
		);
		/*$aDirFormatTokens = array(
			'%artist%' => '(.+)',
			'%title%' => '(.+)',
			'%year%' => '(\d+)',
			'%defects%' => '(.+)',
			'%albumtype%' => '(.+)',
		);*/
		
		// begin import
		$sDirName = iconv($dirAlbum->getEncoding(), 'UTF-8', $dirAlbum->getName());
		echo ('<h1 id="album_'.md5($dirAlbum->getName()).'">'.$sDirName.'</h1><br>');
		
		// check if directory is marked to be skipped
		if (substr_count($sDirName, '[SKIP]') > 0) {
			throw new ImportException('[skipped] - directory is marked to be skipped', E_WARNING);
		}
		
		// check status
		$fileInfo = $dirAlbum->getFile('sbJukebox.txt');
		if ($fileInfo) {
			$aInfo = unserialize($fileInfo->getContents());
		} else {
			$aInfo['state'] = 'new';
		}
		if ($aInfo['state'] == 'imported') {
			throw new ImportException('[skipped] - directory is already imported', E_WARNING);
		}
		
		//$aAlbumInfo = preg_match_masks($this->aKnownDirFormats, )
		
		// cycle through directory formats
		$bPatternFound = FALSE;
		foreach ($this->aKnownDirFormats as $sFormat) {
			
			// init
			$aMeanings = array();
			$aLinks = array();
			
			// build pattern
			$sPattern = str_replace($aDirFormatTokens, $aDirFormatReplacements, $sFormat);
			$sPattern = '/^'.$sPattern.'$/U';
			
			// build matches sequence
			foreach ($aDirFormatTokens as $sMeaning => $sPlaceholder) {
				if (!is_numeric($sMeaning) && substr_count($sFormat, $sPlaceholder) > 0) {
					$aMeanings[strpos($sFormat, $sPlaceholder)] = $sMeaning;
				}
			}
			ksort($aMeanings);
			foreach ($aMeanings as $sMeaning) {
				$aLinks[] = $sMeaning;	
			}
			$aLinks = array_flip($aLinks);
			$aLinks = array_flip($aLinks);
			
			// match directory and assign
			$aMatches = array();
			if (preg_match($sPattern, $dirAlbum->getName(), $aMatches)) {
				
				$bPatternFound = TRUE;
				if ($this->aVerboseFlags['DIRNAME_FORMAT']) {
					echo 'Recognized Format: '.$sFormat.'<br>';
				}
				
				foreach ($aMatches as $iKey => $sMatch) {
					if ($iKey == 0) {
						continue;
					}
					$aAlbumInfo[$aLinks[$iKey-1]] = $sMatch;
				}
				break;
				
			}
			
		}
		
		if (!$bPatternFound) {
			throw new ImportException('[skipped] - directory does not match any pattern');
		}
		
		// prepare info & apply special artist rules
		$aAlbumInfo['artist'] = iconv($dirAlbum->getEncoding(), 'UTF-8', $aAlbumInfo['artist']);
		$aAlbumInfo['title'] = iconv($dirAlbum->getEncoding(), 'UTF-8', $aAlbumInfo['title']);
		
		foreach ($this->aKnownSpecialArtists as $sArtist => $sRealArtist) {
			if ($aAlbumInfo['artist'] == $sArtist) {
				$aAlbumInfo['artist'] = $sRealArtist;
			}
		}
		
		// store results in properies
		$aAlbumProps['label'] = $aAlbumInfo['artist'].' - '.$aAlbumInfo['title'];
		$aAlbumProps['name'] = str2urlsafe($aAlbumProps['label']);
		$aAlbumProps['info_title'] = $aAlbumInfo['title'];
		if (isset($aAlbumInfo['year'])) {
			$aAlbumProps['info_published'] = $aAlbumInfo['year'];
			if ($this->aTagFlags['YEAR']) {
				$aAlbumInfo['tags'][] = $aAlbumInfo['year'];
			}
		} else {
			if ($this->aAbortFlags['NO_YEAR']) {
				throw new ImportException('[abort] - no year given in directory name');
			}
		}
		
		// check cover
		$fileCover = $dirAlbum->getFile($this->aKnownCoverFilenames);
		if ($fileCover) {
			$aAlbumProps['info_coverexists'] = 'TRUE';
			$aAlbumProps['info_coverfilename'] = iconv($dirAlbum->getEncoding(), 'UTF-8', $fileCover->getName());
			// store cover luminance
			$imgCover = new Image(Image::FROMFILE, $dirAlbum->getAbsPath().$fileCover->getName());
			$aHSL = $imgCover->getHSL(500);
			$aAlbumProps['ext_coverhue'] = $aHSL['h'];
			$aAlbumProps['ext_coversaturation'] = $aHSL['s'];
			$aAlbumProps['ext_coverlightness'] = $aHSL['l'];
			unset($imgCover);
			if ($this->aVerboseFlags['COVER_INFO']) {
				$this->echoInfo('good', 'cover found: '.$fileCover->getName().' (hue: '.$aHSL['h'].', saturation: '.$aHSL['s'].', lightness: '.$aHSL['l'].')');
			}
		} else {
			if ($this->aAbortFlags['NO_COVER']) {
				throw new ImportException('[abort] - no cover found');
			}
			if ($this->aVerboseFlags['COVER_INFO']) {
				$this->echoInfo('bad', 'no cover found');
			}
		}
		
		$aAlbumProps['info_relpath'] = iconv($dirAlbum->getEncoding(), 'UTF-8', $dirAlbum->getRelPath($this->nodeJukebox->getProperty('config_sourcepath')));
		
		$aAlbumInfo['properties'] = $aAlbumProps;
		//var_dumpp($aAlbumInfo); die();
		return ($aAlbumInfo);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addTracks($nodeAlbum, $dirAlbum, $dirCD) {
		
		// init helpers
		$bTracksPresent = FALSE;
		
		// scan for tags first
		$aCustomTags = $this->getCustomTags($dirAlbum);
		$nodeAlbum->addTags($aCustomTags);
		
		foreach ($dirCD->getFiles() as $sFileName) {
			
			// at least one track is in there
			$bTracksPresent = TRUE;
			
			// get basic info
			$sRelPath = $dirCD->getRelPath($dirAlbum->getAbsPath()).$sFileName;
			$aTrackInfo = $this->getTrackInfo($dirAlbum->getAbsPath(), $sRelPath);
			
			if ($this->aVerboseFlags['TRACK_NAMES']) {
				echo $aTrackInfo['properties']['label'];
			}
			if (isset($aTrackInfo['note'])) {
				echo ' <span class="note">['.$aTrackInfo['note'].']</span>';
			}
			echo '<br>';
			
			// check artist
			//var_dumpp('TrackArtist: '.$aTrackInfo['artist']);
			$nodeTrackArtist = $this->getArtistNode($aTrackInfo['artist']);
			
			// TODO: find cleaner way to suppress duplicate names
			$sNodeName = $aTrackInfo['properties']['name'];
			if (isset($aImportedTracks[$sNodeName])) {
				$aTrackInfo['properties']['name'] .= '_'.$aImportedTracks[$sNodeName]++;
			} else {
				$aImportedTracks[$sNodeName] = 1;
			}
			
			// build track node
			$nodeTrack = $nodeAlbum->addNode($aTrackInfo['properties']['name'], 'sb_jukebox:track');
			foreach ($aTrackInfo['properties'] as $sProperty => $mValue) {
				$nodeTrack->setProperty($sProperty, $mValue);
			}
			$nodeTrack->setProperty('info_artist', $nodeTrackArtist->getProperty('jcr:uuid'));
			
			// add tags
			$nodeTrack->addTags(array_merge($aTrackInfo['tags'], $aCustomTags));
			//$aAlbumTags = array_merge($aAlbumTags, $aTrackInfo['tags'], $aCustomTags);
			$nodeAlbum->addTags($nodeTrack->getTags());
			
		}
		
		if (!$bTracksPresent) {
			throw new ImportException('[abort] - no tracks present in directory');
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getTrackInfo($sAlbumPath, $sRelPath) {
		
		// init ----------------------------------------------------------------
		
		$aTrackInfo = array(
			'tags' => array(),
		);
		$aNodeProps = array(
			'label' => '',
			'name' => '',
			'info_title' => '',
			'info_index' => 0,
			'info_filename' => '',
			'info_playtime' => '',
			'enc_playtime' => '',
			'enc_mode' => '',
			'enc_bitrate' => '',
		);
		
		// get track info through getid3 ---------------------------------------
		
		$oGetID3 = new getid3();
		$oGetID3 = new getid3(); // instatiate twice because of strange heplerapps bug in getid3!
		error_reporting(0);
		$aInfo = $oGetID3->analyze($sAlbumPath.$sRelPath);
		error_reporting(E_STRICT | E_ALL);
		
		// check premises ------------------------------------------------------
		
		if (!isset($aInfo['tags']['id3v2'])) {
			var_dumpp($sAlbumPath.$sRelPath);
			var_dumpp($aInfo); exit();
			throw new ImportException('[abort] - no ID3v2 tags in '.$sRelPath);
		}
		if (!isset($aInfo['tags']['id3v1'])) {
			throw new ImportException('[abort] - no ID3v1 tags in '.$sRelPath);
		}
		
		// check if artists are equal
		// TODO: check all used tags!
		if ($this->aAbortFlags['DIFFERING_TAGS']) {
			$sArtistV2 = $aInfo['tags']['id3v2']['artist'][0];
			$sArtistV1 = $aInfo['tags']['id3v1']['artist'][0];
			if ($sArtistV1 != $sArtistV2) {
				if (substr($sArtistV2, 0, strlen($sArtistV1)) != $sArtistV1) {
					throw new ImportException('[abort] - artist in V1 ('.$sArtistV1.') does no match artist in V2 ('.$sArtistV2.')');
				}
			}
		}
		
		// set ID3V2 as source
		$aSource = $aInfo['tags']['id3v2'];
		
		// get tags
		$sArtist = $aSource['artist'][0];
		$sTitle = $aSource['title'][0];
		
		// check if mandatory tags are empty
		if ($sArtist == '' || $sTitle == '') {
			throw new ImportException('[abort] - artist empty in '.$sRelPath);
		}
		if (!isset($aInfo['playtime_string']) || !isset($aInfo['playtime_seconds'])) {
			//var_dumpp($aInfo);
			throw new ImportException('[abort] - playtime missing in '.$sRelPath);
		}
		if (!isset($aInfo['mpeg']['audio'])) {
			throw new ImportException('[abort] - mpeg info missing in '.$sRelPath);
		}
		if (!isset($aInfo['mpeg']['audio']['bitrate_mode']) || !isset($aInfo['mpeg']['audio']['bitrate'])) {
			throw new ImportException('[abort] - encoding or bitrate missing in '.$sRelPath);
		}
		
		//var_dumpp($aSource['content_type'][0]);
		if (isset($aSource['content_type'][0]) && $aSource['content_type'][0] != '') {
			$sGenres = iconv($oGetID3->encoding, 'UTF-8', $aSource['content_type'][0]);
			//var_dumpp($sGenres);
			if (substr_count($sGenres, '/')) {
				//echo 'split';
				$aGenres = explode('/', $sGenres);
			} else {
				$aGenres[] = $sGenres;
			}
			//var_dumpp($aGenres);
			foreach ($aGenres as $iKey => $sGenre) {
				$sGenre = trim($sGenre);
				$aGenres[$iKey] = $sGenre;
				if (!preg_match('/[\w ]/', $sGenre)) {
					throw new ImportException('[abort] - genre "'.$sGenre.'" is not valid [\w ]');	
				}
				if ($sParsedGenre = $this->jbGenreLib->getGenre($sGenre)) {
					$aGenres[$iKey] = $sParsedGenre;
				}
			}
			//var_dumpp($aGenres);
			if ($this->aTagFlags['GENRE']) {
				$aTrackInfo['tags'] = $aGenres;
			}
		} else {
			if ($this->aAbortFlags['NO_GENRE']) {
				throw new ImportException('[abort] - no genre given in '.$sRelPath);
			}
			if ($this->aTagFlags['GENRE']) {
				$aTrackInfo['tags'][] = 'NO GENRE';
			}
		}
		
		// finish
		$aTrackInfo['artist'] 			= iconv($oGetID3->encoding, 'UTF-8', $sArtist);
		
		$aNodeProps['label']			= iconv($oGetID3->encoding, 'UTF-8', $sArtist.' - '.$sTitle);
		$aNodeProps['name']				= str2urlsafe(iconv($oGetID3->encoding, 'UTF-8', $sArtist.'_'.$sTitle));
		$aNodeProps['info_title']		= iconv($oGetID3->encoding, 'UTF-8', $sTitle);
		if (isset($aInfo['tags']['id3v2']['track_number'])) {
			$aNodeProps['info_index'] = $aInfo['tags']['id3v2']['track_number'][0];
		}
		// FIXME: needs to use filesystem encoding instead of getid3 encoding!
		$aNodeProps['info_filename']	= iconv($oGetID3->encoding, 'UTF-8', $sRelPath);
		//echo ($aNodeProps['info_filename']);
		$aNodeProps['info_playtime']	= $aInfo['playtime_string'];
		$aNodeProps['enc_playtime']		= round($aInfo['playtime_seconds']);
		$aNodeProps['enc_mode']			= strtoupper($aInfo['mpeg']['audio']['bitrate_mode']);
		$aNodeProps['enc_bitrate']		= round($aInfo['mpeg']['audio']['bitrate'] / 1000);
		if ($this->aTagFlags['BITRATE'] &&  $aNodeProps['enc_mode'] == 'CBR') {
			$aTrackInfo['tags'][] = $aNodeProps['enc_bitrate'].'kbs';
		}
		if ($this->aTagFlags['ENCODING']) {
			$aTrackInfo['tags'][] = $aNodeProps['enc_mode'];
		}
		
		$aTrackInfo['properties']		= $aNodeProps;
		
		// cleanup
		//var_dumpp($aInfo); die();
		unset($aInfo);
		
		return ($aTrackInfo);
		
	}

}

?>