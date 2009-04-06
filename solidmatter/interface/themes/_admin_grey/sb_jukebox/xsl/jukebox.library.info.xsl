<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:import href="../../sb_system/xsl/global.views.xsl" />
	<xsl:import href="../../sb_system/xsl/global.default.xsl" />
	<xsl:import href="../../../_global/xsl/sbform.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
	<html>
	<head>
		<xsl:apply-templates select="/response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbform[@id='search_jukebox']" />
			Number of Albums: <xsl:value-of select="$content/resultset/row/@n_numalbums" /><br/>
			Number of Artists: <xsl:value-of select="$content/resultset/row/@n_numartists" /><br/>
			Number of Titles: <xsl:value-of select="$content/resultset/row/@n_numtitles" /><br/>
		</div>
	</body>
	</html>
	</xsl:template>

</xsl:stylesheet>