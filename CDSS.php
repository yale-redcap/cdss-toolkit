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
//require_once "traits/trait_yes3Fn.php";

/**
 * an autoloader for CDSS classes and traits
 */
require "autoload.php";

class CDSS extends \ExternalModules\AbstractExternalModule 
{
    public $project_id;
    public $event_id;
    public $servicesUrl = "";
    public $setupServicesUrl = "";
    public $reports = [
        ['report_number'=>0, 'report_name'=>"Medications Management Report", 'report_title'=>"Medications management recommendation"],
        ['report_number'=>1, 'report_name'=>"Medication Dosing Report", 'report_title'=>"Dosing recommendation"],
        ['report_number'=>2, 'report_name'=>"High Risk Medications Report", 'report_title'=>"Consequence of taking medication(s)"],
        ['report_number'=>3, 'report_name'=>"Overtreatment Report", 'report_title'=>"Overtreatment recommendation"],
        ['report_number'=>4, 'report_name'=>"Renal Dosing Report", 'report_title'=>"Renal dosing recommendation"]    
    ];
    
    private $img_trashcan_url;

    use trait_yes3Fn;

    public function __construct() {

        parent::__construct(); // call parent (AbstractExternalModule) constructor

        $this->project_id = $this->getProjectId(); // defined in AbstractExternalModule; will return project_id or null

        if ($this->project_id) {

            $this->event_id = $this->getEventId();
            $this->servicesUrl = $this->getUrl("services/cdss_services.php?pid=".$this->project_id);
            $this->setupServicesUrl = $this->getUrl("services/cdss_setup_services.php?pid=".$this->project_id);
        }

        $this->img_trashcan_url = $this->getUrl('images/delete.jpg');
    } // __construct

    function redcap_module_configure_button_display(){
        return true; // show the setup button
    }

    function redcap_every_page_top($project_id)
    {
        if (PAGE == 'DataEntry/record_home.php') {
            ?>

            <div id="cdss-injection">HI MOM</div>

            <style>

                .cdss-report-button {

                    color: slategray;
                    border: 2px solid slategray;
                    background-color: white;
                    font-size: 1em;
                    font-weight: 600;
                    height: 2.5em;
                    border-radius: 1.25em;
                    padding-left: 1em;
                    padding-right: 1em;
                    margin-top: 1em;
                    margin-bottom: 1em;
                }

                .cdss-report-button:hover {
                    color: white;
                    background-color: slategray;
                }

            </style>

            <script>

                const CDSS = {
                    project_id: "<?= $_GET['pid'] ?>",
                    record: "<?= $_GET['id'] ?>",
                    ajax_url: "<?= $this->getUrl('services/cdss_services.php?pid='.$_GET['pid']) ?>",
                    report_url: "<?= $this->getUrl('plugins/cdss_report.php?pid='.$_GET['pid'].'&id='.$_GET['id']) ?>",
                    winNum: 0
                }

                CDSS.openReport = function(){

                    CDSS.winNum++;

                    window.open(CDSS.report_url, `CDSS_${CDSS.record}_${CDSS.winNum}`, "popup=1,width=800,height=800");
                }

                $(function(){

                    const $sib = $("div.projhdr:first");
                    /*
                    const $form = $("<form>", {
                        id: "cdss-report-form",
                        action: CDSS.report_url,
                        method: "post",
                        target: "_blank"
                    });

                    $form
                    .append( $("<input>", {
                        type: "hidden",
                        name: "redcap_csrf_token",
                        value: redcap_csrf_token
                    }))
                    .append( $("<input>", {
                        type: "hidden",
                        name: "project_id",
                        value: CDSS.project_id
                    }))
                    .append( $("<input>", {
                        type: "hidden",
                        name: "record",
                        value: CDSS.record
                    }))
                    .append( $("<button>", {
                        type: "submit",
                        class: "cdss-report-button",
                        text: "GENERATE THE CDSS REPORT"
                    }));

                    $sib.after( $form );
                    */
                    const $button = $("<input>",{
                        type: "button",
                        value: "GENERATE THE CDSS REPORT",
                        class: "cdss-report-button",
                        onClick: "CDSS.openReport()"
                    })

                    $sib.after( $button );

                    console.log("CDSS AHOY: ", CDSS);
                });

            </script>

            <?php
        }
    }

