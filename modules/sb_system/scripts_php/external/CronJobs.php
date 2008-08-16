#!/usr/bin/php -q

<?php

// INFORMATIONS
$written = "Fabian";
$version = "v1.1";
$name = "addcron.php";

// INPUT 
function read() {
    $fp=fopen("/dev/stdin", "r");
    $input=fgets($fp, 255);
    fclose($fp);
    return str_replace("\n", "", $input);
}

// TOUPPER Args
$arg = strtoupper($argv[1]);

// HELP
if ( $arg  == "HELP" ) {
 print("$name $version written by $written...\n\n");
 print("usage: $name add    (add a crontab to user)\n");
 print("       $name help   (show this screen, huh?)\n\n");
}

elseif ( $arg == "ADD" ) {

print("At which minute should the crontab run? Valid: 0-59,* Example: 15 or 15-25            : ");
 $min = read();
 str_replace(" ", "", $min);
 if ( $min == "" ) {
  $min = "*";
 } 

print("At which hour should the crontab run? Valid: 0-23,* Example: 23 or 11-13              : ");
 $hour = read();
 str_replace(" ", "", $hour);
 if ( $hour == "" ) {
  $hour = "*";
 }

print("On which day of the month should this crontab run? Valid: 1-31,* Example: 24 or 12-16 : ");
 $day = read();
 str_replace(" ", "", $day);
 if ( $day == "" ) {
  $day = "*";
 }

print("In which month should this crontab run? Valid: 1-12,* Example: 2 or 4-7               : ");
 $month = read();
 str_replace(" ", "", $month);
 if ( $month == "" ) {
  $month = "*";
 }
print("In which month should this crontab run? Valid: 1-12,* Example: 2 or 4-7               : ");
 $month = read();
 str_replace(" ", "", $month);
 if ( $month == "" ) {
  $month = "*";
 }

print("On which day of the week should this crontab run? Valid: 0-7,* Example: 6 or 2-5      : ");
 $week = read();
 str_replace(" ", "", $read);
 if ( $week == "" ) {
  $week = "*";
 }

print("Do you want to receive messages or errors from your script in an email?  Valid: y/n   : ");
 $mail = read();
 str_replace(" ", "", $mail);
 $mail = strtoupper($mail);
 if ( $mail == "" ) {
  $mail = "> /dev/null 2>&1";
 }
 elseif ( $mail == "N" ) {
  $mail = "> /dev/null 2>&1";
 }
 else { $mail = ""; }

print("The absolut path to your executable? Example: /home/$written/backup.sh                  : ");
 $prog = read();
 str_replace(" ", "", $prog);
 if ( $prog == "" ) {
  print("\nERROR! NO PROGRAM PATH GIVEN...\n\n");
 }
 else {
  exec("crontab -l > temp.cron");
  print("\nOkay, going to install the following crontab:\n\n");
  print("$min $hour $day $month $week $prog $mail\n\n");
  exec("echo \"$min $hour $day $month $week $prog $mail\" >> temp.cron");
  exec("crontab temp.cron");
  exec("rm temp.cron");
  print("... ADDED!\n\n"); 
 }
}

else {
 print("$name $version written by $written...\n\n");
 print("usage: $name add    (add a crontab to user)\n");
 print("       $name help   (show this screen, huh?)\n\n");
}

?>