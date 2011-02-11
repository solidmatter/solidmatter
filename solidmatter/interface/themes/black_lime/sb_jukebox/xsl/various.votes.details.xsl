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
		<div class="toolbar">
			
		</div>
		<div class="nav">
			<span style="float: right;">
				<xsl:if test="($auth[@name='write'] and $jukebox/adminmode = '1') and $master/votes/vote">
					<a class="type remove" href="/{$master/@uuid}/votes/removeAllVotes"><xsl:value-of select="$locale/sbJukebox/actions/unset_votes" /></a>
				</xsl:if>
			</span>
			<a class="type back" href="/{$master/@uuid}">back</a>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="th">
			<span>
				<xsl:attribute name="class">
				<xsl:choose>
					<xsl:when test="$master/@nodetype = 'sbJukebox:Artist'">
						type artist
					</xsl:when>
					<xsl:when test="$master/@nodetype = 'sbJukebox:Album'">
						type album
					</xsl:when>
					<xsl:when test="$master/@nodetype = 'sbJukebox:Track'">
						type track
					</xsl:when>
					<xsl:when test="$master/@nodetype = 'sbJukebox:Playlist'">
						type playlist
					</xsl:when>
				</xsl:choose>
				</xsl:attribute>
				<xsl:value-of select="@label" />
			</span>
		</div>
		
		<table class="default" width="100%">
			<tbody>
				<xsl:choose>
				<xsl:when test="$master/votes/vote">
					<xsl:for-each select="$master/votes/vote">
						<xsl:sort select="@user_label" />
						<xsl:if test="@user_uuid != '00000000000000000000000000000000'">
						<tr>
							<xsl:call-template name="colorize" />
							<td width="{$starcolwidth}">
								<span id="stars_{@user_uuid}" class="stars"><script type="text/javascript">render_stars('<xsl:value-of select="@vote" />', '<xsl:value-of select="@user_uuid" />', false)</script></span>
							</td>
							<td>
								<xsl:value-of select="@user_label" />
							</td>
							<td width="10">
							<xsl:if test="@user_uuid = $userid or ($auth[@name='write'] and $jukebox/adminmode = '1')">
								<a class="type remove icononly" href="/{$master/@uuid}/votes/removeVote/?user_uuid={@user_uuid}" title="{$locale/sbJukebox/actions/remove}"><img src="/theme/sb_jukebox/icons/blank.gif" alt="Dummy" /></a>
							</xsl:if>
							</td>
						</tr>
						</xsl:if>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<!--<tr><td><xsl:value-of select="$locale/sbSystem/texts/no_relations" /></td></tr>-->
				</xsl:otherwise>
				</xsl:choose>
			</tbody>
		</table>
		
	</xsl:template>

</xsl:stylesheet>