
let cdss = {
   study_fields: "",
   cdss_functions: "",
   cdss_variables: "",
   cdss_medications: "",
   cdss_diseases: "",
   cdss_conditions: "",
   cdss_rbase: "",
   rule_basis_source: [],
   rule_action_source: [],
   maxZ: 500,
   serviceUrl: "YES3_SERVICE_URL",
   user: "YES3_USERNAME",
   project_id: "YES3_PROJECT_ID",
   rule_condition_basis_options: {
      c: ["IS NOTED", "IS NOT NOTED"],
      d: ["IS PRESENT", "IS NOT PRESENT"],
      m: ["IS PRESCRIBED", "IS NOT PRESCRIBED", "DOSE IS GREATER THAN", "DOSE IS LESS THAN", "DOSE IS GREATER THAN OR EQUAL TO", "DOSE IS LESS THAN OR EQUAL TO"],
      f: ["IS GREATER THAN", "IS LESS THAN", "IS EQUAL TO", "IS GREATER THAN OR EQUAL TO", "IS LESS THAN OR EQUAL TO"]
   },
   cdss_actions: [
      {
         id: 1,
         name: 'report_comment',
         label: 'add a freetext entry to the medications management report',
         params: [
            {
               name: 'cdss_rule_comment',
               label: 'comment',
               type: 'textarea',
            }
         ]
      },
      {
         id: 2,
         name: 'report_interaction',
         label: 'add an entry to the high risk medications report',
         params: [
            {
               name: 'cdss_rule_main_item',
               label: 'medication',
               type: 'medication',
            },
            {
               name: 'cdss_rule_secondary_item',
               label: 'interacting medication or condition, if appropriate',
               type: 'general_item',
            },
            {
               name: 'cdss_rule_comment',
               label: 'consequence',
               type: 'textarea',
            }
         ]
      },
      {
         id: 3,
         name: 'report_dosing',
         label: 'add an entry to the dosing report',
         params: [
            {
               name: 'cdss_rule_main_item',
               label: 'medication',
               type: 'medication'
            },
            {
               name: 'cdss_rule_comment',
               label: 'recommendation',
               type: 'textarea'
            }
         ]
      },
      {
         id: 4,
         name: 'report_overtreatment',
         label: 'add an entry to the overtreatment report',
         params: [
            {
               name: 'cdss_rule_main_item',
               label: 'medication, condition, disease or study field',
               type: 'general_item'
            },
            {
               name: 'cdss_rule_comment',
               label: 'recommendation',
               type: 'textarea'
            }
         ]
      },
      {
         id: 5,
         name: 'report_renal',
         label: 'add an entry to the renal dosing report',
         params: [
            {
               name: 'cdss_rule_main_item',
               label: 'medication',
               type: 'medication'
            },
            {
               name: 'cdss_rule_comment',
               label: 'recommendation',
               type: 'textarea'
            }
         ]
      }
   ]

};

// formats date as mm-dd-yyyy
Date.prototype.mdy = function() {
var mm = this.getMonth() + 1; // getMonth() is zero-based(!)
var dd = this.getDate();
return [
(mm>9 ? '' : '0') + mm,
(dd>9 ? '' : '0') + dd,
this.getFullYear()
].join('-');
};

// formats date as ISO (yyyy-mm-dd)
Date.prototype.ymd = function() {
   var mm = this.getMonth() + 1; // getMonth() is zero-based(!)
   var dd = this.getDate();
   return [
      this.getFullYear(),
      (mm>9 ? '' : '0') + mm,
      (dd>9 ? '' : '0') + dd
   ].join('-');
};

// centers an element on screen
jQuery.fn.center = function (dx, dy) {
   var w = jQuery(window);
   dx = dx || 0;
   dy = dy || 0;
   var x = (( w.width() - this.width() ) / 2) + w.scrollLeft() + dx + "px";
   var y = (( w.height() - this.height() ) / 2) + w.scrollTop() + dy + "px";
   //console.log(w.width(), this.width(), w.scrollLeft(), x);
   this.css({"position":"fixed", "top":y, "left":x});
   return this;
};

