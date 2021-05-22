<?php

/*
* CDSS SERVICES
* Version 0.0.1 October 2020
*
* Processes AJAX calls from CDSS clients
*
* Required POST parameter: 'request', others as needed by request.
*/

/*
* Output all PHP errors to the browser. Comment out when ready for production.
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$module = new Yale\cdss\CDSS();

$request = $_POST['request']; // always passed

if ( $request === "save-rbase"  ) exit(save_rbase());

else exit("go away");

function save_rbase() {
   global $module;

   $cdss_rbase_text = $_POST['cdss_rbase_text'];
   $crc_before = crc32($cdss_rbase_text);
   $module->setProjectSetting('cdss-rbase', $cdss_rbase_text);

   // validate
   $cdss_rbase_text = $module->getProjectSetting('cdss-rbase');
   $crc_after = crc32($cdss_rbase_text);

   return "before: {$crc_before}, after: {$crc_after}";

}

?>

