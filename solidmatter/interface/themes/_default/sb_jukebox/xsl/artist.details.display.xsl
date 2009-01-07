<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform php" 
	exclude-element-prefixes="html sbform" 
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
		<div class="nav">
			<xsl:call-template name="simplesearch">
				<xsl:with-param name="form" select="$content/sbform[@id='searchArtists']" />
			</xsl:call-template>
			<xsl:call-template name="render_alphanum">
				<xsl:with-param name="url" select="'/-/artists/-/show='"/>
			</xsl:call-template>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="albumcontainer">
			
			<h2>
				<span class="albumdetails" style="float:right;">
					<xsl:call-template name="render_buttons" />
					| <xsl:call-template name="render_stars">
						<xsl:with-param name="voting" select="1" />
					</xsl:call-template>
				</span>
				<span class="type artist"><xsl:value-of select="$content/sbnode/@label" /></span>
			</h2>
			
			<table class="default" width="100%">
				<thead>
					<tr>
						<th colspan="2"><span class="type album"><xsl:value-of select="$locale/sbJukebox/labels/albums" /></span></th>
					</tr>
				</thead>
				<tbody>
				<xsl:choose>
					<xsl:when test="children[@mode='albums']/sbnode">
						<xsl:for-each select="children[@mode='albums']/sbnode">
							<tr>
								<xsl:call-template name="colorize" />
								<td width="50">
									<a class="imglink" href="/{@uuid}/details">
										<img height="50" width="50" src="/{@uuid}/details/getCover/size=50" alt="cover" />
									</a>
								</td>
								<!--<td width="20">
									<a class="imglink" href="/{@uuid}/details/play/sessionid={$system/sessionid}">
										<img src="/theme/sb_jukebox/images/play.png" alt="play" />
									</a>
								</td>-->
								<td>
									<span style="float: right;">
										<xsl:call-template name="render_buttons" />
									</span>
									<a href="/{@uuid}"><xsl:value-of select="@label" /> [<xsl:value-of select="@info_published"/>]</a><br />
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
						<tr><td colspan="5"><xsl:value-of select="$locale/system/general/texts/no_subobjects" /></td></tr>
					</xsl:otherwise>
				</xsl:choose>
				
				</tbody>
			</table>
			
		</div>
			
		<xsl:call-template name="comments" />
		
	</xsl:template>

</xsl:stylesheet>