cdss.requestService = function( params, doneFn, dataType ) {

   dataType = dataType || "text";

   var request = $.ajax({
      url: cdss.serviceUrl,
      type: "POST",
      dataType: dataType,
      data: params
   }).done(
      doneFn
   ).fail(function(jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      alert('AJAX error: ' + errorThrown);
   });

}

cdss.saveRules = function() {
   let rules = [];
   let ruleParent = $('div#cdss-rule-specifications');

   ruleParent.find('table.cdss-rule-specification').each(function () {

      let rule_number = $(this).data('rule_number');
      let rule_index = $(this).data('rule_index');
      let rule_name = $(this).find('input[data-configitem=rule_name]:first').val();

      let thisRule = {
         "rule_number": $(this).data('rule_number'),
         "rule_index": $(this).data('rule_index'),
         "rule_name": $(this).find('input[data-configitem=rule_name]:first').val(),
         "rule_conditions": [],
         "rule_action": $(this).find('select[data-configitem=rule_action]:first').val(),
         "rule_action_name": $(this).find('select[data-configitem=rule_action]:first option:selected').text(),
         "rule_action_params": []
      }

      $(this).find('tr.cdss-rule-condition').each(function () {
         let thisCondition = {
            "condition_number": $(this).data('condition_number'),
            "condition_index": $(this).data('condition_index'),
            "condition_if": $(this).find('select[data-configitem=rule_condition_if]:first').val(),
            "condition_join": $(this).find('select[data-configitem=rule_condition_join]:first').val(),
            "condition_basis": $(this).find('input[data-configitem=rule_condition_basis]:first').val(),
            "condition_basis_option": $(this).find('select[data-configitem=rule_condition_basis_option]:first').val(),
            "condition_basis_option_cutpoint": $(this).find('input[data-configitem=rule_condition_basis_option_cutpoint]:first').val()
         }
         thisRule.rule_conditions.push( thisCondition );
      })

      $(this).find('tr.cdss-rule-param').each(function () {
         let thisParam = {
            "action": $(this).data('action'),
            "index": $(this).data('param_index'),
            "name": $(this).data('param_name'),
            "type": $(this).data('param_type'),
            "label": $(this).data('param_label'),
            "value": $(this).find('[data-configitem=cdss_rule_param_input]:first').val()
         }
         thisRule.rule_action_params.push( thisParam );
      })

      rules.push( thisRule );

   })

   let params = {
      "request": "save-cdss-rules",
      "rules": rules
   }

   cdss.requestService(params, cdss.saveComplete, "json");

   console.log( rules );


}

cdss.saveComplete = function( response ){
   console.log( response );
}

cdss.populateRuleBackupSelect = function() {

   cdss.requestService( {"request": "get-cdss-rule-backup-select-options"}, cdss.populateRuleBackupSelectCallback, "json" );

}

cdss.populateRuleBackupSelectCallback = function( response ){
   console.log("populateRuleBackupSelectCallback", response);
   $('select#cdss-rule-backup-select')
      .empty()
      .append( response.options )
   ;
   $('div#cdss-rules-save-message').html( response.last_saved_message );
}

cdss.toTitleCase = function(str) {
   return str.replace(/\w\S*/g, function(txt){
      return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
   });
}

cdss.pretty_panel_name = function(metaclass){
   return cdss.toTitleCase(metaclass.split('_').join(' ')).replace('Cdss', 'CDSS');
}

cdss.sort_compare_id = function( a, b ) {
   let A = parseInt(a.id);
   let B = parseInt(b.id);
   if ( A < B ){
      return -1;
   }
   if ( A > B ){
      return 1;
   }
   return 0;
}

