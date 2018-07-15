<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="yes"
	/>
	
	
	
	<!-- match formrenderer -->
	<xsl:template name="sbform" match="//sbform">
		<xsl:param name="noLabel" />
		<xsl:param name="label" />
		<form class="default" action="{@action}" method="post" enctype="multipart/form-data" accept-charset="utf-8">
			<xsl:if test="@id"><xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute></xsl:if>
			<table class="default">
				<!-- TODO: configuring label display does not work -->
				<!--<xsl:choose>
					<xsl:when test="$label">
						<tr><th colspan="2"><xsl:call-template name="getLocalizedLabel" /></th></tr>
					</xsl:when>
					<xsl:when test="$noLabel">
						
					</xsl:when>
					<xsl:when test="@label = ''">
						
					</xsl:when>
					<xsl:otherwise>
						
					</xsl:otherwise>
				</xsl:choose>-->
				<thead>
					<tr>
						<th colspan="2">
							<xsl:call-template name="localize">
								<xsl:with-param name="label" select="@label" />
							</xsl:call-template>
							<xsl:apply-templates select="*[@type='hidden']" mode="complete" />
						</th>
					</tr>
				</thead>
				<tbody>
				<xsl:if test="@errorlabel">
					<tr>
						<td></td>
						<td><span class="formerror"><xsl:call-template name="localize"><xsl:with-param name="label" select="@errorlabel" /></xsl:call-template></span></td>
					</tr>
				</xsl:if>
				<xsl:apply-templates select="*[@type!='hidden']" mode="complete" />
				</tbody>
				<tfoot>
				<xsl:apply-templates select="submit" mode="complete" />
				</tfoot>
			</table>
		</form>
	</xsl:template>
	
	<!--<xsl:template match="sbform:group">
		<tr>
			<th class="th2" colspan="2"><legend><xsl:value-of select="@label" /></legend></th>
		</tr>
		<xsl:apply-templates select="sbform:*" />
	</xsl:template>-->
	
	
	
	<xsl:template name="renderLabel">
		<label for="{@name}">
			<xsl:if test="@required = 'true'">
				<xsl:attribute name="class">required</xsl:attribute>
			</xsl:if>
			<xsl:call-template name="localize">
				<xsl:with-param name="label" select="@label" />
			</xsl:call-template>
		</label>
	</xsl:template>
	
	<xsl:template name="renderErrorLabel">
		<xsl:if test="@errorlabel">
			<span class="formerror">
				<xsl:value-of select="' '" />
				<xsl:call-template name="localize">
					<xsl:with-param name="label" select="@errorlabel" />
				</xsl:call-template>
			</span>
		</xsl:if>
	</xsl:template>
	
	
	
	<!-- hidden -->
	<xsl:template match="sbinput[@type='hidden']" mode="complete">
		<xsl:apply-templates select="." mode="inputonly" />
	</xsl:template>
	<xsl:template match="sbinput[@type='hidden']" mode="inputonly">
		<input type="hidden" value="{@value}" name="{@name}" id="{@name}">
			
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- string -->
	<xsl:template match="sbinput[@type='string']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='string']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- email -->
	<xsl:template match="sbinput[@type='email']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='email']" mode="inputonly">
		<input type="email" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- datetime -->
	<xsl:template match="sbinput[@type='datetime']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='datetime']" mode="inputonly">
		<xsl:variable name="date"><xsl:value-of select="substring(@value, 0, 11)" /></xsl:variable>
		<xsl:variable name="time"><xsl:value-of select="substring(@value, 12, 5)" /></xsl:variable>
		<input type="date" size="{@size}" maxlength="{@maxlength}" value="{$date}" name="{@name}_date" id="{@name}_date">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<input type="time" size="{@size}" maxlength="{@maxlength}" value="{$time}" name="{@name}_time" id="{@name}_time">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
