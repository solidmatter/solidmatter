/*
function toggleMenuEntry(iNodeID) {
	alert(iNodeID);
}


/*function toggleMenuEntry(iNodeID) {
	
	var nodeItem = $('entry_' + iNodeID);
	var nodeIcon = $('icon_' + iNodeID);
	var nodeChildren = $('children_' + iNodeID);	
	
	if (nodeIcon.getAttribute('name') == 'close') {
		
		nodeIcon.setAttribute('name', 'open');
		nodeIcon.setAttribute('src', 'modules/sb_system/themes/_admin/images_sysicons/tree_open.gif');
		
		if (nodeChildren != null) {
			nodeItem.removeChild(nodeChildren);
		}
		
		var sUrl = 'backend.view=menu&close=' + iNodeID + '&subjectnodeid=' + iNodeID;
		var myAjaxCloser = new Ajax.Request(
			sUrl, 
			{
				method: 'get', 
				parameters: null
			}
		);
		
	} else {
	
		nodeIcon.setAttribute('name', 'close');
		nodeIcon.setAttribute('src', 'modules/sb_system/themes/_admin/images_sysicons/tree_close.gif');
		
		var sUrl = 'backend.view=menu&open=' + iNodeID + '&subjectnodeid=' + iNodeID;
		var sID = 'entry_' + iNodeID;
		var myAjaxOpener = new Ajax.Updater( 
			sID,
			sUrl, 
			{
				method: 'get', 
				parameters: null 
			}
		);
		
	}
	
	//alert(iNodeID);
	
}*/
/*
function toggleMenuEntry(iNodeID) {
	
	var nodeIcon = $('icon_' + iNodeID);
	
	if (nodeIcon.getAttribute('name') == 'close') {
		updateMenuEntry(iNodeID, 'close');
	} else {
		updateMenuEntry(iNodeID, 'open');
	}
}

function reloadMenuEntry(iNodeID) {
	updateMenuEntry(iNodeID, 'reload');
}

function updateMenuEntry(iNodeID, sMode) {
	
	var nodeItem = $('entry_' + iNodeID);
	var nodeIcon = $('icon_' + iNodeID);
	var nodeChildren = $('children_' + iNodeID);
	
	switch (sMode) {
	
		case 'reload':
			var sUrl = 'backend.view=menu&subjectnodeid=' + iNodeID;
			var sID = 'entry_' + iNodeID;
			var myAjaxOpener = new Ajax.Updater( 
				sID,
				sUrl, 
				{
					method: 'get', 
					parameters: null 
				}
			);
			break;
			
		case 'close':
			nodeIcon.setAttribute('name', 'open');
			nodeIcon.setAttribute('src', 'modules/sb_system/themes/_admin/images_sysicons/tree_open.gif');
			
			if (nodeChildren != null) {
				nodeItem.removeChild(nodeChildren);
			}
			
			var sUrl = 'backend.view=menu&close=' + iNodeID + '&subjectnodeid=' + iNodeID;
			var myAjaxCloser = new Ajax.Request(
				sUrl, 
				{
					method: 'get', 
					parameters: null
				}
			);
			break;
			
			
		case 'open':
			nodeIcon.setAttribute('name', 'close');
			nodeIcon.setAttribute('src', 'modules/sb_system/themes/_admin/images_sysicons/tree_close.gif');
			
			var sUrl = 'backend.view=menu&open=' + iNodeID + '&subjectnodeid=' + iNodeID;
			var sID = 'entry_' + iNodeID;
			var myAjaxOpener = new Ajax.Updater( 
				sID,
				sUrl, 
				{
					method: 'get', 
					parameters: null 
				}
			);
			break;
	
	
	}


}


*/


function toggleMenuEntry(iNodeID) {
	
	var nodeIcon = $('icon_' + iNodeID);
	
	if (nodeIcon.getAttribute('name') == 'close') {
		updateMenuEntry(iNodeID, 'close');
	} else {
		updateMenuEntry(iNodeID, 'open');
	}
}

function reloadMenuEntry(iNodeID) {
	updateMenuEntry(iNodeID, 'reload');
}

function updateMenuEntry(iNodeID, sMode) {
	
	var nodeItem = $('entry_' + iNodeID);
	var nodeIcon = $('icon_' + iNodeID);
	var nodeChildren = $('children_' + iNodeID);
	
	switch (sMode) {
	
		case 'reload':
			var sUrl = 'backend.view=menu&subjectnodeid=' + iNodeID;
			var sID = 'entry_' + iNodeID;
			var myAjaxOpener = new Ajax.Updater( 
				sID,
				sUrl, 
				{
					method: 'get', 
					parameters: null 
				}
			);
			break;
			
		case 'close':
			nodeIcon.setAttribute('name', 'open');
			nodeIcon.setAttribute('src', 'modules/sb_system/themes/_admin/images_sysicons/tree_open.gif');
			
			if (nodeChildren != null) {
				nodeItem.removeChild(nodeChildren);
			}
			
			var sUrl = 'backend.view=menu&close=' + iNodeID + '&subjectnodeid=' + iNodeID;
			var myAjaxCloser = new Ajax.Request(
				sUrl, 
				{
					method: 'get', 
					parameters: null
				}
			);
			break;
			
			
		case 'open':
			nodeIcon.setAttribute('name', 'close');
			nodeIcon.setAttribute('src', 'modules/sb_system/themes/_admin/images_sysicons/tree_close.gif');
			
			var sUrl = 'backend.view=menu&open=' + iNodeID + '&subjectnodeid=' + iNodeID;
			var sID = 'entry_' + iNodeID;
			var myAjaxOpener = new Ajax.Updater( 
				sID,
				sUrl, 
				{
					method: 'get', 
					parameters: null 
				}
			);
			break;
	
	
	}


}


