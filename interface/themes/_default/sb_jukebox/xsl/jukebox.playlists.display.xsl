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
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$master/children[@mode='playlists']" />
		</div>
	</xsl:template>
	
	<xsl:template match="children">
		
		<table class="default" width="100%" summary="">
			<thead>
				<tr>
					<th colspan="2">
						<a style="float:right;" class="type create" href="/-/playlists/create">New Playlist</a>
						<span class="type playlist">Playlists</span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="sbnode">
					<xsl:for-each select="sbnode">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="80">
								<xsl:call-template name="render_stars" />
							</td>
							<td>
								<span style="float: right;">
									<a class="type play" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}">Play</a>
									<xsl:choose>
										<xsl:when test="@uuid = $currentPlaylist/@uuid">
											<a class="type activated" href="/{@uuid}/details/activate/">activate</a>
										</xsl:when>
										<xsl:otherwise>
											<a class="type activate" href="/{@uuid}/details/activate/">activate</a>
										</xsl:otherwise>
									</xsl:choose>
								</span>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a><br />
								
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