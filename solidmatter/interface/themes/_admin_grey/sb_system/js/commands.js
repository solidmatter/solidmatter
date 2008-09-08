
//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* carries out system commands in client UI
*/
var sbCommander = {
	
	// issue command
	issueCommand : function (sCommand, oParams) {
	
		switch (sCommand) {
			
			case "reloadTree":
				parent.frames[0].window.location.reload();
				break;
			
			case "showProgress":
				//alert(oParams.init_url);
				var sInitURL = oParams.init_url;
				var sProgressURL = '/-/utilities/show_progress/user_uuid=' + oParams.user_uuid;
				sProgressURL = sProgressURL + '&subject_uuid=' + oParams.subject_uuid;
				sProgressURL = sProgressURL + '&uid=' + oParams.uid;
				//alert(sProgressURL);
				var ProgressWindow = window.open(sProgressURL ,'name', 'height=30,width=400');
				if (window.focus) {
					ProgressWindow.focus();
				}
				//alert('opened');
				var myAjaxStarter = new Ajax.Request( 
					sInitURL, 
					{
						method: 'get',
						onSuccess: function(transport) {
							ProgressWindow.close();
						}
					}
				);
				break;
		
		}
		
	}
	
}