<?php

/*
* CDSS SERVICES
* Version 0.0.1 October 2020
*
* Processes AJAX calls from CDSS clients
*
* Required POST parameter: 'request', others as needed by request.
*/

namespace Yale\CDSS;

use Exception;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

$module = new CDSS();

if ( isset($_GET['request']) ) {

    $request = $_GET['request']; // always passed
}
else {

    $request = $_POST['request']; // always passed
}
/*
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
*/

/**
 * Validate the csrf token against the list of REDCap-generated tokens
 * for this session.
 */

 /*
if ( !$csrf_token !== $module->getCSRFToken() ) {

    if ( !in_array( $csrf_token, $_SESSION['redcap_csrf_token']) ){

        toesUp("error: invalid csrf token for request '{$request}'.");
    }
}
*/

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
    throw new \Exception("ARHH! CDSS Services reports ".$errmsg);  
}
 
 /**
  * Only functions defined in this namespace will be accepted.
  */
function requestIsValid( $request ):bool 
{
    return function_exists( __NAMESPACE__."\\".$request );
}

function get_cdss_rules()
{
    global $module;

    $record = $_POST['record'];
    $event_id = (int)$_POST['event_id'];

    $cdss_rules = json_decode($module->getProjectSetting('cdss-rules'), true);
    $cdss_medications = json_decode($module->getProjectSetting('cdss-medications'), true);
    $cdss_diseases = json_decode($module->getProjectSetting('cdss-diseases'), true);

    $compiled_rules = [];

    foreach ($cdss_rules as $rule){

        $compiled_rule = [

            'number' => $rule['rule_number']
            , 'name' => $rule['rule_name']
            , 'report' => $rule['rule_action']
            , 'report_name' => $rule['rule_action_name']
            , 'message' => $rule['rule_comment']
            , 'additional_items' => $rule['rule_additional_items']
            , 'conditions' => []
            , 'field_list' => []
            , 'rule_field_list' => []
        ];

        if ( !$compiled_rule['additional_items'] ){

            $compiled_rule['additional_items'] = [];
        }

        foreach ($rule['rule_conditions'] as $cond){

            $cond['condition_basis'] = trim($cond['condition_basis']);

            $cond['additional_fields'] = [];

            $field_name = getConditionField( 
                $cond['condition_basis'], 
                $cdss_medications,
                $cdss_diseases 
            );

            if ( $field_name ){

                $compiled_rule['field_list'][] = $field_name;

                $compiled_rule['rule_field_list'][] = $field_name;

                $cond['field_name'] = $field_name;

                $condXparams = condExpression($cond['condition_basis_option'], $cond['condition_basis_option_cutpoint']);

                $cond['callbackFunction'] = $condXparams[0];

                $cond['callbackArg'] = $condXparams[1];

                $compiled_rule['conditions'][] = $cond;
            }
        }

        for ($i=0; $i<count($compiled_rule['additional_items']); $i++){

            $compiled_rule['additional_items'][$i]['field_name'] = "";

            if ( $compiled_rule['additional_items'][$i]['value'] ){

                $field_name = getConditionField( 
                    $compiled_rule['additional_items'][$i]['value'], 
                    $cdss_medications,
                    $cdss_diseases 
                );

                if ( $field_name ){

                    $compiled_rule['field_list'][] = $field_name;

                    $compiled_rule['additional_items'][$i]['field_name'] = $field_name;
                }
            }
        }

        $compiled_rules[] = $compiled_rule;
    }

    return $compiled_rules;
}

function get_cdss_reports(){
    global $module;

    $printerIconUrl = $module->getUrl("images/printer-3-64.png");

    $html = "";

    $html .= "<div class='cdss_rule_report_controls no-print'>"
        . "<div class='cdss_rule_report_controls_title'>Yale CDSS Toolkit</div>"
        . "<div class='cdss_rule_report_control'>"
        . "<img src='{$printerIconUrl}' onclick='window.print()' title='print or save this report' />"
        . "</div>"
        . "</div>"
    ;

    $rules = get_cdss_rules();

    foreach($module->reports as $report) {

        $html .= get_cdss_report($report, $rules);
    }

    return $html;
}

function get_cdss_report($report, $rules)
{
    global $module;

    $html = "";

    $html .= "<div class='cdss_rule_report_title'>".$report['report_name']." for patient #" . $_POST['record'] . "</div>";

    foreach ($rules as $rule){

        if ( $rule['report']==$report['report_number'] ){

            $html .= get_cdss_rule_report($rule);
        }
    }

    return $html;
}

function conditionBasisLabel($basis)
{
    global $module;

    if ( substr($basis, 0, 3)==="[f]" ){

        return getReportFieldLabel(trim(substr($basis, 3)));
    }
    return ucwords(strtolower(trim(substr($basis, 3))));
}

