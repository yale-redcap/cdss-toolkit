<?php

/*
 * Version 2.0.0 December 2020. Namespace changed from EM namespace to YES3, pending investigation of any adverse EM effects.
 * Version 1.1.0 August 2020. Rewritten to use native REDCap functions throughout.
 */

namespace Yale\CDSS;

use Parsedown;

/*
 * Table to hold debug log messages. Must be created by dba, see logDebugMessage() below.
 */
define('DEBUG_LOG_TABLE', "ydcclib_debug_messages");

trait trait_yes3Fn {

   // calls the generic REDCap query function, which is located in Config/init_functions.php
   // db_query returns mysqli_query() on success, or triggers a fatal redcap fail
   public function runQuery($sql) {
      return db_query($sql);
   }

   // like runQuery, but returns identity value
   public function runInsertQuery($sql) {
      $stmt = db_query($sql);
      if ($stmt == false) {
         return 0;
      } else {
         return db_insert_id();
      }
   } // runInsertQuery

   private function sql_limit_1( $sql ){
      if ( stripos($sql, "LIMIT 1") === false ) {
         return $sql . " LIMIT 1";
      } else {
         return $sql;
      }
   }

   public function fetchValue($sql) {
      $stmt = db_query( $this->sql_limit_1($sql) );
      if ( !$stmt ){
         return null;
      } else {
         $x = mysqli_fetch_array($stmt, MYSQLI_NUM);
         if ( !$x ) return null;
         else return $x[0];
      }
   }

   public function fetchRecord($sql) {
      $r = array();
      $stmt = db_query( $this->sql_limit_1($sql) );
      if ($stmt) {
         $r = db_fetch_assoc($stmt);
         db_free_result($stmt);
      }
      return $r;
   }

   public function fetchRecords($sql) {
      $r = array();
      $stmt = db_query($sql);
      if ($stmt) {
         while ($row = db_fetch_assoc($stmt)) {
            $r[] = $row;
         }
         db_free_result($stmt);
      }
      return $r;
   }

   /*
    * The q_ functions return escaped and quoted strings suitable for queries.
    * Date and Time formats are enforced, and "null" is returned for zero-length arguments.
    */

   public function sql_string($x) {
      if (strlen($x) == 0) {
         return "null";
      } else if (is_numeric($x)) {
         return "'" . $x . "'";
      } else {
         return "'" . db_real_escape_string($x) . "'";
      }
   }

   public function sql_datetime_string($x) {
      if (!$x) {
         return "null";
      } else {
         return "'" . strftime("%F %T", strtotime($x)) . "'";
      }
   }

   public function sql_date_string($x) {
      if (!$x) {
         return "null";
      } else {
         $d = strtotime($x);
         // if this didn't work, could be due to mm-dd-yyyy which doesn't fly
         if (!$d) {
            $date = str_replace('-', '/', $x);
            $d = strtotime($date);
         }
         if ($d) {
            return "'" . strftime("%F", $d) . "'";
         } else {
            return "null";
         }
      }
   }

   public function sql_timestamp_string() {
      return "'" . strftime("%F %T") . "'";
   }

   public function tableExists($table_name){
      $dbname = $this->fetchValue("SELECT DATABASE() AS DB");
      if ( !$dbname ) return false;
      $sql = "SELECT COUNT(*) FROM information_schema.tables"
            ." WHERE table_schema=".$this->sql_string($dbname)
            ." AND table_name=".$this->sql_string($table_name)
            ;
      return $this->fetchValue($sql);
   }

   /*
    * LOGGING DEBUG INFO
    * Call this function to log messages intended for debugging, for example an SQL statement.
    * The log database must exist and its name stored in the DEBUG_LOG_TABLE constant.
    * Required columns: project_id(INT), debug_message_category(VARCHAR(100)), debug_message(TEXT).
    * (best to add an autoincrement id field). Sample table-create query:
    *

         CREATE TABLE ydcclib_debug_messages
         (
             debug_id               INT AUTO_INCREMENT PRIMARY KEY,
             project_id             INT                                 NULL,
             debug_message_category VARCHAR(100)                        NULL,
             debug_message          TEXT                                NULL,
             debug_timestamp        TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP
         );

    */

   public function logDebugMessage($project_id, $msg, $msgcat="") {

      if ( !$this->tableExists(DEBUG_LOG_TABLE) ) return false;

      $sql = "INSERT INTO `".DEBUG_LOG_TABLE."` (project_id, debug_message, debug_message_category) VALUES ("
         .$this->sql_string($project_id).","
         .$this->sql_string($msg).","
         .$this->sql_string($msgcat)
         .");";

      return $this->runQuery($sql);
   }

