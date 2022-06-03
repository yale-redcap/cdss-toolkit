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

namespace Yale\CDSS;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if ( isset($_GET['request']) ) {

    $request = $_GET['request']; // always passed
}
else {

    $request = $_POST['request']; // always passed
}

$csrf_token = "";

if ( isset($_POST['csrf_token']) ) {
    $csrf_token = $_POST['csrf_token'];
 }
 else if ( isset($_GET['csrf_token']) ) {
    $csrf_token = $_GET['csrf_token'];
 }
 else {
   $headers = apache_request_headers();
   if ( $headers['X-CSRF-Token'] ){
      $csrf_token = $headers['X-CSRF-Token'];
   }   
}

if ( !$csrf_token ){
   toesUp("error: csrf token missing for request '{$request}'.");
}

$module = new CDSS();

/**
 * Validate the csrf token against the list of REDCap-generated tokens
 * for this session.
 */

if ( !$csrf_token !== $module->getCSRFToken() ) {

    if ( !in_array( $csrf_token, $_SESSION['redcap_csrf_token']) ){

        toesUp("error: invalid csrf token for request '{$request}'.");
    }
}

if ( $request === "get-cdss-settings"  ) exit(get_cdss_settings());
elseif ( $request === "save-metadata"  ) exit(save_metadata());
elseif ( $request === "save-cdss-rules"  ) exit(save_cdss_rules());
elseif ( $request === "get-cdss-rule-backup-select-options"  ) exit(get_cdss_rule_backup_select_options());

if ( !requestIsValid($request) ) {
    toesUp("error: invalid request: ".$request);
};
 
/**
 * Execute the requested function and head out.
 */
exit ( call_user_func( __NAMESPACE__."\\".$request ) );

/**
 * Handle the reported error by going toes-up.
 */
function toesUp($errmsg)
{
    throw new \Exception("ARHH! YES3 Services reports ".$errmsg);  
}
 
 /**
  * Only functions defined in this namespace will be accepted.
  */
function requestIsValid( $request ):bool 
{
    return function_exists( __NAMESPACE__."\\".$request );
}

//else exit("go away");


function writeCDSSVarsCsv()
{
    global $module;

    $dis = json_decode($module->getProjectSetting('cdss-diseases'), true);

    $med = json_decode($module->getProjectSetting('cdss-medications'), true);

    //return print_r($dis, true);

    $csv = '"Variable / Field Name","Form Name","Section Header","Field Type","Field Label","Choices, Calculations, OR Slider Labels","Field Note","Text Validation Type OR Show Slider Number","Text Validation Min","Text Validation Max",Identifier?,"Branching Logic (Show field only if...)","Required Field?","Custom Alignment","Question Number (surveys only)","Matrix Group Name","Matrix Ranking?","Field Annotation"';

    $csv .= CDSSVarsCsvRows("CDSS disease", $dis);

    $csv .= CDSSVarsCsvRows("CDSS medication", $med);

    $len = strlen($csv);

    header("Content-type: text/plain");
    header("Content-Disposition: attachment; filename=cdss_variables_dd.csv");
    print $csv;
}

function CDSSVarsCsvRows( $prefix, $xx)
{
    $rows = "";

    foreach ($xx as $x){

        $field_name = trim($x['field']);

        $label = $prefix . ": "  . trim($x['name']);

        $rows .= "\n";
        $rows .= "{$field_name},cdss_variables,,yesno,\"{$label}\",,\"CALCULATED BY CDSS\",,,,,,,RH,,,,\" @CDSS\"";
    }
    return $rows;
}


function get_cdss_rules() 
{
    global $module;
    $ruleset = (int)$_POST['ruleset'];

    $rulesJSON = "[]";

    if ( $ruleset===0 ) {
        $rulesJSON = $module->getProjectSetting('cdss-rules');
    }

    if ( !Yes3::is_json_decodable($rulesJSON) ){

        $rulesJSON = "[]";
    }

    return $rulesJSON;
}

function save_cdss_rules() {
   global $module;
   $cdss_rules = $_POST['rules'];
   $module->setProjectSetting('cdss-rules', json_encode($cdss_rules) );
   backup_cdss_rules($cdss_rules);
   return $module->getProjectSetting('cdss-rules');
}

