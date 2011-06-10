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
	
	<xsl:variable name="show_names" select="'true'" />
	<xsl:variable name="show_orgroles" select="'false'" />
	<xsl:variable name="techrole_iterations" select="2" />
	<xsl:variable name="show_access" select="'false'" />
	<xsl:variable name="show_implementation_type" select="'true'" />
	<xsl:variable name="show_requirements" select="'false'" />
	<xsl:variable name="show_empty_techroles" select="'false'" />
	<xsl:variable name="show_empty_persons" select="'false'" />
	
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
			<xsl:when test="$parameters/param[@id='mode'] = 'persons'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="persons" />
			</xsl:when>
			<xsl:when test="$parameters/param[@id='mode'] = 'orgroles'">
				<xsl:apply-templates select="$content/sbnode[@master]" mode="orgroles" />
			</xsl:when>
			<xsl:otherwise>
				crap.
			</xsl:otherwise>
		</xsl:choose>
	</body>
	</html>
	</xsl:template>
		
	<xsl:template match="sbnode" mode="persons">
		
		<h1>Personen in <xsl:value-of select="$master/@label" /> (<xsl:value-of select="count($master/content[@mode='Persons']/sbnode)" />)</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="15%">Name</th>
					<!-- <th width="20%">Bezeichnung/Beschreibung</th> -->
					<th width="33%">Rollen</th>
					<xsl:if test="$show_implementation_type = 'true'">
						<th width="10%">SAP GUI (<xsl:value-of select="count($master/content[@mode='Persons']/sbnode[descendant::sbnode/@implementation_type = 'SAPGUI'])" />)</th>
						<th width="10%">Portal (<xsl:value-of select="count($master/content[@mode='Persons']/sbnode[descendant::sbnode/@implementation_type = 'WEBDYNPRO'])" />)</th>
					</xsl:if>
					<xsl:if test="$show_access = 'true'">
						<th width="10%">A/B</th>
						<th width="10%">C</th>
					</xsl:if>
					<xsl:if test="$show_requirements = 'true'">
						<th width="10%">Drucken (<xsl:value-of select="count($master/content[@mode='Persons']/sbnode[descendant::sbnode/@perm_print = 'REQUIRED'])" />)</th>
						<th width="10%">Datenaustausch(<xsl:value-of select="count($master/content[@mode='Persons']/sbnode[descendant::sbnode/@perm_datatrans = 'REQUIRED'])" />)</th>
					</xsl:if>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$master/content[@mode='Persons']/sbnode">
					<xsl:for-each select="$master/content[@mode='Persons']/sbnode">
						<xsl:if test="content[@mode='OrgRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode or $show_empty_persons = 'true'">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:choose>
									<xsl:when test="$show_names = 'true'">
										<strong><xsl:value-of select="@label" /></strong><br />
									</xsl:when>
									<xsl:otherwise>
										<xsl:choose>
											<xsl:when test="substring(@label, 1, 4) = 'Alle'">
												<strong><xsl:value-of select="@label" /></strong><br />
											</xsl:when>
											<xsl:otherwise>
												<strong>Person</strong><br />
											</xsl:otherwise>
										</xsl:choose>
									</xsl:otherwise>
								</xsl:choose>
								
								<!-- <span style="font-size:0.3em"><br /></span>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span> -->
							</td>
							<!-- <td>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span>
							</td> -->
							<td>
								<xsl:call-template name="render_orgroles" /><!-- deaktiviert -->
							</td>
							<xsl:if test="$show_implementation_type = 'true'">
								<td>
									<xsl:choose>
										<xsl:when test="descendant::sbnode/@implementation_type = 'SAPGUI'">
											✓
										</xsl:when>
										<xsl:when test="descendant::sbnode/@implementation_type = 'WEBDYNPRO'">
											
										</xsl:when>
										<xsl:otherwise>
											???
										</xsl:otherwise>
									</xsl:choose>
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="descendant::sbnode/@implementation_type = 'WEBDYNPRO'">
											✓
										</xsl:when>
										<xsl:when test="descendant::sbnode/@implementation_type = 'SAPGUI'">
											
										</xsl:when>
										<xsl:otherwise>
											???
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</xsl:if>
							<xsl:if test="$show_access = 'true'">
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@access = 'RESTRICTED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@access = 'EXPOSED'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@access = 'EXPOSED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@access = 'RESTRICTED'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							</xsl:if>
							<!-- <td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@damagepotential = 'HIGH' or descendant::sbnode/@damagepotential = 'VERY HIGH' or descendant::sbnode/@damagepotential = 'MEDIUM'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@damagepotential = 'LOW' or descendant::sbnode/@damagepotential = 'NONE'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@damagepotential = 'LOW' or descendant::sbnode/@damagepotential = 'NONE'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@damagepotential = 'HIGH' or descendant::sbnode/@damagepotential = 'VERY HIGH' or descendant::sbnode/@damagepotential = 'MEDIUM'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td> -->
							<xsl:if test="$show_requirements = 'true'">
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@perm_print = 'REQUIRED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_print = 'REQUESTED'">
										[✓]
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_print = 'NO'">
										Χ
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@perm_datatrans = 'REQUIRED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_datatrans = 'REQUESTED'">
										[✓]
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_datatrans = 'NO'">
										Χ
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							</xsl:if>
						</tr>
						</xsl:if>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
		
	</xsl:template>
	
	<xsl:template match="sbnode" mode="orgroles">
		
		<h1>Funktionen in <xsl:value-of select="$master/@label" /> (<xsl:value-of select="count($master/content[@mode='Persons']/sbnode)" />)</h1>
		
		<table class="default fullwidth" width="100%" id="list">
			<thead>
				<!--<tr><th colspan="6" >MainRoles</th></tr>-->
				<tr class="th2">
					<th width="15%">Funktion (Anzahl Personen)</th>
					<!-- <th width="20%">Bezeichnung/Beschreibung</th> -->
					<th width="33%">aktiv in Prozessabschnitten</th>
					<th width="33%">personenbezogene Daten</th>
					<th width="33%">Prozessrollen</th>
				</tr>
			</thead>
			<tbody>
			<xsl:choose>
				<xsl:when test="$master/content[@mode='Persons']/sbnode">
					<xsl:for-each select="$master/content[@mode='Persons']/sbnode">
						<xsl:if test="content[@mode='OrgRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode or $show_empty_persons = 'true'">
						<tr>
							<xsl:call-template name="colorize" />
							<td>
								<xsl:choose>
									<xsl:when test="$show_names = 'true'">
										<strong><xsl:value-of select="@label" /></strong><br />
									</xsl:when>
									<xsl:otherwise>
										<xsl:choose>
											<xsl:when test="substring(@label, 1, 4) = 'Alle'">
												<strong><xsl:value-of select="@label" /></strong><br />
											</xsl:when>
											<xsl:otherwise>
												<strong>Person</strong><br />
											</xsl:otherwise>
										</xsl:choose>
									</xsl:otherwise>
								</xsl:choose>
								
								<!-- <span style="font-size:0.3em"><br /></span>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span> -->
							</td>
							<!-- <td>
								<xsl:value-of select="@label" /><br />
								<span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@description" /></span>
							</td> -->
							<td>
								<xsl:call-template name="render_orgroles" /><!-- deaktiviert -->
							</td>
							<xsl:if test="$show_implementation_type = 'true'">
								<td>
									<xsl:choose>
										<xsl:when test="descendant::sbnode/@implementation_type = 'SAPGUI'">
											✓
										</xsl:when>
										<xsl:when test="descendant::sbnode/@implementation_type = 'WEBDYNPRO'">
											
										</xsl:when>
										<xsl:otherwise>
											???
										</xsl:otherwise>
									</xsl:choose>
								</td>
								<td>
									<xsl:choose>
										<xsl:when test="descendant::sbnode/@implementation_type = 'WEBDYNPRO'">
											✓
										</xsl:when>
										<xsl:when test="descendant::sbnode/@implementation_type = 'SAPGUI'">
											
										</xsl:when>
										<xsl:otherwise>
											???
										</xsl:otherwise>
									</xsl:choose>
								</td>
							</xsl:if>
							<xsl:if test="$show_access = 'true'">
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@access = 'RESTRICTED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@access = 'EXPOSED'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@access = 'EXPOSED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@access = 'RESTRICTED'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							</xsl:if>
							<!-- <td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@damagepotential = 'HIGH' or descendant::sbnode/@damagepotential = 'VERY HIGH' or descendant::sbnode/@damagepotential = 'MEDIUM'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@damagepotential = 'LOW' or descendant::sbnode/@damagepotential = 'NONE'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@damagepotential = 'LOW' or descendant::sbnode/@damagepotential = 'NONE'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@damagepotential = 'HIGH' or descendant::sbnode/@damagepotential = 'VERY HIGH' or descendant::sbnode/@damagepotential = 'MEDIUM'">
										
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td> -->
							<xsl:if test="$show_requirements = 'true'">
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@perm_print = 'REQUIRED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_print = 'REQUESTED'">
										[✓]
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_print = 'NO'">
										Χ
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							<td>
								<xsl:choose>
									<xsl:when test="descendant::sbnode/@perm_datatrans = 'REQUIRED'">
										✓
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_datatrans = 'REQUESTED'">
										[✓]
									</xsl:when>
									<xsl:when test="descendant::sbnode/@perm_datatrans = 'NO'">
										Χ
									</xsl:when>
									<xsl:otherwise>
										???
									</xsl:otherwise>
								</xsl:choose>
							</td>
							</xsl:if>
						</tr>
						</xsl:if>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<tr><td colspan="6"><xsl:value-of select="$locale/sbSystem/texts/no_subobjects" /></td></tr>
				</xsl:otherwise>
			</xsl:choose>
			</tbody>
		</table>
		
	</xsl:template>
	
	
	<xsl:template name="render_orgroles">
		<xsl:if test="content[@mode='OrgRoles']/sbnode">
			<xsl:choose>
				<xsl:when test="$show_orgroles = 'true'">
					<ul>
					<xsl:for-each select="content[@mode='OrgRoles']/sbnode">
						<xsl:if test="children[@mode='gatherTechRoles']/sbnode">
						<li>
							<xsl:value-of select="@label" />
							<xsl:call-template name="render_techroles">
								<xsl:with-param name="iterations_remaining" select="$techrole_iterations" />
							</xsl:call-template>
						</li>
						</xsl:if>
					</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="content[@mode='OrgRoles']/sbnode">
						<xsl:if test="children[@mode='gatherTechRoles']/sbnode">
							<xsl:call-template name="render_techroles">
								<xsl:with-param name="iterations_remaining" select="$techrole_iterations" />
							</xsl:call-template>
						</xsl:if>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
			
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_techroles">
		<xsl:param name="iterations_remaining" />
		<xsl:if test="children[@mode='gatherTechRoles']/sbnode and number($iterations_remaining) > 0">
			<xsl:if test="$show_empty_techroles = 'true' or children[@mode='gatherTechRoles']/sbnode/children[@mode='gatherTechRoles']/sbnode">
			<ul>
			<xsl:for-each select="children[@mode='gatherTechRoles']/sbnode">
				<li>
					<xsl:value-of select="@label" /> [<xsl:value-of select="@name" />]
					<xsl:call-template name="render_techroles">
						<xsl:with-param name="iterations_remaining" select="$iterations_remaining - 1" />
					</xsl:call-template>
				</li>
				<!-- <span style="font-style: italic; font-size: 0.8em;"><xsl:value-of select="@label" /></span><br /> -->
			</xsl:for-each>
			</ul>
			</xsl:if>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="render_techroles2">
		<xsl:if test="children[@mode='debug']/sbnode">
		<table class="invisible" width="100%">
			<xsl:for-each select="children[@mode='debug']/sbnode">
				<tr>
					<xsl:if test="$damagepotential = 'true'">
					<td width="1%">
						<xsl:choose>
							<xsl:when test="@damagepotential = 'HIGH' or @damagepotential = 'VERY HIGH' or @damagepotential = 'MEDIUM'">
								A/B
							</xsl:when>
							<xsl:when test="@damagepotential = 'LOW' or @damagepotential = 'NONE'">
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
						<div style="font-size: 0.8em; white-space:pre-wrap;"><xsl:value-of select="@implementation" /></div>
					</td>
					<td width="33%">
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
				<xsl:when test="$names = 'true'">
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

</xsl:stylesheet>