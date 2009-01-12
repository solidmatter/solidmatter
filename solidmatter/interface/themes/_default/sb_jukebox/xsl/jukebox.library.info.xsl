<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	exclude-element-prefixes="sbform xmlns" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic"
	extension-element-prefixes="dyn">

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
				<xsl:with-param name="form" select="$content/sbform[@id='searchJukebox']" />
			</xsl:call-template>
			
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/latestAlbums" />
			<xsl:apply-templates select="$content/nowPlaying" />
			<xsl:apply-templates select="$content/recommendations" />
		</div>
	</xsl:template>
	
	<xsl:template match="latestAlbums">
		
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="2">
						<span style="float:right"><a class="type coverwall" href="/-/-/displayCoverWall">Cover Wall</a></span>
						<span class="type album"><xsl:value-of select="$locale/sbJukebox/labels/latest_albums" /></span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="resultset/row">
					<tr class="odd">
						<td style="text-align: center;">
							<xsl:for-each select="resultset/row">
								<div class="albumcover">
									<a class="imglink" href="/{@uuid}" style="position:relative;">
										<img height="100" width="100" src="/{@uuid}/details/getCover/size=100" alt="{@label}" title="{@label}" onMouseOver="add_playbutton('{@uuid}', this)" onMouseOut="remove_playbutton(this)" />
									</a><br />
									<xsl:call-template name="render_stars" />
								</div>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
		</table>
		
	</xsl:template>
	
	<xsl:template match="nowPlaying">
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="3">
						<span class="type track"><xsl:value-of select="$locale/sbJukebox/labels/now_playing" /></span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="resultset/row">
					<xsl:for-each select="resultset/row">
						<tr>
							<xsl:call-template name="colorize" />
							<!--<td width="80">
								<xsl:call-template name="render_stars">
									
								</xsl:call-template>
							</td>-->
							<td width="100">
								<xsl:value-of select="@username" />
							</td>
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
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
	
	<xsl:template match="recommendations">
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="4">
						<span class="type recommendations"><xsl:value-of select="$locale/sbJukebox/labels/recommendations" /></span>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="entry">
					<xsl:for-each select="entry">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="100">
								<xsl:value-of select="@username" />
							</td>
							<td>
								<a href="/{@item_uuid}"><xsl:value-of select="@label" /></a>
							</td>
							<td>
								<span style="white-space:nowrap;"><xsl:value-of select="@comment" /></span>
							</td>
							<td width="1%">
								<a class="type play" href="/{@item_uuid}/-/getM3U/playlist.m3u?sid={$sessionid}">play</a>
								<a class="type remove" href="/{@uuid}/actions/remove">remove</a>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="4"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
		</table>
		
	</xsl:template>
	
</xsl:stylesheet>