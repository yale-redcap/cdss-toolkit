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
elseif ( $request === "get-cdss-settings"  ) exit(get_cdss_settings());
elseif ( $request === "save-metadata"  ) exit(save_metadata());

else exit("go away");

function save_metadata() {
   global $module;

   $metaclass = $_POST['metaclass'];
   $key = str_replace("_", "-", $metaclass);
   $data = json_encode($_POST['data']);

   $crc_before = crc32($data);
   $module->setProjectSetting($key, $data);

   // validate
   $data = $module->getProjectSetting($key);
   $crc_after = crc32($data);

   return "{$key}: CRC before={$crc_before}, after={$crc_after}";
}

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

function get_cdss_settings() {
   global $module;

   $cdss_functions = "";
   include $module->getModulePath() . "assets/cdss_function_specs.php";

   $sqlm = "SELECT field_name, element_label, element_enum, element_type, misc FROM redcap_metadata WHERE project_id={$module->project_id} AND element_type<>'descriptive' AND field_name NOT LIKE '%\_complete' ORDER BY field_name";
   $mm = $module->fetchRecords( $sqlm );
   $study_fields = [];
   foreach ( $mm as $m ){
      $study_fields[] = (object)[
         "name" => $m['field_name'],
         "label" => $m['element_label'],
         "code" => str_replace('\n', "\n", $m['element_enum']),
         "comments" => $m['misc']
      ];
   }

   $cdss_rbase = $module->getProjectSetting('cdss-rbase');

   $cdss_medications = $module->getProjectSetting('cdss-medications');
   $cdss_diseases = $module->getProjectSetting('cdss-diseases');
   $cdss_conditions = $module->getProjectSetting('cdss-conditions');
   $cdss_variables = $module->getProjectSetting('cdss-variables');

   if ( !$cdss_medications && !$cdss_diseases && !$cdss_conditions && !$cdss_variables ){
      include $module->getModulePath() . "assets/trim_rules.php";
      $module->setProjectSetting('cdss-medications', $cdss_medications);
      $module->setProjectSetting('cdss-diseases', $cdss_diseases);
      $module->setProjectSetting('cdss-conditions', $cdss_conditions);
      $module->setProjectSetting('cdss-variables', $cdss_variables);
      $cdss_medications = $module->getProjectSetting('cdss-medications');
      $cdss_diseases = $module->getProjectSetting('cdss-diseases');
      $cdss_conditions = $module->getProjectSetting('cdss-conditions');
      $cdss_variables = $module->getProjectSetting('cdss-variables');
   }

   return json_encode([
      'study_fields'=>$study_fields,
      'cdss_functions'=>json_decode($cdss_functions),
      'cdss_variables'=>json_decode($cdss_variables),
      'cdss_medications'=>json_decode($cdss_medications),
      'cdss_diseases'=>json_decode($cdss_diseases),
      'cdss_conditions'=>json_decode($cdss_conditions),
      'cdss_rbase'=>$cdss_rbase
   ]);

}

?>