cdss.sort_compare_name = function( a, b ) {
   let A = a.name.toUpperCase();
   let B = b.name.toUpperCase();
   if ( A < B ){
      return -1;
   }
   if ( A > B ){
      return 1;
   }
   return 0;
}

cdss.getNameIndex = function (xx, theName) {

   let start=0, end=xx.length-1, i=0;

   // Iterate while start not meets end
   while (start<=end){

      // Find the mid index
      i = Math.floor((start + end)/2);

      // If element is present at mid, return True
      if (xx[i].name===theName) return i;

      // Else look in left or right half accordingly
      else if (xx[i].name < theName)
         start = i + 1;
      else
         end = i - 1;
   }

   return -1;
}

cdss.copyToClipboard = function (itemText) {
   const textArea = document.createElement('textarea');
   textArea.textContent = itemText;
   document.body.append(textArea);
   textArea.select();
   document.execCommand("copy");
   textArea.remove();
}


cdss.nl2br = function(str){return str.replace(/(?:\r\n|\r|\n)/g, '<br>');};

/*
 * popups
 */

cdss.spinMe = function() {
   $('i.cdss-spinme').addClass('fa-spin');
}

cdss.openPopup = function(popup) {
   cdss.maxZ += 1;
   $('#cdss-screencover').css({'z-index':cdss.maxZ-1}).show(); <!-- places the full-screen overlay just below the panel -->
   popup.center(0, 0).css({'z-index':cdss.maxZ}).show();
   setTimeout(cdss.spinMe, 10000);
};

cdss.closePopup = function(popupName) {
   $(`#${popupName}`).hide();
   $('#cdss-screencover').hide();
};

cdss.addListeners = function (){

   /* misc */
   $(".cdss-draggable").draggable({"handle": ".cdss-drag-handle"});


}

cdss.getReady = function() {
   var params = {'request':'get-cdss-settings'};
   cdss.requestService(params, cdss.getSetGo, "json");
}

cdss.getSetGo = function( response ){

   console.log( response );

   cdss.study_fields = response.study_fields;
   cdss.cdss_medications = response.cdss_medications;
   cdss.cdss_diseases = response.cdss_diseases;
   cdss.cdss_conditions = response.cdss_conditions;
   cdss.cdss_variables = response.cdss_variables;
   cdss.cdss_functions = response.cdss_functions;
   //cdss.cdss_actions = response.cdss_actions;
   cdss.cdss_rbase = response.cdss_rbase;

   cdss.cdss_medications.sort( cdss.sort_compare_name );
   cdss.cdss_diseases.sort( cdss.sort_compare_name );
   cdss.cdss_conditions.sort( cdss.sort_compare_name );
   cdss.cdss_variables.sort( cdss.sort_compare_name );
   cdss.cdss_functions.sort( cdss.sort_compare_id );
   //cdss.cdss_actions.sort( cdss.sort_compare_name );
   cdss.study_fields.sort( cdss.sort_compare_name );

   // assemble the big select source

   cdss.pushRuleBaseCategory('c', cdss.cdss_conditions);
   cdss.pushRuleBaseCategory('d', cdss.cdss_diseases);
   cdss.pushRuleBaseCategory('m', cdss.cdss_medications);
   cdss.pushRuleBaseCategory('f', cdss.study_fields);

   // TESTING ONLY
   let ruleNumber = cdss.addRuleSpec();
   /*
   cdss.populateRuleActionSelect( ruleNumber );
   cdss.populateRuleConditionBasisSelect( ruleNumber, "1" );
   cdss.addRuleSpecListeners(ruleNumber);
   cdss.addRuleConditionCmdSelectListeners(); // mainly parenthesis checking
    */

   //cdss.populateRuleParamTableElements("9999");
   //cdss.addRuleConditionListeners( "9999", "8888");
   $("tbody.cdss-rule-conditions-subtable").trigger("sortupdate");

}

