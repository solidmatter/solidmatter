
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver M�ller]
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
				
				var sUrl = '/-/menu/-/?close=' + sNodePath;
				var myAjaxCloser = new Ajax.Request(
					sUrl, 
					{
						method: 'get', 
						parameters: null,
						onFailure: function(response) {
							if (response.getHeader('Location') != null) {
								top.location.href = response.getHeader('Location');
							}
						}
					}
				);
				break;
				
			case 'open':
				nodeIcon.setAttribute('name', 'close');
				nodeIcon.setAttribute('src', '/theme/sb_system/icons/tree_wait.gif');
				
				var sUrl = '/-/menu/-/?open=' + sNodePath;
				var sID = 'entry' + sNodeID;
				var myAjaxOpener = new Ajax.Updater( 
					sID,
					sUrl, 
					{
						method: 'get', 
						parameters: null,
						onFailure: function(response) {
							if (response.getHeader('Location') != null) {
								top.location.href = response.getHeader('Location');
							}
						}
					}
				);
				break;
		
		}
	
	},
	
	//--------------------------------------------------------------------------
	// delete a node
	//
	deleteItem : function (sParentUUID, sChildUUID) {
		top.sbUtilities.popupModal('/-/structure/deleteChild/?parentnode='+ sParentUUID + '&childnode=' + sChildUUID, 500, 250, 'sbCommander.issueCommand("reloadTree", null);');
	},
	
	//--------------------------------------------------------------------------
	// pastes a node from the clipboard
	//
    paste : function (sParentUUID) {
		top.sbUtilities.execute('/-/structure/paste/?parentnode=' + sParentUUID);
		top.sbCommander.issueCommand('reloadTree', null);
	},
	
	//--------------------------------------------------------------------------
    // creates a hardlink from the node in clipboard
    //
    createLink : function (sParentUUID) {
		top.sbUtilities.execute('/-/structure/createLink/?parentnode=' + sParentUUID);
		top.sbCommander.issueCommand('reloadTree', null);
	},
	
	//--------------------------------------------------------------------------
    // creates a hardlink from the node in clipboard
    //
    setPrimary : function (sParentUUID, sChildUUID) {
		top.sbUtilities.execute('/-/structure/setPrimary/?parentnode=' + sParentUUID + '&childnode=' + sChildUUID);
		top.sbCommander.issueCommand('reloadTree', null);
	},
	
	//--------------------------------------------------------------------------
	// cuts a node from the tree to clipboard
	//
    cut : function (sParentUUID, sChildUUID) {
		top.sbUtilities.execute('/-/structure/cut/?parentnode=' + sParentUUID + '&childnode=' + sChildUUID);
	},
	
	//--------------------------------------------------------------------------
	// purge trashcan
	//
    purge : function (sTrashcanUUID) {
		top.sbUtilities.execute('/' + sTrashcanUUID + '/content/purge');
    },
	
	//--------------------------------------------------------------------------
	// add to favorites
	//
    addToFavorites : function (sUUID) {
		top.sbUtilities.execute('/-/structure/addToFavorites/?node=' + sUUID);
		top.sbCommander.issueCommand('reloadTree', null);
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
		var sUrl = '/-/contextmenu/-/?mode=create&subjectnode=' + oPath.node_id + '&parentnode=' + oPath.parent_id;
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


