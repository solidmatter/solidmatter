<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	exclude-element-prefixes="html sbform" 
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
		<div class="nav">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchAlbums']" />
			</xsl:call-template>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/albums/-/show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="/response/errors" />
			<xsl:choose>
				<xsl:when test="$content/@show">
					<xsl:call-template name="renderAlbums">
						<xsl:with-param name="data" select="$content/random/resultset/row" />
						<xsl:with-param name="label" select="concat($locale/sbJukebox/labels/albums_beginning_with, ' ', $content/@show)" />
					</xsl:call-template>
				</xsl:when>
				<xsl:when test="$content/@action = 'search'">
					<xsl:call-template name="renderAlbums">
						<xsl:with-param name="data" select="$content/searchresult/resultset/row" />
						<xsl:with-param name="label" select="$locale/system/general/labels/search/results" />
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="renderAlbums">
						<xsl:with-param name="data" select="$content/random/resultset/row" />
						<xsl:with-param name="label" select="$locale/sbJukebox/labels/random_albums" />
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:template>
	
	<xsl:template name="renderAlbums">
		<xsl:param name="data" />
		<xsl:param name="label" />
		<table class="default" width="100%" summary="">
			<thead>
				<tr>
					<th colspan="2"><span class="type album"><xsl:value-of select="$label" /></span></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$data">
					<xsl:for-each select="$data">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="60">
								<a class="imglink" href="/{@uuid}">
									<img height="50" width="50" src="/{@uuid}/details/getCover/size=50" alt="cover" />
								</a>
							</td>
							<td>
								<span style="float: right;">
									<xsl:call-template name="render_buttons" />
								</span>
								<a href="/{@uuid}">
									<xsl:value-of select="@label" />
									<xsl:if test="@n_published">
										[<xsl:value-of select="@n_published" />]
									</xsl:if>
								</a><br />
								<xsl:call-template name="render_stars" />
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
	</xsl:template>

</xsl:stylesheet>