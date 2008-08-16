

function toggleMenuEntry(sNodeID) {
	
	var nodeIcon = $('icon' + sNodeID);
	//alert('children' + sNodeID);
	if (nodeIcon.getAttribute('name') == 'close') {
		updateMenuEntry(sNodeID, 'close');
	} else {
		updateMenuEntry(sNodeID, 'open');
	}
}

function reloadMenuEntry(sNodeID) {
	updateMenuEntry(sNodeID, 'reload');
}

function updateMenuEntry(sNodeID, sMode) {
	
	var nodeItem = $('entry' + sNodeID);
	var nodeIcon = $('icon' + sNodeID);
	var nodeChildren = $('children' + sNodeID);
	
	var sNodePath = sNodeID;
	
	sNodePath = sNodePath.replace(/::/g, '!');
	sNodePath = sNodePath.replace(/:/g, '/');
	sNodePath = sNodePath.replace(/!/g, '::');	
	
	switch (sMode) {
	
		case 'reload':
			var sUrl = 'backend.view=menu&subjectnode=' + sNodePath;
			var sID = 'entry' + sNodeID;
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
			
			var sUrl = 'backend.view=menu&close=' + sNodePath + '&subjectnode=' + sNodePath;
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
			
			var sUrl = 'backend.view=menu&open=' + sNodePath + '&subjectnode=' + sNodePath;
			//alert(sUrl);
			var sID = 'entry' + sNodeID;
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

function openContextMenu(sNodeID) {
	
	//alert('fucjk');
	
	/*var win = new Window('google', 
	{className: "spread", 
	title: "Ruby on Rails", 
	top:70, 
	left:100, 
	width:300, 
	height:200, 
	resizable: true, 
	url: "http://www.google.de/"
	}
	);
	win.toFront();
	win.show();*/
	
	
	Dialog.alert("Close the window 'Test' before opening it again!", 
	{windowParameters:{ width:200, height:130}});
	
	/*var winContext = new Window(
		'menu_context', 
		{
			resizable: false,
			//hideEffect:Element.hide,
			//showEffect:Element.show,
			minWidth: 10
		}
	);
	
	winContext.setAjaxContent('backend.view=menu&subjectnode='+sNodeID, null, true, true);
	winContext.toFront();
	winContext.setDestroyOnClose();
	winContext.show();*/
	
	
}


