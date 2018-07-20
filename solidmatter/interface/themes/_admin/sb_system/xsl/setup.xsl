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
			<div class="layout">
				<div class="left">
					<xsl:apply-templates select="/response/content/sbform[@id='create_database']" />
					<xsl:apply-templates select="/response/content/sbform[@id='create_repository']" />
					<xsl:apply-templates select="/response/content/sbform[@id='create_workspace']" />
				</div>
				<div class="sep" />
				<div class="right">
					<table class="default">
						<thead>
							<tr><th colspan="4">Databases</th></tr>
							<tr class="th2">
								<th>ID</th>
								<th>Host:Port</th>
								<th>Schema</th>
								<th></th>
							</tr>
						</thead>
						<xsl:for-each select="$content/config/databases/*">
							<xsl:call-template name="renderDatabase" />
						</xsl:for-each>
						<tfoot><tr><td colspan="4"></td></tr></tfoot>
					</table>
					<table class="default">
						<thead>
							<tr><th colspan="4">Repositories &amp; Workspaces</th></tr>
							<tr class="th2">
								<th>DB</th>
								<th>ID</th>
								<th>Prefix</th>
								<th></th>
							</tr>
						</thead>
						<xsl:for-each select="$content/config/repositories/*">
							<xsl:call-template name="renderRepository" />
						</xsl:for-each>
						<tfoot><tr><td colspan="4"></td></tr></tfoot>
					</table>
				</div>
			</div>
			
			<xsl:apply-templates select="/response/content/sbform[@id='create']" />
			
			<xsl:apply-templates select="/response/errors" />
			
		</div>
	</body>
	</html>
	</xsl:template>
	
	<xsl:template name="renderDatabase">
		<tr>
			<td><xsl:value-of select="name()" /></td>
			<td><xsl:value-of select="@schema" /></td>
			<td><xsl:value-of select="@host" />:<xsl:value-of select="@port" /></td>
			<td>REMOVE</td>
		</tr>
	</xsl:template>
	
	<xsl:template name="renderRepository">
		<tr>
			<td><xsl:value-of select="@db" /></td>
			<td><xsl:value-of select="name()" /></td>
			<td><xsl:value-of select="@prefix" /></td>
			<td><!-- <a href="/setup?action=init_repo&amp;id={name()}">INIT</a>  -->DELETE</td>			
		</tr>
		<xsl:for-each select="workspace">
			<xsl:call-template name="renderWorkspace" />
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="renderWorkspace">
		<tr>
			<td></td>
			<td><xsl:value-of select="name(..)" />:<xsl:value-of select="@id" /></td>
			<td><xsl:value-of select="@prefix" /></td>
			<td>REMOVE</td>
		</tr>
	</xsl:template>

</xsl:stylesheet>