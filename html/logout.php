<?php
/*
   This code is part of GOsa (https://gosa.gonicus.de)
   Copyright (C) 2003  Cajus Pollmeier

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/* Basic setup, remove eventually registered sessions */
require_once ("../include/php_setup.inc");
require_once ("functions.inc");
header("Content-type: text/html; charset=UTF-8");
get_dir_list("$BASE_DIR/plugins");
session_start ();

/* This message will be set if the logout was caused by main.php- 
 *  if inactivity of user was > than the defined value in gosa.conf (session_lifetime)
 * This Message will be displayed in the logout template.
 * To force displaying the logout template, we have to unset the session.
 */
if(isset($_SESSION['kick_message'])){
  $kmsg = $_SESSION['kick_message'];
  @session_unset();
}else{
  $kmsg = "";
}

/* Do logout-logging and destroy session */
if (!isset($_SESSION["config"])){
  /* Language setup */
  if ($config->data['MAIN']['LANG'] == ""){
    $lang= get_browser_language();
  } else {
    $lang= $config->data['MAIN']['LANG'];
  }
  $lang.=".UTF-8";
  putenv("LANGUAGE=");
  putenv("LANG=$lang");
  setlocale(LC_ALL, $lang);
  $GLOBALS['t_language']= $lang;
  $GLOBALS['t_gettext_message_dir'] = $BASE_DIR.'/locale/';

  /* Set the text domain as 'messages' */
  $domain = 'messages';
  bindtextdomain($domain, "$BASE_DIR/locale");
  textdomain($domain);
    
  /* Set template compile directory */
  $smarty= new smarty();
  if (isset ($config->data['MAIN']['COMPILE'])){
    $smarty->compile_dir= $config->data['MAIN']['COMPILE'];
  } else {
    $smarty->compile_dir= '/var/spool/gosa/';
  }
  $smarty->assign("kmsg",$kmsg);
  $smarty->display (get_template_path('headers.tpl'));
  $smarty->display (get_template_path('logout.tpl'));
  @session_destroy ();
  exit;
}
$ui= $_SESSION["ui"];
$config= $_SESSION["config"];
gosa_log ("User \"".$ui->username."\" logged out");

/* Remove all locks of this user */
del_user_locks($ui->dn);

@session_destroy ();

/* Go back to the base via header */
header ("Location: index.php");

// vim:tabstop=2:expandtab:shiftwidth=2:filetype=php:syntax:ruler:
?>
</html>
