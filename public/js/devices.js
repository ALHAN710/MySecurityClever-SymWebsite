var streamingUrl = $("#streamingUrl");
var in_streamingUrl = $('#devices_streamingUrl');
var _alert = $("#alert");
var in_alert = $('#devices_alerte');
var notifMess = $("#notificationMessage");
var in_notifMess = $('#devices_notificationMessage');
var urlTab = $("#cameraUrlTab");

if ($('#devices_type').val() === 'Camera') {
    //Affichage des champs propre à l'équipement de type Camera
    streamingUrl.removeClass("d-none");
    in_streamingUrl.attr('required', 'required');
    urlTab.removeClass("d-none");

    notifMess.removeClass("d-none");
    in_notifMess.attr('required', 'required');

    //Masquer les champs non utile pour l'équipement de type camera
    _alert.removeClass("d-none");
    in_alert.removeAttr("required");
    _alert.addClass("d-none");
}
else {

    //Masquer les champs propre à l'équipement de type camera
    streamingUrl.removeClass("d-none");
    //$('#devices_streamingUrl').attr('required', false);
    in_streamingUrl.removeAttr("required");
    streamingUrl.addClass("d-none");
    //$("#streamingUrl").hide(); //then hide
    urlTab.addClass("d-none");
    if ($('#devices_type').val() === 'Alarm') {
        //Masquer les champs non utile pour l'équipement de type Alarm
        notifMess.removeClass("d-none");
        in_notifMess.removeAttr("required");
        notifMess.addClass("d-none");

        _alert.removeClass("d-none");
        in_alert.removeAttr("required");
        _alert.addClass("d-none");
    }
    else {
        //Affichage des champs utile aux équipements de type Sensor
        notifMess.removeClass("d-none");
        in_notifMess.attr("required", "required");

        _alert.removeClass("d-none");
        in_alert.attr("required", "required");
    }

}

$('#devices_type').change(function () {
    Str = String($('#devices_type').val());
    Name = $('#devices_type option[value=\"' + Str + '\"]').text();
    console.log('devices_type val ' + Str);
    console.log('Option selected : ' + String(Name));
    console.log('Type of devices_type : ' + jQuery.type($('#devices_type').val()))

    if ($('#devices_type').val() === 'Camera') {
        //Affichage des champs propre à l'équipement de type Camera
        streamingUrl.removeClass("d-none");
        in_streamingUrl.attr('required', 'required');
        urlTab.removeClass("d-none");

        notifMess.removeClass("d-none");
        in_notifMess.attr('required', 'required');

        //Masquer les champs non utile pour l'équipement de type camera
        _alert.removeClass("d-none");
        in_alert.removeAttr("required");
        _alert.addClass("d-none");
    }
    else {

        //Masquer les champs propre à l'équipement de type camera
        streamingUrl.removeClass("d-none");
        //$('#devices_streamingUrl').attr('required', false);
        in_streamingUrl.removeAttr("required");
        streamingUrl.addClass("d-none");
        //$("#streamingUrl").hide(); //then hide
        urlTab.addClass("d-none");
        if ($('#devices_type').val() === 'Alarm') {
            //Masquer les champs non utile pour l'équipement de type Alarm
            notifMess.removeClass("d-none");
            in_notifMess.removeAttr("required");
            notifMess.addClass("d-none");

            _alert.removeClass("d-none");
            in_alert.removeAttr("required");
            _alert.addClass("d-none");
        }
        else {
            //Affichage des champs utile aux équipements de type Sensor
            notifMess.removeClass("d-none");
            in_notifMess.attr("required", "required");

            _alert.removeClass("d-none");
            in_alert.attr("required", "required");
        }

    }
});