function is_condition_basis( $field_name, $conditions ){

    foreach ( $conditions as $cond ){

        if ( $cond['condition_basis']==="[f] ".$field_name ){

            return true;
        }
    }

    return false;
}

function get_cdss_rule_report($rule){
    global $module;

    $project_id = $module->project_id;
    $record     = $_POST['record'];
    //$event_id   = $_POST['event_id'];

    $sql_select = "SELECT d.record, d.event_id";

    $sql_from   = "\nFROM redcap_data d";

    $sql_join = "";

    //$sql_where  = "\nWHERE d.project_id=? AND d.event_id=? AND d.record=? AND d.field_name='cdss_variables_complete'";

    //$sql_where_params = [$project_id, $event_id, $record];

    $sql_where  = "\nWHERE d.project_id=? AND d.record=? AND d.field_name='cdss_variables_complete'";

    $sql_where_params = [$project_id, $record];

    $sql_join_params = [];

    $join_search = ['op', 'cp', '_']; 
    $join_replace = ['(', ')', ' '];

    $i = 0;

    foreach ( $rule['field_list'] as $field_name ){

        $i++;

        $sql_select .= "\n, f{$i}.value AS `" . Yes3::normalized_string($field_name) . "`";
        
        $sql_join .= "\nLEFT JOIN redcap_data f{$i} ON f{$i}.project_id=d.project_id AND f{$i}.event_id=d.event_id AND f{$i}.record=d.record AND f{$i}.field_name=?";
        
        $sql_join_params[] = $field_name;
    }

    $sql = $sql_select . $sql_from . $sql_join . $sql_where;

    $params = array_merge( $sql_join_params, $sql_where_params );

    //exit( "<pre>" . $sql . "\n\n" . print_r($params, true) . "</pre>" );

    $x = Yes3::fetchRecord($sql, $params);

    $rule_expression = "";

    $rule['messages'] = [];

    $rule['satisfied_conditions'] = [];
    
    foreach ($rule['conditions'] as $cond){

        /**
         * the 'if.." selection is only relevant to the first condition
         */
        if ( !$rule_expression ){

            if ( $cond['condition_if']==="if_op" ) {

                $rule_expression = "(";
            }
        }
        else {

            $rule_expression .= " " . str_replace($join_search, $join_replace, $cond['condition_join']);
        }

        $cond_result = "?";

        try {
            $cond_result = call_user_func(
                __NAMESPACE__ . "\\" . $cond['callbackFunction'],
                $x[$cond['field_name']],
                $cond['callbackArg']
            );
        }
        catch(Exception $e){

            $rule['messages'][] = $e->getMessage();
        }

        $rule_expression .= " " . $cond_result;

        if ( $cond_result ){

            $s = trim(
                conditionBasisLabel($cond['condition_basis'])
                . " " . strtolower($cond['condition_basis_option'])
                . " " . strtolower($cond['condition_basis_option_cutpoint'])
            );

            $rule['satisfied_conditions'][] = trim(str_replace(['equal to 1', 'equal to 0'], ['noted', 'not noted'], $s));
        }
    }
    // add any required closing parens
    $nOpenParen = 0;
    $nCloseParen = 0;
    
    for ($j=0; $j<strlen($rule_expression); $j++){

        if ( $rule_expression[$j]==="(" ) $nOpenParen++;
        elseif ( $rule_expression[$j]===")" ) $nCloseParen++;
    }

    $nHanging = $nOpenParen - $nCloseParen;
    for ( $k=0; $k<$nHanging; $k++){

        $rule_expression .= ")";
    }

    // report any non-blank (non-zero) additional items
    // ignore any fields that would have already been reported as a condition basis
    $rule['rule_items_observed'] = [];

    $rule['additional_items_observed'] = [];

    foreach ($rule['rule_field_list'] as $field_name){

        if ( !is_condition_basis($field_name, $rule['conditions']) ){

            if ( $v = $x[$field_name] ) {

                $rule['rule_items_observed'][] = getReportForField($field_name, $v);
            }
        }
    }

    foreach( $rule['additional_items'] as $item){

        if ( $v = $x[$item['field_name']] ){

            $label = conditionBasisLabel($item['value']);

            $item_report = "";
            if ( substr($item['value'], 0, 3)==="[m]" ){

                $item_report = $label . " is prescribed";
            }
            elseif ( substr($item['value'], 0, 3)==="[d]" ){

                $item_report = $label . " has been diagnosed";
            }
            elseif ( substr($item['value'], 0, 3)==="[f]" ){

                $item_report = getReportForField($item['field_name'], $v);
            }
            else {

                $item_report = $label . " is noted";
            }

            // block if exact report has already been made
            if ( !in_array($item_report, array_merge($rule['satisfied_conditions'], $rule['rule_items_observed'])) ){

                $rule['additional_items_observed'][] = $item_report;
            }
        }
    }

    $rule_result = ( eval("return " . $rule_expression . ";") ) ? 1 : 0;

    $html = "";

    if ( $rule_result ){

        $html .= get_cdss_rule_report_html( $rule );
    }

    $debugText = "<pre>" 
        . print_r($rule, true) . "\n\n"
        . $sql . "\n\n" 
        . print_r($params, true) . "\n\n" 
        . print_r($x, true) . "\n\n"
        . $rule_expression . " = " . $rule_result
        . "</pre>";

    //$html .= $debugText;

    return $html;
}

