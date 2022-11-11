<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Yale\Yes3\Yes3;

/**
 * The instantiated EM class
 */
$module = new Yale\CDSS\CDSS();

$record =   $_GET['id'];
//$event_id = (int)$_GET['event_id'];

?>

<!DOCTYPE html>

<html lang="en">

    <head>      
        <meta charset="UTF-8">
        <title>CDSS report for record #<?= $record ?></title>

        <link href="<?= APP_PATH_WEBROOT ?>Resources/css/style.css" rel="stylesheet">
 
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        
        <?= $module->getCodeForV2("cdss_report", $record) ?>
        
        <script type="text/javascript">

            console.log("CDSS Reports", CDSS);

        </script>
    </head>

    <body>

        <div id="cdss-report-container">hi mom</div>

    </body>

</html>