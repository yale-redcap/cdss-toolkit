<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$module = new Yale\CDSS\CDSS();

//exit( $module->getUrl("css/cdss_setup.css") );

//$HtmlPage = new HtmlPage();

//$HtmlPage->addStylesheet( $module->getUrl("css/cdss_setup.css"), "screen,print");

//$HtmlPage->stylesheets[] = array('media'=>"screen,print", 'href'=>$module->getUrl("css/cdss_setup.css"));

//exit(print_r($HtmlPage, true));

//$HtmlPage->ProjectHeader();

?>

<?=$module->getCodeFor("cdss_setup_rules")?>

<div id="cdss-screencover"></div>

<div id="cdss-curtain"></div>

<div class="container" id="cdss-rules-container">

   <div class="row" id="cdss-rules-controls">

      <div class="col-lg cdss-config-panel cdss-config-panel-smxll cdss-look-out-below">
         <div class="cdss-config-panel-row">

            <div class="cdss-config-panel-row-left">
               <input type="button" id="cdss-config-save" class="cdss-large-button" onclick="cdss.saveRules()" value="SAVE ALL CHANGES" />
            </div>
            <div class="cdss-config-panel-row-left cdss-config-italicized-subdued" id="cdss-rules-save-message">
               never saved
            </div>

         </div>
      </div>

      <div class="col-lg cdss-config-panel cdss-look-out-below">

         <div class="cdss-config-panel-row">

            <div class="cdss-config-panel-row-right">
               <input type="button" id="cdss-restore-rule" class="cdss-small-button" onclick="cdss.restoreRuleBackup()" value="restore from backup" />
            </div>

            <div class="cdss-config-panel-row-right">
               <select id="cdss-rule-backup-select"></select>
            </div>

         </div>

      </div>

   </div> <!-- cdss-rules-controls -->

   <div class="row" id="cdss-rules-specifications-row"> <!-- bootstrap flex row -->

      <div class="col-lg cdss-config-panel cdss-look-out-below" id="cdss-rules-specifications-container"> <!-- bootstrap flex column -->
         
         <div class="cdss-config-panel-title">
            CDSS RULE SPECIFICATIONS
         </div>

         <!--div class="cdss-config-panel-description">
            &nbsp;
         </div-->

         <div id="cdss-rule-specifications" class="cdss-sortable">


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

   <table id="cdss-rule-9999" class="cdss-rule-specification cdss-config-table" data-rule_number="9999" data-rule_index="0">

      <tbody>

      <tr class="cdss-config-table-header">

         <td class="cdss-gutter-left">
            <a class="cdss-spec-expander" href="javascript:cdss.expandOrCollapse('9999')"><i class="fas fa-compress-alt fa-lg"></i></a>
         </td>

         <td class="cdss-rule-left-stub">
            &nbsp;
         </td>

         <td class="cdss-rule-input">

            <input type="text" 
                name="rule-name" 
                placeholder="enter the rule name" 
                id="cdss-rule-9999-name" 
                class="cdss-rule-name" 
                data-configitem="rule_name" 
                value="Rule #9999" 
            />
            
            &nbsp;&nbsp;

            <select 
                name="cdss-rule-9999-action" 
                id="cdss-rule-9999-action" 
                class="cdss-rule-action" 
                data-configitem="rule_action" 
                onchange="cdss.ruleActionSelect(9999)" 
            >
               <option value="">-- select a report --</option>

            </select>
           
            <div id="cdss-rule-9999-message" class="cdss-rule-message" data-configitem="rule_message"></div>
         </td>

         <td class="cdss-gutter-right">
            <a href="javascript:cdss.removeRule('9999');"><i class="far fa-trash-alt fa-lg"></i></a>
         </td>

      </tr>

      

      <tr>
         <td colspan="4" class="cdss-rule-condition-container">

            <table class="cdss-rule-conditions-table">

               <tbody class="cdss-rule-conditions-subtable" id="cdss-rule-9999-conditions">

               <tr class="cdss-collapsible cdss-rule-condition" id="cdss-rule-9999-condition-8888" data-rule_number="9999" data-condition_number="8888" data-condition_index="0"> <!-- the template for additional conditions -->

                  <td class="cdss-gutter-left centered">
                  </td>

                  <td class="cdss-rule-condition-left-stub">

                     <select id="cdss-rule-9999-condition-8888-if" class="cdss-rule-condition-if cdss-rule-condition-cmd" data-rule_number="9999" data-configitem="rule_condition_if">
                        <option value="if">IF</option>
                        <option value="if_op">IF (</option>
                     </select>

                     <select id="cdss-rule-9999-condition-8888-join" class="cdss-rule-condition-join  cdss-rule-condition-cmd" data-rule_number="9999" data-configitem="rule_condition_join">
                        <option value="and">AND</option>
                        <option value="or">OR</option>
                        <option value="and_op">AND (</option>
                        <option value="or_op">OR (</option>
                        <option value="cp">)</option>
                        <option value="cp_and_op">) AND (</option>
                        <option value="cp_or_op">) OR (</option>
                        <option value="cp_and">) AND</option>
                        <option value="cp_or">) OR</option>
                     </select>

                  </td>

                  <td class="cdss-rule-condition-basis-panel">
                     <input name="cdss-rule-9999-condition-8888-basis" id="cdss-rule-9999-condition-8888-basis" class="cdss-condition-rule-basis" data-configitem="rule_condition_basis" data-rule_number="9999" data-condition_number="8888" placeholder="start typing or [c]-conditions, [d]-diseases, [m]-medications, [f]-study fields or [space]-all" />
                     <select name="cdss-rule-9999-condition-8888-basis-option" id="cdss-rule-9999-condition-8888-basis-option" class="cdss-condition-rule-basis-option" data-rule_number="9999" data-condition_number="8888" data-configitem="rule_condition_basis_option">
                     </select>
                     <input type="text" placeholder="enter value" name="cdss-rule-9999-condition-8888-basis-option-cutpoint" id="cdss-rule-9999-condition-8888-basis-option-cutpoint" class="cdss-condition-rule-basis-option-cutpoint" data-configitem="rule_condition_basis_option_cutpoint" data-rule_number="9999" data-condition_number="8888" />
                  </td>

                  <td class="cdss-gutter-right centered">
                     <a class="cdss-rule-condition-join" href="javascript:cdss.removeRuleCondition('9999', '8888');" title="remove this condidtion"><i class="far fa-trash-alt"></i></a>
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

         <td class="cdss-rule-condition-left-stub">

            <select id="cdss-rule-9999-condition-8888-then" class="cdss-rule-condition-then cdss-rule-condition-cmd" data-rule_number="9999" data-configitem="rule_condition_then">
               <option value="then">THEN</option>
               <option value="cp_then">) THEN</option>
            </select>

         </td>

         <td class="cdss-rule-params">

            <div class="cdss-rule-params">

                <table class="cdss-rule-params">

                    <tbody>

                        <tr class="cdss-rule-comment">
                            <td class="cdss-rule-comment-label">
                                <div class="cdss-rule-comment-label"></div>
                                <div class="cdss-rule-info">
                                    Along with what you enter into the box to the right, 
                                    all of the medications, conditions, diseases and study fields
                                    that contribute to the rule will - if they are observed for the patient -
                                    be passed to the selected report.
                                </div>
                            </td>
                            <td class="cdss-rule-comment">
                                <textarea id="cdss-rule-9999-comment" class="cdss-rule-comment" data-configitem="rule_comment"></textarea>
                            </td>
                            <td class="cdss-gutter-right">
                                &nbsp;
                            </td>
                        </tr>

                        <tr class="cdss-rule-param-add-additional-item" id="cdss-rule-9999-add-additional-item">
                            <td colspan="3" class="cdss-rule-param-add-item">
                                <a href="javascript: cdss.addRuleParamAdditionalItem('9999');" title="add an additional medicine, disease, condition or study field to report">
                                    <i class="fas fa-plus"></i>
                                    <em>add an additional medicine, disease, condition or study field to report</em>
                                </a>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div> <!-- wrapping div -->

         </td>

         <td class="cdss-gutter-right"></td>

      </tr>

    </tbody>

   </table> <!-- cdss-rule-9999-specification -->

    <table class="cdss-rule-params" id="cdss-param-table-template">

        <tbody>

            <tr class="cdss-rule-param">
                <td class="cdss-rule-param-label">
                blah blah
                </td>
                <td class="cdss-rule-param-input">
                <textarea id="cdss-rule-9999-comment" class="cdss-rule-comment" data-configitem="cdss_rule_param_input"></textarea>
                </td>
                <td class="cdss-gutter-right"></td>
            </tr>

            <tr class="cdss-rule-param-add-additional-item" id="cdss-rule-param-add-item-template">
                <td colspan="4" class="cdss-rule-param-add-item">
                    <a href="" title="add an additional medicine, disease, condition or study field to report">
                        <i class="fas fa-plus"></i>
                        <em>add an additional medicine, disease, condition or study field to report</em>
                    </a>
                </td>
            </tr>

        </tbody>

    </table>

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