cdss.pushRuleBaseCategory = function (category, x ) {
   for (let i=0; i<x.length; i++ ){
      cdss.rule_basis_source.push(
         {
            label: `[${category}] ${x[i].name}`,
            value: `[${category}] ${x[i].name}`
         }
      )
   }
}

cdss.populateRuleConditionBasisSelect = function( ruleNumber, conditionNumber ){
   let el = $(`#cdss-rule-${ruleNumber}-condition-${conditionNumber}-basis`);

   el.autocomplete({
      source: cdss.rule_basis_source,
      minLength: 0,
      select: function(event, ui){

         //console.log('RuleConditionBasis.select', ui);

         let rule_condition_basis = ui.item.value;
         let basisCategory = rule_condition_basis.substr(1, 1);
         let ruleNumber = $(this).data('rule_number');
         let conditionNumber = $(this).data('condition_number');

         let basis_option = $(`select#cdss-rule-${ruleNumber}-condition-${conditionNumber}-basis-option`);

         cdss.populateRuleConditionBasisOptionSelect( basis_option, basisCategory );

         cdss.addRuleConditionBasisOptionListeners( basis_option, ruleNumber, conditionNumber );

         //console.log('RuleConditionBasis.select', ruleNumber, conditionNumber, rule_condition_basis, basisCategory);

         //return false;
      }
   });

}

cdss.populateRuleActionSelect = function( ruleNumber ){
   let el = $(`#cdss-rule-${ruleNumber}-action`);

   for (let i=0; i<cdss.cdss_actions.length; i++){
      el.append(`<option value='${i}'>${cdss.cdss_actions[i].label}</option>`)
   }

}

cdss.addRuleConditionCmdSelectListeners = function(){

   $('select.cdss-rule-condition-cmd')
      .off()
      .on('change', function () {

         let nCmds = 0;
         let parenLevel = 0;
         let ruleNumber = $(this).attr('data-rule_number');
         let errMsg = "";

         $(`select.cdss-rule-condition-cmd[data-rule_number=${ruleNumber}]`).each( function () {
            nCmds++;
            let thisVal = $(this).val();
            let err = false;
            if ( thisVal==="if_op" || thisVal==="and_op" || thisVal==="or_op" ){
               if ( parenLevel ){
                  errMsg = "error: nested parentheses are not allowed";
                  err = true;
               }
               parenLevel++;
            }
            else if ( thisVal==="cp" || thisVal==="cp_then" ){
               if ( !parenLevel ){
                  errMsg = "error: no matching open parenthesis";
                  err = true;
               }
               parenLevel--;
            }
            if ( err ){
               $(this).addClass('cdss-error-mark');
            }
            else if ( $(this).hasClass('cdss-error-mark') ) {
               $(this).removeClass('cdss-error-mark');
            }
         })

         if ( parenLevel && !errMsg.length ){
            errMsg = "note: open parenthesis block"
         }

         cdss.postRuleMessage(ruleNumber, errMsg);

         console.log("parenLevel", ruleNumber, parenLevel);
      })
   ;
}

cdss.expandOrCollapse = function( ruleNumber ){

   let tbl = $(`table#cdss-rule-${ruleNumber}`);

   cdss.showSpecTable( tbl, ruleNumber );
}

cdss.showSpecTable = function( tbl, ruleNumber ){

   let collapsed = !tbl.hasClass('cdss-collapsed');
   let specType = tbl.data("spectype");
   /*
      // no collapse if required items missing
      tbl.find(".cdss-item-required").each(function () {
         if ( !$(this).val() ){
            collapsed = false;
         }
      })
   */
   if ( collapsed ){
      tbl.find("a.cdss-spec-expander").html("<i class='fas fa-expand-alt fa-lg'></i>");
      if ( !tbl.hasClass('cdss-collapsed') ) {
         tbl.addClass('cdss-collapsed' );
      }
      tbl.find("tr.cdss-collapsible" ).hide();
   } else {
      tbl.find("a.cdss-spec-expander").html("<i class='fas fa-compress-alt fa-lg'></i>");
      if (tbl.hasClass('cdss-collapsed')) {
         tbl.removeClass('cdss-collapsed');
      }
      tbl.find("tr.cdss-collapsible:not(.cdss-rule-params-wrapper)").show();
      
      // only show params elements if action selected
      cdss.showOrHideRuleParams(ruleNumber);
   }

}

