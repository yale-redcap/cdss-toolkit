
let cdss = {
   study_fields: "",
   cdss_functions: "",
   cdss_variables: "",
   cdss_medications: "",
   cdss_diseases: "",
   cdss_conditions: "",
   cdss_rbase: "",
   rule_base_source: [],
   rule_action_source: [],
   maxZ: 500,
   serviceUrl: "YES3_SERVICE_URL",
   user: "YES3_USERNAME",
   project_id: "YES3_PROJECT_ID",
   cdss_actions: [
      {
         id: 1,
         name: 'report_comment',
         label: 'add a freetext entry to the medications management report',
         params: [
            {
               name: 'free_text_comment',
               label: 'comment',
               type: 'text',
            }
         ]
      },
      {
         id: 2,
         name: 'report_interaction',
         label: 'add an entry to the high risk medications report',
         params: [
            {
               name: 'medication1',
               label: 'medication',
               type: 'medication'
            },
            {
               name: 'med_con_dis',
               label: 'medication, condition or disease (if relevant)',
               type: 'med_con_dis'
            },
            {
               name: 'consequence',
               label: 'consequence',
               type: 'text'
            }
         ]
      },
      {
         id: 3,
         name: 'report_dosing',
         label: 'add an entry to the dosing report',
         params: [
            {
               name: 'medication',
               label: 'medication',
               type: 'medication'
            },
            {
               name: 'recommendation',
               label: 'recommendation',
               type: 'text'
            }
         ]
      },
      {
         id: 4,
         name: 'report_overtreatment',
         label: 'add an entry to the overtreatment report',
         params: [
            {
               name: 'med_con_dis',
               label: 'medication, condition or disease',
               type: 'med_con_dis_1'
            },
            {
               name: 'med_con_dis',
               label: 'additional medication, condition or disease (if relevant)',
               type: 'med_con_dis_2'
            },
            {
               name: 'med_con_dis',
               label: 'additional medication, condition or disease (if relevant)',
               type: 'med_con_dis_3'
            },
            {
               name: 'study_value_1',
               label: 'study value to report (if relevant)',
               type: 'study_field'
            },
            {
               name: 'study_value_2',
               label: 'additional study value to report (if relevant)',
               type: 'study_field'
            },
            {
               name: 'study_value_3',
               label: 'additional study value to report (if relevant)',
               type: 'study_field'
            },
            {
               name: 'recommendation',
               label: 'recommendation',
               type: 'text'
            }
         ]
      },
      {
         id: 5,
         name: 'report_renal',
         label: 'add an entry to the renal dosing report',
         params: [
            {
               name: 'medication',
               label: 'medication',
               type: 'medication'
            },
            {
               name: 'study_value',
               label: 'study value to report, if any',
               type: 'study_field'
            },
            {
               name: 'recommendation',
               label: 'recommendation',
               type: 'text'
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

cdss.saveComplete = function( response ){
   console.log( response );
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
   cdss.populateRuleActionSelect( "9999" );
   cdss.populateRuleConditionBasisSelect( "9999", "8888" );
   cdss.addRuleSpecListeners("9999");
   $("tbody.cdss-rule-conditions-subtable").trigger("sortupdate");

}

cdss.pushRuleBaseCategory = function (category, x ) {
   for (let i=0; i<x.length; i++ ){
      cdss.rule_base_source.push(
         {
            label: `[${category}] ${x[i].name}`,
            value: x[i].name
         }
      )
   }
}

cdss.populateRuleConditionBasisSelect = function( ruleNumber, conditionNumber ){
   let el = $(`#cdss-rule-${ruleNumber}-condition-${conditionNumber}-basis`);

   el.autocomplete({
      source: cdss.rule_base_source,
      minLength: 0
   });

}

cdss.populateRuleActionSelect = function( ruleNumber ){
   let el = $(`#cdss-rule-${ruleNumber}-action`);

   for (let i=0; i<cdss.cdss_actions.length; i++){
      el.append(`<option value='${i}'>${cdss.cdss_actions[i].label}</option>`)
   }

}

cdss.expandOrCollapse = function( ruleNumber ){

   let tbl = $(`table#cdss-rule-${ruleNumber}`);

   cdss.showSpecTable( tbl );
}

cdss.showSpecTable = function( tbl ){

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
      tbl.find("tr.cdss-collapsible").show();
   }

}

cdss.addRuleSpec = function() {

}

cdss.removeRuleCondition = function(ruleNumber, conditionNumber){
   $(`tr#cdss-rule-${ruleNumber}-condition-${conditionNumber}`).remove();
   $(`tr#cdss-rule-${ruleNumber}-conditions`).trigger('sortupdate');
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

         console.log('addRuleSpecListeners', ruleNumber, conditionIndex);

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

   thisCondition.attr('data-condition_index', thisConditionIndex);

   cdss.populateRuleConditionBasisSelect( ruleNumber, thisConditionNumber );
   cdss.showCondition( ruleNumber, thisConditionNumber );

   console.log("addRuleCondition", conditionHtml);

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


/*
* the approved alternative to $(document).ready()
*/
$( function () {

   $(".cdss-draggable").draggable({"handle": ".cdss-panel-header-row, .cdss-panel-handle, .cdss-drag-handle"});

   cdss.getReady();

})