<!-- 		<xsl:value-of select="$date" /> - '<xsl:value-of select="$time" />' -->
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- password -->
	<xsl:template match="sbinput[@type='password']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='password']" mode="inputonly">
		<input type="password" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- text -->
	<xsl:template match="sbinput[@type='text']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='text']" mode="inputonly">
		<textarea cols="{@columns}" rows="{@rows}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:value-of select="@value" />
		</textarea>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- urlsafe -->
	<xsl:template match="sbinput[@type='urlsafe']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
		<xsl:template match="sbinput[@type='urlsafe']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@name = 'name'">
			<script language="Javascript" type="text/javascript">
				oLabel = document.getElementById('label');
				oName = document.getElementById('name');
				oLabel.addEventListener('change', function (event) {
					if (oName.value == '') {
						oName.value = oLabel.value.replace(/([^a-z0-9]+)/gi, '_');
					}
				});
			</script>
		</xsl:if>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- hexcolor -->
	<xsl:template match="sbinput[@type='color']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='color']" mode="inputonly">
		<input type="color" size="6" maxlength="6" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- integer -->
	<xsl:template match="sbinput[@type='integer']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='integer']" mode="inputonly">
		<input type="number" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@minvalue"><xsl:attribute name="min"><xsl:value-of select="@minvalue" /></xsl:attribute></xsl:if>
			<xsl:if test="@maxvalue"><xsl:attribute name="max"><xsl:value-of select="@maxvalue" /></xsl:attribute></xsl:if>
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- select -->
	<xsl:template match="sbinput[@type='select']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='select']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="{@size}" name="{@name}" id="{@name}">
			<xsl:if test="@multiple = 'TRUE'"><xsl:attribute name="multiple">multiple</xsl:attribute></xsl:if>
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:for-each select="option">
				<option value="{@value}">
					<xsl:if test="@value = $value">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:choose>
					<xsl:when test="@label">
						<xsl:value-of select="dyn:evaluate(@label)"/>
					</xsl:when>
					<xsl:when test="dyn:evaluate(concat(../@label, '_options/option[@id=&quot;', @value, '&quot;]'))">
						<xsl:value-of select="dyn:evaluate(concat(../@label, '_options/option[@id=&quot;', @value, '&quot;]'))"/>
					</xsl:when>
					<xsl:when test="dyn:evaluate(concat(../@label, '/../options_global/option[@id=&quot;', @value, '&quot;]'))">
						<xsl:value-of select="dyn:evaluate(concat(../@label, '/../options_global/option[@id=&quot;', @value, '&quot;]'))"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
<!-- 					<xsl:value-of select="concat(../@label, '/../../options_global/option[@id=&quot;', @value, '&quot;]')"/> -->
				</option>
			</xsl:for-each>
		</select>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- users -->
	<xsl:template match="sbinput[@type='users']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='users']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<xsl:variable name="includeself" select="@includeself" />
		<select size="{@size}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:for-each select="option">
				<xsl:if test="$includeself='TRUE' or @value != /response/metadata/system/userid">
				<option value="{@value}">
					<xsl:if test="@value = $value">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:choose>
					<xsl:when test="@label">
						<xsl:value-of select="dyn:evaluate(@label)"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
				</option>
				</xsl:if>
			</xsl:for-each>
		</select>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- nodeselector -->
	<xsl:template match="sbinput[@type='nodeselector']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='nodeselector']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="{@size}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<option value=""></option>
			<xsl:call-template name="nodeselector_slave">
				<xsl:with-param name="prefix" select="''" />
				<xsl:with-param name="value" select="$value" />
			</xsl:call-template>
		</select>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	<xsl:template name="nodeselector_slave">
		<xsl:param name="prefix" />
		<xsl:param name="value" />
		<xsl:for-each select="sbnode">
			<option value="{@uuid}" class="type {@displaytype}">
				<xsl:if test="@uuid = $value">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>
				<xsl:value-of select="concat($prefix, @label)" />
			</option>
			<xsl:if test="sbnode">
				<xsl:call-template name="nodeselector_slave">
					<xsl:with-param name="prefix" select="concat($prefix, '&#160;&#160;&#160;')" />
					<xsl:with-param name="value" select="$value" />
				</xsl:call-template>
			</xsl:if>
		</xsl:for-each>
	</xsl:template>
	
	
	
	<!-- autocomplete -->
	<xsl:template match="sbinput[@type='autocomplete']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='autocomplete']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<div id="suggest_{@name}" class="ac_suggestions" style="display:none;"></div>
		<xsl:call-template name="renderErrorLabel" />
		<script language="Javascript" type="text/javascript">
			Event.observe(
				window,
				'load',
				function() {
					new Ajax.Autocompleter(
						'<xsl:value-of select="@name"/>',
						'suggest_<xsl:value-of select="@name"/>',
						'<xsl:value-of select="@url"/>',
						{ minChars: <xsl:value-of select="@minchars"/> }
					)
				}
			);
		</script>
	</xsl:template>
	
	
	
	<!-- relation -->
	<xsl:template match="sbinput[@type='relation']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='relation']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<!-- TODO: on change of relation type clear traget data -->
		<select size="1" name="type_{@name}" id="type_{@name}">
			<xsl:for-each select="option">
				<xsl:sort select="@text" />
				<option value="{@value}">
					<xsl:if test="@value = $value">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:choose>
