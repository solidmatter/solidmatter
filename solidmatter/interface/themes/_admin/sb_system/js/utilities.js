
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* provides general utility functions
*/
var sbUtilities = {
	
	wModalPopup : null,
	
	//--------------------------------------------------------------------------
	// issue command
	initProgress : function (sURL, sID) {
		
		
		
	},
	
	//--------------------------------------------------------------------------
	// open modal window
	//
	popupModal : function (sURL, iWidth, iHeight, sCallback) {
		
		this.modal(true);
		
		var iWindowLeft = (screen.width - iWidth) / 2;
		var iWindowTop = (screen.height - iHeight) / 2;
		
		var wModalPopup = null;
		
		if (false && window.showModalDialog) { // browser supports modal windows
			window.showModalDialog(
				sURL,
				"name",
				'dialogWidth:' + iWidth + 'px;dialogHeight:' + iHeight + 'px'
			);
		} else { // fall back
			this.wModalPopup = window.open (
				sURL,
				'name',
				'height=' + iHeight + ',width=' + iWidth + ',top=' + iWindowTop + ',left=' + iWindowLeft + ',toolbar=no,directories=no,location=no;status=no,menubar=no,scrollbars=no,resizable=no ,modal=yes'
			);
			if (this.wModalPopup.opener == null) {
  				this.wModalPopup.opener = window;
			}
			if (sCallback != null) {
				window.onModalPopupClosed = sCallback;
			}
		}
		
	},
	
	//--------------------------------------------------------------------------
	// close modal window
	//
	closeModal : function (bConfirmed) {
		this.wModalPopup.close();
		this.modal(false);
		if (bConfirmed && window.onModalPopupClosed != null) {
			eval(window.onModalPopupClosed);
		}
	},
	
	//--------------------------------------------------------------------------
	// grey out or open the menu and work frames
	//
	modal : function (bState) {
		
		var oWindow1 = top.frames[0];
		var oWindow2 = top.frames[1];
		var eDiv1 = top.frames[0].document.getElementById('modalbackground');
		var eDiv2 = top.frames[1].document.getElementById('modalbackground');
		
		this.cover_page(eDiv1, oWindow1);
		this.cover_page(eDiv2, oWindow2);
		
		var sStyle = "none";
		if (bState) {
			sStyle = "block";
		}
		
		eDiv1.style.display = sStyle;
		eDiv2.style.display = sStyle;
		
	},
	
	//--------------------------------------------------------------------------
	// match size and position of an element to the window
	//
	match_size : function (eLayer, oWindow) {
		
		eLayer.style.width = oWindow.innerWidth;
		eLayer.style.height = oWindow.innerHeight;
		eLayer.style.top = oWindow.pageYOffset;
		eLayer.style.left = oWindow.pageXOffset;
	
	},
	
	//--------------------------------------------------------------------------
	// cover the whole page
	//
	cover_page : function (eLayer, oWindow) {
		
		eLayer.style.width = oWindow.document.body.scrollWidth;
		eLayer.style.height = oWindow.document.body.scrollHeight;
		eLayer.style.top = 0;
		eLayer.style.left = 0;
		
	},
	
	//--------------------------------------------------------------------------
	// just open an url in background
	//
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