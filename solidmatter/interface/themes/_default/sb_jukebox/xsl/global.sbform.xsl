<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	version="1.0" 
	exclude-result-prefixes="html sbform" 
	exclude-element-prefixes="html sbform" 
	xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sbform="http://www.solidbytes.net/sbform"
	xmlns:dyn="http://exslt.org/dynamic" extension-element-prefixes="dyn">

	<xsl:output 
		method="html"
		encoding="UTF-8"
		standalone="yes"
		indent="yes"
	/>
	
	<xsl:template name="sbform" match="//sbform">
		<xsl:param name="noLabel" select="false" />
		<form class="default" action="{@action}" method="post" enctype="multipart/form-data" accept-charset="utf-8">
			<table class="default" summary="form">
				<tr><th colspan="2"><xsl:value-of select="dyn:evaluate(@label)" /></th></tr>
				<xsl:apply-templates select="*" mode="complete" />
				<xsl:if test="@errorlabel">
					<tr><td></td><td><span class="formerror"><xsl:value-of select="dyn:evaluate(@errorlabel)" /></span></td></tr>
				</xsl:if>
			</table>
		</form>
	</xsl:template>
	
	<!-- hidden -->
	<xsl:template match="sbinput[@type='hidden']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='hidden']" mode="inputonly">
		<input type="hidden" value="{@value}" name="{@name}" id="{@name}"></input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- string -->
	<xsl:template match="sbinput[@type='string']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- email -->
	<xsl:template match="sbinput[@type='email']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='email']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- password -->
	<xsl:template match="sbinput[@type='password']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- text -->
	<xsl:template match="sbinput[@type='text']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- urlsafe -->
	<xsl:template match="sbinput[@type='urlsafe']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- integer -->
	<xsl:template match="sbinput[@type='integer']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='integer']" mode="inputonly">
		<input type="text" size="{@size}" maxlength="{@maxlength}" value="{@value}" name="{@name}" id="{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- select -->
	<xsl:template match="sbinput[@type='select']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='select']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="{@size}" name="{@name}" id="{@name}">
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
					<xsl:otherwise>
						<xsl:value-of select="@text" />
					</xsl:otherwise>
					</xsl:choose>
				</option>
			</xsl:for-each>
		</select>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- users -->
	<xsl:template match="sbinput[@type='users']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
	</xsl:template>
	
	<!-- nodeselector -->
	<xsl:template match="sbinput[@type='nodeselector']" mode="complete">
		<tr>
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
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
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
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
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
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
			<td width="30%"><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td width="70%">
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='relation']" mode="inputonly">
		<xsl:variable name="value" select="@value" />
		<select size="1" name="type_{@name}" id="type_{@name}">
			<xsl:for-each select="option">
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
			</xsl:for-each>
		</select>
		<input type="text" size="50" maxlength="250" value="{@value}" name="target_{@name}" id="target_{@name}">
			<xsl:if test="@disabled"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:if>
			<xsl:if test="@errorlabel"><xsl:attribute name="class">formerror</xsl:attribute></xsl:if>
		</input>
		<div id="suggest_{@name}" class="ac_suggestions" style="display:none;"></div>
		<xsl:if test="@errorlabel"><span class="formerror"><xsl:value-of select="concat(' ', dyn:evaluate(@errorlabel))" /></span></xsl:if>
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
								return ('target_<xsl:value-of select="@name"/>=' + oTextInput.value + '&amp;' + ' type_<xsl:value-of select="@name"/>=' + oType.value);
							}
						}
					)
				}
			);
		</script>
	</xsl:template>
	
	<!-- checkbox -->
	<xsl:template match="sbinput[@type='checkbox']" mode="complete">
		<tr>
			<td><label for="{@name}"><xsl:value-of select="dyn:evaluate(@label)" /></label></td>
			<td>
				<xsl:apply-templates select="." mode="inputonly" />
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="sbinput[@type='checkbox']" mode="inputonly">
		<input type="checkbox" name="{@name}" id="{@name}">
			<xsl:if test="@value='TRUE'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
		</input>
	</xsl:template>
	
	<!-- submit -->
	<xsl:template match="submit" mode="complete">
		<tr>
			<td></td>
			<td><xsl:apply-templates select="." mode="inputonly" /></td>
		</tr>
	</xsl:template>
	<xsl:template match="submit" mode="inputonly">
		<input type="submit" class="button" name="{@value}" value="{dyn:evaluate(@label)}" />
	</xsl:template>
	
	
</xsl:stylesheet>