    function redcap_data_entry_form ( int $project_id, string $record = NULL, string $instrument, int $event_id, int $group_id = NULL, int $repeat_instance = 1 )
    {
        if ( $instrument==="cdss_variables" || $instrument==="cdss" ){

            $this->prepare_cdss_form($project_id, $event_id, $record);
        }
    }

    private function prepare_cdss_form($project_id, $event_id, $record )
    {
        ?>
            <script>

                let CDSS = {
                    windowNumber: 0,
                    windowPrefix: "cdss"
                }

                CDSS.openReportPage = function(){

                    let url="<?= $this->getUrl("plugins/cdss_report.php?pid=".$project_id."&event_id=".$event_id."&record=".$record) ?>"
                        + "&csrf_token=" + redcap_csrf_token
                    ;

                    CDSS.openPopupWindow(url);
                }

                CDSS.openPopupWindow = function(url, w, h) {

                    w = w || 1160;
                    h = h || 700;

                    windowNamePrefix = "CDSS";

                    CDSS.windowNumber++;

                    let windowName = windowNamePrefix + CDSS.windowNumber;

                    console.log(url,windowName);

                    // Fixes dual-screen position                         Most browsers      Firefox
                    let dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
                    let dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

                    let width  = window.innerWidth  ? window.innerWidth  : document.documentElement.clientWidth  ? document.documentElement.clientWidth  : screen.width;
                    let height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

                    let left = ((width  / 2) - (w / 2)) + dualScreenLeft;
                    let top  = ((height / 2) - (h / 2)) + dualScreenTop;
                    let newWindow = window.open(url, windowName, 'width=' + w + ',height=' + h + ',top=' + top + ',left=' + left);

                    if(!newWindow || newWindow.closed || typeof newWindow.closed=='undefined') {
                        alert("It looks like popups from REDCap are blocked on your computer.<br />Please call the data management team to enable REDCap popups.")
                    }

                    // Puts focus on the newWindow
                    if (window.focus) {
                        //newWindow.focus();
                    }

                    //return false;
                };

                $(function(){

                    $("div.cdss-report-button").html("<a href='javascript:CDSS.openReportPage()'>open the CDSS report page</a>");
                })

            </script>
        <?php
    }

    function redcap_save_record($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance)
    {
        $sql = "SELECT form_name FROM redcap_metadata WHERE project_id=? AND field_name=?";

        $medication_form_name = Yes3::fetchValue($sql, [$project_id, $this->getProjectSetting('cdss-medication-field')]);

        $diagnosis_form_name  = Yes3::fetchValue($sql, [$project_id, $this->getProjectSetting('cdss-diagnosis-field')]);

        //Yes3::logDebugMessage($project_id, $medication_form_name, 'redcap_save_record: medication_form_name');
        //Yes3::logDebugMessage($project_id, $diagnosis_form_name, 'redcap_save_record: diagnosis_form_name');
        //Yes3::logDebugMessage($project_id, $instrument, 'redcap_save_record: instrument');

        if ( !$repeat_instance ) $repeat_instance = 1;

        if ( $instrument===$medication_form_name){

            $this->cdss_item_form($project_id, $record, $instrument, $event_id, $repeat_instance
                , json_decode($this->getProjectSetting('cdss-medications'), true)
                , $this->getProjectSetting('cdss-medication-field')
                , $this->getProjectSetting('cdss-cdss-medications-field')
                , $this->getProjectSetting('cdss-medication-dose-field')
            );
        }    
        elseif ( $instrument===$diagnosis_form_name){

            $this->cdss_item_form($project_id, $record, $instrument, $event_id, $repeat_instance
                , json_decode($this->getProjectSetting('cdss-diseases'), true)
                , $this->getProjectSetting('cdss-diagnosis-field')
                , $this->getProjectSetting('cdss-cdss-diseases-field')
            );
        }
    }

