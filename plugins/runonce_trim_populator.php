<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$module = new Yale\TRIM\TRIM();

$HtmlPage = new HtmlPage();
$HtmlPage->ProjectHeader();

$comet = new mysqli("db", "root", "root", "comet");
if ($comet->connect_errno) {
   echo "Failed to connect to MySQL: (" . $comet->connect_errno . ") " . $comet->connect_error;
}

patients();
medications();
diagnoses();
q();


function medications() {
   global $module;

   $commitdata = true;
   $logevents = false;

   $sql = "
SELECT m.`participant_id` AS trim_id
  , m.`medication_prescribed` AS `med_prescribed`
  , m.`medication_reported` AS `med_reported`
  , mrules.`name_in_rules_id`-16 AS `med_rule`
  , m.`medication_total_daily_dose` AS `med_dose`
  , IF(m.`medication_reconciliation`=0, NULL, m.`medication_reconciliation`) AS `med_reconciliation`
  , m.`medication_notes` AS `med_reconciliation_notes`
FROM comet_medications m
INNER JOIN comet_participants p ON p.`participant_id`=m.`participant_id`
INNER JOIN comet_patients pt ON pt.`patient_id`=p.`patient_id`
LEFT JOIN comet_med_name_in_rules mrules ON mrules.`name_in_rules`=m.`medication_rule_name`
ORDER BY m.`participant_id`  
   ";

   $meds = comet_fetchRecords($sql);

   $data = array();

   $instance = 0;

   $trim_id = 0;

   foreach ($meds as $med) {

      if ( $med['trim_id'] !== $trim_id ){
         $instance=0;
         $trim_id = $med['trim_id'];
      }

      $instance++;

      $data[$med['trim_id']]['repeat_instances'][$module->event_id]['medication'][$instance] = [
         'med_prescribed' => $med['med_prescribed'],
         'med_reported' => $med['med_reported'],
         'med_rule' => $med['med_rule'],
         'med_dose' => $med['med_dose'],
         'med_reconciliation' => $med['med_reconciliation'],
         'med_reconciliation_notes' => $med['med_reconciliation_notes'],
         'medication_complete' => '2'
      ];

   }

   $rc = REDCap::saveData($module->project_id, 'array', $data, 'normal', 'YMD', 'flat', null, false, $logevents, $commitdata);

   print_r( $rc );

}

function diagnoses() {
   global $module;

   $commitdata = true;
   $logevents = false;

   $sql = "
SELECT dx.`participant_id` AS trim_id
  , dx.`comet_diagnosis_code` AS `dx_code`
  , dx.`comet_diagnosis_description` AS `dx_description`
  , dx.`comet_diagnosis_rule_name` AS `dx_rule_name`
  , dxr.`comet_diagnosis_rule_name_id` AS `dx_rule`
FROM comet_diagnoses dx
INNER JOIN comet_participants p ON p.`participant_id`=dx.`participant_id`
INNER JOIN comet_patients pt ON pt.`patient_id`=p.`patient_id`
LEFT JOIN comet_diagnosis_rule_names dxr ON dxr.`comet_diagnosis_rule_name`=dx.`comet_diagnosis_rule_name`
ORDER BY dx.`participant_id`
   ";

   $dxs = comet_fetchRecords($sql);

   $data = array();

   $instance = 0;

   $trim_id = 0;

   foreach ($dxs as $dx) {

      if ( $dx['trim_id'] !== $trim_id ){
         $instance=0;
         $trim_id = $dx['trim_id'];
      }

      $instance++;

      $data[$dx['trim_id']]['repeat_instances'][$module->event_id]['diagnosis'][$instance] = [
         'dx_code' => $dx['dx_code'],
         'dx_description' => $dx['dx_description'],
         'dx_rule' => $dx['dx_rule'],
         'diagnosis_complete' => '2'
      ];

   }

   $rc = REDCap::saveData($module->project_id, 'array', $data, 'normal', 'YMD', 'flat', null, false, $logevents, $commitdata);

   print_r( $rc );

}

