<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" 
	extension-element-prefixes="dyn"
	xmlns:php="http://php.net/xsl"
	>

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
				<xsl:with-param name="form" select="$content/sbform[@id='searchArtists']" />
			</xsl:call-template>
		</div>
		<div class="nav">
			<span style="float: right;">
				<xsl:if test="$auth[@name='write'] and $jukebox/adminmode = '1'">
					<a class="type maintenance" href="/{$master/@uuid}/fix" title="{$locale/sbJukebox/actions/XXXXXXXXX}">Fix</a>
				</xsl:if>
			</span>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/artists/-/?show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="th" id="highlight_{@uuid}">
			<span class="actions" style="float:right;">
				<xsl:call-template name="addtag">
					<xsl:with-param name="form" select="$content/sbform[@id='addTag']" />
				</xsl:call-template>
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_buttons" />
				<span style="margin-left: 15px;"></span>
				<xsl:call-template name="render_stars" />
				<span style="margin-left: 5px;"></span>
				<xsl:call-template name="render_votebuttons" />
			</span>
			<span class="type artist"><xsl:value-of select="$content/sbnode/@label" /></span>
		</div>
		<xsl:call-template name="render_tags" />
		
		<table class="default" width="100%">
			<thead>
				<tr>
					<th colspan="3"><span class="type album"><xsl:value-of select="$locale/sbJukebox/labels/albums" /></span></th>
				</tr>
			</thead>
			<tbody>
			<xsl:call-template name="render_albumlist">
				<xsl:with-param select="children[@mode='albums']" name="albumlist" />
			</xsl:call-template>
			</tbody>
		</table>
	
		<table class="default" width="100%">
			<thead>
				<tr>
					<th><span class="type track"><xsl:value-of select="$locale/sbJukebox/labels/tracks_on_other_albums" /></span></th>
					<th><span class="type album"><xsl:value-of select="$locale/sbJukebox/labels/source_album" /></span></th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/tracks/resultset/row">
					<xsl:for-each select="$content/tracks/resultset/row">
						<tr>
							<xsl:call-template name="colorize" />
							<!--<td width="80">
								<xsl:call-template name="render_stars">
									
								</xsl:call-template>
							</td>-->
							<td>
								<a href="/{@uuid}"><xsl:value-of select="@label" /></a>
								<!--<a href="/{@uuid}/song/play/sessionid={$system/sessionid}" class="type sb_action_play"> </a>-->
							</td>
							<td>
								<a href="/{@albumuuid}"><xsl:value-of select="@albumlabel" /></a>
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
		
		<xsl:choose>
			<xsl:when test="$content/sk_concerts">
				<div class="th_songkick_outer" title="{$locale/sbJukebox/labels/artist_is_linked_to_songkick}"><div class="th_songkick_inner" id="songkick">
					<span class="type concerts">
						<xsl:value-of select="$locale/sbJukebox/labels/concerts" />
					</span>
				</div></div>
			</xsl:when>
			<xsl:when test="$content/sk_artists">
				<div class="th">
					<span class="type concerts">
						<xsl:value-of select="$locale/sbJukebox/labels/concerts" />
						 (<xsl:value-of select="$locale/sbJukebox/labels/please_choose_artist" />)
					</span>
				</div>
			</xsl:when>
			<xsl:when test="$content/sk_exception">
				<div class="th">
					<span class="type concerts">
						<xsl:value-of select="$locale/sbJukebox/labels/concerts" />
						 (ERROR!)
					</span>
				</div>
			</xsl:when>
			<xsl:otherwise>
				
			</xsl:otherwise>
		</xsl:choose>
		
		<xsl:choose>
			<xsl:when test="$content/sk_artists">
				<table class="default" width="100%">
					<tbody>
					<xsl:for-each select="$content/sk_artists/resultsPage/results/artist">
					<xsl:sort select="@displayName" />
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:choose>
									<xsl:when test="@id">
										<span style="float:right;"><xsl:value-of select="@onTourUntil" /></span>
										<!-- <a href="http://api.songkick.com/api/3.0/artists/{@id}/calendar.xml?apikey=Y3UXq8R3WcUUbeWZ"><xsl:value-of select="@displayName" /></a> -->
										<!-- <a href="http://www.songkick.com/artists/{@id}"><xsl:value-of select="@displayName" /></a> -->
										<a href="/{$master/@uuid}/concerts/linkToSongkick?songkick_id={@id}"><xsl:value-of select="@displayName" /></a>
									</xsl:when>
									<xsl:otherwise>
										<!-- <a href="/{$master/@uuid}/concerts/linkToSongkick?songkick_id={@id}"><xsl:value-of select="@displayName" /></a> -->
										<xsl:value-of select="@displayName" />
									</xsl:otherwise>
								</xsl:choose>
							</td>
						</tr>
					</xsl:for-each>
					</tbody>
				</table>
			</xsl:when>
			<xsl:when test="$content/sk_concerts">
				<table class="default" width="100%">
					<tbody>
					<xsl:for-each select="$content/sk_concerts/resultsPage/results/event">
					<xsl:sort select="start/@date" />
					<xsl:sort select="@displayName" />
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<!-- <xsl:value-of select="@displayName" /> -->
								<xsl:choose>
									<xsl:when test="@type = 'Concert'">
										<a href="{@uri}" title="{@displayName}" target="_blank"><xsl:value-of select="venue/@displayName" target="_blank" /></a>
									</xsl:when>
									<xsl:when test="@type = 'Festival'">
										<a href="{@uri}" title="{@displayName}" target="_blank"><xsl:value-of select="@displayName" target="_blank" /> (<xsl:value-of select="venue/@displayName" target="_blank" />)</a>
									</xsl:when>
									<xsl:otherwise>
										??? UNKNOWN TYPE ???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:value-of select="php:functionString('datetime_convert', string(start/@date), string('Y-m-d'), string($locale/sbSystem/date/middle))" />
								<xsl:if test="end/@date and start/@date != end/@date">
									 - <xsl:value-of select="php:functionString('datetime_convert', string(end/@date), string('Y-m-d'), string($locale/sbSystem/date/middle))" />							
								</xsl:if>
							</td>
							<td>
								<xsl:value-of select="venue/metroArea/@displayName" />, <xsl:value-of select="venue/metroArea/country/@displayName" />
							</td>
							<td>
								<xsl:value-of select="@onTourUntil" />
							</td>
						</tr>
					</xsl:for-each>
					</tbody>
				</table>
			</xsl:when>
			<xsl:otherwise>
				<!--<tr><td><xsl:value-of select="$locale/sbSystem/texts/no_relations" /></td></tr>-->
			</xsl:otherwise>
		</xsl:choose>
			
		
		<xsl:call-template name="render_relationlist" />
		
			
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>