function get_cdss_rule_report_html( $rule )
{
    global $module;

    $html = "";

    $html .= "<div class='cdss_rule_report_message_title'>" . $module->reports[$rule['report']]['report_title'] . "</div>";

    $html .= "<div class='cdss_rule_report_message'>" . $rule['message'] . "</div>";

    $html .= get_cdss_rule_report_section_html( $rule['satisfied_conditions'], "The following condition(s) contributed to this report:" );

    $html .= get_cdss_rule_report_section_html( array_merge($rule['rule_items_observed'], $rule['additional_items_observed']), "Additional information:" );

    $html .= "<div class='cdss_rule_report_comments'>comments</div>";

    return $html;
}

function get_cdss_rule_report_section_html( $list, $title ){

    if ( !$list ){

        return "";
    }

    $html = "";

    $html .= "<div class='cdss_rule_report_section'>";

    $html .= "<p>" . $title . "</p>";

    $html .= "<ul>";

    foreach ($list as $item ){

        $html .= "<li>" . $item . "</li>";
    }

    $html .= "</ul>";

    $html .= "</div>";

    return $html;
}

function getReportForField( $field_name, $value )
{
    global $module;

    $label = getReportFieldLabel($field_name);

    $choiceLabel = $module->getChoiceLabel($field_name, $value);

    $value = intval($value); if ( !$value ) $value = 0;

    if ( $value===1 ){

        if ( isCDSSMedicationField($field_name) ) {

            return $label . " is prescribed";
        }

        if ( isCDSSDiseaseField($field_name) ) {

            return $label . " has been diagnosed";
        }

        if ( isCDSSCalcField($field_name) ) {

            return $label . " was noted";
        }
    }

    elseif ( $value > 1 && isCDSSMedicationField($field_name) ){

        return $label . " dose is " . $value;
    }

    if ( $choiceLabel ){

        return "The response to '" . $label . "' is: " . $choiceLabel;
    }

    return $label . " is " . $value;
}

function isCDSSMedicationField($field_name)
{
    return ( substr($field_name, 0, 7) === "cdss_m_" ) ? true:false;
}

function isCDSSDiseaseField($field_name)
{
    return ( substr($field_name, 0, 7) === "cdss_d_" ) ? true:false;
}

function isCDSSCalcField($field_name)
{
    return ( substr($field_name, 0, 10) === "cdss_calc_" ) ? true:false;
}

function getReportFieldLabel($field_name){
    global $module;

    $search = ['CDSS calculated field:', 'CDSS medication:', 'CDSS disease:'];
    $replace = ['', '', ''];

    $s = sentenceCase( trim( str_ireplace($search, $replace, $module->getFieldLabel($field_name)) ) );

    return $s;
}

function sentenceCase($s)
{
    return strtoupper($s[0]) . strtolower(substr($s, 1));
}

function condExpression( $condOption, $condOptionVal="" )
{
    if ( $condOption==="IS PRESENT"){

        return ["condEvalBool", "1"];
    }
    elseif ( $condOption==="IS NOT PRESENT"){

        return ["condEvalBool", "0"];
    }
    if ( $condOption==="IS NOTED"){

        return ["condEvalBool", "1"];
    }
    elseif ( $condOption==="IS NOT NOTED"){

        return ["condEvalBool", "0"];
    }
    elseif ( $condOption==="IS PRESCRIBED"){

        return ["condEvalPrescribed", "1"];
    }
    elseif ( $condOption==="IS NOT PRESCRIBED"){

        return ["condEvalPrescribed", "0"];
    }
    elseif ( $condOption==="IS EQUAL TO"){

        return ["condEvalEQ", $condOptionVal];
    }
    elseif ( $condOption==="IS GREATER THAN"){

        return ["condEvalGT", $condOptionVal];
    }
    elseif ( $condOption==="IS LESS THAN"){

        return ["condEvalLT", $condOptionVal];
    }
    elseif ( $condOption==="IS GREATER THAN OR EQUAL TO"){

        return ["condEvalGE", $condOptionVal];
    }
    elseif ( $condOption==="IS LESS THAN OR EQUAL TO"){

        return ["condEvalLE", $condOptionVal];
    }
    elseif ( $condOption==="DOSE IS LESS THAN OR EQUAL TO"){

        return ["condEvalDoseEQ", $condOptionVal];
    }
    elseif ( $condOption==="DOSE IS GREATER THAN OR EQUAL TO"){

        return ["condEvalDoseGE", $condOptionVal];
    }
    elseif ( $condOption==="DOSE IS LESS THAN"){

        return ["condEvalDoseLE", $condOptionVal];
    }
    elseif ( $condOption==="DOSE IS GREATER THAN"){

        return ["condEvalDoseGT", $condOptionVal];
    }
    
}

