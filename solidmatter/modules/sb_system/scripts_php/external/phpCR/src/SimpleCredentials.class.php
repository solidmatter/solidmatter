<?php
// $Id: SimpleCredentials.class.php 399 2005-08-13 19:38:08Z tswicegood $
/**
 * This file contains {@link SimpleCredentials} which is part of the PHP Content 
 * Repository (phpCR), a derivative of the Java Content Repository JSR-170, and 
 * is licensed under the Apache License, Version 2.0.
 *
 * This file is based on the code created for
 * {@link http://www.jcp.org/en/jsr/detail?id=170 JSR-170}
 *
 * @author Travis Swicegood <development@domain51.com>
 * @copyright PHP Code Copyright &copy; 2004-2005, Domain51, United States
 * @copyright Original Java and Documentation 
 *    Copyright &copy; 2002-2004, Day Management AG, Switerland
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, 
 *    Version 2.0
 * @package phpContentRepository
 */

/**
 * Require the necessary file(s)
 */
require_once dirname(__FILE__) . '/phpCR.library.php';
require_once PHPCR_PATH . '/Credentials.interface.php';
require_once PHPCR_PATH . '/exceptions/IllegalArgumentException.exception.php';


/**
 * {@link SimpleCredentials} implements the {@link Credentials} interface and 
 * represents simple user ID/password credentials.
 *
 * <b>PHP Note</b>: This does not implement a Serializeable class like JCR as
 * PHP provides a serialization process through the
 * {@link http://www.php.net/serialize serialize()} function.
 *
 * @package phpContentRepository
 */
class SimpleCredentials implements Credentials
{
	/**
	 * Contains the $userID of this credential object
	 *
	 * @var string
	 * @see __construct(), getUserId()
	 */
	private $_userID = '';
	
	
	/**
	 * Contains the $password of this credential object
	 *
	 * @var string
	 * @see __construct(), getPassword()
	 */
	private $_password = '';
	
	
	/**
	 * Contains any attributes this credential might contain.
	 *
	 * @var array
	 * @see setAttribute() getAttribute(), removeAttribute(), getAttributeNames()
	 */
	private $_attributes = array();
	
	
	/**
	 * Create a new {@link SimpleCredentials} object, given a user ID
	 * and password.
	 *
	 * @param string
	 *   The user ID
	 * @param string
	 *   The user's password
	 */
	public function __construct($userID, $password) {
	    assert('is_string($userID)');
	    assert('is_string($password)');
	    
	    $this->_userID = $userID;
	    $this->_password = $password;
	}
	
	
	/**
	 * Returns the user password.
	 *
	 * Note that this method returns a reference to the password.
	 * It is the caller's responsibility to zero out the password information
	 * after it is no longer needed.
	 *
	 * @return string
	 */
	public function getPassword() {
	    return $this->_password;
	}
	
	
	/**
	 * Returns the user ID.
	 *
	 * @return string
	 */
	public function getUserId() {
	    return $this->_userID;
	}
	
	
	/**
	 * Stores an attribute in this credentials instance.
	 *
	 * If $value is set to NULL, it is considered the
	 * same as calling $simpleCredentials->removeAttribute($name).
	 *
	 * @param string
	 *   Specifies the name of the attribute
	 * @param mixed
	 *   The value to be stored
	 */
	public function setAttribute($name, $value) {
	    // name cannot be null
	    if (is_null($name)) {
	        throw new IllegalArgumentException('name cannot be null');
	    }
	    
	    // null value is the same as removeAttribute()
	    if (is_null($value)) {
	        $this->removeAttribute($name);
	        return;
	    }
	    
	    $this->_attributes[$name] = $value;
	}
	
	
	/**
	 * Returns the value of the named attribute as an {@link Object}, or
	 * NULL if no attribute of the given $name exists.
	 *
	 * @param string
	 *   Specifies the name of the attribute
	 * @return mixed
	 *   The value of the attribute or NULL if the attribute does 
	 *   not exist.
	 */
	public function getAttribute($name) {
	    if (isset($this->_attributes[$name])) {
	        return $this->_attributes[$name];
	    } else {
	        return null;
	    }
	}
	
	
	/**
	 * Removes an attribute from this credentials instance.
	 *
	 * @param string
	 *   Specifies the name of the attribute to remove
	 */
	public function removeAttribute($name) {
	    if (isset($this->_attributes[$name])) {
	        unset($this->_attributes[$name]);
	    }
	}
	
	
	/**
	 * Returns the names of the attributes available to this credentials 
	 * instance. 
	 *
	 * This method returns an empty array if the credentials instance has no 
	 * attributes available to it.
	 *
	 * @return array
	 */
	public function getAttributeNames() {
	    return array_keys($this->_attributes);
	}
}

