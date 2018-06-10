<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

	<xsl:import href="../../sb_system/xsl/global.views.xsl" />
	<xsl:import href="../../sb_system/xsl/global.default.xsl" />

	<xsl:key name="unique_persons" match="sbnode[@nodetype='sbIdM:Person']" use="@uuid"/>
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>

	<xsl:template match="/">
	<html>
	<head>
		<xsl:apply-templates select="/response/metadata" />
	</head>
	<body>
		<xsl:call-template name="views" />
		<div class="workbench">
			<xsl:apply-templates select="response/errors" />
			<xsl:apply-templates select="$content/sbnode[@master]" />
		</div>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode">
		
		<table class="default" width="100%" id="list">
			<thead>
				<tr><th class="th2">Tags</th></tr>
			</thead>
			<tbody>
				<tr><td>
					<xsl:for-each select="$master/branchtags/tag">
						<a href="/{$master/@uuid}/details/-/?tagid={@id}">
							<xsl:if test="@id = $content/@tagid">
								<xsl:attribute name="style">font-weight:bold;</xsl:attribute>
							</xsl:if>
							<xsl:value-of select="." />
						</a>
						<xsl:if test="position() != last()"> - </xsl:if>
					</xsl:for-each>
				</td></tr>
			</tbody>
			<tfoot><tr><td></td></tr></tfoot>
		</table>
	
		<table class="default" width="100%" id="list">
			<thead>
				<tr><th class="th2" colspan="6" >Result</th></tr>
				<tr class="th2">
					<th width="33%"><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					<th width="33%">Infos</th>
					<th width="33%">Questions</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="content[@mode='tagged']//sbnode">
					<!-- <xsl:for-each select="children[@mode='debug']//sbnode[@nodetype='sbIdM:Person']"> -->
						<xsl:for-each select="content[@mode='tagged']//sbnode">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<a href="/{@uuid}/"><span class="type {@displaytype}"><xsl:value-of select="@label" /></span></a>
							</td>
							<td>
								<xsl:call-template name="break">
									<xsl:with-param name="text" select="@description" />
								</xsl:call-template>
							</td>
							<td style="white-space:pre-wrap;">
								<xsl:value-of select="@questions" />
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
			<tfoot><tr><td colspan="3"></td></tr></tfoot>
		</table>
		
	</xsl:template>
	
	<xsl:template name="render_techroles">
		<xsl:if test="children[@mode='gatherTechRoles']/sbnode">
			<xsl:for-each select="children[@mode='gatherTechRoles']/sbnode">
				<a href="/{@uuid}"><span class="type {@displaytype}"><xsl:value-of select="@label" /> [<xsl:value-of select="@name" />]</span></a><br />
			</xsl:for-each>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>