function patients(){
   global $module;

   $commitdata = true;
   $logevents = false;

   $sql = "
SELECT p.`participant_id` AS `trim_id`
  , p.`patient_id` AS `pt_identifier`
  , p.`patient_visit_id` AS `enc_identifier`
  , pt.`last_name` AS `pt_last_name`
  , pt.`first_name` AS `pt_first_name`
  , LOWER(pt.`sex`) AS `pt_sex`
  , IF(pt.`sex`='MALE', 'Mr.', 'Ms.') AS `pt_prefix`
  , CURRENT_DATE - INTERVAL pt.`age` YEAR - INTERVAL 365*RAND() DAY AS `pt_birthdate`
FROM comet_participants p
  INNER JOIN comet_patients pt ON pt.`patient_id`=p.`patient_id`   
   ";

   $patients = comet_fetchRecords($sql);

   $data = array();

   foreach ( $patients as $pt ){

      $data[$pt['trim_id']][$module->event_id] = [
         'pt_identifier' => $pt['pt_identifier'],
         'enc_identifier' => $pt['enc_identifier'],
         'pt_last_name' => $pt['pt_last_name'],
         'pt_first_name' => $pt['pt_first_name'],
         'pt_sex' => $pt['pt_sex'],
         'pt_prefix' => $pt['pt_prefix'],
         'pt_birthdate' => $pt['pt_birthdate'],
         'patient_complete' => '2'
      ];

   }

   $rc = REDCap::saveData($module->project_id, 'array', $data, 'normal', 'YMD', 'flat', null, false, $logevents, $commitdata);
   print_r( $rc );
}