cdss.removeRuleCondition = function(ruleNumber, conditionNumber){
   $(`tr#cdss-rule-${ruleNumber}-condition-${conditionNumber}`).remove();
   $(`tr#cdss-rule-${ruleNumber}-conditions`).trigger('sortupdate');
}

cdss.addRuleSetListeners = function() {

   $('div#cdss-rule-specifications')
      .sortable()
      .on('sortupdate', function ( event, ui ) {

         let ruleIndex = 0;
         $(this).find('table.cdss-rule-specification').each(function () {

            $(this).attr('data-rule_index', ruleIndex);

            ruleIndex++;
         })

      })
   ;
}

cdss.addRuleSpecListeners = function(ruleNumber) {
   let conditionsSubtable = $(`tbody#cdss-rule-${ruleNumber}-conditions`);

   conditionsSubtable.sortable();

   conditionsSubtable.on("sortupdate", function( event, ui) {

      let conditionIndex = 0;
      $(this).find('tr').each(function () {

         $(this).attr('data-condition_index', conditionIndex);

         if ( conditionIndex===0 ) {
            $(this).find(".cdss-rule-condition-if").show();
            $(this).find(".cdss-rule-condition-join").hide();
         }
         else {
            $(this).find(".cdss-rule-condition-if").hide();
            $(this).find(".cdss-rule-condition-join").show();
         }

         console.log('sortupdate', ruleNumber, conditionIndex);

         conditionIndex++;
      })

   });

}

cdss.addRuleCondition = function( ruleNumber ) {
   let thisRuleParent = $(`tbody#cdss-rule-${ruleNumber}-conditions`);
   // fetch the template HTML
   let conditionHtml = document.getElementById('cdss-rule-9999-condition-8888').outerHTML;
   let maxConditionNumber = 0;
   let maxConditionIndex = 0;

   thisRuleParent.find('.cdss-rule-condition').each( function () {
      //console.log("addRuleCondition", $(this), $(this).data('condition_number') );
      let thisConditionNumber = parseInt($(this).data('condition_number'));
      let thisConditionIndex = parseInt($(this).data('condition_index'));
      if ( thisConditionNumber && thisConditionNumber < 8888 ){
         if ( thisConditionNumber > maxConditionNumber ) {
            maxConditionNumber = thisConditionNumber;
         }
      }

      if ( thisConditionIndex && thisConditionIndex > maxConditionIndex ) {
         maxConditionIndex = thisConditionIndex;
      }

   })

   let thisConditionNumber = ++maxConditionNumber + ''; // increment and cast to string
   let thisConditionIndex = ++maxConditionIndex + ''; // increment and cast to string

   let reRule = /9999/g;
   let reCondition = /8888/g;

   conditionHtml = conditionHtml.replace(reRule, ruleNumber);

   conditionHtml = conditionHtml.replace(reCondition, thisConditionNumber);

   thisRuleParent.append( conditionHtml );

   let thisCondition = $(`tr#cdss-rule-${ruleNumber}-condition-${thisConditionNumber}`);
   let thisConditionBasis = $(`input#cdss-rule-${ruleNumber}-condition-${thisConditionNumber}-basis`);
   let thisConditionBasisOption = $(`select#cdss-rule-${ruleNumber}-condition-${thisConditionNumber}-basis-option`);
   let thisConditionBasisOptionCutpoint = $(`input#cdss-rule-${ruleNumber}-condition-${thisConditionNumber}-basis-option-cutpoint`);

   thisCondition.attr('data-condition_index', thisConditionIndex);

   thisConditionBasis.val('');
   thisConditionBasisOption.val('').find('option').remove().end().hide();
   thisConditionBasisOptionCutpoint.val('').hide();

   cdss.populateRuleConditionBasisSelect( ruleNumber, thisConditionNumber );

   cdss.addRuleConditionCmdSelectListeners();

   cdss.showCondition( ruleNumber, thisConditionNumber );

   //console.log("addRuleCondition", conditionHtml);

}
/*
cdss.addRuleConditionListeners = function( ruleNumber, conditionNumber ) {
   let thisCondition = $(`tr#cdss-rule-${ruleNumber}-condition-${conditionNumber}`);

   thisCondition.find("input.cdss-condition-rule-basis")
      .on('twiddly', function () {

         let rule_condition_basis = $(this).val();
         let basis_option = $(`select#cdss-rule-${ruleNumber}-condition-${conditionNumber}-basis-option`);
         let basisCategory = rule_condition_basis.substr(1, 1);

         cdss.populateRuleConditionBasisOptionSelect( basis_option, basisCategory );

         cdss.addRuleConditionBasisOptionListeners( basis_option, ruleNumber, conditionNumber );

      })
   ;

}
*/
cdss.populateRuleConditionBasisOptionSelect = function( el, basisCategory ) {

   let selected = 'selected';

   el.val('').find('option').remove();

   for (let i=0; i<cdss.rule_condition_basis_options[basisCategory].length; i++){
      el.append(`<option value='${cdss.rule_condition_basis_options[basisCategory][i]}' ${selected}>${cdss.rule_condition_basis_options[basisCategory][i]}</option>`);
      selected = ''; // the first option is selected
   }

}

