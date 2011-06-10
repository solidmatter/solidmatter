<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html" 
	xmlns:html="http://www.w3.org/1999/xhtml">

	<xsl:import href="../../sb_system/xsl/global.default.xsl" />

	<xsl:key name="kUniquePersons" match="sbnode[@nodetype='sbIdM:Person']" use="@uuid"/>
	<xsl:key name="kUniqueTechRoles" match="sbnode[@nodetype='sbIdM:TechRole']" use="@uuid"/>
	
	
	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="no"
	/>
	
	<xsl:variable name="access" select="'false'" />
	<xsl:variable name="show_names" select="'false'" />
	<xsl:variable name="linked" select="'true'" />
	
	<xsl:template match="/">
	<html>
	<head>
		<xsl:for-each select="$modules/*">
			<link rel="stylesheet" href="/theme/{name()}/css/styles_print.css" type="text/css" media="all" />
		</xsl:for-each>
	</head>
	<body>
		<xsl:apply-templates select="response/errors" />
		<xsl:choose>
			<xsl:when test="$parameters/param[@id='mode'] = 'usermain'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="usermain" />
			</xsl:when>
			<xsl:when test="$parameters/param[@id='mode'] = 'userall'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="userall" />
			</xsl:when>
			<xsl:when test="$parameters/param[@id='mode'] = 'dsb'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="dsb" />
			</xsl:when>
			<xsl:otherwise>
				crap.
			</xsl:otherwise>
		</xsl:choose>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode" mode="usermain">
		
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
						<xsl:sort select="@name" />
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
								<!-- <xsl:choose>
									<xsl:when test="descendant::./@damagepotential = 'HIGH'">
										Juchuu!
									</xsl:when>
									<xsl:when test="descendant::./@damagepotential = 'LOW'">
										Juchheissassa!
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose> -->
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
		
		<b>Legende Schutzbedarf:</b> Χ = nicht schutzwürdig/anwendbar, <!-- ▼ = niedrig  , -->◇ = normal, ▲ = hoch, <span style="font-size: 1.2em; font-weight: normal;">⚠ </span> = sehr hoch<br />
		<b>Legende Rechte:</b> - = keine Angabe, Χ = nicht erforderlich, [✓] = wünschenswert bzw. angefordert, ✓ = notwendig
		
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
						<tr id="{@name}">
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
	
	<xsl:template match="sbnode" mode="dsb">
		
		<h1>Berechtigungskonzept</h1>
		
		<xsl:choose>
			<xsl:when test="$content/userassignable_roles/nodes/sbnode">
				<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole']]">
					<xsl:sort select="@name" />
					<xsl:if test="@active = 'TRUE'">
						<h2 style="border-bottom: 2pt solid black;">
							<xsl:value-of select="@label" />
						</h2>
						<xsl:value-of select="@description" /> 
						(<xsl:value-of select="count(content[@mode='Persons']/sbnode)" /> 
						<xsl:choose>
							<xsl:when test="count(content[@mode='Persons']/sbnode) = 1"> Person</xsl:when>
							<xsl:when test="count(content[@mode='Persons']/sbnode) > 1"> Personen</xsl:when>
							<xsl:otherwise> ???</xsl:otherwise>
						</xsl:choose>)
						<h3>Prozessrollen und -aufgaben</h3>
						<ul>
							<xsl:for-each select="children[@mode='debug']/sbnode">
								<xsl:if test="@active = 'TRUE'">
									<li><xsl:value-of select="@label" /></li>
								</xsl:if>
							</xsl:for-each>
						</ul>
						<h3>ausgeübte Tätigkeiten</h3>
						<ul>
							<!-- <xsl:for-each select="children[@mode='gatherTechRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode[@active='TRUE' and not(@uuid = preceding-sibling::sbnode/@uuid)]">
								<li><xsl:value-of select="@label" /></li>
							</xsl:for-each> -->
							<!-- <xsl:for-each select="children[@mode='gatherTechRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode[@active='TRUE' and generate-id()=generate-id(key('kUniqueTechRoles', @uuid)[1])]"> -->
							<xsl:for-each select="children/sbnode/children/sbnode[@active='TRUE' and not(@uuid = preceding-sibling::sbnode/@uuid) and not(@uuid = ../../preceding-sibling::sbnode/children/sbnode/@uuid)]">
								<li><xsl:value-of select="@label" /></li>
							</xsl:for-each>
						</ul>
						<h3>Datenzugriffe</h3>
						<xsl:call-template name="break">
							<xsl:with-param name="text" select="@data_personal" />
						</xsl:call-template>
						<br />
						<xsl:if test="@explanation != ''">
							<h3>Erläuterungen</h3>
							<xsl:call-template name="break">
								<xsl:with-param name="text" select="@explanation" />
							</xsl:call-template>
						</xsl:if>
					</xsl:if>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$locale/sbSystem/texts/no_subobjects" />
			</xsl:otherwise>
		</xsl:choose>
			
		
	</xsl:template>
	
	
	<xsl:template name="render_techroles">
		<xsl:if test="children[@mode='debug']/sbnode">
			<xsl:for-each select="children[@mode='debug']/sbnode">
				<xsl:choose>
					<xsl:when test="$linked = 'true'">
						<a href="#{@name}"><xsl:value-of select="@name" /></a>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@name" />
					</xsl:otherwise>
				</xsl:choose>
				<br />
				<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@label" /></span><br />
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_techroles2">
		<xsl:if test="children[@mode='debug']/sbnode">
		<table class="invisible" width="100%">
			<xsl:for-each select="children[@mode='debug']/sbnode">
				<tr>
					<xsl:if test="$access = 'true'">
					<td width="1%">
						<xsl:choose>
							<xsl:when test="@access = 'RESTRICTED'">
								A/B
							</xsl:when>
							<xsl:when test="@access = 'EXPOSED'">
								C
							</xsl:when>
							<xsl:otherwise>
								???
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
						<xsl:choose>
							<xsl:when test="@implementation_type = 'SAPGUI'">
								SAP GUI
							</xsl:when>
							<xsl:when test="@implementation_type = 'WEBDYNPRO'">
								WebDynPro
							</xsl:when>
							<xsl:otherwise>
								???
							</xsl:otherwise>
						</xsl:choose>
						<br /><br />
						<div style="font-size: 0.8em; white-space:pre-wrap;"><xsl:value-of select="@implementation" /></div>
					</td>
					<td width="33%">
						Vertraulichkeit: <xsl:call-template name="render_protreq"><xsl:with-param name="level" select="@protreq_confidentiality" /></xsl:call-template><br />
						Integrität: <xsl:call-template name="render_protreq"><xsl:with-param name="level" select="@protreq_integrity" /></xsl:call-template><br />
						<br />
						Lesen: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_read" /></xsl:call-template><br />
						Schreiben: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_write" /></xsl:call-template><br />
						Drucken: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_print" /></xsl:call-template><br />
						Datenaustausch: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_datatrans" /></xsl:call-template><br />
						<br />
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
			<xsl:choose>
				<xsl:when test="$show_names = 'true'">
					<xsl:for-each select="content[@mode='Persons']/sbnode">
						<xsl:value-of select="@label" /><br />
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="count(content[@mode='Persons']/sbnode)" /> Personen
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_permission">
		<xsl:param name="permission" />
		<xsl:choose>
			<xsl:when test="$permission='UNSPECIFIED'">
				-
			</xsl:when>
			<xsl:when test="$permission='NO'">
				Χ
			</xsl:when>
			<xsl:when test="$permission='REQUESTED'">
				[✓]
			</xsl:when>
			<xsl:when test="$permission='REQUIRED'">
				✓
			</xsl:when>
			<xsl:otherwise>
				-
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template name="render_protreq">
		<xsl:param name="level" />
		<xsl:choose>
			<xsl:when test="$level='NONE'">
				Χ<!-- ⇓⇑↔↓↑▼ -->
			</xsl:when>
			<xsl:when test="$level='LOW'">
				◇<!-- ▼  -->
			</xsl:when>
			<xsl:when test="$level='MEDIUM'">
				◇
			</xsl:when>
			<xsl:when test="$level='HIGH'">
				▲
			</xsl:when>
			<xsl:when test="$level='VERY HIGH'">
				<span style="font-size: 1.2em; font-weight: normal;">⚠ </span>
			</xsl:when>
			<xsl:otherwise>
				???
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="sbnode" mode="userall">
		
		<h1>Sammelrollen</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="20%">Name</th>
					<!-- <th width="20%">Bezeichnung/Beschreibung</th> -->
					<th width="80%">Daten</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/userassignable_roles/nodes/sbnode">
					<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole']]">
						<xsl:sort select="@name" />
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<!--<strong><xsl:value-of select="@name" /></strong><br />
								<span style="font-size:0.3em"><br /></span>-->
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span>
							</td>
							<td>
								<xsl:call-template name="render_data" />
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
	
	<xsl:template name="render_data">
		<xsl:if test="children[@mode='gatherTechRoles']/sbnode">
			<xsl:for-each select="children[@mode='gatherTechRoles']/sbnode">
				<!-- <strong style="margin-Top:1em; text-decoration:underline;"><xsl:value-of select="@label" /></strong><br /><br /> -->
				<xsl:call-template name="render_data2" />
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_data2">
		<xsl:if test="children[@mode='gatherTechRoles']/sbnode">
			<xsl:for-each select="children[@mode='gatherTechRoles']/sbnode">
				<!-- <span style="text-decoration:underline;"><xsl:value-of select="@label" /></span>
				( Lesen: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_read" /></xsl:call-template> / 
				Schreiben: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_write" /></xsl:call-template>)<br /><br /> -->
				<div style="font-size: 0.8em; white-space:pre-wrap; margin-bottom:1em;"><xsl:value-of select="@data" /></div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
		
	<xsl:template name="unused">
		<table class="invisible" width="100%">
			<xsl:for-each select=".//sbnode[@nodetype='sbIdM:TechRole']">
				<tr>
					<xsl:if test="$access = 'true'">
					<td width="1%">
						<xsl:choose>
							<xsl:when test="@access = 'RESTRICTED'">
								A/B
							</xsl:when>
							<xsl:when test="@access = 'EXPOSED'">
								C
							</xsl:when>
							<xsl:otherwise>
								???
							</xsl:otherwise>
						</xsl:choose>
					</td>
					</xsl:if>
					<!--<td width="33%">-->
						<!-- <xsl:value-of select="@name" /><br />
						<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@label" /></span><br />
						<div style="font-size: 0.8em; white-space:pre-wrap; margin-top:0.5em;"><xsl:value-of select="@description" /></div> -->
						<!-- [<xsl:value-of select="@damagepotential" />] --> 
						 <!--
						<xsl:value-of select="@label" /><br />
						<div style="font-style: italic; font-size: 0.8em; white-space:pre-wrap; margin-top:0.5em;"><xsl:value-of select="@description" /></div>
						 
					</td>-->
					<td width="33%">
						<!--Vertraulichkeit: <xsl:call-template name="render_protreq"><xsl:with-param name="level" select="@protreq_confidentiality" /></xsl:call-template><br />
						Integrität: <xsl:call-template name="render_protreq"><xsl:with-param name="level" select="@protreq_integrity" /></xsl:call-template><br />
						<br />
						Lesen: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_read" /></xsl:call-template><br />
						Schreiben: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_write" /></xsl:call-template><br />
						Drucken: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_print" /></xsl:call-template><br />
						Datenaustausch: <xsl:call-template name="render_permission"><xsl:with-param name="permission" select="@perm_datatrans" /></xsl:call-template><br />
						<br />-->
						
						<!-- <xsl:value-of select="parent::*/parent::*/@label" />: <xsl:value-of select="@label" /> -->
						
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
	</xsl:template>
	
</xsl:stylesheet>