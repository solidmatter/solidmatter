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
			<xsl:if test="$master/user_authorisations/authorisation[@name='write' and @grant_type='ALLOW']">
				<span style="float: right;">
					<a class="type remove" href="javascript:request_confirmation('/{$master/@uuid}/fix/delete');"><xsl:value-of select="$locale/sbSystem/actions/delete" /></a>
				</span>
			</xsl:if>
			<a class="type back" href="/{$master/@uuid}"><xsl:value-of select="$locale/sbSystem/actions/back" /></a>
		</div>
		<div class="content">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</xsl:template>
	
	<xsl:template match="sbnode">
		
		<div class="th">
			<span class="type info">Info</span>
		</div>
		<div class="odd" style="padding:10px;">
			<ul class="default">
				<li><xsl:call-template name="renderBasicInfo" /></li>
			</ul>
		</div>
		
		<xsl:if test="$content/actions">
			<xsl:call-template name="renderActions" />
		</xsl:if>
		
		<xsl:choose>
			<xsl:when test="@nodetype = 'sbJukebox:Artist'">
				<xsl:apply-templates select="$content/sbform" />
			</xsl:when>
			<xsl:when test="@nodetype = 'sbJukebox:Album'">
				<xsl:apply-templates select="$content/sbform" />
				<div class="th">
					<a href="javascript:toggle('')">Tracks are in wrong order</a>
				</div>
			</xsl:when>
			<xsl:when test="@nodetype = 'sbJukebox:Track'">
				<xsl:apply-templates select="$content/sbform" />
			</xsl:when>
		</xsl:choose>
		
	</xsl:template>
	
	<xsl:template name="renderBasicInfo">
		<xsl:choose>
			<xsl:when test="@nodetype='sbSystem:Comment'" >
				
			</xsl:when>
			<xsl:otherwise>
				<a href="/{@uuid}/fix">
					<xsl:call-template name="iconize" />
					<xsl:value-of select="@label" />
				</a>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="children[@mode='debug']/sbnode[@nodetype='sbSystem:Comment']">
			C:<xsl:value-of select="count(children[@mode='debug']/sbnode[@nodetype='sbSystem:Comment'])" />
		</xsl:if>
		<xsl:if test="all_votes/vote">
			V:<xsl:value-of select="count(all_votes/vote) - 1" />
		</xsl:if>
		<xsl:if test="existingRelations/relation">
			R:<xsl:value-of select="count(existingRelations/relation)" />
		</xsl:if>
		<xsl:if test="@info_lyrics != ''">
			L:Y
		</xsl:if>
		<xsl:if test="children[@mode='debug']">
			<ul class="default">
				<xsl:call-template name="colorize" />
				<xsl:for-each select="children[@mode='debug']/sbnode[@nodetype!='sbSystem:Comment'] | tracks/sbnode">
					<li>
						<xsl:call-template name="colorize" />
						<xsl:call-template name="renderBasicInfo" />
					</li>
				</xsl:for-each>
			</ul>
		</xsl:if>
		
	</xsl:template>

	<xsl:template name="renderActions">
		<table class="default" width="100%" style="font-size:80%;">
			<thead>
				<tr>
					<th colspan="7">
						<span class="type track">actions</span>
					</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="$content/actions/entry">
					<tr>
						<xsl:call-template name="colorize" />
						<xsl:attribute name="style">
							<xsl:if test="@ignore = '1'">color:#555;</xsl:if>
							<xsl:if test="@executed = 'TRUE' and @success = 'TRUE'">background-color:#050;</xsl:if>
						</xsl:attribute>
						<td width="30%">
							<span>
								<xsl:call-template name="iconize" />
								<xsl:value-of select="@label" disable-output-escaping="yes" />
							</span>
						</td>
						<td width="10%">
							<xsl:value-of select="@type" />
						</td>
						<td width="50%">
							<xsl:choose>
								<xsl:when test="@type = 'relabel'">
									"<xsl:value-of select="@old_label" disable-output-escaping="yes" />" -&gt; <br/>
									"<xsl:value-of select="@new_label" disable-output-escaping="yes" />"
								</xsl:when>
								<xsl:when test="@type = 'rename'">
									"<xsl:value-of select="@old_name" disable-output-escaping="yes" />" -&gt; <br/>
									"<xsl:value-of select="@new_name" disable-output-escaping="yes" />"
								</xsl:when>
								<xsl:when test="@type = 'change_property'">
									"<xsl:value-of select="@old_content" disable-output-escaping="yes" />" [<xsl:value-of select="@property" />] -&gt; <br/>
									"<xsl:value-of select="@new_content" disable-output-escaping="yes" />"
								</xsl:when>
								<xsl:when test="@type = 'rename_file'">
									"<xsl:value-of select="@old_filename" disable-output-escaping="yes" />" -&gt; <br/>
									"<xsl:value-of select="@new_filename" disable-output-escaping="yes" />"
								</xsl:when>
								<xsl:when test="@type = 'retag_mp3'">
									"<xsl:value-of select="@old_tag" disable-output-escaping="yes" />" [<xsl:value-of select="@tag" />] -&gt; <br/>
									"<xsl:value-of select="@new_tag" disable-output-escaping="yes" />"
								</xsl:when>
								<xsl:when test="@type = 'relocate'">
									"<xsl:value-of select="@old_parent" disable-output-escaping="yes" />" -&gt; <br/>
									"<xsl:value-of select="@new_parent" disable-output-escaping="yes" />"
								</xsl:when>
							</xsl:choose>
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
	
</xsl:stylesheet>