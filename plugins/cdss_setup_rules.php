<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$module = new Yale\cdss\CDSS();

//exit( $module->getUrl("css/cdss_setup.css") );

$HtmlPage = new HtmlPage();

//$HtmlPage->addStylesheet( $module->getUrl("css/cdss_setup.css"), "screen,print");

//$HtmlPage->stylesheets[] = array('media'=>"screen,print", 'href'=>$module->getUrl("css/cdss_setup.css"));

//exit(print_r($HtmlPage, true));

$HtmlPage->ProjectHeader();

?>

<?=$module->getCodeFor("cdss_setup_rules")?>

<div id="cdss-screencover"></div>

<div id="cdss-curtain"></div>

<div class="container">

   <div class="row">

      <div class="col-lg cdss-config-panel cdss-config-panel-smxll cdss-look-out-below">
         <div class="cdss-config-panel-row">

            <div class="cdss-config-panel-row-left">
               <input type="button" id="cdss-config-save" class="cdss-large-button" onclick="CDSSConfig.saveConfig()" value="SAVE ALL CHANGES" />
            </div>
            <div class="cdss-config-panel-row-left cdss-config-italicized-subdued" id="cdss-config-save-message">
               never saved
            </div>

         </div>
      </div>

      <div class="col-lg cdss-config-panel cdss-look-out-below">

         <div class="cdss-config-panel-row">

            <div class="cdss-config-panel-row-right">
               <input type="button" id="cdss-config-restore" class="cdss-small-button" onclick="CDSSConfig.loadConfig(1)" value="restore from backup" />
            </div>

            <div class="cdss-config-panel-row-right">
               <select id="cdss-config-backup-select"></select>
            </div>

         </div>

      </div>

   </div>

   <div class="row">

      <div class="col-lg cdss-config-panel cdss-look-out-below">
         
         <div class="cdss-config-panel-title">
            CDSS RULE SPECIFICATIONS
         </div>

         <div class="cdss-config-panel-description">
            You may specify up to one million rules.
         </div>

         <div id="cdss-rule-specifications" class="cdss-sortable">

            <table class="cdss-config-table" id="cdss-rule-specification-1">

               <tr class="cdss-config-table-header">

                  <td class="cdss-gutter-left">
                     <a class="cdss-spec-expander" href="javascript:cdss.expandOrCollapse('rule', 1)"><i class="fas fa-compress-alt fa-lg"></i></a>
                  </td>

                  <td class="cdss-config-table-item-description">
                     rule name
                  </td>
                  <td class="cdss-config-table-item-value">

                     <input type="text" class="cdss-input-large" name="rule-name" id="rule-name-1" data-configitem="rule_name" />

                  </td>

                  <td class="cdss-gutter-right">
                     <a href="javascript:CDSSConfig.removeSpec('rule', 1);"><i class="fas fa-times fa-lg"></i></a>
                  </td>

               </tr>

               <tr class="cdss-collapsible">

                  <td class="cdss-gutter-left"></td>

                  <td class="cdss-config-table-item-description cdss-rule-if">
                     IF
                  </td>
                  <td class="cdss-config-table-item-value cdss-single-line">

                     <div class="cdss-rule-is-or-not">
                        <input type="checkbox" class="balloon" name="rule-not-1" id="rule-not-1" data-configitem="rule_not" value="not" />
                        <label for="rule-not-1">
                           not
                        </label>
                     </div>

                     <div class="cdss-rule-basis">
                        <input name="rule-basis-1" id="rule-basis-1" data-configitem="rule_basis" />
                     </div>


                  </td>

                  <td class="cdss-gutter-right"></td>

               </tr>

            </table> <!-- PROTOTYPE RULESPEC TABLE -->
         </div>

         <div style="width:100%">
            <div class="cdss-config-table-add">
               <a href='javascript:CDSSConfig.addRuleSpec();'><i class="fas fa-plus fa-2x"></i></a>
            </div>
         </div>

      </div>

   </div>

</div>


<!-- ====== PROTOTYPE RULESPEC TABLE ====== -->

<table class="cdss-config-table" id="cdss-rule-specification-prototype" style="display:none">

   <tr class="cdss-config-table-header">

      <td class="cdss-gutter-left">
         <a class="cdss-spec-expander" href="javascript:CDSSConfig.expandOrCollapse('rule', 9999)"><i class="fas fa-compress-alt fa-lg"></i></a>
      </td>

      <td class="cdss-config-table-item-description">
         rule name
      </td>
      <td class="cdss-config-table-item-value">

         <input type="text" class="cdss-input-large" name="rule-name" id="rule-name-9999" data-configitem="rule_name" />

      </td>

      <td class="cdss-gutter-right">
         <a href="javascript:CDSSConfig.removeSpec('rule', 9999);"><i class="fas fa-times fa-lg"></i></a>
      </td>

   </tr>

   <tr class="cdss-collapsible">

      <td class="cdss-gutter-left"></td>

      <td class="cdss-config-table-item-description">
         minimum width (pixels)
      </td>
      <td class="cdss-config-table-item-value">

         <div class="cdss-vertical-option">
            <label>
               <input type="radio" name="rule-base-not-9999" data-configitem="rule_base_not" value="not" />
               not
            </label>
         </div>

         <input name="rule-base-9999" id="rule-base-9999" data-configitem="rule_base" />

      </td>

      <td class="cdss-gutter-right"></td>

   </tr>

</table> <!-- PROTOTYPE RULESPEC TABLE -->

<!-- yesno, hello -->

<div id="cdss-yesno-panel" class="cdss-panel cdss-draggable dialogWithShadow" style="display:none">

   <div class="cdss-panel-header-row">
      <div class="cdss-panel-row-left" id="cdss-yesno-panel-title">
         A QUESTION FOR YOU
      </div>
      <div class="cdss-panel-row-right">
         <a href="javascript: CDSSConfig.closePanel('cdss-yesno-panel')"><i class="fas fa-times"></i></a>
      </div>
   </div>

   <div id="cdss-yesno-message" class="cdss-panel-row cdss-panel-message">
   </div>

   <div class="cdss-panel-row">
      <div style='float:left'>
         <input type="button" value="yes, make it so" onClick="CDSSConfig.Yes();" class="cdss-panel-button" />
      </div>
      <div style='float:right'>
         <input type="button" value="no, skip it" onClick="CDSSConfig.closePanel('cdss-yesno-panel');" class="cdss-panel-button" />
      </div>
   </div>

</div> <!-- YesNo -->

<div id="cdss-hello-panel" class="cdss-panel cdss-draggable" style="display:none">
   <div class="cdss-panel-header-row">
      <div class="cdss-panel-row-left" id="cdss-hello-panel-title">
         AND NOW THIS
      </div>
      <div class="cdss-panel-row-right">
         <a href="javascript:CDSSConfig.helloClose();"><i class="fas fa-times fa-2x"></i></a>
      </div>
   </div>

   <div id="cdss-hello-message" class="cdss-panel-row cdss-panel-message">
   </div>
</div> <!-- hello -->
