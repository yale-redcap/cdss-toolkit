<?php


namespace Yale\CDSS;

use ExternalModules\AbstractExternalModule;

/*
 * Load any traits or classes here.
 *
 * Note that while external classes can't be instantiated within the EM class,
 * loading them here makes them available for any PHP script running in the EM (i.e. plugins).
 * Also, static methods in loaded classes can be used by the EM class without instantiating.
 */
require_once "traits/trait_yes3Fn.php";


class CDSS extends \ExternalModules\AbstractExternalModule {

   use ye3Fn;

   public $project_id;
   public $event_id;
   private $img_trashcan_url;

   public function __construct() {

      parent::__construct(); // call parent (AbstractExternalModule) constructor

      $this->project_id = $this->getProjectId(); // defined in AbstractExternalModule; will return project_id or null

      if ($this->project_id) {
         $this->event_id = $this->getEventId();
      }

      $this->img_trashcan_url = $this->getUrl('images/delete.jpg');

   } // __construct

   function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id = NULL, $repeat_instance = 1) {

      $project_id_sql = $this->sql_string($project_id);
      $event_id_sql = $this->sql_string($event_id);
      $record_sql = $this->sql_string($record);

      if ( $instrument==="medications") {

         /*
          * get options for SELECT controls
          */

         $med_rule_choices = json_encode( $this->getChoiceLabels('med_rule') );
         $med_reconciliation_choices = json_encode( $this->getChoiceLabels('med_reconciliation') );

         /*
          * assemble the meds data object
          */

         $sql = "
 SELECT IFNULL(m.`instance`,1) AS `med_number`
  , IFNULL(m1.`value`, '') AS `med_prescribed`
  , IFNULL(m2.`value`, '') AS `med_reported`
  , IFNULL(m3.`value`, '') AS `med_dose`
  , IFNULL(m4.`value`, '') AS `med_rule`
  , IFNULL(m5.`value`, '') AS `med_reconciliation`
  , IFNULL(m6.`value`, '') AS `med_reconciliation_notes`
FROM redcap_data m
  LEFT JOIN redcap_data m1 ON m1.`project_id`=m.`project_id` AND m1.`record`=m.`record` AND m1.`event_id`=m.`event_id` AND m1.`instance`<=>m.`instance` AND m1.`field_name`='med_prescribed'
  LEFT JOIN redcap_data m2 ON m2.`project_id`=m.`project_id` AND m2.`record`=m.`record` AND m2.`event_id`=m.`event_id` AND m2.`instance`<=>m.`instance` AND m2.`field_name`='med_reported'
  LEFT JOIN redcap_data m3 ON m3.`project_id`=m.`project_id` AND m3.`record`=m.`record` AND m3.`event_id`=m.`event_id` AND m3.`instance`<=>m.`instance` AND m3.`field_name`='med_dose'
  LEFT JOIN redcap_data m4 ON m4.`project_id`=m.`project_id` AND m4.`record`=m.`record` AND m4.`event_id`=m.`event_id` AND m4.`instance`<=>m.`instance` AND m4.`field_name`='med_rule'
  LEFT JOIN redcap_data m5 ON m5.`project_id`=m.`project_id` AND m5.`record`=m.`record` AND m5.`event_id`=m.`event_id` AND m5.`instance`<=>m.`instance` AND m5.`field_name`='med_reconciliation'
  LEFT JOIN redcap_data m6 ON m6.`project_id`=m.`project_id` AND m6.`record`=m.`record` AND m6.`event_id`=m.`event_id` AND m6.`instance`<=>m.`instance` AND m6.`field_name`='med_reconciliation_notes'
WHERE m.`project_id`={$project_id_sql}
  AND m.`event_id`={$event_id_sql}
  AND m.`record`={$record_sql}
  AND m.`field_name`='medication_complete'
ORDER BY m.`instance`        
         ";

         $meds = json_encode( $this->fetchRecords($sql) );

         ?>

         <style>

            table.cdss-meds-reconciliation {
               width: 100%;
               border-spacing: 0;
               border-collapse: collapse;
            }

            table.cdss-meds-reconciliation thead,  table.cdss-meds-reconciliation tbody {
               display: block;
            }

            table.cdss-meds-reconciliation tbody {
               height: 400px;
               overflow-y: scroll;
               overflow-x: hidden;
            }

            table.cdss-meds-reconciliation th, table.cdss-meds-reconciliation td {
               font-size: 11px;
               font-weight: 600;
               padding-top: 2px;
               padding-bottom: 2px;
               padding-left: 4px;
               padding-right: 4px;
            }

            table.cdss-meds-reconciliation th {
               height: 30px;
               background-color: #0083C1;
               border: 1px solid #0083C1;
               vertical-align: bottom;
               color: #D6E6EF;
            }

            table.cdss-meds-reconciliation td.cdss-even {
               background-color: white;
            }

            table.cdss-meds-reconciliation td.cdss-odd {
               background-color: #E7F1F8;
            }

            table.cdss-meds-reconciliation td * {
               font-size: 11px;
               font-weight: 600;
               color: #00699B;
               background-color: transparent;
            }

            th.cdss-meds-reconciliation-stub, td.cdss-meds-reconciliation-stub {
               width: 30px;
            }

            td.cdss-meds-reconciliation-stub {
               vertical-align: top;
               text-align: center;
               color: lightslategray;
               background-color: white;
               padding-top: 6px !important;
            }

            td.cdss-med_prescribed,
               td.cdss-med_reported,
               td.cdss-med_rule,
               td.cdss-med_reconciliation {
               vertical-align: top;
               border-left: 1px solid #0083C1;
               border-right: 1px solid #0083C1;
               border-bottom: 1px solid #0083C1;
            }

            td.cdss-med_prescribed textarea, td.cdss-med_reported textarea {
               width: 100%;
               height: 80px;
               resize: vertical;
               border: 0;
            }

            td.cdss-med_reconciliation textarea {
               width: 100%;
               height: 56px;
               resize: vertical;
               border: 1px solid lightgray;
            }

            td.cdss-med_rule select, td.cdss-med_reconciliation select {
               height: 24px;
               border: 0;
               width: 100%;
            }

            div.cdss-table-title {
               width: 100%;
               float: left;
               clear: both;
               font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
               color: #006FA4;
               font-size: 14px;
               margin-top: 8px;
               margin-right: 0;
               margin-bottom: 8px;
               margin-left: 0;
               font-weight: 800;
            }

            div.cdss-table-intro {
               width: 100%;
               float: left;
               clear: both;
               color: #585858;
               margin-bottom: 8px;
            }

         </style>

         <script>

            $(function() {

               let medsContainer = $('tr[sq_id=meds_container] > td').first();
               let meds = <?= $meds ?>;
               let meds_html = "";

               meds_html += "<div class='cdss-table-title'>MEDICATIONS LIST</div>";

               meds_html += "<div class='cdss-table-intro'>At this time we are going to go through each one of your medications. Please tell me all of the medications you have in your home including the over the counter medications. I am interested in the name of the medication along with the dose and instructions on how you take the medication.</div>";

               meds_html += "<table class='cdss-meds-reconciliation'>" +
                  "<thead><tr>" +
                  "<th class='cdss-meds-reconciliation-stub'>&nbsp;</th>" +
                  "<th class='cdss-table-cell'>prescribed</th>" +
                  "<th class='cdss-table-cell'>reported</th>" +
                  "<th class='cdss-table-cell'>rule</th>" +
                  "<th class='cdss-table-cell'>reconciliation</th>" +
                  "</tr></thead>" +
                  "<tbody>"
               ;

               //console.log(meds);

               let odd = true;

               for ( var m=0; m<meds.length; m++ ){

                  meds_html += cdss_meds_table_row( meds[m], odd );

                  odd = !odd;

               }

               meds_html += "</tbody></table>";

               medsContainer
                  .css({
                     'background-color': 'white',
                     'border': '3px solid #0083C1',
                     'padding': '6px'
                  })
                  .html( meds_html )
               ;

               $(window).resize(function () {
                  let twidth = medsContainer.width();
                  $('.cdss-table-cell').css('width', (twidth - 50)/4 + 'px');
               })
               .trigger('resize');

            });

            function cdss_table_resize() {
            }

            function cdss_meds_table_row( x, odd ){

               let med_rule_choices = <?= $med_rule_choices ?>;
               let med_reconciliation_choices = <?= $med_reconciliation_choices ?>;
               let selected = "";
               let row_class = ( odd ) ? 'cdss-odd' : 'cdss-even';
               let row_html = `<tr>`;

               row_html += `<td class='cdss-meds-reconciliation-stub'>${x.med_number}.<br /><br /><a href='javascript:cdss_remove_med(${x.med_number});'><img src='<?= $this->img_trashcan_url ?>'></a></td>\n`;

               row_html += `<td class='cdss-med_prescribed cdss-table-cell ${row_class}'><textarea id='med_prescribed-${x.med_number}'>${x.med_prescribed}</textarea></td>`;

               row_html += `<td class='cdss-med_reported cdss-table-cell ${row_class}'><textarea id='med_reported-${x.med_number}'>${x.med_reported}</textarea></td>`;

               row_html += `<td class='cdss-med_rule cdss-table-cell ${row_class}'><select id='med_rule-${x.med_number}'>\n`;
               row_html += "<option value=''>&nbsp;</option>\n";
               $.each( med_rule_choices, function(med_rule_option, med_rule_label) {
                  selected = ( x.med_rule == med_rule_option ) ? "selected" : "";
                  row_html += `<option value='${med_rule_option}' ${selected}>${med_rule_label}</option>\n`;
               });
               row_html += "</select></td>";

               row_html += `<td class='cdss-med_reconciliation cdss-table-cell ${row_class}'><select id='med_reconciliation-${x.med_number}'>\n`;
               row_html += "<option value=''>&nbsp;</option>\n";
               $.each( med_reconciliation_choices, function(med_reconciliation_option, med_reconciliation_label) {
                  selected = ( x.med_reconciliation == med_reconciliation_option ) ? "selected" : "";
                  row_html += `<option value='${med_reconciliation_option}' ${selected}>${med_reconciliation_label}</option>\n`;
               });
               row_html += `</select><br /><textarea  id='med_reconciliation_notes-${x.med_number}'>${x.med_reconciliation_notes}</textarea></td>`;

               row_html += "</tr>";

               return row_html;
            }

         </script>

         <?php

      } // medications

      else if ( $instrument==="chart_review") {

         /*
          * get options for SELECT controls
          */

         $dx_rule_choices = json_encode( $this->getChoiceLabels('dx_rule') );

         /*
          * assemble the meds data object
          */

         $sql = "
 SELECT IFNULL(m.`instance`,1) AS `dx_number`
  , IFNULL(m1.`value`, '') AS `dx_code`
  , IFNULL(m2.`value`, '') AS `dx_description`
  , IFNULL(m3.`value`, '') AS `dx_rule`
FROM redcap_data m
  LEFT JOIN redcap_data m1 ON m1.`project_id`=m.`project_id` AND m1.`record`=m.`record` AND m1.`event_id`=m.`event_id` AND m1.`instance`<=>m.`instance` AND m1.`field_name`='dx_code'
  LEFT JOIN redcap_data m2 ON m2.`project_id`=m.`project_id` AND m2.`record`=m.`record` AND m2.`event_id`=m.`event_id` AND m2.`instance`<=>m.`instance` AND m2.`field_name`='dx_description'
  LEFT JOIN redcap_data m3 ON m3.`project_id`=m.`project_id` AND m3.`record`=m.`record` AND m3.`event_id`=m.`event_id` AND m3.`instance`<=>m.`instance` AND m3.`field_name`='dx_rule'
WHERE m.`project_id`={$project_id_sql}
  AND m.`event_id`={$event_id_sql}
  AND m.`record`={$record_sql}
  AND m.`field_name`='diagnosis_complete'
ORDER BY m.`instance`        
         ";

         $diagnoses = json_encode( $this->fetchRecords($sql) );

         ?>

         <style>

            table.cdss-diagnoses {
               width: 100%;
               border-spacing: 0;
               border-collapse: collapse;
            }

            table.cdss-diagnoses thead,  table.cdss-diagnoses tbody {
               display: block;
            }

            table.cdss-diagnoses tbody {
               height: 400px;
               overflow-y: scroll;
               overflow-x: hidden;
            }

            table.cdss-diagnoses th, table.cdss-diagnoses td {
               font-size: 11px;
               font-weight: 600;
               padding-top: 2px;
               padding-bottom: 2px;
               padding-left: 4px;
               padding-right: 4px;
            }

            table.cdss-diagnoses th {
               height: 30px;
               background-color: #0083C1;
               border: 1px solid #0083C1;
               vertical-align: bottom;
               color: #D6E6EF;
            }

            table.cdss-diagnoses td.cdss-even {
               background-color: white;
            }

            table.cdss-diagnoses td.cdss-odd {
               background-color: #E7F1F8;
            }

            table.cdss-diagnoses td * {
               vertical-align: middle;
               font-size: 11px;
               font-weight: 600;
               color: #00699B;
               background-color: transparent;
            }

            table.cdss-diagnoses input[type=text], table.cdss-diagnoses select {
               height: 30px;
               padding-right: 6px;
               padding-left: 6px;
               border: 1px solid lightgray;
               background-color: transparent;
               width: 100% !important;
               max-width: 100% !important;
            }

            th.cdss-diagnoses-stub, td.cdss-diagnoses-stub {
               width: 50px;
            }

            td.cdss-diagnoses-stub {
               text-align: center;
               color: lightslategray;
               background-color: white;
            }

            div.cdss-table-title {
               width: 100%;
               float: left;
               clear: both;
               font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
               color: #006FA4;
               font-size: 14px;
               margin-top: 8px;
               margin-right: 0;
               margin-bottom: 8px;
               margin-left: 0;
               font-weight: 800;
            }

            div.cdss-table-intro {
               width: 100%;
               float: left;
               clear: both;
               color: #585858;
               margin-bottom: 8px;
            }

         </style>

         <script>

            $(function() {

               let dxContainer = $('tr[sq_id=ehr_dx_container] > td').first();
               let diagnoses = <?= $diagnoses ?>;
               let dx_html = "";

               dx_html += "<div class='cdss-table-title'>DIAGNOSES</div>";

               dx_html += "<table class='cdss-diagnoses'>" +
                  "<thead><tr>" +
                  "<th class='cdss-diagnoses-stub'>&nbsp;</th>" +
                  "<th class='cdss-dx_code'>code</th>" +
                  "<th class='cdss-dx_description'>description</th>" +
                  "<th class='cdss-dx_rule'>rule</th>" +
                  "</tr></thead>" +
                  "<tbody>"
               ;

               //console.log(meds);

               let odd = true;

               for ( var i=0; i<diagnoses.length; i++ ){

                  dx_html += cdss_dx_table_row( diagnoses[i], odd );

                  odd = !odd;

               }

               dx_html += "</tbody></table>";

               dxContainer
                  .css({
                     'background-color': 'white',
                     'border': '3px solid #0083C1',
                     'padding': '6px'
                  })
                  .html( dx_html )
               ;

               $(window).resize(function () {
                  let twidth = dxContainer.width() - 70;
                  $('.cdss-dx_code').css('width', 0.2 * twidth + 'px');
                  $('.cdss-dx_description').css('width', 0.5 * twidth + 'px');
                  $('.cdss-dx_rule').css('width', 0.3 * twidth + 'px');
               })
                  .trigger('resize');

            });

            function cdss_dx_table_row( x, odd ){

               let dx_rule_choices = <?= $dx_rule_choices ?>;
               let selected = "";
               let row_class = ( odd ) ? 'cdss-odd' : 'cdss-even';
               let row_html = `<tr>`;

               row_html += `<td class='cdss-diagnoses-stub'>${x.dx_number}.&nbsp;<a href='javascript:cdss_remove_dx(${x.dx_number});'><img src='<?= $this->img_trashcan_url ?>'></a></td>\n`;

               row_html += `<td class='cdss-dx_code ${row_class}'><input type='text' id='dx_code-${x.dx_number}' value='${x.dx_code}' /></td>`;

               row_html += `<td class='cdss-dx_description ${row_class}'><input type='text' id='dx_description-${x.dx_number}' value='${x.dx_description}' /></td>`;

               row_html += `<td class='cdss-dx_rule ${row_class}'><select id='dx_rule-${x.dx_number}'>\n`;
               row_html += "<option value=''>&nbsp;</option>\n";
               $.each( dx_rule_choices, function(dx_rule_option, dx_rule_label) {
                  selected = ( x.dx_rule == dx_rule_option ) ? "selected" : "";
                  row_html += `<option value='${dx_rule_option}' ${selected}>${dx_rule_label}</option>\n`;
               });
               row_html += "</select></td>";

               row_html += "</tr>";

               return row_html;
            }

         </script>

         <?php

      } // chart review

   } // redcap_data_entry_form

   function redcap_module_configure_button_display(){
      return false; // hide the setup button
   }

}