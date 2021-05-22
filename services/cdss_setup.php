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

function get_cdss_settings() {
   global $module;

   $cdss_functions = "";
   include $module->getModulePath() . "assets/cdss_function_specs.php";

   $sqlm = "SELECT field_name, element_label, element_enum, element_type, misc FROM redcap_metadata WHERE project_id={$module->project_id} AND element_type<>'descriptive' AND field_name NOT LIKE '%\_complete' ORDER BY field_name";
   $mm = $module->fetchRecords( $sqlm );
   $fields = [];
   foreach ( $mm as $m ){
      $fields[] = (object)[
         "name" => $m['field_name'],
         "label" => $m['element_label'],
         "code" => str_replace('\n', "\n", $m['element_enum']),
         "comments" => $m['misc']
      ];
   }

   $study_fields = json_encode( $fields );
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
      'cdss_functions'=>$cdss_functions,
      'cdss_variables'=>$cdss_variables,
      'cdss_medications'=>$cdss_medications,
      'cdss_diseases'=>$cdss_diseases,
      'cdss_conditions'=>$cdss_conditions,
      'cdss_rbase'=>$cdss_rbase
   ]);

}

?>

