
let cdss = {
   study_fields: "",
   cdss_functions: "",
   cdss_variables: "",
   cdss_medications: "",
   cdss_diseases: "",
   cdss_conditions: "",
   cdss_rbase: "",
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

cdss.load_metadata_panel = function( metaclass, readonly ) {

   readonly = readonly || false;

   var metadata = cdss[metaclass];
   var panel = $("<div>", {id: metaclass, "class": "cdss-metadata-list-container"});
   var panel_content = $("<div>", {id: metaclass + "-content", "class": "cdss-metadata-list-content"});
   var panelTitle = cdss.pretty_panel_name(metaclass);

   for ( var i=0; i<metadata.length; i++){

      itemtag = metaclass + "." + metadata[i].name;

      if ( metadata[i].label.length ) {
         title = metadata[i].label;
      } else {
         title = `Click to select '${metadata[i].name}'`;
      }
      panel_content.append(`<div class="cdss-metadata-list-item" title="${title}" data-metaclass="${metaclass}" data-itemname="${metadata[i].name}">${metadata[i].name}</div>`);
   }

   panel
      .append(`<div class="cdss-metadata-list-header">${panelTitle}</div>`)
      .append( panel_content )
   ;

   if ( readonly ) {
      panel.append(`<button class="cdss-metadata-button cdss-button-inspect" data-metaclass="${metaclass}">inspect</button>`);
   } else {
      panel.append(`<button class="cdss-metadata-button cdss-button-edit" data-metaclass="${metaclass}">edit</button>`)
         .append(`<button class="cdss-metadata-button cdss-button-delete" data-metaclass="${metaclass}">delete</button>`)
         .append(`<button class="cdss-metadata-button cdss-button-add" data-metaclass="${metaclass}">new</button`)
      ;
   }

   $('div#cdss_metadata_selectors').append( panel );
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

cdss.addListeners = function (){

   $('div.cdss-metadata-list-item').on('click', function () {

      if ( !$(this).hasClass('cdss-item-selected') ) {
         $(this).siblings().removeClass('cdss-item-selected');
         $(this).addClass('cdss-item-selected');
         let itemName = $(this).html();
         cdss.copyToClipboard($(this).html());
         $(this).parent().parent().find('button.cdss-button-edit').show();
         $(this).parent().parent().find('button.cdss-button-inspect').show();
         $(this).parent().parent().find('button.cdss-button-delete').show();
      } else {
         $(this).removeClass('cdss-item-selected');
         $(this).parent().parent().find('button.cdss-button-edit').hide();
         $(this).parent().parent().find('button.cdss-button-inspect').hide();
         $(this).parent().parent().find('button.cdss-button-delete').hide();
      }

   });

   $('button#cdss-save-rbase').on('click', function () {
      let params = {
         'request': 'save-rbase',
         'cdss_rbase_text': $('textarea#cdss-rbase-text').val()
      }
      cdss.requestService( params, cdss.saveComplete );
   });

   /*
    * special keypress actions for code editing:
    *   tab insertion (tab = 3 spaces)
    *   tab removal
    *   hanging indents
    */
   $('textarea#cdss-rbase-text').on('keydown', function ( k ) {

      var keyCode = k.keyCode || k.which;
      var start = this.selectionStart;
      var end = this.selectionEnd;
      var spaces = "   ";
      var tabLength = spaces.length;

      // tab or shift-tab
      if ( keyCode === 9 ){
         k.preventDefault();
         // shift-tab: remove tab spaces
         if ( k.shiftKey ){
            if ( this.value.substring(start-tabLength, start)===spaces ){
               this.value = this.value.substring(0, start-tabLength) + this.value.substring(end);
               this.selectionStart = this.selectionEnd = start - tabLength;
            }
         }
         // tab
         else {
            this.value = this.value.substring(0, start) + spaces + this.value.substring(end);
            this.selectionStart = this.selectionEnd = start + spaces.length;
         }
      }
      // backspace: remove tab spaces if indicated, otherwise default action
      else if ( keyCode === 8 ){
         if ( this.value.substring(start-tabLength, start)===spaces ){
            k.preventDefault();
            this.value = this.value.substring(0, start-tabLength) + this.value.substring(end);
            this.selectionStart = this.selectionEnd = start - tabLength;
         }
      }
      // cr: hanging indent if indicated ( always if following ':' )
      else if ( keyCode === 13 ){
         var startOfLine = this.value.substring(0, start).lastIndexOf('\n') + 1;
         //console.log('start:', start, 'end:', end, 'startOfLine:', startOfLine, 'charAt: ['+this.value.charAt(start-1)+']');
         var indent = ( this.value.charAt(start-1)===':' ) ? spaces : "";
         for ( var i=startOfLine; i<start; i+=tabLength){
            if ( this.value.substring(i, i+tabLength)===spaces ) {
               indent += spaces;
            } else {
               break;
            }
         }
         if ( indent.length ){
            k.preventDefault();
            this.value = this.value.substring(0, start) + "\n" + indent + this.value.substring(end);
            this.selectionStart = this.selectionEnd = start + indent.length + 1;
         }
      }

   }); /* rbase-text keyboard handler */

   /* inspectors */

   cdss.nl2br = function(str){return str.replace(/(?:\r\n|\r|\n)/g, '<br>');};

   cdss.inspector_open = function( metaclass, itemname ) {
      var popup = $('div#cdss-inspector');
      var popup_content = $('div#cdss-inspector-content');
      var tbl = "";
      var i = cdss.getNameIndex( cdss[metaclass], itemname );

      console.log('inspector_open', metaclass, itemname, i, cdss[metaclass][i]);


      $('div#cdss-inspector-metaclass').html( cdss.pretty_panel_name(metaclass) );
      //$('div#cdss-inspector-itemname').html( itemname );

      tbl = "<table>";

      tbl += `<tr><td class="cdss-table-left">name:</td><td class="cdss-table-right cdss-metadata-name">${cdss[metaclass][i].name}</td>`;

      if (cdss[metaclass][i].label) tbl += `<tr><td class="cdss-table-left">label:</td><td class="cdss-table-right">${cdss.nl2br(cdss[metaclass][i].label)}</td>`;

      if (cdss[metaclass][i].code) tbl += `<tr><td class="cdss-table-left">coding:</td><td class="cdss-table-right">${cdss.nl2br(cdss[metaclass][i].code)}</td>`;

      if (cdss[metaclass][i].params) tbl += `<tr><td class="cdss-table-left">parameters:</td><td class="cdss-table-right">${cdss.nl2br(cdss[metaclass][i].params)}</td>`;

      if (cdss[metaclass][i].example) tbl += `<tr><td class="cdss-table-left">example:</td><td class="cdss-table-right">${cdss.nl2br(cdss[metaclass][i].example)}</td>`;

      if (cdss[metaclass][i].comments) tbl += `<tr><td class="cdss-table-left">comments:</td><td class="cdss-table-right">${cdss.nl2br(cdss[metaclass][i].comments)}</td>`;

      tbl += "</table>";

      popup_content.html(tbl);

      cdss.openPopup(popup);
   }

   cdss.inspector_close = function() {
      cdss.closePopup("cdss-inspector");
   }

   cdss.editor_open = function( metaclass, itemname ) {
      var popup = $('div#cdss-editor');
      var popup_content = $('div#cdss-editor-content');
      var tbl = "";
      var i = cdss.getNameIndex( cdss[metaclass], itemname );

      //console.log('editor_open', metaclass, itemname, i, cdss[metaclass][i]);

      $('div#cdss-editor-metaclass').html( cdss.pretty_panel_name(metaclass) );
      //$('div#cdss-editor-itemname').html( itemname );

      tbl = "<table>";

      tbl += `<tr><td class="cdss-table-left">name:</td><td class="cdss-table-right cdss-metadata-name">${cdss[metaclass][i].name}</td>`;

      tbl += `<tr><td class="cdss-table-left">label:</td><td class="cdss-table-right"><input type="text" class="cdss-editor-string" id="cdss-editor-label" /></td>`;

      tbl += `<tr><td class="cdss-table-left">coding:</td><td class="cdss-table-right"><textarea class="cdss-editor-text" id="cdss-editor-code"></textarea></td>`;

      tbl += `<tr><td class="cdss-table-left">comments:</td><td class="cdss-table-right"><textarea class="cdss-editor-text" id="cdss-editor-comments"></textarea></td>`;

      tbl += `<tr><td colspan='2' class='cdss-editor-button-container'><button class='cdss-editor-button cdss-editor-button-save' onclick="cdss.editor_save('${metaclass}', ${i});">SAVE AND CLOSE</button></td></tr>`;

      tbl += "</table>";

      popup_content.html(tbl);

      $('input#cdss-editor-label').val(cdss[metaclass][i].label);
      $('textarea#cdss-editor-code').val(cdss[metaclass][i].code);
      $('textarea#cdss-editor-comments').val(cdss[metaclass][i].comments);

      cdss.openPopup(popup);
   }

   cdss.editor_close = function() {
      cdss.closePopup("cdss-editor");
   }

   cdss.editor_save = function(metaclass, i){

      cdss[metaclass][i].label = $('input#cdss-editor-label').val();
      cdss[metaclass][i].code = $('textarea#cdss-editor-code').val();
      cdss[metaclass][i].comments = $('textarea#cdss-editor-comments').val();

      console.log('editor_save', metaclass, i, cdss[metaclass][i]);

      var params = {
         'request': 'save-metadata',
         'metaclass': metaclass,
         'data': cdss[metaclass]
      }
      cdss.requestService(params, cdss.editor_save_callback);

      cdss.editor_close();
   }

   cdss.editor_save_callback = function( response ){
      console.log('editor_save_callback', response);
   }

   $("button.cdss-button-inspect").off().on("click", function () {

      var metaclass = $(this).attr('data-metaclass');

      var itemname = $(`div[data-metaclass=${metaclass}].cdss-item-selected`).attr("data-itemname");

      cdss.inspector_open( metaclass, itemname );

   });

   $("button.cdss-button-edit").off().on("click", function () {

      var metaclass = $(this).attr('data-metaclass');

      var itemname = $(`div[data-metaclass=${metaclass}].cdss-item-selected`).attr("data-itemname");

      cdss.editor_open( metaclass, itemname );

   });

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

   $('textarea#cdss-rbase-text').val(cdss.cdss_rbase);

   cdss.cdss_medications.sort( cdss.sort_compare_name );
   cdss.cdss_diseases.sort( cdss.sort_compare_name );
   cdss.cdss_conditions.sort( cdss.sort_compare_name );
   cdss.cdss_variables.sort( cdss.sort_compare_name );
   cdss.cdss_functions.sort( cdss.sort_compare_id );
   cdss.study_fields.sort( cdss.sort_compare_name );

   cdss.load_metadata_panel( "study_fields", true );
   cdss.load_metadata_panel( "cdss_functions", true );
   cdss.load_metadata_panel( "cdss_variables" );
   cdss.load_metadata_panel( "cdss_diseases" );
   cdss.load_metadata_panel( "cdss_medications" );
   cdss.load_metadata_panel( "cdss_conditions" );

   // all retrievals will be by name
   cdss.cdss_functions.sort( cdss.sort_compare_name );

   cdss.addListeners();

}

$(document).ready(function () {

   cdss.getReady();

});


