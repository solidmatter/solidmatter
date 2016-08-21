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
				<tr>
					<th colspan="3">MainRoles</th>
					<th colspan="3" style="text-align:right;">
						Print Version: 
						<a href="/{@uuid}/details/print?mode=usermain" target="_blank">User/Main</a> / 
						<a href="/{@uuid}/details/print?mode=usermain2" target="_blank">User/Main 2</a> / 
						<a href="/{@uuid}/details/print?mode=mainsub" target="_blank">Main/Sub</a> / 
						<a href="/{@uuid}/details/print?mode=mainuser" target="_blank">Main/User</a> / 
						<a href="/{@uuid}/details/print?mode=rolespersons" target="_blank">Roles/Users</a> / 
						<a href="/{@uuid}/details/print?mode=userall" target="_blank">User/All</a> / 
						<a href="/{@uuid}/details/print?mode=dsb" target="_blank">DSB</a>
					</th>
				</tr>
				<tr class="th2">
					<th width="15%"><xsl:value-of select="$locale/sbSystem/labels/name" /></th>
					<th width="5%">TechName</th>
					<th width="20%"><xsl:value-of select="$locale/sbSystem/labels/description" /></th>
					<!-- <th width="10%">Tags</th> -->
					<th width="33%">TechRoles</th>
					<th width="33%">Persons</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/main_roles/nodes/sbnode">
					<xsl:for-each select="$content/main_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole']]">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<a href="/{@uuid}/details"><span class="type {@displaytype}"><xsl:value-of select="@label" /></span></a>
							</td>
							<td>
								<xsl:value-of select="@name" />
							</td>
							<td>
								<xsl:value-of select="@description" />
							</td>
							<!-- <td>
								<xsl:for-each select="tags/tag">
									<xsl:value-of select="." />
									<xsl:if test="position() != last()"> - </xsl:if>
								</xsl:for-each>
							</td> -->
							<td>
								<xsl:call-template name="render_techroles" />
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
		
	</xsl:template>
	
	<xsl:template name="render_techroles">
		<xsl:if test="children[@mode='debug']/sbnode">
		<ul>
			<xsl:for-each select="children[@mode='debug']/sbnode">
				<li>
					<a href="/{@uuid}"><span class="type {@displaytype}"><xsl:value-of select="@label" /> [<xsl:value-of select="@name" />]</span></a>
					<!--<xsl:call-template name="render_techroles" />-->
				</li>
			</xsl:for-each>
		</ul>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_persons">
		<xsl:if test="content[@mode='Persons']/sbnode">
		<ul>
			<xsl:for-each select="content[@mode='Persons']/sbnode">
				<li><a href="/{@uuid}"><span class="type {@displaytype}"><xsl:value-of select="@label" /></span></a></li>
			</xsl:for-each>
		</ul>
		</xsl:if>
	</xsl:template>

</xsl:stylesheet>