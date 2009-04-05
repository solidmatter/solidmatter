<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	exclude-element-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn"
	xmlns:php="http://php.net/xsl"
	>

	<xsl:import href="global.default.xsl" />
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="yes"
		doctype-system="http://www.w3.org/TR/html4/loose.dtd" 
		doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN"
	/>
	
	<xsl:template match="/">
		<xsl:call-template name="layout" />
	</xsl:template>
	
	<xsl:template name="content">
		<div class="toolbar">
			
		</div>
		<div class="nav">
			
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
			
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="th">
			<span class="type recommendation"><xsl:value-of select="$locale/sbJukebox/actions/recommend"/>: <xsl:value-of select="@label" /></span>
		</div>

		<xsl:apply-templates select="//sbform[@id='recommendation']" />
		
	</xsl:template>

</xsl:stylesheet>