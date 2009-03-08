<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

define('JCR_PROPERTY_', )

//------------------------------------------------------------------------------
/** TODO: complete this
*/
class sbCR_PropertyDefinition {
	
	protected $aPropertyInformation = array(
		// JCR attributes
		'PropertyName' => '',
		'RequiredType' => '',
		'isMultiple' => FALSE,
		'ValueConstraints' => array(),
		'isEditable' => TRUE,
		'isProtected' => FALSE,
		// sbCR attributes
		'isProtectedOnCreation' => FALSE,
		'LabelPath' => '',
		'InternalType' => '',
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crRepositoryStructure, $sNodeTypeName, $sPropertyName) {
		
		$this->crRepositoryStructure = $crRepositoryStructure;
		$this->aPropertyInformation = $crRepositoryStructure->getPropertyData($sNodeTypeName, $sPropertyName);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	*  Gets the default value(s) of the property.
	* @param 
	* @return 
	*/
	public function getDefaultValues() {
		return ($this->aPropertyInformation['DefaultValues']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Gets the required type of the property.
	* @param 
	* @return 
	*/
	public function getRequiredType()  {
		return ($this->aPropertyInformation['RequiredType']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Gets the array of constraint strings.
	* @param 
	* @return 
	*/
	public function getValueConstraints()  {
		// constraints are not visible right now
		// TODO: make constraints visible and implement custom constraints
		return (array());
	}
	
	//--------------------------------------------------------------------------
	/**
	* Reports whether this property can have multiple values.
	* @param 
	* @return 
	*/
	public function isMultiple() {
		// multiple values are currently not supported
		// TODO: implement multiple properties?
		return (FALSE);
	}
	
	
	
}

?>