<!-- 					<xsl:when test="@label"> -->
<!-- 						<xsl:value-of select="dyn:evaluate(@label)"/> -->
<!-- 					</xsl:when> -->
					<xsl:when test="dyn:evaluate(concat('$locale//relations/relation[@id=&quot;', @value, '&quot;]'))">
						<xsl:value-of select="dyn:evaluate(concat('$locale//relations/relation[@id=&quot;', @value, '&quot;]'))"/>...
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
<!-- 					<xsl:value-of select="concat('$locale//relations/relation[id=&quot;', @value, '&quot;]')"/> -->
				</option>
			</xsl:for-each>
		</select>
		<input type="hidden" name="target_uuid_{@name}" id="target_uuid_{@name}"/>
		<input type="text" size="50" maxlength="250" value="{@value}" name="target_{@name}" id="target_{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<div id="suggest_{@name}" class="ac_suggestions" style="display:none;"></div>
		<xsl:call-template name="renderErrorLabel" />
		<!-- TODO: somehow disable form submit button if data is inconsistent (uuid is empty) -->
		<script language="Javascript" type="text/javascript">
			Event.observe(
				window,
				'load',
				function() {
					var oType = $('type_<xsl:value-of select="@name"/>');
					new Ajax.Autocompleter(
						'target_<xsl:value-of select="@name"/>',
						'suggest_<xsl:value-of select="@name"/>',
						'<xsl:value-of select="@url"/>',
						{ 
							minChars: <xsl:value-of select="@minchars"/>, 
							//parameters: 'type_<xsl:value-of select="@name"/>='+oType.value 
							callback: function(oTextInput) {
								$('target_uuid_<xsl:value-of select="@name"/>').value = '';
								return ('target_<xsl:value-of select="@name"/>=' + oTextInput.value + '&amp;' + ' type_<xsl:value-of select="@name"/>=' + oType.value);
							},
							updateElement: function(oTextInput) {
								$('target_uuid_<xsl:value-of select="@name"/>').value = oTextInput.firstChild.firstChild.data;
								$('target_<xsl:value-of select="@name"/>').value = oTextInput.lastChild.firstChild.data;
							}
						}
					)
				}
			);
		</script>
	</xsl:template>
	
	
	
	<!-- codeeditor -->
	<xsl:template match="sbinput[@type='codeeditor']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='codeeditor']" mode="inputonly">
		<textarea style="height: 350px; width: 100%;" maxlength="{@maxlength}" name="{@name}" id="{@name}">
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
			<xsl:value-of select="@value" />
		</textarea>
		<script language="Javascript" type="text/javascript">
			editAreaLoader.init({
				id: "<xsl:value-of select="@name" />"	// id of the textarea to transform		
				,start_highlight: true	// if start with highlight
				,allow_resize: "no"
				,allow_toggle: false
				,language: "en"
				,syntax: "<xsl:value-of select="@syntax" />"
			});
		</script>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- checkbox -->
	<xsl:template match="sbinput[@type='checkbox']" mode="complete">
		<tr>
			<td width="30%"><xsl:call-template name="renderLabel" /></td>
			<td width="70%" style="text-align:left;">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='checkbox']" mode="inputonly">
		<xsl:choose>
		<xsl:when test="OLD">
			<input type="checkbox" name="{@name}" id="{@name}">
				<xsl:attribute name="title">
					<xsl:choose>
						<xsl:when test="substring(@label, 1, 1) = '$'">
							<xsl:value-of select="dyn:evaluate(@label)" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="@label" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
				<xsl:if test="@value='TRUE'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</input>
		</xsl:when>
		<xsl:otherwise>
			<div class="fancy_checkbox">
				<input type="checkbox" name="{@name}" id="{@name}">
					<xsl:if test="@value='TRUE'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
					<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
				</input>
				<label for="{@name}">
					<xsl:attribute name="title">
						<xsl:choose>
							<xsl:when test="substring(@label, 1, 1) = '$'">
								<xsl:value-of select="dyn:evaluate(@label)" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="@label" />
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				✖</label>
<!-- 				<label for="{@name}">✓</label> -->
			</div>
		</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	
	
	<!-- fileupload -->
	<xsl:template match="sbinput[@type='fileupload']" mode="complete">
		<tr>
			<td><xsl:call-template name="renderLabel" /></td>
			<td>
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='fileupload']" mode="inputonly">
		<input type="file" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
		</input>
		<xsl:call-template name="renderErrorLabel" />
	</xsl:template>
	
	
	
	<!-- multifileupload -->
	<xsl:template match="sbinput[@type='multifileupload']" mode="complete">
		<tr>
			<td><xsl:call-template name="renderLabel" /></td>
			<td>
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='multifileupload']" mode="inputonly">
		<div id="fileInputFrame"><input type="file" /></div>
		<div id="attachments" style="padding: 5px;">
			<!--<p>attached files will be shown here</p>-->
			<ul id="attachFileList" class="itemlist">
			</ul>
		</div>
		<script type="text/javascript" language="javascript">
			
			var sCheck = '';
			
			//------------------------------------------------------------------
			// 
			// note: thx to wooster for helping out with some stuff
			//
			function addFile() {
				
				// check if file changed
				var elemSlot = document.getElementById("fileInputFrame").firstChild;
				if (elemSlot.value == '' || elemSlot.value == sCheck) {
					return (false);
				}
				
				var elemList = document.getElementById("attachFileList");
				
				var elemEntry = document.createElement("li");
				
				elemSlot.name = "<xsl:value-of select="@name" />[]";
				elemSlot.style.display = "none";
				elemEntry.appendChild(elemSlot);
				
				var elemRemove = document.createElement('button');
				elemRemove.appendChild(document.createTextNode('X'));
				elemRemove.onclick = function() {
					elemEntry.removeChild(elemSlot);
					elemList.removeChild(elemEntry);
					li.parentNode.removeChild(li);
				};
				elemEntry.appendChild(elemRemove);
				
				var elemText = document.createTextNode(' ' + elemSlot.value);
				elemEntry.appendChild(elemText);
				
				sCheck = elemSlot.value;
			
				var elemNewSlot = document.createElement("input");
				elemNewSlot.type = "file";
				elemNewSlot.setAttribute("onclick", "addFile()");
				document.getElementById("fileInputFrame").appendChild(elemNewSlot);
				
				elemList.appendChild(elemEntry);
				
			}
			
			window.setInterval('addFile()', 20);
			
		</script>
	</xsl:template>
	
	
	
	<!-- submit -->
	<xsl:template match="submit" mode="complete">
		<tr class="lastline">
			<td width="30%"></td>
			<td width="70%"><xsl:apply-templates select="." mode="inputonly" /></td>
		</tr>
	</xsl:template>
	<xsl:template match="submit" mode="inputonly">
		<input type="submit" class="button" name="{@value}">
			<xsl:attribute name="value">
				<xsl:call-template name="localize">
					<xsl:with-param name="label" select="@label" />
				</xsl:call-template>
			</xsl:attribute>
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
		</input>
	</xsl:template>
	
	
</xsl:stylesheet>