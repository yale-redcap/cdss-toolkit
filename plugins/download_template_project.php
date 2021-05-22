<?php


/*
 * Output all PHP errors to the browser. Comment out when ready for production.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/*
 * Technically we don't have to instantiate the $module object, since the EM loader does that for us.
 * However, doing so helps phpStorm with code checking.
 */
$module = new Yale\DOMCovXplorations\DOMCovXplorations();

domcovx_download_repo_metadata();

function domcovx_download_repo_metadata() {
   global $module;

   $repo_host = $module->getProjectSetting("repo-project-host");
   $repo_apitoken = $module->getProjectSetting("repo-project-apitoken");

   $requests_host = $module->getProjectSetting("requests-project-host");
   $requests_apitoken = $module->getProjectSetting("requests-project-apitoken");

   $subscriber_id = $module->getProjectSetting("subscriber-id");

   $domcovx_form_name = "domcovx_variables";

   /*
    * assemble an array of repo field names from the approved requests
    */
   $rqparams = array(
      'token' => $requests_apitoken,
      'content' => 'record',
      'format' => 'json',
      'fields' => 'request_item_name',
      'filterLogic' => '[subscriber_id]=' . $subscriber_id . ' AND [request_approved]=1 AND [request_item_type]=1'
   );

   if (!$approved = json_decode($module->REDCapAPI($requests_host, $rqparams), true)) exit('fail: no fields approved');

   $fields = [];
   foreach ($approved as $f) {
      $fields[] = $f['request_item_name'];
   }

   /*
    * get the data dictionary from the repo for the selected fields
    */
   $ddparams = array(
      'token' => $repo_apitoken,
      'content' => 'metadata',
      'format' => 'csv',
      'fields' => $fields
   );
   $dd_csv = $module->REDCapAPI($repo_host, $ddparams);

   /*
    * Now we have to change the form_name (2nd column) to 'domcovx_variables'
    * I bet there's some regex out there to do this in one step...
    */

   // convert the CSV file to an array
   $dd_records = explode(PHP_EOL, $dd_csv);

   // the modified CSV (just the form_name column updated)
   $new_dd_csv = $dd_records[0] . PHP_EOL; // header row (column names) is unchanged
   $K = count($dd_records);
   // process the data rows
   for ($j = 1; $j < $K; $j++) {
      // skip any trailing null records from the explode
      if ($dd_records[$j]) {
         $i0 = strpos($dd_records[$j], ",");
         $i1 = strpos($dd_records[$j], ",", $i0 + 1);
         $new_dd_csv .= substr($dd_records[$j], 0, $i0 + 1) . $domcovx_form_name . substr($dd_records[$j], $i1) . PHP_EOL;
      }
   }

   /*********************************************************
    *
    * WHAT FOLLOWS IS CRIBBED FROM:
    * /redcap_vx.y.z/Design/zip_instrument_download.php
    *
    *********************************************************/

   // Set name of temp file for zip
   $inOneHour = date("YmdHis", mktime(date("H") + 1, date("i"), date("s"), date("m"), date("d"), date("Y")));
   $target_zip = APP_PATH_TEMP . "{$inOneHour}_pid{$module->project_id}_" . generateRandomHash(6) . ".zip";

   // download filename
   $download_filename = $domcovx_form_name . "_" . date("Y-m-d_Hi") . ".zip";

   // I guess the Zip archive requires a Byte-Order-Mark?
   $data_dictionary = addBOMtoUTF8($new_dd_csv);

   // Create zip file
   $zip = new ZipArchive;

   // Start writing to zip file
   if ($zip->open($target_zip, ZipArchive::CREATE) !== TRUE) exit("fail: zip flipped");

   // Add OriginID.txt to zip file
   $zip->addFromString("OriginID.txt", SERVER_NAME);

   // Add data dictionary to zip file
   $zip->addFromString("instrument.csv", $data_dictionary);

   // Done adding to zip file
   $zip->close();

   // Download file and then delete it from the server
   header('Pragma: anytextexeptno-cache', true);
   header('Content-Type: application/octet-stream"');
   header('Content-Disposition: attachment; filename="' . $download_filename . '"');
   header('Content-Length: ' . filesize($target_zip));
   ob_end_flush();
   readfile_chunked($target_zip);
   unlink($target_zip); // unlink is PHP for 'delete'
}
