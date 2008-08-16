<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:output 
		method="xml"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:template match="/">
		<xsl:choose>
		<xsl:when test="/response/content/errors">
			<error />
		</xsl:when>
		<xsl:otherwise>
			<!--<xsl:copy-of select="/response/content/requestnode/sbnode/children" />-->
			<xsl:apply-templates select="/response/content/requestnode/sbnode" />
		</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="/response/content/requestnode/sbnode">
		<xsl:call-template name="menuentry" />
	</xsl:template>
	
	<xsl:template name="menuentry">
		<xsl:for-each select="*">
		<li>
			<xsl:choose>
			<xsl:when test="@subnodes = 0">
				<img src="modules/sb_system/themes/_admin/images_sysicons/blank.gif" alt="" />
			</xsl:when>
			<xsl:otherwise>
				<a>
					<xsl:choose>
					<xsl:when test="count(./*) > 0">
						<!--<xsl:attribute name="href"><xsl:value-of select="$base_url"/>close=<xsl:value-of select="@id" /></xsl:attribute>-->
						<!--<xsl:attribute name="href">backend.view=menu&amp;close=<xsl:value-of select="@nodeid" /></xsl:attribute>-->
						<xsl:attribute name="href">javascript:menu.close=<xsl:value-of select="@nodeid" /></xsl:attribute>
						<img src="modules/sb_system/themes/_admin/images_sysicons/tree_close.gif" alt="close" />
					</xsl:when>
					<xsl:otherwise>
						<!--<xsl:attribute name="href"><xsl:value-of select="$base_url"/>open=<xsl:value-of select="@id" /></xsl:attribute>-->
						<xsl:attribute name="href">javascript:menu.open=<xsl:value-of select="@nodeid" /></xsl:attribute>
						<img src="modules/sb_system/themes/_admin/images_sysicons/tree_open.gif" alt="open" />
					</xsl:otherwise>
					</xsl:choose>
				</a>
			</xsl:otherwise>
			</xsl:choose>
			<!-- item link -->
			<a target="main">
				<xsl:attribute name="class">type <xsl:value-of select="@type" /></xsl:attribute>
				<xsl:if test="@custom_icon">
					<xsl:attribute name="style">background-image: url(../<xsl:value-of select="@custom_icon" />);</xsl:attribute>
				</xsl:if>
				<xsl:attribute name="href">backend.nodeid=<xsl:value-of select="@nodeid" /></xsl:attribute>
				<xsl:value-of select="@label" />
			</a>
			<xsl:if test="*">
				<ul>
					<xsl:call-template name="menuentry" />
				</ul>
			</xsl:if>
		</li>
		</xsl:for-each>
	</xsl:template>

</xsl:stylesheet>