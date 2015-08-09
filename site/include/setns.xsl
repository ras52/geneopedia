<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:d="http://genmine.com/data">

  
  <xsl:template match="@*|node()">
    <xsl:copy>
      <xsl:apply-templates select="@*|node()"/>
    </xsl:copy>
  </xsl:template>
 
  <xsl:template xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" 
                match="rdf:*">
    <xsl:element name="rdf:{local-name(.)}">
      <xsl:apply-templates select="@*|node()"/>
    </xsl:element>
  </xsl:template>
 
  <xsl:template xmlns:dcterms="http://purl.org/dc/terms/" 
                match="dcterms:*">
    <xsl:element name="dcterms:{local-name(.)}">
      <xsl:apply-templates select="@*|node()"/>
    </xsl:element>
  </xsl:template>

  <xsl:template xmlns:wdrs="http://www.w3.org/2007/05/powder-s#"
                match="wdrs:*">
    <xsl:element name="wdrs:{local-name(.)}">
      <xsl:apply-templates select="@*|node()"/>
    </xsl:element>
  </xsl:template>
</xsl:stylesheet>
