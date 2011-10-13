<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
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
		<xsl:if test="not($content/pagecontent/nodes/sbnode)">
			NO CONTENT DEFINED
		</xsl:if>
		<xsl:for-each select="$content/pagecontent/nodes/sbnode">
			<div class="gadget">
				<xsl:choose>
					<xsl:when test="@width != ''">
						<xsl:attribute name="style">width: <xsl:value-of select="@width" /></xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:attribute name="style">width: 100%</xsl:attribute>
					</xsl:otherwise>
				</xsl:choose>
				<div class="gadget_header"><xsl:value-of select="@label" /></div>
				<div class="gadget_body">
					<xsl:if test="@description != ''">
						<div class="description">
							<xsl:call-template name="break">
								<xsl:with-param name="text" select="@description" />
							</xsl:call-template>
						</div>
					</xsl:if>
					<xsl:if test="@url != ''">
						<iframe id="{@name}" width="100%" src="{@url}">
							<xsl:if test="@height != ''">
								<xsl:attribute name="height"><xsl:value-of select="@height" /></xsl:attribute>
							</xsl:if>
						</iframe>
					</xsl:if>
				</div>
			</div>
		</xsl:for-each>
	</xsl:template>

</xsl:stylesheet>