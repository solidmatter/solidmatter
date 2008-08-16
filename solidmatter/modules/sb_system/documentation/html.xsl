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

	<xsl:template match="/doc">
		<html>
		<head>
			<title>solidMatter API Documentation</title>
			<link rel="stylesheet" href="styles.css" />
		</head>
		<body>
			<h1>solidMatter API Documentation</h1>
			<h2>Table of Contents</h2>
			<ul>
			<xsl:for-each select="chapter">
				<li><a href="#{@id}"><xsl:value-of select="@title" /></a>
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
			<xsl:apply-templates select="chapter" />
		</body>
		</html>
	</xsl:template>
	
	<xsl:template match="chapter">
		<h2 id="{@id}"><xsl:value-of select="position()" /> - <xsl:value-of select="@title" /></h2>
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="section">
		<h3 id="{@id}">X.<xsl:value-of select="position()" /> - <xsl:value-of select="@title" /></h3>
		<xsl:apply-templates />
	</xsl:template>
	
	<xsl:template match="subsection">
		<h4 id="{@id}">X.X.<xsl:value-of select="position()" /> - <xsl:value-of select="@title" /></h4>
		<xsl:apply-templates />
	</xsl:template>
	
	
	<xsl:template match="p">
		<xsl:copy-of select="." />
	</xsl:template>
	<xsl:template match="pre">
		<xsl:copy-of select="." />
	</xsl:template>
	
	
	<xsl:template match="ul">
		<xsl:copy-of select="." />
	</xsl:template>
	
</xsl:stylesheet>
