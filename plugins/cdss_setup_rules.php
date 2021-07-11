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

<div class="container" id="cdss-rules-container">

   <div class="row" id="cdss-rules-controls">

      <div class="col-lg cdss-config-panel cdss-config-panel-smxll cdss-look-out-below">
         <div class="cdss-config-panel-row">

            <div class="cdss-config-panel-row-left">
               <input type="button" id="cdss-config-save" class="cdss-large-button" onclick="cdss.saveConfig()" value="SAVE ALL CHANGES" />
            </div>
            <div class="cdss-config-panel-row-left cdss-config-italicized-subdued" id="cdss-config-save-message">
               never saved
            </div>

         </div>
      </div>

      <div class="col-lg cdss-config-panel cdss-look-out-below">

         <div class="cdss-config-panel-row">

            <div class="cdss-config-panel-row-right">
               <input type="button" id="cdss-config-restore" class="cdss-small-button" onclick="cdss.loadConfig(1)" value="restore from backup" />
            </div>

            <div class="cdss-config-panel-row-right">
               <label for="cdss-config-backup-select"></label><select id="cdss-config-backup-select"></select>
            </div>

         </div>

      </div>

   </div> <!-- cdss-rules-controls -->

   <div class="row" id="cdss-rules-specifications-row"> <!-- bootstrap flex row -->

      <div class="col-lg cdss-config-panel cdss-look-out-below" id="cdss-rules-specifications-container"> <!-- bootstrap flex column -->
         
         <div class="cdss-config-panel-title">
            CDSS RULE SPECIFICATIONS
         </div>

         <div class="cdss-config-panel-description">
            You may specify up to one million rules.
         </div>

         <div id="cdss-rule-specifications" class="cdss-sortable">

            <table id="cdss-rule-9999" class="cdss-rule-specification cdss-rule-specification-template cdss-config-table" data-rule_number="9999" data-rule_index="0">

               <tbody>

                  <tr class="cdss-config-table-header">

                     <td class="cdss-gutter-left">
                        <a class="cdss-spec-expander" href="javascript:cdss.expandOrCollapse('9999')"><i class="fas fa-compress-alt fa-lg"></i></a>
                     </td>

                     <td class="cdss-rule-left-stub">
                        rule name
                     </td>

                     <td class="cdss-rule-input">
                        <label for="cdss-rule-9999-name"></label><input type="text" name="rule-name" id="cdss-rule-9999-name" class="cdss-rule-name" data-configitem="rule_name" value="Rule #9999" />
                     </td>

                     <td class="cdss-gutter-right">
                        <a href="javascript:cdss.removeSpec('rule', '9999');"><i class="fas fa-times fa-lg"></i></a>
                     </td>

                  </tr>

                  <tr>
                     <td colspan="4">

                        <table class="cdss-rule-conditions-table">

                           <tbody class="cdss-rule-conditions-subtable" id="cdss-rule-9999-conditions">

                              <tr class="cdss-collapsible cdss-rule-condition" id="cdss-rule-9999-condition-8888" data-rule_number="9999" data-condition_number="8888" data-condition_index="0"> <!-- the template for additional conditions -->

                                 <td class="cdss-gutter-left centered">
                                 </td>

                                 <td class="cdss-condition-left-stub">

                                    <div class="cdss-rule-condition-if">
                                       IF
                                    </div>

                                    <div class="cdss-rule-condition-join">
                                       <label for="cdss-rule-9999-condition-8888-join"></label>
                                       <select id="cdss-rule-9999-condition-8888-join" data-configitem="rule_condition_join">
                                          <option value="and">&nbsp;&nbsp;AND</option>
                                          <option value="or">&nbsp;&nbsp;OR</option>
                                          <option value="oparen">(</option>
                                          <option value="cparen">)</option>
                                       </select>
                                    <div>

                                 </td>

                                 <td class="cdss-rule-condition-basis-panel">
                                    <input name="cdss-rule-9999-condition-8888-basis" id="cdss-rule-9999-condition-8888-basis" class="cdss-condition-rule-basis" data-configitem="rule_condition_basis" data-rule_number="9999" data-condition_number="8888" placeholder="start typing or [c]-conditions, [d]-diseases, [m]-medications, [f]-study fields or [space]-all" />
                                    <select name="cdss-rule-9999-condition-8888-basis-option" id="cdss-rule-9999-condition-8888-basis-option" class="cdss-condition-rule-basis-option" data-rule_number="9999" data-condition_number="8888" data-configitem="rule_condition_basis_option">
                                    </select>
                                    <input type="text" placeholder="enter value" name="cdss-rule-9999-condition-8888-basis-option-cutpoint" id="cdss-rule-9999-condition-8888-basis-option-cutpoint" class="cdss-condition-rule-basis-option-cutpoint" data-configitem="rule_condition_basis_option_cutpoint" data-rule_number="9999" data-condition_number="8888" />
                                 </td>

                                 <td class="cdss-gutter-right centered">
                                    <a class="cdss-rule-condition-join" href="javascript:cdss.removeRuleCondition('9999', '8888');" title="remove this condidtion"><i class="fas fa-times"></i></a>
                                 </td>

                              </tr>

                           </tbody>
                        </table>
                     </td>
                  </tr>

                  <tr class="cdss-collapsible">
                     <td colspan="4"><a href="javascript:cdss.addRuleCondition('9999');" title="add a new rule condition"><i class="fas fa-plus"></i></a></td>
                  </tr>

                  <tr class="cdss-collapsible">

                     <td class="cdss-gutter-left"></td>

                     <td class="cdss-rule-left-stub">

                        <div class="cdss-rule-condition-if">
                           THEN
                        </div>

                     </td>

                     <td class="cdss-rule-input">

                           <select name="cdss-rule-9999-action" id="cdss-rule-9999-action" class="cdss-rule-action" data-configitem="rule_action" onchange="cdss.ruleActionSelect(9999)">
                              <option value="">-- select a report action --</option>
                           </select>

                     </td>

                     <td class="cdss-gutter-right"></td>

                  </tr>
                  
                  <tr id="cdss-rule-9999-params_wrapper" class="cdss-rule-params-wrapper cdss-collapsible">

                     <td class="cdss-gutter-left"></td>

                     <td class="cdss-rule-left-stub"></td>

                     <td class="cdss-rule-params">

                        <div class="cdss-rule-params">
                           HI MOM
                        </div> <!-- wrapping div -->

                     </td>

                     <td class="cdss-gutter-right"></td>

                  </tr>

               </tbody>

            </table> <!-- cdss-rule-9999-specification -->

         </div> <!-- cdss-rule-specifications -->

         <div style="width:100%">
            <div class="cdss-config-table-add">
               <a href='javascript:cdss.addRuleSpec();'><i class="fas fa-plus fa-2x"></i></a>
            </div>
         </div>

      </div> <!-- cdss-rules-specifications-container (flex col) -->

   </div> <!-- cdss-rules-specifications-row (flex row) -->

