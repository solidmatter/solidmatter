
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

var sCurrentPlaylistUUID = null;

//------------------------------------------------------------------------------
/**
* 
*/
function add_playbutton(sUUID, oCover) {
	
	/*var ePlayButton = document.createElement('a');
	var ePlayImage = document.createElement('img');
	
	
	ePlayButton.setAttribute('href', '/' + sUUID + '/details/getM3U');
	ePlayImage.setAttribute('src', '/theme/sb_jukebox/icons/play.png');
	ePlayButton.setAttribute('style', 'position: absolute; top:0px; right:0;');
	
	ePlayButton.appendChild(ePlayImage);
	oCover.parentNode.insertBefore(ePlayButton, oCover.parentNode.firstChild);*/
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function remove_playbutton(oCover) {
	
	//oCover.parentNode.removeChild(oCover.parentNode.firstChild);
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function toggle(sID) {
	
	var oContainer = $(sID);
	
	if (oContainer.style.display != 'none') {
		oContainer.style.display = 'none';
	} else {
		if (oContainer.tagName == 'TR') {
			oContainer.style.display = 'table-row';
		} else {
			oContainer.style.display = 'block';
		}
	}
}

//------------------------------------------------------------------------------
/**
* 
*/
function toggle_albumdetails(sAlbumUUID) {
	
	var oContainer = $('details_' + sAlbumUUID).parentNode;
	
	if (oContainer.style.display != 'none') {
		oContainer.style.display = 'none';
	} else {
		oContainer.style.display = 'table-row';
		if (!oContainer.loaded) {
			var sUrl = '/' + sAlbumUUID + '/details/displayInline';
			var sID = 'details_' + sAlbumUUID;
			var myAjaxOpener = new Ajax.Updater( 
				sID,
				sUrl,
				{
					method: 'get', 
					parameters: null 
				}
			);
			oContainer.loaded = true;
		}
	}
}

//------------------------------------------------------------------------------
/**
* 
*/
function select_playlist(sPlaylistUUID) {
	
	sCurrentPlaylistUUID = sPlaylistUUID;
	var oCurrentPlaylist = $('current_playlist_link');
	var oSelectedPlaylist = $('select_' + sPlaylistUUID);
	
	var sUrl = '/' + sPlaylistUUID + '/details/activate';
	var myAjaxOpener = new Ajax.Request(
		sUrl,
		{
			method: 'get', 
			parameters: null,
			asynchronous: false
		}
	);
	
	var sUrl = '/' + sPlaylistUUID;
	oCurrentPlaylist.firstChild.data = oSelectedPlaylist.firstChild.data;
	oCurrentPlaylist.href = sUrl;
	oCurrentPlaylist.style.display = '';
	toggle('writablePlaylists');
}

//------------------------------------------------------------------------------
/**
* 
*/
function add_to_playlist(sSubjectUUID, sPlaylistUUID) {
	
	// URL Scheme = "{$currentPlaylist/@uuid}/details/addItem/?item={@uuid}"
	var sURL = '/' + sCurrentPlaylistUUID + '/details/addItem/?item=' + sSubjectUUID; 
	request_and_highlight(sURL, sSubjectUUID);
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function add_to_favorites(sSubjectUUID) {
	
	// URL Scheme = "/-/favorites/addItem/?item={@uuid}"
	var sURL = '/-/favorites/addItem/?item=' + sSubjectUUID; 
	request_and_highlight(sURL, sSubjectUUID);
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function request_and_highlight(sURL, sSubjectUUID) {
	
	var myAjaxOpener = new Ajax.Request(
		sURL,
		{
			method: 'get', 
			parameters: null,
			asynchronous: false,
			onComplete: function(response) {
				highlight(sSubjectUUID, response.getHeader('X-sbException'));
    		}
		}
	);
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function highlight(sSubjectUUID, iCode) {
	
	var sStartingColor = '#00FF00'; // start with green for debugging reasons
	//alert (iCode);
	if (iCode == null) {
		iCode = "0";
	}
	
	
	switch (iCode) {
		case "0": sStartingColor = '#BBBBBB'; break; // success
		case "1": sStartingColor = '#FF0000'; break; // failure
	}
	
	new Effect.Highlight($('highlight_' + sSubjectUUID), { startcolor: sStartingColor, restorecolor: true });
	
}