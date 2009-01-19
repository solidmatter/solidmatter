<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** TODO: complete this
*/
class sbCR_PropertyDefinition {
	
	protected $aPropertyInformation = array(
		'PropertyName' => '',
		'RequiredType' => NULL,
		'isMultiple' => FALSE,
		'ValueConstraints' => array(),
		'LabelPath' => '',
		'isEditable' => TRUE,
		'isProtected' => FALSE,
		'isProtectedOnCreation' => FALSE,
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crRepositoryStructure, $sNodeTypeName) {
		
//		$this->crRepositoryStructure = $crRepositoryStructure;
//		$this->aNodeTypeInformation['NodeTypeName'] = $sNodeTypeName;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	*  Gets the default value(s) of the property.
	* @param 
	* @return 
	*/
	public function getDefaultValues() {
		return ($this->aNodeTypeInformation['DefaultValues']);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Gets the required type of the property.
	* @param 
	* @return 
	*/
	public function getRequiredType()  {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Gets the array of constraint strings.
	* @param 
	* @return 
	*/
	public function getValueConstraints()  {
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Reports whether this property can have multiple values.
	* @param 
	* @return 
	*/
	public function isMultiple() {
		
	}
	
	
	
}

?>