</div> <!-- cdss-rules-container -->

<div id="cdss-rule-templates">

   <table class="cdss-rule-params" id="cdss-param-table-template">

      <tbody>

         <tr class="cdss-rule-param" id="cdss-rule-param-textarea-template">
            <td class="cdss-rule-param-label">
               add a freetext entry to the medications management report
            </td>
            <td class="cdss-rule-param-input">
               <textarea id="cdss-rule-9999-param-comment" class="cdss-rule-param-comment" data-configitem="cdss_rule_comment"></textarea>
            </td>
            <td class="cdss-gutter-right"></td>
         </tr>

         <tr class="cdss-rule-param" id="cdss-rule-param-medication-template">
            <td class="cdss-rule-param-label">
               select a medication
            </td>
            <td class="cdss-rule-param-input">
               <select id="cdss-rule-9999-param-main_item" class="cdss-rule-param-medication" data-configitem="cdss_rule_main_item">
                  <option value="">-- select a medication --</option>
               </select>
            </td>
            <td class="cdss-gutter-right"></td>
         </tr>

         <tr class="cdss-rule-param" id="cdss-rule-param-general_item-template">
            <td class="cdss-rule-param-label">
               select a med, condition, disease or field
            </td>
            <td class="cdss-rule-param-input">
               <input id="cdss-rule-9999-param-secondary_item" class="cdss-rule-param-general_item" data-configitem="cdss_rule_secondary_item" />
            </td>
            <td class="cdss-gutter-right"></td>
         </tr>

         <tr class="cdss-rule-param cdss-rule-param-additional-item" id="cdss-rule-param-additional_item-template">
            <td class="cdss-rule-param-label">
               additional item to report
            </td>
            <td class="cdss-rule-param-input">
               <input class="cdss-rule-param-general_item" />
            </td>
            <td class="cdss-gutter-right cdss-rule-param-remove-item">
               <a href="javascript:cdss.removeRuleParamAdditionalItem('9999', '1');" title="remove this additional item"><i class="fas fa-times"></i></a>
            </td>
         </tr>

         <tr class="cdss-rule-param" id="cdss-rule-param-add-item-template">
            <td colspan="4" class=" cdss-rule-param-add-item">
               <a href="" title="add an additional item to report"><i class="fas fa-plus"></i></a>
            </td>
         </tr>

      </tbody>

   </table> <!-- param table template -->

</div>

<!-- yesno, hello -->

<div id="cdss-yesno-panel" class="cdss-panel cdss-draggable dialogWithShadow" style="display:none">

   <div class="cdss-panel-header-row">
      <div class="cdss-panel-row-left" id="cdss-yesno-panel-title">
         A QUESTION FOR YOU
      </div>
      <div class="cdss-panel-row-right">
         <a href="javascript: cdss.closePanel('cdss-yesno-panel')"><i class="fas fa-times"></i></a>
      </div>
   </div>

   <div id="cdss-yesno-message" class="cdss-panel-row cdss-panel-message">
   </div>

   <div class="cdss-panel-row">
      <div style='float:left'>
         <input type="button" value="yes, make it so" onClick="cdss.Yes();" class="cdss-panel-button" />
      </div>
      <div style='float:right'>
         <input type="button" value="no, skip it" onClick="cdss.closePanel('cdss-yesno-panel');" class="cdss-panel-button" />
      </div>
   </div>

</div> <!-- YesNo -->

<div id="cdss-hello-panel" class="cdss-panel cdss-draggable" style="display:none">
   <div class="cdss-panel-header-row">
      <div class="cdss-panel-row-left" id="cdss-hello-panel-title">
         AND NOW THIS
      </div>
      <div class="cdss-panel-row-right">
         <a href="javascript:cdss.helloClose();"><i class="fas fa-times fa-2x"></i></a>
      </div>
   </div>

   <div id="cdss-hello-message" class="cdss-panel-row cdss-panel-message">
   </div>
</div> <!-- hello -->
