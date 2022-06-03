
let CDSS = {
   study_fields: "",
   cdss_variables: "",
   cdss_medications: "",
   cdss_diseases: "",
   cdss_conditions: "",
   cdss_rbase: "",
   maxZ: 500,
   serviceUrl: "",
   user: "",
   project_id: "",
   csrf_token: "",
   record: "",
   event_id: ""
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

CDSS.requestService = function( params, doneFn, dataType ) {

   dataType = dataType || "text";

   var request = $.ajax({
      url: CDSS.moduleObject.getUrl('services/cdss_services.php'),
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

CDSS.saveComplete = function( response ){
   console.log( response );
}

CDSS.toTitleCase = function(str) {
   return str.replace(/\w\S*/g, function(txt){
      return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
   });
}

CDSS.pretty_panel_name = function(metaclass){
   return CDSS.toTitleCase(metaclass.split('_').join(' ')).replace('Cdss', 'CDSS');
}

CDSS.spinMe = function() {
    $('i.cdss-spinme').addClass('fa-spin');
}

CDSS.openPopup = function(popup) {
    CDSS.maxZ += 1;
    $('#cdss-screencover').css({'z-index':CDSS.maxZ-1}).show(); // places the full-screen overlay just below the panel
    popup.center(0, 0).css({'z-index':CDSS.maxZ}).show();
    setTimeout(CDSS.spinMe, 10000);
};

CDSS.closePopup = function(popupName) {
    $(`#${popupName}`).hide();
    $('#cdss-screencover').hide();
};

CDSS.getReady = function() {

    var params = {

        'request':'get_cdss_reports',
        'record': CDSS.record,
        'event_id': CDSS.event_id
    };

    CDSS.requestService(params, CDSS.getSetGo, "html");
}

CDSS.getSetGo = function( response ){

    console.log( 'getSetGo', response );

    $('#cdss-report-container').html(response); ;
}

$(document).ready(function () {

    $(".cdss-draggable").draggable({"handle": ".cdss-drag-handle"});

    /*
    attach the csrf token to every AJAX request
    https://stackoverflow.com/questions/22063612/adding-csrftoken-to-ajax-request
    */
    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        jqXHR.setRequestHeader('X-CSRF-Token', CDSS.csrf_token);
    });

    CDSS.getReady();
});


