<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$module = new Yale\cdss\CDSS();

$HtmlPage = new HtmlPage();
$HtmlPage->ProjectHeader();

?>

<?=$module->getCodeFor("cdss_setup")?>

<div id="cdss-screencover"></div>

<div id="cdss-curtain"></div>

<div id="cdss_setup_canvas">

   <div id="cdss_metadata_selectors"></div>

   <textarea id="cdss-rbase-text"></textarea>

   <div class="cdss-row">
      <button id="cdss-save-rbase" class="cdss-editor-button cdss-editor-button-save">SAVE RULEBASE</button>
   </div>

   <!-- inspector -->
   <div class="cdss-popup cdss-draggable" id="cdss-inspector">

      <div class="cdss-popup-header cdss-drag-handle">
         <div class="cdss-popup-row-left" id="cdss-inspector-title">
            CDSS inspector
         </div>
         <div class="cdss-popup-row-left" id="cdss-inspector-metaclass">
            xxx
         </div>
         <!--div class="cdss-popup-row-left" id="cdss-inspector-itemname">
            xxx
         </div-->
         <div class="cdss-popup-row-right">
            <a href="javascript: cdss.inspector_close()"><i class="fas fa-times cdss-spinme"></i></a>
         </div>
      </div>

      <div class="cdss-popup-content" id="cdss-inspector-content"></div>

   </div> <!-- inspector -->

   <!-- editor -->
   <div class="cdss-popup cdss-draggable" id="cdss-editor">

      <div class="cdss-popup-header cdss-drag-handle">
         <div class="cdss-popup-row-left" id="cdss-editor-title">
            CDSS editor
         </div>
         <div class="cdss-popup-row-left" id="cdss-editor-metaclass">
            xxx
         </div>
         <!--div class="cdss-popup-row-left" id="cdss-editor-itemname">
            xxx
         </div-->
         <div class="cdss-popup-row-right">
            <a href="javascript: cdss.editor_close()"><i class="fas fa-times"></i></a>
         </div>
      </div>

      <div class="cdss-popup-content" id="cdss-editor-content"></div>


   </div> <!-- editor -->

   <!-- rule builder -->
   <div class="cdss-popup cdss-draggable" id="cdss-rule-builder">

      <div class="cdss-popup-header cdss-drag-handle">
         <div class="cdss-popup-row-left popup-title" id="cdss-editor-title">
            CDSS Rule Builder
         </div>
         <div class="cdss-popup-row-left" id="cdss-editor-metaclass">
            xxx
         </div>
         <!--div class="cdss-popup-row-left" id="cdss-editor-itemname">
            xxx
         </div-->
         <div class="cdss-popup-row-right">
            <a href="javascript: cdss.editor_close()"><i class="fas fa-times cdss-spinme"></i></a>
         </div>
      </div>

   </div> <!-- rule builder -->

</div> <!-- canvas -->
