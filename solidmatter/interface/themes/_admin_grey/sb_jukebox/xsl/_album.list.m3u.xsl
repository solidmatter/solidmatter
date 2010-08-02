<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0">

	<xsl:import href="global.default.xsl" />

	<xsl:output 
		method="text"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
		<xsl:apply-templates select="response/errors" />
		<xsl:apply-templates select="response/content/list/sbnode" />
	</xsl:template>
	
	<xsl:template match="response/content/list/sbnode">#EXTM3U<xsl:for-each select="children[@mode='play']/sbnode">
#EXTINF:0,<xsl:value-of select="@label" />
http://test.backend/<xsl:value-of select="@uuid" />/song/play/sessionid=<xsl:value-of select="$system/sessionid" />
</xsl:for-each></xsl:template>

</xsl:stylesheet>