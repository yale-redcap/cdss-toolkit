
let cdss = {
   study_fields: "",
   cdss_functions: "",
   cdss_variables: "",
   cdss_medications: "",
   cdss_diseases: "",
   cdss_conditions: "",
   cdss_rbase: "",
   rule_base_source: [],
   maxZ: 500,
   serviceUrl: "YES3_SERVICE_URL",
   user: "YES3_USERNAME",
   project_id: "YES3_PROJECT_ID"
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
   cdss.cdss_rbase = response.cdss_rbase;

   cdss.cdss_medications.sort( cdss.sort_compare_name );
   cdss.cdss_diseases.sort( cdss.sort_compare_name );
   cdss.cdss_conditions.sort( cdss.sort_compare_name );
   cdss.cdss_variables.sort( cdss.sort_compare_name );
   cdss.cdss_functions.sort( cdss.sort_compare_id );
   cdss.study_fields.sort( cdss.sort_compare_name );

   // assemble the big select source

   cdss.pushRuleBaseCategory('c', cdss.cdss_conditions);
   cdss.pushRuleBaseCategory('d', cdss.cdss_diseases);
   cdss.pushRuleBaseCategory('m', cdss.cdss_medications);

   // TESTING ONLY
   cdss.populateRuleBaseSelect( $("#rule-basis-1") );

   cdss.addListeners();

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

cdss.populateRuleBaseSelect = function( el ){

   el.autocomplete({
      source: cdss.rule_base_source,
      minLength: 0
   });

}

cdss.expandOrCollapse = function( specType, specNumber ){

   let tbl = $(`table#cdss-${specType}-specification-${specNumber}`);

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


/*
* the approved alternative to $(document).ready()
*/
$( function () {

   $(".cdss-draggable").draggable({"handle": ".cdss-panel-header-row, .cdss-panel-handle, .cdss-drag-handle"});

   cdss.getReady();

})