   public function REDCapAPI( $host, $params ){

      $url = preg_replace('^/$^', '', $host) . "/api/";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));

      $response = curl_exec($ch);

      if (curl_errno($ch)) {
         $error_message = curl_error($ch);
      }

      curl_close($ch);

      if ( $error_message ) exit( $error_message );

      return $response;
   }

   /*** getCodeFor( libname ) *****************************************************************************************
    *
    * Returns JS and CSS code for YES3 'libraries'
    *
    * Expected file name patterns:
    *   css/[libname].css
    *   js/[libname].js
    *   services/[libname].php
    *
    * Code is wrapped in script and style tags and so can be inserted into html.
    *
    * JS code can include tags that will be resolved as follows:
    *   YES3_USERNAME    - The REDCap user name
    *   YES3_SUPER_USER  - 1 or 0 depending on user admin privs
    *   YES3_PROJECT_ID  - The project_id (PID)
    *   YES3_SERVICE_URL - The "safe URL" of the PHP script providing server-side (AJAX) services
    *                      ( services/[libname].php )
    *
    */

   public function getCodeFor( $libname ){

      $s = "\n<!-- getCodeFor: {$libname} -->";

      $js = str_replace(
         [
            'YES3_USERNAME',
            'YES3_SUPER_USER',
            'YES3_PROJECT_ID',
            'YES3_SERVICE_URL'
         ],
         [
            USERID,
            SUPER_USER,
            $this->getProjectId(),
            $this->getUrl("services/{$libname}_services.php")
         ],
         file_get_contents( $this->getModulePath()."js/{$libname}.js" )
      );

      $css = file_get_contents( $this->getModulePath()."css/{$libname}.css" );

      if ( $js ) $s .= "\n<script>{$js}</script>";
      if ( $css ) $s .= "\n<style>{$css}</style>";

      return $s;
   }

   public function markdownToHtml( $s ){
      $Parsedown = new \Parsedown();
      return $Parsedown->text( $s );
   }


   public function objectProperties()
   {
       $propKeys = [];

       /**
        * A ReflectionObject is apparently required to distinuish the non-private properties of this object
        * https://www.php.net/ReflectionObject
        */
       $publicProps = (new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC+\ReflectionProperty::IS_PROTECTED);

       foreach( $publicProps as $rflxnProp){
           $propKeys[] = $rflxnProp->name;
       }
        
       $props = [ 'CLASS' => __CLASS__ ];

       foreach ( $propKeys as $propKey ){

           $json = json_encode($this->$propKey);

           /**
            * some properties can't be json-encoded...
            */
           if ( $json===false ){
               $props[$propKey] = "json encoding failed for {$propKey}: " . json_last_error_msg();
           }
           else {
               $props[$propKey] = $this->$propKey;
           }
       }

       if ( !$json = json_encode($props) ){
           return json_encode(['message'=>json_last_error_msg()]);
       }
       
       return $json;
   }

   public function yes3UserRights()
   {
       $user = $this->getUser()->getRights();

       $formPermString = str_replace("[", "", $user['data_entry']);

       $formPerms = explode("]", $formPermString);
       $formPermissions = [];
       foreach( $formPerms as $formPerm){

           if ( $formPerm ){
               
               $formPermParts = explode(",", $formPerm);
               $formPermissions[ $formPermParts[0] ] = $formPermParts[1];
           }
       }

       return [

           'username' => $this->getUser()->getUsername(),
           'isDesigner' => ( $this->getUser()->hasDesignRights() ) ? 1:0,
           'isSuper' => ( $this->getUser()->isSuperUser() ) ? 1:0,
           'group_id' => (int)$user['group_id'],
           'dag' => ( $user['group_id'] ) ? \REDCap::getGroupNames(true, $user['group_id']) : "",
           'export' => (int)$user['data_export_tool'],
           'import' => (int)$user['data_import_tool'],
           'api_export' => (int)$user['api_export'],
           'api_import' => (int)$user['api_import'],
           'form_permissions' => $formPermissions
       ];
   }

   public function getCodeForV2( string $libname ):string
   {
       $s = "";
       $js = "";
       $css = "";
       
       $s .= "\n<!-- enhanced getCodeFor: {$libname} -->";
 
       $js .= file_get_contents( $this->getModulePath()."js/{$libname}.js" );

       $js .= "\n" . $this->initializeJavascriptModuleObject() . ";";

       $js .= "\nCDSS.moduleObject = " . $this->getJavascriptModuleObjectName() . ";";

       $js .= "\nCDSS.moduleObjectName = '" . $this->getJavascriptModuleObjectName() . "';";

       $js .= "\nCDSS.moduleProperties = " . $this->objectProperties() . ";\n";

       $js .= "\nCDSS.userRights = " . json_encode( $this->yes3UserRights() ) . ";\n";

       $css .= file_get_contents( $this->getModulePath()."css/{$libname}.css" );

       if ( $js ) $s .= "\n<script>{$js}</script>";

       if ( $css ) $s .= "\n<style>{$css}</style>";

       //print $s;

       return $s;
   }


}