function condEvalBool($value, $criterion)
{
    $value = intval($value); if ( !$value ) $value = 0;

    return ( $value == intval($criterion) ) ? 1:0;
}

function condEvalPrescribed($value, $criterion)
{
    $value = intval($value); 
    if ( !$value ) $value = 0;
    else $value = 1;
    
    return ( $value == intval($criterion) ) ? 1:0;
}

function condEvalDoseEQ($value, $criterion)
{
    $value = intval($value); if ( !$value ) $value = 0;

    /**
     * if the value is not > 1 it is not a dose, so the dose is unknown
     */
    if ( $value <= 1 ){

        return 0;
    }
    
    return ( $value == intval($criterion) ) ? 1:0;
}

function condEvalDoseGT($value, $criterion)
{
    $value = intval($value); if ( !$value ) $value = 0;

    /**
     * if the value is not > 1 it is not a dose, so the dose is unknown
     */
    if ( $value <= 1 ){

        return 0;
    }
    
    return ( $value > intval($criterion) ) ? 1:0;
}

function condEvalDoseLT($value, $criterion)
{
    $value = intval($value); if ( !$value ) $value = 0;

    /**
     * if the value is not > 1 it is not a dose, so the dose is unknown
     */
    if ( $value <= 1 ){

        return 0;
    }
    
    return ( $value < intval($criterion) ) ? 1:0;
}

function condEvalDoseGE($value, $criterion)
{
    $value = intval($value); if ( !$value ) $value = 0;

    /**
     * if the value is not > 1 it is not a dose, so the dose is unknown
     */
    if ( $value <= 1 ){

        return 0;
    }
    
    return ( $value >= intval($criterion) ) ? 1:0;
}

function condEvalDoseLE($value, $criterion)
{
    $value = intval($value); if ( !$value ) $value = 0;

    /**
     * if the value is not > 1 it is not a dose, so the dose is unknown
     */
    if ( $value <= 1 ){

        return 0;
    }
    
    return ( $value <= intval($criterion) ) ? 1:0;
}

function condEvalEQ($value, $criterion)
{
    $value = intval($value); 
    if ( !$value ) $value = 0;

    return ( $value == intval($criterion) ) ? 1:0;
}

function condEvalGT($value, $criterion)
{
    $value = intval($value); 
    if ( !$value ) $value = 0;

    return ( $value > intval($criterion) ) ? 1:0;
}

function condEvalGE($value, $criterion)
{
    $value = intval($value); 
    if ( !$value ) $value = 0;

    return ( $value >= intval($criterion) ) ? 1:0;
}

function condEvalLT($value, $criterion)
{
    $value = intval($value); 
    if ( !$value ) $value = 0;

    return ( $value < intval($criterion) ) ? 1:0;
}

function condEvalLE($value, $criterion)
{
    $value = intval($value); 
    if ( !$value ) $value = 0;

    return ( $value <= intval($criterion) ) ? 1:0;
}

function getConditionField( $condBasis, $meds, $diseases )
{
    $condBasisType = substr(trim($condBasis), 0, 3);
    $condBasisName = substr(trim($condBasis), 4);

    if ( $condBasisType==="[f]" ){

        return $condBasisName;
    }

    if ( $condBasisType==="[m]" ){

        return conditionFieldForBasis( $condBasisName, $meds);
    }

    if ( $condBasisType==="[d]" ){

        return conditionFieldForBasis( $condBasisName, $diseases);
    }

    return "";
}

function conditionFieldForBasis( $condBasisName, $condBasis )
{
    foreach( $condBasis as $item ){

        if ( strtoupper(trim($item['name']))===strtoupper(trim($condBasisName)) ){

            return trim($item['field']);
        }
    }

    return "";
}

