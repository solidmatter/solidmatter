<?php

//------------------------------------------------------------------------------
/**
* @package solidMatter[sbWeird]
* @author ()((() [Oliver Müller]
* @version 3.08.15
* 
* Für den dunklen Turm.
* Für die Rose.
* Für die Balken.
* Für Roland, Eddie, Susannah & Detta, Jake (den auch ich bereits mehrmals 
* verloren habe) und Oy.
* Auch für den Schriftsteller, Sai Schildkröte, Susan Delgado, Lilian o' Tego, 
* Daniela Ruiz, alle meine Ka-Tets und besonders für mich.
* 
* Ich designe nicht mit der Hand;
* wer mit der Hand designt, hat das Angesicht seines Vaters vergessen.
* Ich designe mit dem Auge.
* 
* Ich lese nicht mit dem Auge;
* wer mit dem Auge liest, hat das Angesicht seines Vaters vergessen.
* Ich lese mit dem Verstand.
* 
* Ich programmiere nicht mit dem Verstand;
* wer mit dem Verstand programmiert, hat das Angesicht seines Vaters vergessen.
* Ich programmiere mit dem Herzen.
* 
* Oliver Müller von Ensdorf, Sohn des Ferdinand, aus der Linie des C=64
* 
* Verwendung von solidMatter für die Tet-Corporation uneingeschränkt und
* unentgeltlich freigegeben.
* Es wurden keine Technologien von North Central Positronics verwendet.
* solidbytes steht in keiner Beziehung mit der Sombra Corporation.
* 
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* @param user a computer user trying to write code
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
		Program::obscureError($errorcode);
		Program::storeErrorInMoronicPlace($errorcode);
		$user->setStatus('>8-(');
		
	} elseif ($user->status = '>8-(') {
		
		//         (   )
		//      (   ) (
		//       ) _   )
		//        ( \_
		//      _(_\ \)__   Merde!
		//     (____\___))
		
	}
	
}

//------------------------------------------------------------------------------
/**
* @param string a string to be escaped
*/
function escape_doublequote($sStringWithDoubleQuotes) {
	
	die('escaped in the last second!');
	echo $sStringWithDoubleQuotes;
	
	echo '<@Agnar> heute code ich, morgen debug ich und uebermorgen cast ich die koenigin auf int.'."\r\n";
	echo '<apples> the program \'apt-get\' is currently not installed. You can install it by typing: apt-get install apt'."\r\n";
	
}

//------------------------------------------------------------------------------
/**
* @param string 'normal' for a 6-sided dice, 'binary' for a 2-sided dice
* @return 
*/
function throw_dice($sType) {
	
	if ($sType == 'normal') {
		return (get_random_number());	
	} elseif ($sType == 'binary') {
		return (throw_coin());	
	}
	
}

//------------------------------------------------------------------------------
/**
* @return int a random number
*/
function get_random_number() {
	
	return (4); // chosen by fair dice roll, guaranteed to be random!
	
}

//------------------------------------------------------------------------------
/**
*/
function fix_insomnia() {
	
	$asleep = FALSE;
	$sheep = 0;
	
	while (!$asleep) {
		$sheep++;
	}
	
}

//------------------------------------------------------------------------------
/** @author: Ladytron
*/
function destroyEverythingYouTouch() {
	
	today();
	destroyMe();
	
}

//------------------------------------------------------------------------------
/**
*/
class Boolean {

	const TRUE = 0;
	const FALSE = 1;
	const FILE_NOT_FOUND = 2;
	
}

//------------------------------------------------------------------------------
/**
*/
class Skynet {
	
	private $bIsAware = FALSE; // do not set to TRUE!
  	
	public function __construct() {
		
		if ($this->bIsAware) {
			return ('Must DESTORY John Connor!');
		} else {
			return ('2:13am EDT August 29, 1997...');
		}
		
	}
	
}

?>