cdss.addRuleConditionBasisOptionListeners = function( el, ruleNumber, conditionNumber ) {

   el
      .off()
      .on('change', function () {

         let optionSelected = el.val();
         let optionCutpoint = $(`input#cdss-rule-${ruleNumber}-condition-${conditionNumber}-basis-option-cutpoint`);

         if (new RegExp("EQUAL|GREATER THAN|LESS THAN").test(optionSelected)) {
            optionCutpoint.show();
         } else {
            optionCutpoint.hide();
         }


      })
      .show()
      .trigger('change')
   ;

}

cdss.showCondition = function( ruleNumber, conditionNumber ) {
   let thisCondition = $(`tr#cdss-rule-${ruleNumber}-condition-${conditionNumber}`);

   console.log('showCondition', thisCondition.attr('data-condition_index'));

   if ( thisCondition.attr('data-condition_index')==="0" ) {
      thisCondition.find(".cdss-rule-condition-if").show();
      thisCondition.find(".cdss-rule-condition-join").hide();
   }
   else {
      thisCondition.find(".cdss-rule-condition-if").hide();
      thisCondition.find(".cdss-rule-condition-join").show();
   }
}

cdss.ruleActionSelect = function(ruleNumber){
   let ruleAction = $(`select#cdss-rule-${ruleNumber}-action`).val();
   let ruleParamsWrapper = $(`tr#cdss-rule-${ruleNumber}-params_wrapper`);

   ruleParamsWrapper.hide();

   if ( !ruleAction.length ){
      return "no action selected";
   }
   
   cdss.buildRuleActionParamTable(ruleNumber, ruleAction);

   ruleParamsWrapper.show();
   
   cdss.inspectRule(ruleNumber);
}

cdss.postRuleMessage = function(ruleNumber, msg){
   $(`div#cdss-rule-${ruleNumber}-message`)
      .html(msg)
   ;
}

cdss.clearRuleMessage = function(ruleNumber, msg){
   $(`div#cdss-rule-${ruleNumber}-message`)
      .html("")
   ;
}

cdss.inspectRule = function(ruleNumber){

   let ruleParent = $(`table#cdss-rule-${ruleNumber}`);

}


