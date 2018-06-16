<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

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
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/albums/-/?show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="/response/errors" />
			<xsl:choose>
				<xsl:when test="$content/@show">
					<xsl:call-template name="renderAlbums">
						<xsl:with-param name="label" select="concat($locale/sbJukebox/labels/albums_beginning_with, ' ', $content/@show)" />
					</xsl:call-template>
				</xsl:when>
				<xsl:when test="$content/@action = 'search'">
					<xsl:call-template name="renderAlbums">
						<xsl:with-param name="label" select="$locale/sbSystem/labels/search/results" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="renderAlbums">
						<xsl:with-param name="label" select="$locale/sbJukebox/labels/random_albums" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	<xsl:template name="renderAlbums">
		<xsl:param name="label" />
		<table class="default" width="100%" summary="">
			<thead>
				<tr>
					<th colspan="3"><span class="type album"><xsl:value-of select="$label" /></span></th>
				</tr>
			</thead>
			<tbody>
			<xsl:call-template name="render_albumlist">
				<xsl:with-param select="$content/albums" name="albumlist" />
			</xsl:call-template>
			</tbody>
		</table>
	</xsl:template>

</xsl:stylesheet>