function backup_cdss_rules( $cdss_rules ) {
   global $module;

   if ( $cdss_rules_backups_json = $module->getProjectSetting('cdss-rules-backups') ) {
      $cdss_rules_backups = json_decode( $cdss_rules_backups_json );
   } else {
      $cdss_rules_backups = [];
   }

   $cdss_rules_backup = [
      'backup_date' => strftime("%F %T"),
      'user' => USERID,
      'cdss_rules' => $cdss_rules
   ];

   array_unshift($cdss_rules_backups, $cdss_rules_backup);

   $module->setProjectSetting('cdss-rules-backups', json_encode($cdss_rules_backups) );
}

function get_cdss_rule_backup_select_options() {
    global $module;

    if ( !$cdss_rules_backups_json =  $module->getProjectSetting('cdss-rules-backups') ) {

        $options =  "<option disabled value=''>NO BACKUPS AVAILABLE</option>";
        $last_saved = "never saved";
    }
    else {

        $cdss_rules_backups = json_decode($cdss_rules_backups_json, true);

        $options = "<option value=''>-- select a backup set --</option>";

        for ( $i=0; $i<count($cdss_rules_backups); $i++ ){

            $nrules = count( $cdss_rules_backups[$i]['cdss_rules'] );

            $options .=
                "<option value='{$i}'>{$cdss_rules_backups[$i]['backup_date']} ({$nrules} rules)</option>";

        }

        $last_saved = "last saved " . $cdss_rules_backups[0]['backup_date'];
    }

   return json_encode( [
      'options' => $options,
      'last_saved_message' => $last_saved
   ]);
}

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

    $cdss_medications = $module->getProjectSetting('cdss-medications');
    $cdss_diseases = $module->getProjectSetting('cdss-diseases');
    $cdss_conditions = $module->getProjectSetting('cdss-conditions');
    $cdss_variables = $module->getProjectSetting('cdss-variables');
    
    $cdss_rbase = file_get_contents( $module->getModulePath() . "assets/kbase.txt" );

    $all_drugs = [];
    $all_diseases = [];
    preg_match_all('/drugs\(\".+\"\)/U', $cdss_rbase, $all_drugs);
    preg_match_all('/diseases\(\".+\"\)/U', $cdss_rbase, $all_diseases);

    $unique_drugs = [];
    $unique_diseases = [];

    $search = [ "drugs", "diseases", "(", ")", "'", '"' ];
    $replace = [ "", "", "", "", "","" ];

    foreach($all_drugs[0] as $drug){    

        $names =  explode(",", strtoupper(str_replace($search, $replace, $drug)));

        foreach ($names as $name) {

            $name = trim($name);

            if ( !in_array($name, $unique_drugs)){

                $unique_drugs[] = $name;
            }
        }
    }

    foreach($all_diseases[0] as $disease){    

        $name =  strtoupper(str_replace($search, $replace, $disease));

        if ( !in_array($name, $unique_diseases)){

            $unique_diseases[] = $name;
        }
    }

    sort($unique_diseases);

    sort($unique_drugs);

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

    // for now, 'actions' are just functions that start with 'report_'
    $cdss_functions = json_decode($cdss_functions);
    $cdss_actions = [];
    foreach ( $cdss_functions as $cdss_function ) {
        if ( substr( $cdss_function->name, 0, 7)==="report_" ) {
            $cdss_actions[] = $cdss_function;
        }
    }

    return json_encode([
        'study_fields'=>$study_fields,
        'cdss_actions'=>$cdss_actions,
        'cdss_variables'=>json_decode($cdss_variables),
        'cdss_medications'=>json_decode($cdss_medications),
        'cdss_diseases'=>json_decode($cdss_diseases),
        'cdss_conditions'=>json_decode($cdss_conditions),
        //'cdss_rbase'=>$cdss_rbase,
        'all_drugs' => $all_drugs,
        'all_diseases' => $all_diseases,   
        'drugs' => $unique_drugs,
        'diseases' => $unique_diseases   
        ]);

}

?>

