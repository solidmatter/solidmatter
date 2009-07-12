<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	exclude-element-prefixes="sbform xmlns" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic"
	xmlns:php="http://php.net/xsl"
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
		<div class="toolbar">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchJukebox']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			<a class="type coverwall" href="/-/-/displayCoverWall">Cover Wall</a>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/latestAlbums" />
			<!--<xsl:if test="$content/nowPlaying/resultset/row">
				<xsl:apply-templates select="$content/nowPlaying" />
			</xsl:if>
			<xsl:if test="$content/recommendations/entry">
				<xsl:apply-templates select="$content/recommendations" />
			</xsl:if>-->
			<xsl:apply-templates select="$content/nowPlaying" />
			<xsl:apply-templates select="$content/recommendations" />
			<xsl:apply-templates select="$content/latestComments" />
		</div>
	</xsl:template>
	
	<xsl:template match="latestAlbums">
		
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="2">
						<span style="float:right;">
							<xsl:choose>
								<xsl:when test="$content/@expand = 'latestAlbums'">
									<a class="type collapse" href="/"><xsl:value-of select="$locale/sbJukebox/actions/collapse" /></a>
								</xsl:when>
								<xsl:otherwise>
									<!-- TODO: only display if there are enough comments -->
									<xsl:if test="1 or count($nodes) > 9">
										<a class="type expand" href="/-/-/-/?expand=latestAlbums"><xsl:value-of select="$locale/sbJukebox/actions/expand" /></a>
									</xsl:if>
								</xsl:otherwise>
							</xsl:choose>
						</span>
						<span class="type album"><xsl:value-of select="$locale/sbJukebox/labels/latest_albums" /></span>
						<a class="type rss" href="/rss/latestalbums/{$jukebox/usertoken}" target="_blank" style="margin-left: 20px;">RSS</a> 
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
										<img height="104" width="112" src="/theme/sb_jukebox/images/case_100.png" alt="{@label}" title="{@label}" style="background: url('/{@uuid}/details/getCover/?size=100') 11px 2px; margin-bottom: 1px;" />
										<!--<img height="100" width="100" src="/{@uuid}/details/getCover/?size=100" alt="{@label}" title="{@label}" onMouseOver="add_playbutton('{@uuid}', this)" onMouseOut="remove_playbutton(this)" />-->
									</a><br />
									<xsl:call-template name="render_stars" />
								</div>
							</xsl:for-each>
						</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<!--<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>-->
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
						<span class="type ear"><xsl:value-of select="$locale/sbJukebox/labels/now_playing" /></span>
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
							<td width="1%" style="white-space:nowrap;">
								<xsl:call-template name="render_buttons" />
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<!--<tr><td colspan="5"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>-->
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
						<span class="type recommendation"><xsl:value-of select="$locale/sbJukebox/labels/recommendations" /></span>
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
								<xsl:call-template name="break">
									<xsl:with-param name="text" select="@comment" />
								</xsl:call-template>
							</td>
							<td width="1%" style="white-space:nowrap;">
								<a class="type play icononly" href="/{@item_uuid}/-/getM3U/playlist.m3u?sid={$sessionid}" title="{$locale/sbJukebox/actions/play}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
								<a class="type remove icononly" href="/{@uuid}/actions/remove" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<!--<tr><td colspan="4"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>-->
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
		</table>
		
	</xsl:template>
	
	<xsl:template match="latestComments">
		<table class="default" width="100%" summary="CHANGEME">
			<thead>
				<tr>
					<th colspan="3">
						<span style="float:right;">
							<xsl:choose>
								<xsl:when test="$content/@expand = 'latestComments'">
									<a class="type collapse" href="/"><xsl:value-of select="$locale/sbJukebox/actions/collapse" /></a>
								</xsl:when>
								<xsl:otherwise>
									<!-- TODO: only display if there are enough comments -->
									<xsl:if test="1 or count($nodes) > 9">
										<a class="type expand" href="/-/-/-/?expand=latestComments"><xsl:value-of select="$locale/sbJukebox/actions/expand" /></a>
									</xsl:if>
								</xsl:otherwise>
							</xsl:choose>
						</span>
						<span class="type comment"><xsl:value-of select="$locale/sbSystem/labels/comments" /></span>
						<a class="type rss" href="/rss/latestcomments/{$jukebox/usertoken}" target="_blank" style="margin-left: 20px;">RSS</a>
					</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="resultset/row">
					<xsl:for-each select="resultset/row">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:value-of select="@username" />
							</td>
							<td width="25%">
								<xsl:value-of select="php:functionString('datetime_mysql2local', string(@created), string($locale/sbSystem/formats/datetime_short))" />
							</td>
							<td>
								<a href="/{@item_uuid}#comments"><xsl:value-of select="@item_label" /></a>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<!--<tr><td colspan="4"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>-->
				</xsl:otherwise>
			</xsl:choose>
			
			</tbody>
		</table>
		
	</xsl:template>
	
</xsl:stylesheet>