    public function cdss_item_form($project_id, $record, $instrument, $event_id, $repeat_instance
        , $cdss_item_definitions
        , $redcap_item_field_name
        , $redcap_cdss_item_names_field
        , $redcap_cdss_dose_field=""
    )
    {
        if ( $redcap_cdss_dose_field ){
            $sql = "
            SELECT m.`record`, m.`event_id`, IFNULL(m.`instance`, '1') AS `instance`
            , UPPER(m.`value`) AS `value`
            , d.`value` AS `dose`
            FROM redcap_data m
              LEFT JOIN redcap_data d ON d.`project_id`=m.`project_id` AND d.`event_id`=m.`event_id` AND d.`record`=m.`record` AND d.`instance`<=>m.`instance` AND d.`field_name`=?
            WHERE m.`project_id`=? AND m.`record`=? AND m.`event_id`=? AND IFNULL(m.`instance`, '1')=?
            AND m.`field_name`=?
            ";
            $params = [ $redcap_cdss_dose_field, $project_id, $record, $event_id, $repeat_instance, $redcap_item_field_name];

            Yes3::logDebugMessage($project_id, $sql, 'medication_form: sql');
            Yes3::logDebugMessage($project_id, print_r($params, true), 'medication_form: params');
        }
        else {
            $sql = "
            SELECT m.`record`, m.`event_id`, IFNULL(m.`instance`, '1') AS `instance`
            , UPPER(m.`value`) AS `value`
            FROM redcap_data m
            WHERE m.`project_id`=? AND m.`record`=? AND m.`event_id`=? AND IFNULL(m.`instance`, '1')=?
            AND m.`field_name`=?
            ";
            $params = [ $project_id, $record, $event_id, $repeat_instance, $redcap_item_field_name];
        }

 
        $mm = Yes3::fetchRecords($sql, $params);

        $cdss_item_names = "";

        foreach( $mm as $m ){

            foreach( $cdss_item_definitions as $cdss_item_definition){

                if ( $cdss_item_definition['code'] ){

                    $search_terms = explode(",", strtoupper($cdss_item_definition['code']));
                    
                    //Yes3::logDebugMessage($project_id, print_r($search_terms, true), 'cdss_item_form: search terms');

                    foreach ($search_terms as $search_term){

    
                        if ( stripos($m['value'], trim($search_term)) !== false ){

                            if ( $cdss_item_names ) {

                                $cdss_item_names .= "\n";
                            }

                            $cdss_item_names .= strtoupper(trim($cdss_item_definition['name']));

                            // update the cdss indicator

                            if ( $cdss_item_definition['field'] ){

                                /**
                                 * encode the dose into the value
                                 */
                                if ( $redcap_cdss_dose_field && $m['dose'] ){

                                    $value = $m['dose'];
                                }
                                else {

                                    $value = "1";
                                }
                                
                                \REDCap::saveData([
                                   'project_id'=>$project_id,
                                    'records'=>$record,
                                    'dataFormat'=>'array',
                                    'data'=>[
                                        $record => [
                                            $event_id => [
                                                $cdss_item_definition['field'] => $value
                                            ]
                                        ]
                                    ],
                                    'overwritebehavior'=>'overwrite',
                                    'dataLogging'=>FALSE,
                                    'commitData'=>TRUE
                                ]);
                            }

                            break;
                        }
                    }
                }
            }
        }

        if ( $redcap_cdss_item_names_field ){

            \REDCap::saveData([
                'project_id'=>$project_id,
                'records'=>$record,
                'dataFormat'=>'array',
                'data'=>[
                    $record => [
                        'repeat_instances' => [
                            $event_id => [
                                $instrument => [
                                    $repeat_instance => [
                                        $redcap_cdss_item_names_field => $cdss_item_names
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'overwritebehavior'=>'overwrite',
                'dataLogging'=>FALSE,
                'commitData'=>TRUE
            ]);
        }
    }
}