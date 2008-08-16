


var sbUtilities = {
	
	// issue command
	initProgress : function (sURL, sID) {
		
		
		
	},
	
	popupModal : function (sURL, iWidth, iHeight) {
		
		var iWindowLeft = (screen.width - iWidth) / 2;
		var iWindowTop = (screen.height - iHeight) / 2;
		
		if (false && window.showModalDialog) { // browser supports modal windows
			window.showModalDialog(
				sURL,
				"name",
				'dialogWidth:' + iWidth + 'px;dialogHeight:' + iHeight + 'px'
			);
		} else { // fall back
			window.open(
				sURL,
				'name',
				'height=' + iHeight + ',width=' + iWidth + ',top=' + iWindowTop + ',left=' + iWindowLeft + ',toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no ,modal=yes'
			);
		}
	},
	
	execute : function (sURL) {
		
		var myAjaxRequest = new Ajax.Request( 
			sURL, 
			{
				method: 'get', 
				parameters: null,
				asynchronous: false 
			}
		);
		
	}
		
}


/*function newWindow(a_str_windowURL, a_str_windowName, a_int_windowWidth, a_int_windowHeight, a_bool_scrollbars, a_bool_resizable, a_bool_menubar, a_bool_toolbar, a_bool_addressbar, a_bool_statusbar, a_bool_fullscreen) {
  var int_windowLeft = (screen.width - a_int_windowWidth) / 2;
  var int_windowTop = (screen.height - a_int_windowHeight) / 2;
  var str_windowProperties = 'height=' + a_int_windowHeight + ',width=' + a_int_windowWidth + ',top=' + int_windowTop + ',left=' + int_windowLeft + ',scrollbars=' + a_bool_scrollbars + ',resizable=' + a_bool_resizable + ',menubar=' + a_bool_menubar + ',toolbar=' + a_bool_toolbar + ',location=' + a_bool_addressbar + ',statusbar=' + a_bool_statusbar + ',fullscreen=' + a_bool_fullscreen + '';
  var obj_window = window.open(a_str_windowURL, a_str_windowName, str_windowProperties)
    if (parseInt(navigator.appVersion) >= 4) {
      obj_window.window.focus();
    }
}*/