
let cdss = {
   study_fields: "",
   cdss_variables: "",
   cdss_medications: "",
   cdss_diseases: "",
   cdss_conditions: "",
   cdss_rbase: "",
   editor_saved_metaclass: "",
   editor_saved_itemname: "",
   maxZ: 500,
   redcap_csrf_token: "YES3_CSRF_TOKEN",
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

   params['redcap_csrf_token'] = cdss.redcap_csrf_token;

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


cdss.load_metadata_panel = function( metaclass, readonly, selected_itemname ) {

   readonly = readonly || false;

   cdss[metaclass].sort( cdss.sort_compare_name );

   var metadata = cdss[metaclass];

    let panel_content = null;

    // create the panel if it doesn't exist
    if ( !$(`div#${metaclass}`).length ){

        let panelContainer = $("<div>", {"class": "yes3-flex-vtop-hleft"});

        let panel = $("<div>", {id: metaclass, "class": "cdss-metadata-list-container"});

        let panelTitle = cdss.pretty_panel_name(metaclass);

        panel_content = $("<div>", {id: metaclass + "-content", "class": "cdss-metadata-list-content"});

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
      
        panelContainer.append( panel );
      
        $('div#cdss_metadata_selectors').append( panelContainer );
    }
    else {

        panel_content = $(`div#${metaclass}-content`);
    }

    let title = "";
    let selected = "";

    panel_content.empty();

    for ( var i=0; i<metadata.length; i++){

        selected = ( metadata[i].name === cdss.editor_saved_itemname ) ? "cdss-item-selected" : "";

        title = `Click to select '${metadata[i].name}'`;

        panel_content.append(`<div id="${metaclass}_${i}" class="cdss-metadata-list-item ${selected}" title="${title}" data-metaclass="${metaclass}" data-itemname="${metadata[i].name}" data-itemindex="${i}">${metadata[i].name}</div>`);
    }

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

    // Oops, may not be an ordered list. Try linear search.
    for (i=0; i<xx.length; i++){

        if (xx[i].name===theName) return i;
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

cdss.setMetaclassItemListeners = function( metaclass ){

    $(`div.cdss-metadata-list-item[data-metaclass=${metaclass}]:not(.yes3-handled)`)
        .addClass("yes3-handled")
        .on('click', function () {

            if ( !$(this).hasClass('cdss-item-selected') ) {

                $('div.cdss-item-selected').removeClass(('cdss-item-selected'));
                $(this).addClass('cdss-item-selected');
                $(this).closest('.cdss-metadata-list-container').find('button:not(.cdss-button-add)').show();

            } else {

                $(this).removeClass('cdss-item-selected');
                $(this).closest('.cdss-metadata-list-container').find('button:not(.cdss-button-add)').hide();

            }

        })
    ;
}

cdss.addListeners = function (){

    cdss.setMetaclassItemListeners('cdss_nedications');
    cdss.setMetaclassItemListeners('cdss_diseases');
    /*
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

cdss.editor_open = function( metaclass, i ) {
    var popup = $('div#cdss-editor');
    var popup_content = $('div#cdss-editor-content');
    var tbl = "";
    //var i = cdss.getNameIndex( cdss[metaclass], itemname );

    //console.log('editor_open', metaclass, itemname, i, cdss[metaclass][i]);

    $('div#cdss-editor-metaclass').html( cdss.pretty_panel_name(metaclass) );
    //$('div#cdss-editor-itemname').html( itemname );

    tbl = "<table>";

    //tbl += `<tr><td class="cdss-table-left">name:</td><td class="cdss-table-right cdss-metadata-name">${cdss[metaclass][i].name}</td>`;

    tbl += `<tr><td class="cdss-table-left">name:</td><td class="cdss-table-right"><input type="text" class="cdss-editor-string" id="cdss-editor-name" /></td>`;

    //tbl += `<tr><td class="cdss-table-left">label:</td><td class="cdss-table-right"><input type="text" class="cdss-editor-string" id="cdss-editor-label" /></td>`;

    tbl += `<tr><td class="cdss-table-left">REDCap field:</td><td class="cdss-table-right"><select class="cdss-editor-string" id="cdss-editor-field"></select></td>`;

    tbl += `<tr><td class="cdss-table-left">coding:</td><td class="cdss-table-right"><textarea class="cdss-editor-text" id="cdss-editor-code"></textarea></td>`;

    tbl += `<tr><td class="cdss-table-left">comments:</td><td class="cdss-table-right"><textarea class="cdss-editor-text" id="cdss-editor-comments"></textarea></td>`;

    tbl += `<tr><td colspan='2' class='cdss-editor-button-container'><button class='cdss-editor-button cdss-editor-button-save' onclick="cdss.editor_save('${metaclass}', ${i});">SAVE AND CLOSE</button></td></tr>`;

    tbl += "</table>";

    popup_content.html(tbl);

    $('select#cdss-editor-field').empty().append(cdss.studyFieldOptionsHtml);

    $('input#cdss-editor-name').val(cdss[metaclass][i].name);
    //$('input#cdss-editor-label').val(cdss[metaclass][i].label);
    $('textarea#cdss-editor-code').val(cdss[metaclass][i].code);
    $('select#cdss-editor-field').val(cdss[metaclass][i].field);
    $('textarea#cdss-editor-comments').val(cdss[metaclass][i].comments);

    cdss.openPopup(popup);
}

cdss.editor_close = function() {
    cdss.closePopup("cdss-editor");
}

cdss.editor_add = function(metaclass){

    let i = cdss[metaclass].length;

    const newItemName = "* NEW ITEM *";

    cdss[metaclass].push({
        name: newItemName,
        field: "",
        code: "",
        comments: ""
    });

    const panel_content = $(`div#${metaclass}-content`);

    $("div.cdss-item-selected").removeClass("cdss-item-selected");

    let $newItem = $("<div>", {
        id: `${metaclass}_${i}`,
        class: "cdss-metadata-list-item",
        title: `Click to select ${cdss[metaclass][i].name}`,
        text: newItemName,
        "data-metaclass": metaclass,
        "data-itemname": newItemName,
        "data-itemindex": i
    });

    panel_content
        //.append(`<div id="${metaclass}_${i}" class="cdss-metadata-list-item" title="${title}" data-metaclass="${metaclass}" data-itemname="${cdss[metaclass][i].name}" data-itemindex="${i}">${cdss[metaclass][i].name}</div>`)
        .append( $newItem )
    ;

    cdss.setMetaclassItemListeners(metaclass);

    $newItem.trigger("click");

    panel_content.animate({scrollTop: panel_content.prop("scrollHeight")}, 500);

    cdss.editor_open( metaclass, i);
}

cdss.editor_save = function(metaclass, i){

    cdss[metaclass][i].name = $('input#cdss-editor-name').val();
    //cdss[metaclass][i].label = $('input#cdss-editor-label').val();
    cdss[metaclass][i].code = $('textarea#cdss-editor-code').val();
    cdss[metaclass][i].field = $('select#cdss-editor-field').val();
    cdss[metaclass][i].comments = $('textarea#cdss-editor-comments').val();

    // update the displayed list
    
    let $itemEl = $(`div#${metaclass}_${i}`);

    $itemEl.attr('data-itemname', cdss[metaclass][i].name);

    $itemEl.html(cdss[metaclass][i].name);

    console.log('editor_save', metaclass, i, cdss[metaclass][i]);

    // save metaclass list
    cdss.saveMetaclassList( metaclass, cdss[metaclass][i].name );

    cdss.editor_close();
}

cdss.saveMetaclassList = function(metaclass, itemname){

    itemname = itemname || "";

    cdss.editor_saved_metaclass = metaclass;
    cdss.editor_saved_itemname = itemname;

    var params = {
        'request': 'save-metadata',
        'metaclass': metaclass,
        'data': cdss[metaclass]
    }
    cdss.requestService(params, cdss.editor_save_callback, "json");
}

cdss.editor_save_callback = function( response ){

    cdss[cdss.editor_saved_metaclass] = response;

    cdss[cdss.editor_saved_metaclass].sort( cdss.sort_compare_name );

    cdss.populateMetaclassPanel(cdss.editor_saved_metaclass, cdss.editor_saved_itemname);

    console.log('editor_save_callback', response);
}

cdss.populateMetaclassPanel = function( metaclass, selectItemname ){

    selectItemname = selectItemname || "";

    const $container = $(`div#${metaclass}-content`);

    $container.empty();

    let selected = false;

    for ( var i=0; i<cdss[metaclass].length; i++){

        selected = ( cdss[metaclass][i].name === selectItemname ) ? "cdss-item-selected" : "";

        $container.append( $("<div>", {
                id: `${metaclass}_${i}`,
                class: `cdss-metadata-list-item ${selected}`,
                title: `Click to select ${cdss[metaclass][i].name}`,
                text: cdss[metaclass][i].name,
                "data-metaclass": metaclass,
                "data-itemname": cdss[metaclass][i].name,
                "data-itemindex": i
            })
        );
    }
    
    cdss.setMetaclassItemListeners(metaclass);

    if ( selectItemname ){

        $container.find(`div[data-itemname="${selectItemname}"]`)[0].scrollIntoView();
    }
}

/*
cdss.add_medication_fields = function(){

    for (let i=0; i<cdss.cdss_medications.length; i++){

        cdss.cdss_medications[i].field = "cdss_m_" + cdss.cdss_medications[i].name.toLowerCase();
    }

    var params = {
        'request': 'save-metadata',
        'metaclass': 'cdss_medications',
        'data': cdss.cdss_medications
    }
    cdss.requestService(params, cdss.editor_save_callback);

    console.log(cdss.cdss_medications);
}
*/

cdss.writeCDSSVarsCsv = function(){

    url = cdss.serviceUrl + "&request=writeCDSSVarsCsv&csrf_token="+redcap_csrf_token;
    window.open(url);
}

cdss.setButtonListeners = function() {
    /*
    $("button.cdss-button-inspect").off().on("click", function () {

        var metaclass = $(this).attr('data-metaclass');
    
        var itemname = $(`div[data-metaclass=${metaclass}].cdss-item-selected`).attr("data-itemname");
    
        cdss.inspector_open( metaclass, itemname );
    
    });
    */
    $("button.cdss-button-edit").off("click").on("click", function () {
    
        var metaclass = $(this).attr('data-metaclass');
    
        //var itemname = $(`div[data-metaclass=${metaclass}].cdss-item-selected`).attr("data-itemname");
    
        var itemindex = $(`div[data-metaclass=${metaclass}].cdss-item-selected`).attr("data-itemindex");
    
        cdss.editor_open( metaclass, itemindex );
    
    });
    
    $("button.cdss-button-add").off("click").on("click", function () {
    
        var metaclass = $(this).attr('data-metaclass');
    
        cdss.editor_add( metaclass );
    });
    
    $("button.cdss-button-delete").off("click").on("click", function () {
    
        let metaclass = $(this).attr('data-metaclass');

        let $container = $(`div#${metaclass}`);

        let $theCondemned = $container.find("div.cdss-item-selected");

        if ( $theCondemned.length ) {

            let itemname  = $theCondemned.data("itemname");

            let i  = $theCondemned.data("itemindex");

            if ( confirm(`Are you SURE you want to delete '${itemname}'?`) ) {
               
                $theCondemned.remove();

                cdss[metaclass].splice(i, 1);

                $container.find('button:not(.cdss-button-add)').hide();

                // save metaclass list
                cdss.saveMetaclassList( metaclass );
            }
        }
    });
}

/*
    * popups
    */

cdss.spinMe = function() {
    $('i.cdss-spinme').addClass('fa-spin');
}

cdss.openPopup = function(popup) {
    cdss.maxZ += 1;
    $('#cdss-screencover').css({'z-index':cdss.maxZ-1}).show(); // places the full-screen overlay just below the panel
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

    console.log( 'getSetGo', response );

    cdss.study_fields = response.study_fields;
    cdss.cdss_medications = response.cdss_medications;
    cdss.cdss_diseases = response.cdss_diseases;
    //cdss.cdss_conditions = response.cdss_conditions;
    //cdss.cdss_variables = response.cdss_variables;
    //cdss.cdss_functions = response.cdss_functions;
    //cdss.cdss_rbase = response.cdss_rbase;
    //cdss.all_drugs = response.all_drugs;
    //cdss.all_diseases = response.all_diseases;
    //cdss.drugs = response.drugs;
    //cdss.diseases = response.diseases;

    //$('textarea#cdss-rbase-text').val(cdss.cdss_rbase);

    cdss.cdss_medications.sort( cdss.sort_compare_name );
    cdss.cdss_diseases.sort( cdss.sort_compare_name );
    //cdss.cdss_conditions.sort( cdss.sort_compare_name );
    //cdss.cdss_variables.sort( cdss.sort_compare_name );
    //cdss.cdss_functions.sort( cdss.sort_compare_id );
    cdss.study_fields.sort( cdss.sort_compare_name );

    //cdss.load_metadata_panel( "study_fields", true );
    //cdss.load_metadata_panel( "cdss_functions", true );
    //cdss.load_metadata_panel( "cdss_variables" );
    cdss.load_metadata_panel( "cdss_diseases" );
    cdss.load_metadata_panel( "cdss_medications" );
    //cdss.load_metadata_panel( "cdss_conditions" );

    // all retrievals will be by name
    //cdss.cdss_functions.sort( cdss.sort_compare_name );

    cdss.addListeners();

    cdss.setButtonListeners();

    cdss.studyFieldOptionsHtml = "<option value=''>&nbsp;</option>";

    let label = "";

    for (let i=0; i<cdss.study_fields.length; i++){

        label = `${cdss.study_fields[i].name} - ${cdss.study_fields[i].label.substring(0,80)}`;

        cdss.studyFieldOptionsHtml += `\n<option value=${cdss.study_fields[i].name}>${label}</option>`;
    }
}

$(document).ready(function () {

    /*
    attach the csrf token to every AJAX request
    https://stackoverflow.com/questions/22063612/adding-csrftoken-to-ajax-request
    */
    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        jqXHR.setRequestHeader('X-CSRF-Token', redcap_csrf_token);
    });

    cdss.getReady();

});


