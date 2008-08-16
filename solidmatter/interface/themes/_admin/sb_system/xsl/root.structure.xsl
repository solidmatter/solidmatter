<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">
	
	<xsl:import href="global.default.xsl" />
	<xsl:import href="global.views.xsl" />
	<xsl:import href="global.sbform.xsl" />
	
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
		<!--<xsl:call-template name="views" />-->
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="response/content/sbform[@id='create']" />
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template match="response/content">
		
	</xsl:template>

	<xsl:template name="sbform" match="response/content/sbform[@id='create']">
		<form class="default" action="{@action}" method="post" enctype="multipart/form-data" accept-charset="utf-8">
			<table class="default">
				<tr><th colspan="2"><label>
					<xsl:value-of select="dyn:evaluate(@label)" />: 
					<span class="type {$system/csstype}"><xsl:value-of select="$locale//nodetypes/type[@id=$content/@nodetype]" /></span>
					in
					<span class="type {$content/parent/sbnode/@csstype}"><xsl:value-of select="$content/parent/sbnode/@label" /></span>
				</label></th></tr>
				<xsl:if test="@errorlabel"><br/><xsl:value-of select="dyn:evaluate(@errorlabel)" /></xsl:if>
				<xsl:apply-templates select="*" mode="complete" />
			</table>
		</form>
	</xsl:template>	
	
</xsl:stylesheet>