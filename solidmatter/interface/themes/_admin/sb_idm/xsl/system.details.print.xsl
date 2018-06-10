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
	<xsl:variable name="show_names">
		<xsl:choose>
			<xsl:when test="$parameters/param[@id='names']">true</xsl:when>
			<xsl:otherwise>false</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="verbose">
		<xsl:choose>
			<xsl:when test="$parameters/param[@id='verbose']">true</xsl:when>
			<xsl:otherwise>false</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="linked" select="'true'" />
	<xsl:variable name="hide_implicit_status_on_roles" select="'true'" />
	
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
			<xsl:when test="$parameters/param[@id='mode'] = 'usermain2'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="usermain2" />
			</xsl:when>
			<xsl:when test="$parameters/param[@id='mode'] = 'mainuser'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="mainuser" />
			</xsl:when>
			<xsl:when test="$parameters/param[@id='mode'] = 'mainsub'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="mainsub" />
			</xsl:when>
			<xsl:when test="$parameters/param[@id='mode'] = 'rolespersons'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="rolespersons" />
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
					<xsl:if test="$verbose='true'">
						<th width="20%">Rollen-Owner</th>
					</xsl:if>
					<th width="33%">Personen</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/userassignable_roles/nodes/sbnode[@active='TRUE']">
					<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole'] and @active='TRUE']">
						<xsl:sort select="@name" />
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:if test="$verbose='true'">
									<strong><xsl:value-of select="@name" /></strong><br />
									<span style="font-size:0.3em"><br /></span>
								</xsl:if>
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
							<xsl:if test="$verbose='true'">
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
							</xsl:if>
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
				<xsl:when test="$content/main_roles/nodes/sbnode[@active='TRUE']">
					<xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE']">
					<!-- <xsl:sort select="@name" /> -->
						<tr id="{@name}">
							<xsl:call-template name="colorize" />
							<td>
								<strong><xsl:value-of select="@name" /></strong><br />
								<span style="font-size:0.3em"><br /></span>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span><br />
								<span style="font-size:0.3em"><br /></span>
								<xsl:if test="existingRelations/relation[@id='AlsoRequires']">
									Benötigt Rolle(n):
									<xsl:for-each select="existingRelations/relation[@id='AlsoRequires']">
										<xsl:variable name="requiredrole" select="@target_uuid" />
										<br /><xsl:value-of select="$content/main_roles/nodes/sbnode[@active='TRUE' and @uuid=$requiredrole]/@name"></xsl:value-of> 
									</xsl:for-each>
								</xsl:if>
								<!-- <xsl:value-of select="@constraints" /><br /><br /> -->
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
	
	<xsl:template match="sbnode" mode="rolespersons">
		
		<h1>Sammelrollen</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="15%">Kürzel</th>
					<th width="15%">Name</th>
					<!-- <th width="20%">Bezeichnung/Beschreibung</th> -->
					<!-- <th width="33%">Einzelrollen</th> -->
					<th width="20%">Owner Vergabe</th>
					<th width="20%">Owner Inhalte</th>
					<th width="33%">Personen</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/userassignable_roles/nodes/sbnode[@active='TRUE']">
					<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole'] and @active='TRUE']">
						<xsl:sort select="@name" />
						<xsl:variable name="name" select="@name" />
						<xsl:variable name="label" select="@label" />
						<xsl:variable name="ownerd" select="existingRelations/relation[@id='MayBeAssignedBy']/@target_label" />
						<xsl:variable name="ownerc" select="existingRelations/relation[@id='HasRoleOwner']/@target_label" />
						<xsl:for-each select="content[@mode='Persons']/sbnode">
							<tr>
								<xsl:call-template name="colorize" />
								<td>
									<xsl:value-of select="$name" />
								</td>
								<td>
									<xsl:value-of select="$label" />
								</td>
								<td>
									<xsl:value-of select="$ownerd" />
								</td>
								<td>
									<xsl:value-of select="$ownerc" />
								</td>
								<td>
									<xsl:value-of select="@label" />
								</td>
							</tr>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
	</xsl:template>
	
	<xsl:template match="sbnode" mode="usermain2">
		
		<h1>Rollenkatalog</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="15%">Funktion \ Rolle</th>
					<xsl:if test="$verbose='true'">
						<th width="20%">Name</th>
					</xsl:if>
					<th width="40%">Beschreibung</th>
					<th width="15%">Personen</th>
					<!-- <th width="33%">Einzelrollen</th> -->
					<xsl:if test="$verbose='true'">
						<th width="25%">Rollen-Owner Vergabe</th>
						<th width="25%">Rollen-Owner Struktur</th>
					</xsl:if>
					<xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE' and @priority!='HIDE']">
						<xsl:sort select="@priority" />
						<th width="1%">
						<!-- <span style="position:relative; top:0px; left:0px; -moz-transform : rotate(90deg);
								  -o-transform : rotate(90deg);
								  -webkit-transform : rotate(90deg);
								  transform : rotate(90deg);"> -->
								<span class="vertical" style="width:1.2em">
									<!-- <xsl:value-of select="@name" /> -->
									<xsl:value-of select="@label" />
								</span>
						</th>
					</xsl:for-each>
					<!-- <th width="33%">Personen</th> -->
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$content/userassignable_roles/nodes/sbnode">
					<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole'] and @active='TRUE' and @priority!='HIDE']">
						<xsl:sort select="@name" />
						<xsl:variable name="userrole" select="." />
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:value-of select="@label" />
							</td>
							<xsl:if test="$verbose='true'">
								<td>
									<xsl:value-of select="@name" />
								</td>
							</xsl:if>
							<td>
								<xsl:value-of select="@description" />
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="$show_names='true'"><xsl:call-template name="render_persons" /></xsl:when>
									<xsl:otherwise><xsl:value-of select="count(content[@mode='Persons']/sbnode)" /></xsl:otherwise>
								</xsl:choose>
							</td>
							<!-- <td>
								<xsl:call-template name="render_techroles" />
							</td> -->
							<xsl:if test="$verbose='true'">
								<td>
									<xsl:value-of select="existingRelations/relation[@id='MayBeAssignedBy']/@target_label" />
								</td>
								<td>
									<xsl:value-of select="existingRelations/relation[@id='HasRoleOwner']/@target_label" />
								</td>
							</xsl:if>
							<xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE' and @priority!='HIDE']">
								<xsl:sort select="@priority" />
								<xsl:variable name="mainrole" select="." />
								<xsl:choose>
									<xsl:when test="$userrole/children[@mode='debug']/sbnode[@uuid=$mainrole/@uuid]">
										<td>X</td>
									</xsl:when>
									<xsl:when test="$userrole/children[@mode='debug']/sbnode/existingRelations/relation[@id='AlsoRequires' and @target_uuid=$mainrole/@uuid] and $hide_implicit_status_on_roles='true'">
										<td>X</td>
									</xsl:when>
									<xsl:when test="$userrole/children[@mode='debug']/sbnode/existingRelations/relation[@id='AlsoRequires' and @target_uuid=$mainrole/@uuid]">
										<td>(X)</td>
									</xsl:when>
									<xsl:otherwise>
										<td></td>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
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
	
	<xsl:template match="sbnode" mode="mainuser">
		<table>
			<tr>
				<th colspan="">Rollenzuordnungen</th>
			</tr>
			<tr>
				<th>Prozessrolle</th>
				<th>Funktionen</th>
				<th>Anzahl Personen</th>
				<th>Namen der Personen</th>
			</tr>
			<xsl:choose>
				<xsl:when test="$content/main_roles/nodes/sbnode[@active='TRUE']">
					<xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE']">
						<xsl:variable name="mainrole_uuid" select="@uuid" />
						<tr>
							<td><xsl:value-of select="@label" /></td>
							<td>
								<xsl:for-each select="content[@mode='UserassignableRoles']/sbnode">
									<xsl:value-of select="@label" /><br />
								</xsl:for-each>
							</td>
							<td>
								<xsl:value-of select="count(content[@mode='Persons']/sbnode)" />
							</td>
							<td>
								<xsl:for-each select="content[@mode='Persons']/sbnode">
									<xsl:value-of select="@label" /><br />
								</xsl:for-each>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$locale/sbSystem/texts/no_subobjects" />
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>
	
	<xsl:template match="sbnode" mode="mainsub">
		<table>
			<!-- <tr>
				<th>Prozessrolle/-aufgabe</th>
				<th>Tätigkeiten/Daten</th>
				<th>Schutzbedarf</th>
				<th>Einstufung</th>
			</tr> -->
			<xsl:choose>
				<xsl:when test="$content/main_roles/nodes/sbnode[@active='TRUE']">
					<xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE']">
						<xsl:sort select="@priority" />
						<xsl:variable name="mainrole_uuid" select="@uuid" />
						<tr>
							<th colspan="2">
								<strong><xsl:value-of select="@label" /></strong>
							</th>
						</tr>
						<tr>
							<td>
								<strong>Tätigkeiten</strong><br /><br />
								<ul>
								<xsl:for-each select="children[@mode='debug']/sbnode[@active='TRUE']">
									<li><xsl:value-of select="@label" /></li>
								</xsl:for-each>
								</ul>
								<strong>Datenzugriffe</strong><br /><br />
								<xsl:call-template name="break">
									<xsl:with-param name="text" select="@data_personal" />
								</xsl:call-template>
							</td>
							<td>
								<strong>Schutzbedarf</strong><br /><br />
								Vertraulichkeit: 
								<xsl:choose>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_confidentiality='VERY_HIGH']">
									Sehr hoch
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_confidentiality='HIGH']">
									Normal
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_confidentiality='MEDIUM']">
									Normal
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_confidentiality='LOW']">
									Niedrig
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_confidentiality='UNSPECIFIED']">
									n/a
								</xsl:when>
								<xsl:otherwise>
									???
								</xsl:otherwise>
								</xsl:choose>
								<br />
								Integrität: 
								<xsl:choose>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_integrity='VERY_HIGH']">
									Sehr hoch
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_integrity='HIGH']">
									Normal
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_integrity='MEDIUM']">
									Normal
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_integrity='LOW']">
									Niedrig
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @protreq_integrity='UNSPECIFIED']">
									n/a
								</xsl:when>
								<xsl:otherwise>
									???
								</xsl:otherwise>
								</xsl:choose>
								
								<br /><br /><strong>Zugriffsvariante</strong><br /><br />
								
								<xsl:choose>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @access='RESTRICTED']">
									Terminal Server
								</xsl:when>
								<xsl:when test="children[@mode='debug']/sbnode[@active='TRUE' and @access='EXPOSED']">
									Portal
								</xsl:when>
								<xsl:otherwise>
									???
								</xsl:otherwise>
								</xsl:choose>
							</td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$locale/sbSystem/texts/no_subobjects" />
				</xsl:otherwise>
			</xsl:choose>
		</table>
	</xsl:template>
	
	<xsl:template match="sbnode" mode="dsb">
		
		<!-- <h1>Berechtigungskonzept</h1> -->
		
		<xsl:choose>
			<xsl:when test="$content/userassignable_roles/nodes/sbnode[@active='TRUE']">
				<!-- loop over userassignable roles -->
				<xsl:for-each select="$content/userassignable_roles/nodes/sbnode[children[@mode='debug']/sbnode[@nodetype='sbIdM:TechRole'] and @active='TRUE']">
					<xsl:sort select="@priority" />
					<xsl:sort select="@name" />
					<xsl:variable name="userrole" select="." />
					<xsl:if test="@active = 'TRUE'">
						<h3 style="border-bottom: 2pt solid black;">
							<xsl:value-of select="@label" />
						</h3>
						<xsl:value-of select="@description" /> 
						<!-- HACK: Anzahl Personen, aber generelle Rollen ausschließen -->
						<xsl:if test="@name!='ZP_FH_LB' and @name!='ZP_FH_PROF' and @name!='ZP_FH_L' and @name!='ZP_FH_LFBA'">
							(<xsl:value-of select="count(content[@mode='Persons']/sbnode)" /> 
							<xsl:choose>
								<xsl:when test="count(content[@mode='Persons']/sbnode) = 1"> Person</xsl:when>
								<xsl:when test="count(content[@mode='Persons']/sbnode) > 1"> Personen</xsl:when>
								<xsl:otherwise> ???</xsl:otherwise>
							</xsl:choose>)
						</xsl:if>
						
						<xsl:if test="@explanation != ''">
							<h4>Erläuterungen</h4>
							<xsl:call-template name="break">
								<xsl:with-param name="text" select="@explanation" />
							</xsl:call-template>
						</xsl:if>
						
						<!-- <h4>Prozessrollen und -aufgaben</h4> -->
						<!-- loop over main roles -->
						<!-- <xsl:for-each select="children[@mode='gatherTechRoles']/sbnode[@active='TRUE']"> -->
						<!-- <xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE' and @priority!='HIDE']"> -->
						<xsl:for-each select="$content/main_roles/nodes/sbnode[@active='TRUE']">
							<xsl:sort select="@priority" />
							<xsl:variable name="mainrole" select="." />
							<xsl:choose>
								<xsl:when test="$userrole/children[@mode='debug']/sbnode[@uuid=$mainrole/@uuid] or $userrole/children[@mode='debug']/sbnode/existingRelations/relation[@id='AlsoRequires' and @target_uuid=$mainrole/@uuid]">
									<h4><xsl:value-of select="@label" /></h4>
									<p><strong><em>ausgeübte Tätigkeiten</em></strong></p>
									<!-- <xsl:for-each select="children/sbnode/children/sbnode[@active='TRUE' and not(@uuid = preceding-sibling::sbnode/@uuid) and not(@uuid = ../../preceding-sibling::sbnode/children/sbnode/@uuid)]"> -->
									
										<!-- loop over activities -->
										<xsl:for-each select="children[@mode='debug']/sbnode[@active='TRUE']">
											- <xsl:value-of select="@label" /><br/>
										</xsl:for-each>
									
									<p><strong><em>Datenzugriffe</em></strong></p>
									<xsl:call-template name="break">
										<xsl:with-param name="text" select="@data_personal" />
									</xsl:call-template>										
								</xsl:when>
								<xsl:otherwise>
									<!-- do nothing -->
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						
						
						
							<!-- <xsl:for-each select="children[@mode='gatherTechRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode[@active='TRUE' and not(@uuid = preceding-sibling::sbnode/@uuid)]">
								<li><xsl:value-of select="@label" /></li>
							</xsl:for-each> -->
							<!-- <xsl:for-each select="children[@mode='gatherTechRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode[@active='TRUE' and generate-id()=generate-id(key('kUniqueTechRoles', @uuid)[1])]"> -->
							
						
						
						
					</xsl:if>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$locale/sbSystem/texts/no_subobjects" />
			</xsl:otherwise>
		</xsl:choose>
			
		
	</xsl:template>
	
	
	<xsl:template name="render_techroles">
		<xsl:if test="children[@mode='debug']/sbnode[@active='TRUE']">
			<xsl:for-each select="children[@mode='debug']/sbnode[@active='TRUE']">
				<xsl:if test="$verbose='true'">
					<xsl:choose>
						<xsl:when test="$linked = 'true'">
							<a href="#{@name}"><xsl:value-of select="@name" /></a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="@name" />
						</xsl:otherwise>
					</xsl:choose>
					<xsl:if test="existingRelations/relation[@id='AlsoRequires']">
						<xsl:for-each select="existingRelations/relation[@id='AlsoRequires']">
							<xsl:variable name="requiredrole" select="@target_uuid" />
							<br /><xsl:value-of select="$content/main_roles/nodes/sbnode[@active='TRUE' and @uuid=$requiredrole]/@name"></xsl:value-of> (impl.)
						</xsl:for-each>
					</xsl:if>
					<br />
				</xsl:if>
				<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@label" /></span><br />
			</xsl:for-each>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_techroles2">
		<xsl:if test="children[@mode='debug']/sbnode">
		<table class="invisible" width="100%">
			<xsl:for-each select="children[@mode='debug']/sbnode[@active='TRUE']">
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
						<!--<br />
						<div style="font-size: 0.8em; white-space:pre-wrap"><xsl:value-of select="@data" /></div> -->
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
	
	<!-- http://stackoverflow.com/questions/7432869/how-to-convert-hyphen-in-text-feature-list-to-html-list-items-using-xslt -->
	<!-- <xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
    <xsl:output indent="yes"/>
    <xsl:strip-space elements="*"/>
    <xsl:template match="AdvNotes">
        <xsl:variable name="tokens" select="tokenize(.,'-')"/>
        <p>
            <xsl:value-of select="$tokens[1]"/>
        </p>
        <ul>
            <xsl:for-each select="remove($tokens,1)">
                <li>
                    <xsl:value-of select="."/>
                </li>
            </xsl:for-each>
        </ul>
    </xsl:template>
	</xsl:stylesheet> -->
	<xsl:template name="to_list">
        <xsl:param name="li" select="substring-after(.,'-')"/>
        <xsl:variable name="item" select="substring-before($li,'-')"/>
        <xsl:choose>
            <xsl:when test="not($item)">
                <li>
                    <xsl:value-of select="$li"/>
                </li>
            </xsl:when>
            <xsl:otherwise>
                <li>
                    <xsl:value-of select="$item"/>
                </li>   
                <xsl:call-template name="to_list">
                 <xsl:with-param name="li" select="substring-after($li,'-')"/>
                </xsl:call-template> 
            </xsl:otherwise> 
        </xsl:choose>
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