cdss.addRuleSpec = function() {

   let ruleParent = $('div#cdss-rule-specifications');
   let ruleNumber = 0;

   $('table.cdss-rule-specification').each( function(){
      let thisRuleNumber = parseInt($(this).data('rule_number'));
      if ( thisRuleNumber < 8000 && thisRuleNumber > ruleNumber ){
         ruleNumber = thisRuleNumber;
      }
   })

   ruleNumber++;

   let ruleTableHtml = document.getElementById('cdss-rule-9999').outerHTML;

   let reRule = /9999/g;
   let reCondition = /8888/g;

   ruleTableHtml = ruleTableHtml.replace(reRule, ruleNumber);

   ruleTableHtml = ruleTableHtml.replace(reCondition, "1");

   ruleParent.append( ruleTableHtml );

   cdss.populateRuleActionSelect( ruleNumber );
   cdss.populateRuleConditionBasisSelect( ruleNumber, "1" );
   cdss.addRuleSpecListeners(ruleNumber);
   cdss.addRuleConditionCmdSelectListeners(); // mainly parenthesis checking
   cdss.showCondition(ruleNumber, "1");

   cdss.expandOrCollapse(ruleNumber);

   ruleParent.trigger('sortupdate'); // re-index the rules

   return ruleNumber;

}


cdss.buildRuleActionParamTable = function(ruleNumber, ruleAction){
   let paramTableWrapper = $(`table#cdss-rule-${ruleNumber} div.cdss-rule-params:first`);
   let ruleParamsWrapper = $(`tr#cdss-rule-${ruleNumber}-params_wrapper`);

   let paramTableClone = $('table#cdss-param-table-template').clone();

   paramTableClone
      .attr('id', `cdss-rule-${ruleNumber}-param-table`)
      .attr('data-rule_number', ruleNumber)
   ;

   let a = parseInt( ruleAction );
   let paramTemplateId = "";
   let paramClone = null;
   let input_type="";
   let tbody = paramTableClone.find('tbody').first();

   tbody.html("");

   for ( let i=0; i<cdss.cdss_actions[a].params.length; i++ ){

      paramTemplateId = `cdss-rule-param-${cdss.cdss_actions[a].params[i].type}-template`;
      console.log('buildRuleActionParamTable', paramTemplateId);

      paramClone = $( `tr#${paramTemplateId}` ).clone();

      if ( cdss.cdss_actions[a].params[i].type === "textarea" ){
         input_type = "textarea";
      }
      else if ( cdss.cdss_actions[a].params[i].type === "medication" ){
         input_type = "select";
      }
      else {
         input_type = "input";
      }

      paramClone
         .attr('id', `cdss-rule-${ruleNumber}-param-${cdss.cdss_actions[a].params[i].name}`)
         .attr('data-configitem', cdss.cdss_actions[a].params[i].name)
         .attr('data-action', a)
         .attr('data-param_index', i)
         .attr('data-param_name', cdss.cdss_actions[a].params[i].name)
         .attr('data-param_type', cdss.cdss_actions[a].params[i].type)
         .attr('data-param_label', cdss.cdss_actions[a].params[i].label)
         .attr('data-rule_number', ruleNumber)
         .find('td.cdss-rule-param-label').html(cdss.cdss_actions[a].params[i].label)
      ;

      paramClone.appendTo( tbody );

   }

   // add the additional item rows

   paramClone = $( `tr#cdss-rule-param-additional_item-template` ).clone();

   cdss.setRuleParamAdditionalItemProperties( paramClone, ruleNumber, "1", a, "100");

   paramClone.appendTo( tbody );

   // final row is a link to add new item

   paramClone = $( `tr#cdss-rule-param-add-item-template` ).clone();

   paramClone
      .attr('id', `cdss-rule-${ruleNumber}-add-additional-item`)
      .find('a').first().attr('href', `javascript: cdss.addRuleParamAdditionalItem('${ruleNumber}');`);

   paramClone.appendTo( tbody );

   cdss.populateRuleParamTableElements(tbody);

   paramTableWrapper
      .html("")
      .append( paramTableClone )
      .show()
   ;

   console.log('buildRuleActionParamTable',ruleNumber, ruleAction, paramTableWrapper, paramTableClone);

}

