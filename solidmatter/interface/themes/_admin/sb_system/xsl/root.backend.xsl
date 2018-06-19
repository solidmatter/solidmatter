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
			<script type="text/javascript" src="{$scripts_js}/prototype/prototype.js"></script>
			<script type="text/javascript" src="{$scripts_js}/utilities.js"></script>
			<script type="text/javascript" src="{$scripts_js}/commands.js"></script>
		</head>
		<frameset cols="280, *" border="2" bordercolor="#8888AA">
			<frame src="{$relativeRoot}/-/menu" name="navigation" />
			<xsl:choose>
				<xsl:when test="$content/lastRecallableAction/lastRecallableAction">
					<frame src="{$relativeRoot}{$content/lastRecallableAction/lastRecallableAction}" name="main" />
				</xsl:when>
				<xsl:otherwise>
					<frame src="{$relativeRoot}/-/welcome" name="main" />
				</xsl:otherwise>
			</xsl:choose>
		</frameset>
		</html>
	</xsl:template>

</xsl:stylesheet>