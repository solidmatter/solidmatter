<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn">

	<xsl:import href="global.default.xsl" />
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
		<link rel="stylesheet" href="{$stylesheets_css}/styles.css" type="text/css" media="all" />
<!-- 		<link rel="stylesheet" href="{$stylesheets_css}/styles_login.css" type="text/css" media="all" /> -->
		<link rel="stylesheet" href="{$stylesheets_css}/styles_setup.css" type="text/css" media="all" />
<!-- 		<link rel="stylesheet" href="{$stylesheets_css}/styles_forms.css" type="text/css" media="all" /> -->
	</head>
	<body>
		<xsl:apply-templates select="response/errors" />
		<div class="setup">
			<div class="logo"><h1><b>solid</b><i>Matter</i></h1><h2>Setup</h2></div>
			<xsl:apply-templates select="/response/content/sbform[@id='create']" />
			<ul>
			<xsl:for-each select="$content/repositories/repository">
				<xsl:call-template name="renderRepository" />
			</xsl:for-each>
			</ul>
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template name="renderRepository">
		<li>
			Repository: "<xsl:value-of select="@id" />" (Prefix: "<xsl:value-of select="@prefix" />")
			<xsl:for-each select="workspaces/workspace">
				<xsl:call-template name="renderWorkspace" />
			</xsl:for-each>
		</li>
	</xsl:template>

	<xsl:template name="renderWorkspace">
		<br />
		Workspace: "<xsl:value-of select="@id" />" (Prefix: "<xsl:value-of select="@prefix" />")
	
	</xsl:template>

</xsl:stylesheet>