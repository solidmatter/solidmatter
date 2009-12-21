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
		<div class="toolbar">
			
		</div>
		<div class="nav">
			
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/playlists" />
		</div>
	</xsl:template>
	
	<xsl:template match="playlists">
		
		<table class="default" width="100%" summary="">
			<thead>
				<tr>
					<th colspan="5">
						<a style="float:right;" class="type create" href="/-/playlists/create">
							<xsl:value-of select="$locale/sbJukebox/labels/new_playlist"/>
						</a>
						<span class="type playlist"><xsl:value-of select="$locale/sbJukebox/menu/playlists"/></span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="row">
					<xsl:for-each select="row">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="80">
								<xsl:call-template name="render_stars" />
							</td>
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a><br />
							</td>
							<td>
								<xsl:value-of select="@numtracks" />
							</td>
							<td>
								<xsl:value-of select="@user" />
							</td>
							<td width="10">
								<a class="type play icononly" href="/{@uuid}/details/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
		</table>
		
	</xsl:template>

</xsl:stylesheet>