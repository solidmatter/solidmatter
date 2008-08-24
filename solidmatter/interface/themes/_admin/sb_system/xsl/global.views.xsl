<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template name="views" priority="100">
		<xsl:choose>
			<xsl:when test="boolean('true')">
				<!--<div style="position:fixed;">
					<xsl:apply-templates select="/response/content/sbnode/views[1]" />
				</div>
				<div style="height:48px;" />-->
				<xsl:apply-templates select="/response/content/sbnode/views[1]" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="/response/content/sbnode/views[1]" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="/response/content/sbnode/views[1]">
		<div id="modalbackground"></div>
		<div class="path">
			<xsl:for-each select="../ancestors/sbnode">
				<xsl:sort order="descending" />
				<xsl:if test="@nodetype != 'sb_system:root'">
					<a href="/{@uuid}"><span class="type {@csstype}"><xsl:value-of select="@label" /></span></a>
					<xsl:if test="position() != last()+1">
						/
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
			<a href="/{../@uuid}"><span class="type {../@csstype}"><xsl:value-of select="../@label" /></span></a>
		</div>
		<div class="views">
			<xsl:apply-templates select="/response/metadata/stopwatch" />
			<ul><xsl:for-each select="view">
				<li><a href="/{$subjectid}/{@name}">
					<xsl:if test="@name=$content/@view">
						<xsl:attribute name="class">active</xsl:attribute>
					</xsl:if>
					<span>
					<xsl:variable name="view" select="@name" />
					<!--<xsl:value-of select="$view" />-->
					<!--<xsl:value-of select="//locale[@lang=/response/metadata/language]/system/general/views/*[name()=$view]" />-->
					<!--<xsl:value-of select="//locale[@lang=$lang]/system/general/views/*[name()=$view]" />-->
					<xsl:value-of select="$locale/*/views/view[@id=$view]" />
					</span>
				</a></li>
			</xsl:for-each>
			<li><a href="/{$content/@uuid}/{$content/@view}/{$content/@action}/debug" target="_blank"><span>XML</span></a></li>
			</ul></div>
		<xsl:apply-templates select="/response/content/errors" />
	</xsl:template>
	
	<xsl:template match="/response/metadata/stopwatch">
		<span style="color:#336; position:absolute; top:0; right:0;">
			<!--<xsl:attribute name="title">
				LOAD:<xsl:value-of select="number(substring(load, 0, 6)) * 1000" />ms | 
				PHP:<xsl:value-of select="number(substring(php, 0, 6)) * 1000" />ms |
				PDO:<xsl:value-of select="number(substring(pdo, 0, 6)) * 1000" />ms
			</xsl:attribute>
			<xsl:value-of select="number(substring(execution_time, 0, 6)) * 1000" />ms-->
			<xsl:attribute name="title">
				LOAD:<xsl:value-of select="load" />ms | 
				PHP:<xsl:value-of select="php" />ms |
				PDO:<xsl:value-of select="pdo" />ms
			</xsl:attribute>
			<xsl:value-of select="execution_time" />ms
		</span>
	</xsl:template>
	
</xsl:stylesheet>