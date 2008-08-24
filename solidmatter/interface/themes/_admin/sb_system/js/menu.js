
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* all special functionalities required by the tree menu only 
*/
var sbMenu = {
	
	//--------------------------------------------------------------------------
	// open/close menu branches
	//
	toggleMenuEntry : function (sNodeID) {
	
		var nodeIcon = $('icon' + sNodeID);
		
		if (nodeIcon.getAttribute('name') == 'close') {
			sbMenu._updateMenuEntry(sNodeID, 'close');
		} else {
			sbMenu._updateMenuEntry(sNodeID, 'open');
		}
		
	},
	
	//--------------------------------------------------------------------------
    // just reload a branch
    //
	reloadMenuEntry : function (sNodeID) {
		sbMenu._updateMenuEntry(sNodeID, 'open');
	},
	
	//--------------------------------------------------------------------------
    // update a branch with desired method
    //
	_updateMenuEntry : function (sNodeID, sMode) {
	
		var nodeItem = $('entry' + sNodeID);
		var nodeIcon = $('icon' + sNodeID);
		var nodeChildren = $('children' + sNodeID);
		
		var sNodePath = sNodeID;
		
		sNodePath = sNodePath.replace(/::/g, '!');
		sNodePath = sNodePath.replace(/:/g, '/');
		sNodePath = sNodePath.replace(/!/g, '::');
		
		switch (sMode) {
		
			case 'reload':
				alert('reload not finished yet');
				/*var sUrl = 'backend.view=menu&subjectnode=' + sNodePath;
				var sID = 'entry' + sNodeID;
				var myAjaxOpener = new Ajax.Updater( 
					sID,
					sUrl, 
					{
						method: 'get', 
						parameters: null 
					}
				);*/
				break;
				
			case 'close':
				nodeIcon.setAttribute('name', 'open');
				nodeIcon.setAttribute('src', '/theme/sb_system/icons/tree_open.gif');
				
				if (nodeChildren != null) {
					nodeItem.removeChild(nodeChildren);
				}
				
				var sUrl = '/-/menu/-/close=' + sNodePath;
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
				nodeIcon.setAttribute('src', '/theme/sb_system/icons/tree_wait.gif');
				
				var sUrl = '/-/menu/-/open=' + sNodePath;
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
	
	},
	
	//--------------------------------------------------------------------------
	// delete a node
	//
	delete : function (sParentUUID, sChildUUID) {
		top.sbUtilities.popupModal('/-/structure/deleteChild/parentnode='+ sParentUUID + '&childnode=' + sChildUUID, 500, 250, "top.sbCommander.issueCommand('reloadTree', null);");
		//top.sbCommander.issueCommand('reloadTree', null);
	},
	
	//--------------------------------------------------------------------------
	// pastes a node from the clipboard
	//
    paste : function (sParentUUID) {
		top.sbUtilities.execute('/-/structure/paste/parentnode=' + sParentUUID);
		top.sbCommander.issueCommand('reloadTree', null);
	},
	
	//--------------------------------------------------------------------------
    // creates a hardlink from the node in clipboard
    //
    createLink : function (sParentUUID) {
		top.sbUtilities.execute('/-/structure/createLink/parentnode=' + sParentUUID);
		top.sbCommander.issueCommand('reloadTree', null);
	},
	
	//--------------------------------------------------------------------------
	// cuts a node from the tree to clipboard
	//
    cut : function (sParentUUID, sChildUUID) {
		top.sbUtilities.execute('/-/structure/cut/parentnode=' + sParentUUID + '&childnode=' + sChildUUID);
		//sbCommander.issueCommand('reloadTree', null);
	}

}

//------------------------------------------------------------------------------
/**
* functionalities for the tree rightclick context menu
*/
var sbContextMenu = {

 	// private attributes
    _menus : new Array,
    _attachedElement : null,
    _menuElement : null,
    _preventDefault : true,
    _preventForms : true,
	
	//--------------------------------------------------------------------------
    // public method. Sets up whole context menu stuff...
    //
    init : function (conf) {
	
        if ( document.all && document.getElementById && !window.opera ) {
            sbContextMenu.IE = true;
        }

        if ( !document.all && document.getElementById && !window.opera ) {
            sbContextMenu.FF = true;
        }

        if ( document.all && document.getElementById && window.opera ) {
            sbContextMenu.OP = true;
        }

        if ( sbContextMenu.IE || sbContextMenu.FF ) {

            document.oncontextmenu = sbContextMenu._show;
            document.onclick = sbContextMenu._hide;

            if (conf && typeof(conf.preventDefault) != "undefined") {
                sbContextMenu._preventDefault = conf.preventDefault;
            }

            if (conf && typeof(conf.preventForms) != "undefined") {
                sbContextMenu._preventForms = conf.preventForms;
            }

        }
      	
      	sbContextMenu._menuElement = $('contextmenu');

    },

	//--------------------------------------------------------------------------
    // public method. Attaches context menus to specific class names
    //
    attach : function (classNames, menuId) {

        if (typeof(classNames) == "string") {
            sbContextMenu._menus[classNames] = menuId;
        }

        if (typeof(classNames) == "object") {
            for (x = 0; x < classNames.length; x++) {
                sbContextMenu._menus[classNames[x]] = menuId;
            }
        }

    },
	

    //--------------------------------------------------------------------------
    // private method. Get which context menu to show
    /*_getMenuElementId : function (e) {

        if (sbContextMenu.IE) {
            sbContextMenu._attachedElement = event.srcElement;
        } else {
            sbContextMenu._attachedElement = e.target;
        }

        while(sbContextMenu._attachedElement != null) {
            var className = sbContextMenu._attachedElement.className;

            if (typeof(className) != "undefined") {
                className = className.replace(/^\s+/g, "").replace(/\s+$/g, "")
                var classArray = className.split(/[ ]+/g);

                for (i = 0; i < classArray.length; i++) {
                    if (sbContextMenu._menus[classArray[i]]) {
                        return sbContextMenu._menus[classArray[i]];
                    }
                }
            }

            if (sbContextMenu.IE) {
                sbContextMenu._attachedElement = sbContextMenu._attachedElement.parentElement;
            } else {
                sbContextMenu._attachedElement = sbContextMenu._attachedElement.parentNode;
            }
        }

        return null;

    },*/

	//--------------------------------------------------------------------------
	// private method. returns false as you may have guessed...
	//
    _returnfalse : function () {
		return (false);
	},

    //--------------------------------------------------------------------------
    // private method. Shows context menu
    //
    _getReturnValue : function (e) {
        
        var returnValue = true;
        var evt = sbContextMenu.IE ? window.event : e;

        if (evt.button != 1) {
            
            if (evt.target) {
                var el = evt.target;
            } else if (evt.srcElement) {
                var el = evt.srcElement;
            }
			
            var sTagname = el.tagName.toLowerCase();
            
            if ((sTagname == "input" || sTagname == "textarea")) {
                if (!sbContextMenu._preventForms) {
                    returnValue = true;
                } else {
                    returnValue = false;
                }
            } else {
                if (!sbContextMenu._preventDefault) {
                    returnValue = true;
                } else {
                    returnValue = false;
                }
            }
            
            if (sTagname != 'a') {
            	returnValue = true;
            }
            
        }
        return returnValue;

    },
	
	//--------------------------------------------------------------------------
	// private method. returns necessary info to generate the contect menu
	// 
    _getMenuPath : function (e) {
	
		var evt = sbContextMenu.IE ? window.event : e;
		if (evt.target) {
            var el = evt.target;
        } else if (evt.srcElement) {
            var el = evt.srcElement;
        }
        
        var sNodePath = el.getAttribute('id');
        
        if (sNodePath == null) {
        	return { 'node_id' : '/', 'parent_id' : '' };
        } else {
			sNodePath = sNodePath.replace(/::/g, '!');
			sNodePath = sNodePath.replace(/:/g, '/');
			sNodePath = sNodePath.replace(/!/g, '::');
		}
		
		if (sNodePath.lastIndexOf('/') == 0) {
			return { 'node_id' : sNodePath, 'parent_id' : '/' };
		} else {
			sParentPath = sNodePath.substring(0, sNodePath.lastIndexOf('/'));
			return { 'node_id' : sNodePath, 'parent_id' : sParentPath };
		}
        
	},
	
	//--------------------------------------------------------------------------
	// private method. loads the contextmenu content into the div
	//
    _loadContent : function (e) {
		
		var oPath = sbContextMenu._getMenuPath(e);
		var sUrl = '/-/contextmenu/-/mode=create&subjectnode=' + oPath.node_id + '&parentnode=' + oPath.parent_id;
		var sID = 'contextmenu';
		var myAjaxOpener = new Ajax.Updater( 
			sID,
			sUrl, 
			{
				method: 'get', 
				parameters: null,
				asynchronous: false 
			}
		);
		
	},

    //--------------------------------------------------------------------------
    // private method. Shows context menu
    //
    _show : function (e) {

       /* sbContextMenu._hide();
        //var menuElementId = sbContextMenu._getMenuElementId(e);

        if (menuElementId) {
            var position = sbContextMenu._getMousePosition(e);
            sbContextMenu._menuElement = document.getElementById(menuElementId);
            sbContextMenu._menuElement.style.left = position.x + 'px';
            sbContextMenu._menuElement.style.top = position.y + 'px';
            sbContextMenu._menuElement.style.display = 'block';
            return false;
        }

        return sbContextMenu._getReturnValue(e);*/
		
		var bReturnValue = sbContextMenu._getReturnValue(e);
		
		if (!bReturnValue) {
			sbContextMenu._loadContent(e);
			var position = sbContextMenu._getMousePosition(e);
	        sbContextMenu._menuElement.style.left = position.x + 'px';
	        sbContextMenu._menuElement.style.top = position.y + 'px';
	        sbContextMenu._menuElement.style.display = 'block';
		}
		
        //return false;
        return bReturnValue;
		
    },


	//--------------------------------------------------------------------------
    // private method. Hides context menu
    //
    _hide : function () {

        if (sbContextMenu._menuElement) {
            sbContextMenu._menuElement.style.display = 'none';
        }

    },


    //--------------------------------------------------------------------------
    // private method. Returns mouse position
    //
    _getMousePosition : function (e) {

        if (sbContextMenu.IE) {
            if (document.documentElement.scrollTop) {
                var scrollTop = document.documentElement.scrollTop;
                var scrollLeft = document.documentElement.scrollLeft;
            } else {
                var scrollTop = document.body.scrollTop;
                var scrollLeft = document.body.scrollLeft;
            }
        }

        if (sbContextMenu.FF) {
            var scrollTop = window.pageYOffset;
            var scrollLeft = window.pageXOffset;
        }

        var evt = sbContextMenu.IE ? window.event : e;
        var mX = scrollLeft + evt.clientX;
        var mY = scrollTop + evt.clientY;

        return { 'x' : mX, 'y' : mY };

    }

}









/*
var iPointerX = 0;
var iPointerY = 0;
var oContextMenu = null;

function openContextMenu(sNodeID) {
	
	var sUrl = 'backend.view=contextmenu&subjectnode=' + sNodeID;
	//alert(sUrl);
	var sID = 'contextmenu';
	var myAjaxOpener = new Ajax.Updater( 
		sID,
		sUrl, 
		{
			method: 'get', 
			parameters: null 
		}
	);
	oContextMenu.style.display = 'block';
	//showContextMenu();
}

function showContextMenu() {
	oContextMenu.style.display = 'block';
}

function hideContextMenu() {
	oContextMenu.style.display = 'none';
}


*/









/*

if (window.Event) document.captureEvents(Event.MOUSEUP);

function nocontextmenu(e) {

	e.cancelBubble = true;
	e.returnValue = false;
	
	return false;
	
}

function norightclick(e){

	if (window.Event){
		if (e.which == 2 || e.which == 3);
		return false;
	}
	
	else if (e.button == 2 || e.button == 3){
		e.cancelBubble = true;
		e.returnValue = false;
		return false;
	}
	
}

document.oncontextmenu = nocontextmenu;
document.onmousedown = norightclick;

*/







/*
function nocontextmenu(e){
	var oContextMenu = $('contextmenu');
	iPointerX = window.pageXOffset+e.clientX;
	iPointerY = window.pageYOffset+e.clientY;
	//alert (iPointerX + '|' + iPointerY);
	oContextMenu.style.left = (iPointerX - 10) + 'px';
	oContextMenu.style.top = (iPointerY - 10) + 'px';
	
	//This function takes care of Net 6 and IE.
	return false;
}
*/





/*

var _replaceContext = false;		// replace the system context menu?
var _mouseOverContext = false;		// is the mouse over the context menu?
var _noContext = false;			// disable the context menu?
var _divContext = $('contextmenu');	// makes my life easier

InitContext();

function InitContext()
{
	_divContext.onmouseover = function() { _mouseOverContext = true; };
	_divContext.onmouseout = function() { _mouseOverContext = false; };
	
	$('aDisable').onclick = DisableContext;
	$('aEnable').onclick = EnableContext;
	
	document.body.onmousedown = ContextMouseDown;
	document.body.oncontextmenu = ContextShow;
}

// call from the onMouseDown event, passing the event if standards compliant
function ContextMouseDown(event)
{
	if (_noContext || _mouseOverContext)
		return;

	// IE is evil and doesn't pass the event object
	if (event == null)
		event = window.event;
		
	// we assume we have a standards compliant browser, but check if we have IE
	var target = event.target != null ? event.target : event.srcElement;

	// only show the context menu if the right mouse button is pressed
	//   and a hyperlink has been clicked (the code can be made more selective)
	if (event.button == 2 && target.tagName.toLowerCase() == 'a')
		_replaceContext = true;
	else if (!_mouseOverContext)
		_divContext.style.display = 'none';
}

function CloseContext()
{
	_mouseOverContext;
	_divContext.style.display = 'none';
}

// call from the onContextMenu event, passing the event
// if this function returns false, the browser's context menu will not show up
function ContextShow(event)
{
	if (_noContext || _mouseOverContext)
		return;

	// IE is evil and doesn't pass the event object
	if (event == null)
		event = window.event;
		
	// we assume we have a standards compliant browser, but check if we have IE
	var target = event.target != null ? event.target : event.srcElement;
	
	if (_replaceContext) 
	{
		$('aContextNav').href = target.href;
		$('aAddWebmark').href = 'http://luke.breuer.com/webmark/?addurl=' +
			encodeURIComponent(target.href) + '&title=' +
			encodeURIComponent(target.innerHTML);
		
		// document.body.scrollTop does not work in IE
		var scrollTop = document.body.scrollTop ? document.body.scrollTop : 
			document.documentElement.scrollTop;
		var scrollLeft = document.body.scrollLeft ? document.body.scrollLeft : 
			document.documentElement.scrollLeft;
			
		// hide the menu first to avoid an "up-then-over" visual effect
		_divContext.style.display = 'none';
		_divContext.style.left = event.clientX + scrollLeft + 'px';
		_divContext.style.top = event.clientY + scrollTop + 'px';
		_divContext.style.display = 'block';
		
		_replaceContext = false;
		
		return false;
	}
}

function DisableContext()
{
	_noContext = true;
	CloseContext();
	$('aEnable').style.display = '';
	
	return false;
}

function EnableContext()
{
	_noContext = false;
	_mouseOverContext = false; // this gets left enabled when "disable menus" is chosen
	$('aEnable').style.display = 'none';
	
	return false;
}

*/















/*
var display_url=0
						
var ie5=document.all&&document.getElementById
var ns6=document.getElementById&&!document.all
if (ie5||ns6)
//var menuobj=document.getElementById("ie5menu")

function showmenuie5(e){
//Find out how close the mouse is to the corner of the window
var rightedge=ie5? document.body.clientWidth-event.clientX : window.innerWidth-e.clientX
var bottomedge=ie5? document.body.clientHeight-event.clientY : window.innerHeight-e.clientY

//if the horizontal distance isn't enough to accomodate the width of the context menu
if (rightedge<menuobj.offsetWidth)
//move the horizontal position of the menu to the left by it's width
menuobj.style.left=ie5? document.body.scrollLeft+event.clientX-menuobj.offsetWidth : window.pageXOffset+e.clientX-menuobj.offsetWidth
else
//position the horizontal position of the menu where the mouse was clicked
menuobj.style.left=ie5? document.body.scrollLeft+event.clientX : window.pageXOffset+e.clientX

//same concept with the vertical position
if (bottomedge<menuobj.offsetHeight)
menuobj.style.top=ie5? document.body.scrollTop+event.clientY-menuobj.offsetHeight : window.pageYOffset+e.clientY-menuobj.offsetHeight
else
menuobj.style.top=ie5? document.body.scrollTop+event.clientY : window.pageYOffset+e.clientY

menuobj.style.visibility="visible"
return false
}

function hidemenuie5(e){
menuobj.style.visibility="hidden"
}

function highlightie5(e){
var firingobj=ie5? event.srcElement : e.target
if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){
if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode //up one node
firingobj.style.backgroundColor="highlight"
firingobj.style.color="white"
if (display_url==1)
window.status=event.srcElement.url
}
}

function lowlightie5(e){
var firingobj=ie5? event.srcElement : e.target
if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){
if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode //up one node
firingobj.style.backgroundColor=""
firingobj.style.color="black"
window.status=''
}
}

function jumptoie5(e){
var firingobj=ie5? event.srcElement : e.target
if (firingobj.className=="menuitems"||ns6&&firingobj.parentNode.className=="menuitems"){
if (ns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode
if (firingobj.getAttribute("target"))
window.open(firingobj.getAttribute("url"),firingobj.getAttribute("target"))
else
window.location=firingobj.getAttribute("url")
}
}

if (ie5||ns6){
menuobj.style.display=''
document.oncontextmenu=''
document.onclick=hidemenuie5
}
*/











