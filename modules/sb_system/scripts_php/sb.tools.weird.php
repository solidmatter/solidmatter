<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author ()((() [Oliver Müller]
* 	@version 0.50.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function change_programmer_status($user) {
	
	if ($user->status == 'tired') {
		
		$cup = $user->cookCoffee();
		$user->drink($cup);
		$user->setStatus('less_tired');
		
	} elseif ($user->status == 'less_tired') {
		
		$user->writeCode();
		$user->setStatus('tired');
	
	} elseif ($user->status == 'insomniac') {
		
		fix_insomnia();
		
	} elseif ($user->status == 'bored') {
		
		$user->writeUselessCode();
		$user->doSomethingUntilStatusChange();
		
	} elseif ($user->status == ':-)') {
		
		$errorcode = Program::produceIncomprehensibleError();
		Program::storeErrorInMoronicPlace($errorcode);
		Program::obscureError($errorcode);
		$user->setStatus('>8-(');
		
	}
	
}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function escape_doublequote() {
	
	die('escaped in the last second!');
	echo '<@Agnar> heute code ich, morgen debug ich und uebermorgen cast ich die koenigin auf int.'."\r\n";
	echo '<apples> the program \'apt-get\' is currently not installed. You can install it by typing: apt-get install apt'."\r\n";
	
}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function throw_dice($type) {
	
	if ($type == 'normal') {
		return (get_random_number());	
	} elseif ($type == 'binary') {
		return (throw_coin());	
	}

}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function get_random_number() {
	
	return (4); // chosen by fair dice roll, guaranteed to be random!
	
}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function fix_insomnia() {
	
	$asleep = FALSE;
	$sheep = 0;
	
	while (!$asleep) {
		$sheep++;
	}
	
}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
class Boolean {

	const TRUE = 0;
	const FALSE = 1;
	const FILE_NOT_FOUND = 2;
	
}

?>