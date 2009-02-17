<?xml version='1.0' encoding='UTF-8'?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >

	<xsl:output
	method="html"
	encoding="UTF-8"
	doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
	omit-xml-declaration="yes"
	standalone="yes"
	indent="yes"
	media-type="text/html"/>
	
	<xsl:template match="/">
		<html>
		<head>
			<title>solidMatter Documentation</title>
		<link rel="stylesheet" href="/theme/sb_system/css/styles_documentation.css" type="text/css" media="all" />
		</head>
		<body>
			<h1>solidMatter Documentation</h1>
			<xsl:call-template name="toc" />
			<xsl:call-template name="content" />
		</body>
		</html>
	</xsl:template>
	
	<xsl:template name="toc">
		<h2>Table of Contents</h2>
		<ul>
		<xsl:for-each select="response/content/doc/chapter">
			<li><a href="#{@id}"><strong><xsl:value-of select="@title" /></strong></a>
			<ul>
			<xsl:for-each select="section">
				<li><a href="#{@id}"><xsl:value-of select="@title" /></a>
				<ul>
				<xsl:for-each select="subsection">
					<li><a href="#{@id}"><xsl:value-of select="@title" /></a></li>
				</xsl:for-each>
				</ul>
				</li>
			</xsl:for-each>
			</ul>
			</li>
		</xsl:for-each>
		</ul>
	</xsl:template>
	
	<xsl:template name="content">
		<xsl:for-each select="response/content/doc/chapter">
			<xsl:variable name="chapter" select="position()" />
			<h2 id="{@id}"><xsl:value-of select="position()" /><![CDATA[ ]]><xsl:value-of select="@title" /></h2>
			<xsl:apply-templates select="./*[name() != 'section']" />
			<xsl:for-each select="section">
				<xsl:variable name="section" select="position()" />
				<h3 id="{@id}"><xsl:value-of select="$chapter" />.<xsl:value-of select="position()" /><![CDATA[ ]]><xsl:value-of select="@title" /></h3>
				<xsl:apply-templates select="./*[name() != 'subsection']" />
				<xsl:for-each select="subsection">
					<h4 id="{@id}"><xsl:value-of select="$chapter" />.<xsl:value-of select="$section" />.<xsl:value-of select="position()" /><![CDATA[ ]]><xsl:value-of select="@title" /></h4>
					<xsl:apply-templates select="./*" />
				</xsl:for-each>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template match="action">
		<div class="action"><xsl:copy-of select="." /></div>
	</xsl:template>
	<xsl:template match="caution">
		<div class="caution"><xsl:copy-of select="." /></div>
	</xsl:template>
	<xsl:template match="info">
		<div class="info"><xsl:copy-of select="." /></div>
	</xsl:template>
	<xsl:template match="code">
		<pre class="code"><xsl:copy-of select="." /></pre>
	</xsl:template>
	
	<xsl:template match="p">
		<xsl:copy-of select="." />
	</xsl:template>
	<xsl:template match="ul">
		<xsl:copy-of select="." />
	</xsl:template>
	
</xsl:stylesheet>
