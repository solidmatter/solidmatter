/* Example: <a href="javascript:popup_simple('http://foo.bar', 300, 200)">Open the Popup Window</a> */

function popup_simple(sURL, iWidth, iHeight) {
	dNow = new Date();
	iID = dNow.getTime();
	eval("page" + iID + " = window.open(sURL, '" + iID + "', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=" + iWidth + ",height=" + iHeight + ",left = 200,top = 200');");
}