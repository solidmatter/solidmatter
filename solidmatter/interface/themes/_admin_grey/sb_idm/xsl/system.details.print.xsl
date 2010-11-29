<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

	<xsl:import href="../../sb_system/xsl/global.default.xsl" />

	<xsl:key name="unique_persons" match="sbnode[@nodetype='sbIdM:Person']" use="@uuid"/>
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:variable name="damagepotential" select="'truedsfdfsdf'" />
	
	<xsl:template match="/">
	<html>
	<head>
		<xsl:for-each select="$modules/*">
			<link rel="stylesheet" href="/theme/{name()}/css/styles_print.css" type="text/css" media="all" />
		</xsl:for-each>
	</head>
	<body>
		<xsl:apply-templates select="response/errors" />
		<xsl:apply-templates select="$content/sbnode[@master]" />
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode">
		
		<h1>Sammelrollen</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="15%">Name</th>
					<!-- <th width="20%">Bezeichnung/Beschreibung</th> -->
					<th width="33%">Einzelrollen</th>
					<th width="20%">Rollen-Owner</th>
					<th width="33%">Personen</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/userassignable_roles/nodes/sbnode">
					<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole']]">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<strong><xsl:value-of select="@name" /></strong><br />
								<span style="font-size:0.3em"><br /></span>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span>
							</td>
							<!-- <td>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span>
							</td> -->
							<td>
								<xsl:call-template name="render_techroles" />
							</td>
							<td>
								<xsl:if test="existingRelations/relation[@id='HasRoleOwner']">
									Struktur:<br />
									<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="existingRelations/relation[@id='HasRoleOwner']/@target_label" /></span><br />
								</xsl:if>
								<xsl:if test="existingRelations/relation[@id='MayBeAssignedBy']">
									Vergabe:<br />
									<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="existingRelations/relation[@id='MayBeAssignedBy']/@target_label" /></span><br />
								</xsl:if>
							</td>
							<td>
								<xsl:call-template name="render_persons" />
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
		
		<h1>Einzelrollen</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="15%">Name</th>
					<!-- <th width="20%">Bezeichnung/Beschreibung</th>
					<th width="20%">Beschränkungen</th>
					<th width="33%">Systemaktivitäten</th> -->
					<th width="85%">Einzelaktivitäten / Implementierung / Daten</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/main_roles/nodes/sbnode">
					<xsl:for-each select="$content/main_roles/nodes/sbnode">
					<!-- <xsl:sort select="@name" /> -->
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<strong><xsl:value-of select="@name" /></strong><br />
								<span style="font-size:0.3em"><br /></span>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span><br />
								<span style="font-size:0.3em"><br /></span>
								<xsl:value-of select="@constraints" /><br /><br />
								<!-- Rollen-Owner: <br />
								<xsl:value-of select="existingRelations/relation[@id='HasRoleOwner']/@target_label" /> -->
							</td>
							<!-- <td>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span><br />
							</td>
							<td>
								<xsl:value-of select="@constraints" /><br />
							</td> -->
							<td>
								<xsl:call-template name="render_techroles2" />
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
		
	</xsl:template>
	
	<xsl:template name="render_techroles">
		<xsl:if test="children[@mode='debug']/sbnode">
			<xsl:for-each select="children[@mode='debug']/sbnode">
				<xsl:value-of select="@name" /><br />
				<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@label" /></span><br />
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_techroles2">
		<xsl:if test="children[@mode='debug']/sbnode">
		<table class="invisible" width="100%">
			<xsl:for-each select="children[@mode='debug']/sbnode">
				<tr>
					<xsl:if test="@damagepotential = 'true'">
					<td width="1%">
						<xsl:choose>
							<xsl:when test="@damagepotential = 'HIGH' or @damagepotential = 'VERY HIGH'">
								S3
							</xsl:when>
							<xsl:when test="@damagepotential = 'MEDIUM'">
								S2
							</xsl:when>
							<xsl:when test="@damagepotential = 'LOW'">
								S1
							</xsl:when>
							<xsl:otherwise>
								S0
							</xsl:otherwise>
						</xsl:choose>
					</td>
					</xsl:if>
					<td width="33%">
						<!-- <xsl:value-of select="@name" /><br />
						<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@label" /></span><br />
						<div style="font-size: 0.8em; white-space:pre-wrap; margin-top:0.5em;"><xsl:value-of select="@description" /></div> -->
						<!-- [<xsl:value-of select="@damagepotential" />] --> <xsl:value-of select="@label" /><br />
						<div style="font-style: italic; font-size: 0.8em; white-space:pre-wrap; margin-top:0.5em;"><xsl:value-of select="@description" /></div>
					</td>
					<td width="33%">
						<div style="font-size: 0.8em; white-space:pre-wrap;"><xsl:value-of select="@implementation" /></div>
					</td>
					<td width="33%">
						<div style="font-size: 0.8em; white-space:pre-wrap"><xsl:value-of select="@data" /></div>
					</td>
					
					<!-- Variante mit <br> - macht Probleme mit dem Excel-Import (jeweils neue Zeile)
					<td width="33%">
						 <xsl:value-of select="@label" /><br />
						<div style="font-style: italic; font-size: 0.8em; margin-top:0.5em;"><xsl:call-template name="break"><xsl:with-param name="text" select="@description" /></xsl:call-template></div>
					</td>
					<td width="33%">
						<div style="font-size: 0.8em;"><xsl:call-template name="break"><xsl:with-param name="text" select="@implementation" /></xsl:call-template></div>
					</td>
					<td width="33%">
						<div style="font-size: 0.8em;"><xsl:call-template name="break"><xsl:with-param name="text" select="@data" /></xsl:call-template></div>
					</td> -->
					
				</tr>
				
			</xsl:for-each>
		</table>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_persons">
		<xsl:if test="content[@mode='Persons']/sbnode">
			<xsl:value-of select="count(content[@mode='Persons']/sbnode)" /> Personen
			<!--<xsl:for-each select="content[@mode='Persons']/sbnode">
				<xsl:value-of select="@label" /><br />
			</xsl:for-each>-->
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>