function q(){
   global $module;

   $commitdata = true;
   $logevents = false;

   $sql = "
SELECT q.`participant_id` AS `trim_id`
     
  , IF(q.`meds_diabetes`=0, NULL, q.`meds_diabetes`) AS `meds_diabetes`
  , IF(q.`ms_doesanyonehelp`=0, NULL, q.`ms_doesanyonehelp`) AS `ms_doesanyonehelp`
  , IF(q.`ms_cananyonehelp`=0, NULL, q.`ms_cananyonehelp`) AS `ms_cananyonehelp`
  , IF(q.`ms_whohelps`=0, NULL, q.`ms_whohelps`) AS `ms_whohelps`
  , q.`ms_whohelps_othertext`
  , IF(q.`ms_ae`=0, NULL, q.`ms_ae`) AS `ms_ae`
  , q.`ms_stopone_medication`
  , q.`ms_stopone_reason`
  , q.`ms_keepone_medication`
  , q.`ms_keepone_reason`
 
  , IF(q.`phq2_1`=0, NULL, q.`phq2_1`) AS `phq2_1`
  , IF(q.`phq2_2`=0, NULL, q.`phq2_2`) AS `phq2_2`
  
  , IF(q.`adl_1`=0, NULL, q.`adl_1`) AS `adl_1`
  , IF(q.`adl_2`=0, NULL, q.`adl_2`) AS `adl_2`
  , IF(q.`adl_3`=0, NULL, q.`adl_3`) AS `adl_3`
  , IF(q.`adl_4`=0, NULL, q.`adl_4`) AS `adl_4`
  , IF(q.`adl_5`=0, NULL, q.`adl_5`) AS `adl_5`
  , IF(q.`adl_6`=0, NULL, q.`adl_6`) AS `adl_6`
  , IF(q.`adl_7`=0, NULL, q.`adl_7`) AS `adl_7`

  , IF(q.`hs_falls`=0, NULL, q.`hs_falls`) AS `hs_falls`
  , IF(q.`hs_cigarettes`=0, NULL, q.`hs_cigarettes`) AS `hs_cigarettes`
  , IF(q.`hs_hospital`=0, NULL, q.`hs_hospital`) AS `hs_hospital`
  , IF(q.`hs_ratehealth`=0, NULL, q.`hs_ratehealth`) AS `hs_ratehealth`
  , IF(q.`hs_one_iadl`=0, NULL, q.`hs_one_iadl`) AS `hs_one_iadl`
  , IF(q.`hs_walk`=0, NULL, q.`hs_walk`) AS `hs_walk`
  , IF(q.`hs_constipation`=0, NULL, q.`hs_constipation`) AS `hs_constipation`
  , IF(q.`hs_dizziness`=0, NULL, q.`hs_dizziness`) AS `hs_dizziness`
  , IF(q.`hs_sleepmed`=0, NULL, q.`hs_sleepmed`) AS `hs_sleepmed`
  , IF(q.`hs_sleepmed_type`=0, NULL, q.`hs_sleepmed_type`) AS `hs_sleepmed_type`
  
  , q.`hs_trails_errors`
  
  , IF(q.`morisky_1`=0, NULL, q.`morisky_1`) AS `morisky_1`
  , IF(q.`morisky_2`=0, NULL, q.`morisky_2`) AS `morisky_2`
  , IF(q.`morisky_3`=0, NULL, q.`morisky_3`) AS `morisky_3`
  , IF(q.`morisky_4`=0, NULL, q.`morisky_4`) AS `morisky_4`
  , IF(q.`morisky_5`=0, NULL, q.`morisky_5`) AS `morisky_5`
  , IF(q.`morisky_6`=0, NULL, q.`morisky_6`) AS `morisky_6`
  , IF(q.`morisky_7`=0, NULL, q.`morisky_7`) AS `morisky_7`
  , IF(q.`morisky_8`=0, NULL, q.`morisky_8`) AS `morisky_8`
  
  , q.`mr_age` AS `ehr_age`
  , lower(q.`mr_gender`) AS `ehr_gender`
  , q.`mr_bmi` AS `ehr_bmi`
  , q.`mr_egfr` AS `ehr_egfr`
  , q.`mr_hgba1c` AS `ehr_hgba1c`
  , q.`mr_creatinineclearance` AS `ehr_creatinine_clearance`
  , q.`mr_systbp` AS `ehr_mean_systolic_bp`
  , q.`mr_diasbp` AS `ehr_mean_diastolic_bp`
  , q.`mr_bp_list` AS `ehr_bp_list`
  
  
FROM comet_medsq q
  INNER JOIN comet_participants p ON p.`participant_id`=q.`participant_id`
  INNER JOIN comet_patients pt ON pt.`patient_id`=p.`patient_id`
  
ORDER BY q.`participant_id`  
   ";

   $xx = comet_fetchRecords($sql);

   $data = array();

   foreach ( $xx as $x ){

      $data[$x['trim_id']][$module->event_id] = [

         'meds_diabetes' => $x['meds_diabetes'],
         'ms_doesanyonehelp' => $x['ms_doesanyonehelp'],
         'ms_cananyonehelp' => $x['ms_cananyonehelp'],
         'ms_whohelps' => $x['ms_whohelps'],
         'ms_whohelps_othertext' => $x['ms_whohelps_othertext'],
         'ms_ae' => $x['ms_ae'],
         'ms_stopone_medication' => $x['ms_stopone_medication'],
         'ms_stopone_reason' => $x['ms_stopone_reason'],
         'ms_keepone_medication' => $x['ms_keepone_medication'],
         'ms_keepone_reason' => $x['ms_keepone_reason'],

         'phq2_1' => $x['phq2_1'],
         'phq2_2' => $x['phq2_2'],

         'adl_1' => $x['adl_1'],
         'adl_2' => $x['adl_2'],
         'adl_3' => $x['adl_3'],
         'adl_4' => $x['adl_4'],
         'adl_5' => $x['adl_5'],
         'adl_6' => $x['adl_6'],
         'adl_7' => $x['adl_7'],

         'hs_falls' => $x['hs_falls'],
         'hs_cigarettes' => $x['hs_cigarettes'],
         'hs_hospital' => $x['hs_hospital'],
         'hs_ratehealth' => $x['hs_ratehealth'],
         'hs_one_iadl' => $x['hs_one_iadl'],
         'hs_walk' => $x['hs_walk'],
         'hs_constipation' => $x['hs_constipation'],
         'hs_dizziness' => $x['hs_dizziness'],
         'hs_sleepmed' => $x['hs_sleepmed'],
         'hs_sleepmed_type' => $x['hs_sleepmed_type'],

         'hs_trails_errors' => $x['hs_trails_errors'],

         'morisky_1' => $x['morisky_1'],
         'morisky_2' => $x['morisky_2'],
         'morisky_3' => $x['morisky_3'],
         'morisky_4' => $x['morisky_4'],
         'morisky_5' => $x['morisky_5'],
         'morisky_6' => $x['morisky_6'],
         'morisky_7' => $x['morisky_7'],
         'morisky_8' => $x['morisky_8'],

         'ehr_age' => $x['ehr_age'],
         'ehr_gender' => $x['ehr_gender'],
         'ehr_bmi' => $x['ehr_bmi'],
         'ehr_egfr' => $x['ehr_egfr'],
         'ehr_hgba1c' => $x['ehr_hgba1c'],
         'ehr_creatinine_clearance' => $x['ehr_creatinine_clearance'],
         'ehr_mean_systolic_bp' => $x['ehr_mean_systolic_bp'],
         'ehr_mean_diastolic_bp' => $x['ehr_mean_diastolic_bp'],
         'ehr_bp_list' => $x['ehr_bp_list'],

         'medications_complete' => '2',
         'medication_survey_complete' => '2',
         'health_survey_complete' => '2',
         'trail_making_test_complete' => '2',
         'medications_adherence_complete' => '2',
         'chart_review_complete' => '2'

      ];

   }

   $rc = REDCap::saveData($module->project_id, 'array', $data, 'normal', 'YMD', 'flat', null, false, $logevents, $commitdata);
   print_r( $rc );
}


function comet_fetchRecords($sql) {
   global $comet;

   $r = array();
   $stmt = $comet->query($sql);
   if ($stmt) {
      while ($row = $stmt->fetch_assoc()) {
         $r[] = $row;
      }
      $stmt->free_result();
   }
   return $r;
}


?>