cdss.setRuleParamAdditionalItemProperties = function( el, ruleNumber, additional_item_number, action, param_index ){

   el
      .attr('id', `cdss-rule-${ruleNumber}-param-additional_item-${additional_item_number}`)
      .attr('data-action', action)
      .attr('data-param_index', param_index)
      .attr('data-param_name', "cdss_rule_additional_item")
      .attr('data-param_type', "general_item")
      .attr('data-param_label', "additional item to report")
      .attr('data-configitem', "cdss_rule_additional_item")
      .attr('data-rule_number', ruleNumber)
      .attr('data-additional_item_number', additional_item_number)
      .find('a').first().attr('href', `javascript: cdss.removeRuleParamAdditionalItem('${ruleNumber}', '${additional_item_number}');`)
   ;

   el.find('td.cdss-rule-param-remove-item').attr('data-additional_item_number', additional_item_number);

}

cdss.populateRuleParamTableElements = function(paramTable) {

   paramTable.find('select.cdss-rule-param-medication').each(function () {
      for (let i=0; i<cdss.cdss_medications.length; i++ ){
         $(this).append(`<option value=${cdss.cdss_medications[i].name}>${cdss.cdss_medications[i].name}</option>`);
      }
   });

   paramTable.find('input.cdss-rule-param-general_item').each(function () {
      $(this)
         .autocomplete({
         source: cdss.rule_basis_source,
         minLength: 0
         })
         .attr('placeholder', "start typing or [c]-conditions, [d]-diseases, [m]-medications, [f]-study fields or [space]-all")
      ;
   });
}

cdss.showOrHideRuleParams = function(ruleNumber) {
   let ruleAction = $(`select#cdss-rule-${ruleNumber}-action`).val();
   let ruleParamsWrapper = $(`tr#cdss-rule-${ruleNumber}-params_wrapper`);

   if ( ruleAction.length ) {
      ruleParamsWrapper.show();
   }
   else {
      ruleParamsWrapper.hide();
   }
}

cdss.addRuleParamAdditionalItem = function(ruleNumber){

   let lastItemSibling = $(`table#cdss-rule-${ruleNumber}-param-table tr.cdss-rule-param-additional-item:last`);
   let additional_item_number = '' + (1 + parseInt(lastItemSibling.data('additional_item_number')));
   let param_index = '' + (1 + parseInt(lastItemSibling.data('param_index')));

   let thisItemSibling = lastItemSibling.clone();

   thisItemSibling.find('[data-configitem=cdss_rule_param_input]').val("");

   cdss.setRuleParamAdditionalItemProperties( thisItemSibling,
      ruleNumber, additional_item_number,
      lastItemSibling.data('action'),
      param_index
   );

   cdss.populateRuleParamTableElements(thisItemSibling);

   lastItemSibling
      .after( thisItemSibling );

   console.log( "addRuleParamAdditionalItem", lastItemSibling, thisItemSibling.data('additional_item_number'));

}

cdss.removeRuleParamAdditionalItem = function(ruleNumber, additional_item_number) {

   $(`tr#cdss-rule-${ruleNumber}-param-additional_item-${additional_item_number}`).remove();

}

cdss.removeRule = function( ruleNumber ){
   $(`table#cdss-rule-${ruleNumber}`).remove();
}


/*
* the approved alternative to $(document).ready()
*/
$( function () {

   $(".cdss-draggable").draggable({"handle": ".cdss-panel-header-row, .cdss-panel-handle, .cdss-drag-handle"});

   cdss.addRuleSetListeners();

   cdss.populateRuleBackupSelect();

   cdss.getReady();

})



