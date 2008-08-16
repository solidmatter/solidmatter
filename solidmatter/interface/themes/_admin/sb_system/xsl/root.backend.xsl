<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform">
	
	<xsl:import href="global.default.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
		<html>
		<head>
			<title>solidMatter Backend</title>
		</head>
		<frameset cols="300, *" border="7">
			<frame src="/-/menu" name="navigation" />
			<frame src="/-/welcome" name="main" />
		</frameset>
		</html>
	</xsl:template>

</xsl:stylesheet>