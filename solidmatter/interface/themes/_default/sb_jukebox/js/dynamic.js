
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbJukebox]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

// global helper variables

var sActivePlaylistUUID = null; // stores the active playlist UUID
var sDisplayedPlaylistUUID = null; // stores the currently viewed playlist UUID
var bWriteAllowed = false; // is set to true if user can write currently viewed playlist

var sConfirmationURL = null; // stores the URL to follow after confirmation

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
function request_confirmation(sURL) {
	
	sConfirmationURL = sURL;
	
	new Effect.Opacity('confirmation', { 
		from: 0.0, 
		to: 1.0, 
		duration: 0.5, 
		beforeStart: function() {
			new Effect.Opacity('confirmation', { 
				to: 0, 
				duration: 0,
				afterFinish: function() {
					$('confirmation').style.display = 'block';
				}
			});
		}
	});
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function yes() {
	
	window.location.href = sConfirmationURL;
	
}

//------------------------------------------------------------------------------
/**
* 
*/
function no() {
	
	new Effect.Opacity('confirmation', { 
		from: 1.0, 
		to: 0.0, 
		duration: 0.5, 
		afterFinish: function() {
			$('confirmation').style.display = 'none';
		}
	});
	
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
function show_(sID) {
	
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
function add_to_playlist(sSubjectUUID, sPlaylistUUID) {
	
	// URL Scheme = "{$activePlaylist/@uuid}/details/addItem/?item={@uuid}"
	var sURL = '/' + sActivePlaylistUUID + '/details/addItem/?item=' + sSubjectUUID; 
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


//------------------------------------------------------------------------------
/**
* 
*/
function select_playlist(sPlaylistUUID) {
	
	sActivePlaylistUUID = sPlaylistUUID;
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
	
	if (sDisplayedPlaylistUUID != null) {
		check_playlistactions();
	}
	
}

//------------------------------------------------------------------------------
// only for usage with displaying a playlist
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
// remove an entry and fade it out
// TODO: removed items may still be copied or moved
//
function remove(sUUID) {
	
	var sURL = '/' + sDisplayedPlaylistUUID + '/details/removeItem/?item=' + sUUID + '&silent=1';
	var myAjaxRemover = new Ajax.Request(
		sURL, 
		{
			method: 'get', 
			parameters: null
		}
	);
	$('item_' + sUUID).fade({ afterFinish: redraw });
	
}

//------------------------------------------------------------------------------
// redraw resp. recolor all list entries
//
function redraw() {
	
	var sClass = 'odd';
	var eChildren = $('playlist').childElements();

	var iOdd = 1;
	for (var i=0; i<eChildren.length; i++) {
		if (eChildren[i].style.display == 'none') {
			iOdd = 1 - iOdd;
		}
		var sItemID = eChildren[i].id;
		var bChecked = $(sItemID.replace(/item\_/, "check_")).checked;
		if (iOdd == 1) {
			sClass = 'odd';
		} else {
			sClass = 'even';
		}
		if (bChecked) {
			sClass = sClass + '_selected';
		}
		eChildren[i].className = sClass;
		iOdd = 1 - iOdd;
	}
	
}

//------------------------------------------------------------------------------
// callback on dragging and dropping an item
//
function reorder(info) {
	
	var aCurrentState = getOrder(oPlaylist);
	var sSubject = '';
	var sNextSibling = '';
	
	for (var i=0; i<aCurrentState.length; i++) {
		if (aInitialState[i] != aCurrentState[i]) { // different item in lists
			if (aCurrentState[i] == aInitialState[i+1] && aCurrentState[i+1] == aInitialState[i]) { // items switched
				sSubject = aCurrentState[i];
				sNextSibling = aCurrentState[i+1];
				update(sSubject, sNextSibling);
				break;
			} else if (aCurrentState[i] == aInitialState[i+1]) { // missing item = moved down
				for (var j=i; j<aCurrentState.length; j++) { // find missing item
					if (aCurrentState[j] != aInitialState[j+1]) {
						if (!aCurrentState[j+1]) { // item moved to end of list
							sPreviousSibling = aCurrentState[j-1];
							sSubject = aCurrentState[j];
							update(sSubject, sPreviousSibling); // move item just before last item
							update(sPreviousSibling, sSubject); // flip the two
						} else { // item moved down
							sSubject = aCurrentState[j];
							sNextSibling = aCurrentState[j+1];
							update(sSubject, sNextSibling);
						}
						break;
					}
				}
				break;
			} else { // item moved up
				sSubject = aCurrentState[i];
				sNextSibling = aCurrentState[i+1];
				update(sSubject, sNextSibling);
				break;
			}
		}
	}
	
	aInitialState = aCurrentState;

}

//------------------------------------------------------------------------------
// assistant function the saves a change
//
function update (sSubject, sNextSibling) {
	
	sURL = '/' + sDisplayedPlaylistUUID + '/details/orderBefore/?subject=' + sSubject + '&nextsibling=' + sNextSibling;
	var myAjaxUpdater = new Ajax.Request(
		sURL, 
		{
			method: 'get', 
			parameters: null,
			asynchronous: true 
		}
	);
	
}

//------------------------------------------------------------------------------
// gets an array with the current ordered uuids
//
function getOrder() {
	
	var aCurrentOrder = new Array();
	var aOrderedNodes = oPlaylist.getElementsByTagName("li");
	for (var i=0; i<aOrderedNodes.length; i++) {
		aCurrentOrder[i] = aOrderedNodes[i].getAttribute('id').substr(5);
	}
	
	return (aCurrentOrder);
	
}

//------------------------------------------------------------------------------
// 
//
function toggle_checked(sUUID) {
	
	var oItem = $('item_' + sUUID);

	switch (oItem.className) {
		case "odd": 
			oItem.className = 'odd_selected';
			break;
		case "even": 
			oItem.className = 'even_selected';
			break;
		case "odd_selected": 
			oItem.className = 'odd';
			break;
		case "even_selected": 
			oItem.className = 'even';
			break;
	}

	check_playlistactions();
	
}

//------------------------------------------------------------------------------
// 
//
function check_playlistactions() {
	
	var bActionVisible = false;
	var bCopy = true;
	var bMove = true;
	var bRemove = true;
	
	// don't show actions if there is no active playlist or if it is currently displayed
	if (sActivePlaylistUUID == null || sActivePlaylistUUID == sDisplayedPlaylistUUID) {
		bCopy = false;
		bMove = false;
	}
	
	// show actions if at least one playlist entry ist checked
	var aCheckboxes = $$('input.helper');
	var bActiveFound = false;
	for (var i=0; i<aCheckboxes.length; i++) {
		if (aCheckboxes[i].checked) {
			bActiveFound = true;
		}
	}
	
	// toggle different action types individually
	if (bCopy && bActiveFound) {
		$('playlist_actions_copy').style.display = 'inline';
		bActionVisible = true;
	} else {
		$('playlist_actions_copy').style.display = 'none';
	}
	if (bMove && bActiveFound && bWriteAllowed) {
		$('playlist_actions_move').style.display = 'inline';
		bActionVisible = true;
	} else {
		$('playlist_actions_move').style.display = 'none';
	}
	if (bRemove && bActiveFound && bWriteAllowed) {
		$('playlist_actions_remove').style.display = 'inline';
		bActionVisible = true;
	} else {
		$('playlist_actions_remove').style.display = 'none';
	}
	
	// show title if any action is visible
	if (bActionVisible) {
		$('playlist_actions').style.display = 'inline';
	} else {
		$('playlist_actions').style.display = 'none';
	}
	
}

//------------------------------------------------------------------------------
// 
//
function submit_form(sMode) {
	
	var oForm = $('playlist_form');
	
	switch (sMode) {
		case "copy":
			oForm.action = '/' + sDisplayedPlaylistUUID + '/details/copyItems/?target=' + sActivePlaylistUUID;
			break;
		case "move":
			oForm.action = '/' + sDisplayedPlaylistUUID + '/details/moveItems/?target=' + sActivePlaylistUUID;
			break;
		case "remove":
			oForm.action = '/' + sDisplayedPlaylistUUID + '/details/removeItems';
			break;
		default:
			alert('unrecognized mode: ' + sMode);
			break;
	}

	$('playlist_form_submit').click();

}

//------------------------------------------------------------------------------
//
//
function open_player(sURL) {
	
	oPlayerFrame = window.top.document.getElementById('player_iframe');
	
	if (oPlayerFrame == null) { // open in new window
		
		window.open(sURL,'sbJukeboxPlayer',"resizable=no,toolbar=no,scrollbars=yes,menubar=no,status=no,directories=n o,width=350,height=700,left=50,top=50");
		
	} else { // open in separate Frame
		
		oContentFrame = window.top.document.getElementById('content_iframe');
//		console.log("content.width: " + oPlayerFrame.style.marginRight);
		
		oPlayerFrame.src = sURL;
		oPlayerFrame.style.display = 'block';
		oPlayerFrame.style.width = '300px';
//		oContentFrame.style.paddingRight = '350px';
		oContentFrame.style.width = 'calc(100% - 300px)';
		
		console.log("player.width: " + oPlayerFrame.style.width);
		console.log("content.width: " + oContentFrame.style.width);
		
//		window.top.padding = '0 350px 0 0';
		console.log("Opening: " + sURL);
		
	}
	
	

}
