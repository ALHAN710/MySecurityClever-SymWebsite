$(document).ready(function () { // Get checkbox statuses from localStorage if available (IE)
    var _switchValues = "{\"switch-house-lock\":" + houseSecurityState + ",";
    // Select elements by their data attribute
    const $entryMotionIdElements = $('[data-entry-motionid]');
    // Map over each element and extract the data value
    const $entryMotionIds =
        $.map($entryMotionIdElements, item => $(item).data('entryMotionid'));
    // You'll now have array containing string values
    //console.log($entryMotionIds); // eg: ["1", "2", "3"]
    // Select elements by their data attribute
    const $entryMotionValueElements = $('[data-entry-motionvalue]');
    // Map over each element and extract the data value
    const $entryMotionValues =
        $.map($entryMotionValueElements, item => $(item).data('entryMotionvalue'));
    // You'll now have array containing string values
    //console.log($entryMotionValues); // eg: ["1", "2", "3"]
    var str = "switch-motion-";
    var json = "";
    var tabId = [];
    var s = "";
    $.each($entryMotionIds, function (index, value) {
        s = str + value;
        tabId[s] = value;
        json += "\"" + s + "\":" + $entryMotionValues[index] + ",";

    })
    //console.log(json);
    _switchValues += json;
    //console.log(_switchValues);

    // Select elements by their data attribute
    const $entryFireIdElements = $('[data-entry-fireid]');
    // Map over each element and extract the data value
    const $entryFireIds =
        $.map($entryFireIdElements, item => $(item).data('entryFireid'));
    // You'll now have array containing string values
    //console.log($entryFireIds); // eg: ["1", "2", "3"]
    // Select elements by their data attribute
    const $entryFireValueElements = $('[data-entry-firevalue]');
    // Map over each element and extract the data value
    const $entryFireValues =
        $.map($entryFireValueElements, item => $(item).data('entryFirevalue'));
    // You'll now have array containing string values
    //console.log($entryFireValues); // eg: ["1", "2", "3"]
    str = "switch-fire-";
    json = "";
    $.each($entryFireIds, function (index, value) {
        s = str + value;
        tabId[s] = value;
        json += "\"" + str + value + "\":" + $entryFireValues[index] + ","
    });
    //console.log(json);
    _switchValues += json;
    //console.log(_switchValues);

    // Select elements by their data attribute
    const $entryFloodIdElements = $('[data-entry-floodid]');
    // Map over each element and extract the data value
    const $entryFloodIds =
        $.map($entryFloodIdElements, item => $(item).data('entryFloodid'));
    // You'll now have array containing string values
    //console.log($entryFloodIds); // eg: ["1", "2", "3"]
    // Select elements by their data attribute
    //const $entryFloodValueElements = $('[data-entry-floodvalue]');
    // Map over each element and extract the data value
    const $entryFloodValues =
        $.map($entryFloodValueElements, item => $(item).data('entryFloodvalue'));
    // You'll now have array containing string values
    //console.log($entryFloodValues); // eg: ["1", "2", "3"]
    str = "switch-flood-";
    json = "";
    $.each($entryFloodIds, function (index, value) {
        s = str + value;
        tabId[s] = value;
        json += "\"" + str + value + "\":" + $entryFloodValues[index] + ","
    });
    //console.log(json);
    _switchValues += json;

    // Select elements by their data attribute
    const $entryEmergencyIdElements = $('[data-entry-emergencyid]');
    // Map over each element and extract the data value
    const $entryEmergencyIds =
        $.map($entryEmergencyIdElements, item => $(item).data('entryEmergencyid'));
    // You'll now have array containing string values
    //console.log($entryEmergencyIds); // eg: ["1", "2", "3"]
    // Select elements by their data attribute
    const $entryEmergencyValueElements = $('[data-entry-emergencyvalue]');
    // Map over each element and extract the data value
    const $entryEmergencyValues =
        $.map($entryEmergencyValueElements, item => $(item).data('entryEmergencyvalue'));
    // You'll now have array containing string values
    //console.log($entryEmergencyValues); // eg: ["1", "2", "3"]
    str = "switch-emergency-";
    json = "";
    $.each($entryEmergencyIds, function (index, value) {
        s = str + value;
        tabId[s] = value;
        json += "\"" + str + value + "\":" + $entryEmergencyValues[index] + ","
    });
    //console.log(json);
    _switchValues += json;

    // Select elements by their data attribute
    const $entryDoorIdElements = $('[data-entry-doorid]');
    // Map over each element and extract the data value
    const $entryDoorIds =
        $.map($entryDoorIdElements, item => $(item).data('entryDoorid'));
    // You'll now have array containing string values
    //console.log($entryDoorIds); // eg: ["1", "2", "3"]
    // Select elements by their data attribute
    const $entryDoorValueElements = $('[data-entry-doorvalue]');
    // Map over each element and extract the data value
    const $entryDoorValues =
        $.map($entryDoorValueElements, item => $(item).data('entryDoorvalue'));
    // You'll now have array containing string values
    //console.log($entryDoorValues); // eg: ["1", "2", "3"]
    str = "switch-door-";
    json = "";
    $.each($entryDoorIds, function (index, value) {
        s = str + value;
        tabId[s] = value;
        json += "\"" + str + value + "\":" + $entryDoorValues[index] + ","
    });
    //console.log(json);
    _switchValues += json;

    _switchValues = _switchValues.substring(0, _switchValues.length - 1);
    _switchValues += "}";
    //console.log(_switchValues);
    //console.log(tabId);

    var tabAlarmIp = [];
    // Select elements by their data attribute
    const $entryAlarmIpElements = $('[data-entry-alarmip]');
    // Map over each element and extract the data value
    const $entryAlarmIps =
        $.map($entryAlarmIpElements, item => $(item).data('entryAlarmip'));
    // You'll now have array containing string values
    //console.log($entryAlarmIps); // eg: ["1", "2", "3"]
    $.each($entryAlarmIps, function (index, value) {
        tabAlarmIp[index] = value;
    });


    if (localStorage) { // Menu minifier status (Contract/expand side menu on large screens)
        console.log('localStorage : ' + localStorage);

        var checkboxValue = localStorage.getItem('minifier');

        if (checkboxValue === 'true') {
            $('#sidebar,#menu-minifier').addClass('mini');
            $('#minifier').prop('checked', true);

        } else {

            if ($('#minifier').is(':checked')) {
                $('#sidebar,#menu-minifier').addClass('mini');
                $('#minifier').prop('checked', true);
            } else {
                $('#sidebar,#menu-minifier').removeClass('mini');
                $('#minifier').prop('checked', false);
            }
        }

        // Switch statuses
        /*
        var switchValues = JSON.parse(localStorage.getItem('switchValues')) || {};
        //console.log(localStorage.getItem('switchValues'));
        //console.log('switchValues : ' + switchValues);

        $.each(switchValues, function (key, value) { // Apply only if element is included on the page
            if ($('[data-unit="' + key + '"]').length) {


                // In case of Camera unit - play video
                //if (key === "switch-camera-1" || key === "switch-camera-2") {
                if (String(key).indexOf("switch-camera-") >= 0) {
                    $('[data-unit="' + key + '"] video')[0].play();

                    console.log('key : ' + key);
                    console.log('value : ' + value);

                    if (value === true) { // Apply appearance of the "unit" and checkbox element
                        $('[data-unit="' + key + '"]').addClass("active");
                        $("#" + key).prop('checked', true);
                        $("#" + key).closest("label").addClass("checked");
                    }

                } else {
                    $('[data-unit="' + key + '"]').removeClass("active");
                    $("#" + key).prop('checked', false);
                    $("#" + key).closest("label").removeClass("checked");
                    if (String(key).indexOf("switch-camera-") >= 0) {
                        $('[data-unit="' + key + '"] video')[0].pause();
                    }
                }
            }
        });*/
    }
    //console.log('_switchValues : ' + _switchValues);
    init(JSON.parse(_switchValues) || {}); //Init switch appearance

    function init(switchValues) {
        $.each(switchValues, function (key, value) { // Apply only if element is included on the page
            //console.log('key : ' + key);
            //console.log('value : ' + value);
            if ($('[data-unit="' + key + '"]').length) {

                if (value === 1) { // Apply appearance of the "unit" and checkbox element
                    $('[data-unit="' + key + '"]').addClass("active");
                    $("#" + key).prop('checked', true);
                    $("#" + key).closest("label").addClass("checked");
                    $('#' + key).closest("label").toggleClass("checked", true);

                    // In case of Camera unit - play video
                    if (key === "switch-camera-1" || key === "switch-camera-2") {
                        $('[data-unit="' + key + '"] video')[0].play();
                    }

                } else {
                    $('[data-unit="' + key + '"]').removeClass("active");
                    $("#" + key).prop('checked', false);
                    $("#" + key).closest("label").removeClass("checked");
                    if (key === "switch-camera-1" || key === "switch-camera-2") {
                        $('[data-unit="' + key + '"] video')[0].pause();
                    }
                }
            }
        });
    }

    //Actualisation des états des équipements toutes les 500ms
    //setInterval(updateState, 500);
    /*
    function updateState() {
        $.getJSON('/gpio?refresh', function (data) {
            
        }).fail(function (err) {
            console.log("err getJSON Device State " + JSON.stringify(err));
        });
    }
    */

    // Contract/expand side menu on click. (only large screens)
    $('#minifier').click(function () {

        $('#sidebar,#menu-minifier').toggleClass('mini');

        // Save side menu status to localStorage if available (IE)
        if (localStorage) {
            checkboxValue = this.checked;
            localStorage.setItem('minifier', checkboxValue);
        }

    });


    // Side menu toogler for medium and small screens
    $('[data-toggle="offcanvas"]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });


    // Switch (checkbox element) toogler
    $('.switch input[type="checkbox"]').on("change", function (t) {

        // Check the time between changes to prevert Android native browser execute twice
        // If you dont need support for Android native browser - just call "switchSingle" function
        if (this.last) {

            this.diff = t.timeStamp - this.last;

            // Don't execute if the time between changes is too short (less than 250ms) - Android native browser "twice bug"
            // The real time between two human taps/clicks is usually much more than 250ms"
            if (this.diff > 250) {

                this.last = t.timeStamp;

                iot.switchSingle(this.id, this.checked);

            } else {
                return false;
            }

        } else { // First attempt on this switch element
            this.last = t.timeStamp;

            iot.switchSingle(this.id, this.checked);

        }
    });

    // All ON/OFF controls
    $('.lights-control').click(function () {

        var target = $(this).closest('.lights-controls').data('controls');
        var action = $(this).data('action');

        iot.switchGroup(target, action);
    });

    // Reposition to center when a modal is shown
    $('.modal.centered').on('show.bs.modal', iot.centerModal);

    // Reset/Stop countdown timer (EXIT NOW)
    $('#armModal').on('hide.bs.modal', iot.clearCountdown);

    // Garage doors controls
    /*$('.doors-control').click(function () {

        var target = $(this).closest('.timer-controls').data('controls');
        var action = $(this).data('action');

        iot.garageDoors(target, action);
    });*/

    // Alerts "Close" callback - hide modal and alert indicator dot when user closes all alerts
    $('#alertsModal .alert').on('close.bs.alert', function () {
        var sum = $('#alerts-toggler').attr('data-alerts');
        sum = sum - 1;
        $('#alerts-toggler').attr('data-alerts', sum);

        if (sum === 0) {
            $('#alertsModal').modal('hide');
            $('#alerts-toggler').attr('data-toggle', 'none');

        }

    });

    // Show/hide tips (popovers) - FAB button (right bottom on large screens)
    $('#info-toggler').click(function () {

        if ($('body').hasClass('info-active')) {
            $('[data-toggle="popover-all"]').popover('hide');
            $('body').removeClass('info-active');
        } else {
            $('[data-toggle="popover-all"]').popover('show');
            $('body').addClass('info-active');
        }
    });

    // Hide tips (popovers) by clicking outside
    $('body').on('click', function (pop) {

        if (pop.target.id !== 'info-toggler' && $('body').hasClass('info-active')) {
            $('[data-toggle="popover-all"]').popover('hide');
            $('body').removeClass('info-active');
        }

    });

});

// Apply necessary changes, functionality when content is loaded
$(window).on('load', function () { // This script is necessary for cross browsers icon sprite support (IE9+, ...)
    svg4everybody();

    // Washing machine - demonstration of running program/cycle
    /*$('#wash-machine').timer({
        countdown: true,
        format: '%H:%M:%S',
        duration: '1h17m10s',
        callback: function () {
            $('[data-unit="wash-machine"]').removeClass("active");
        }
    });*/

    if ($('[data-unit="switch-camera-1"]').hasClass("active")) {
        var activeVideo = $('[data-unit="switch-camera-1"] video').get(0);

        if (activeVideo.paused) {
            activeVideo.autoplay = true;
            activeVideo.load();
            activeVideo.play();
        } else {
            activeVideo.pause();
        }
    }

    if ($('[data-unit="switch-camera-2"]').hasClass("active")) {
        var activeVideo = $('[data-unit="switch-camera-2"] video').get(0);

        if (activeVideo.paused) {
            activeVideo.autoplay = true;
            activeVideo.load();
            activeVideo.play();
        } else {
            activeVideo.pause();
        }
    }

    // "Timeout" function is not neccessary - important is to hide the preloader overlay
    setTimeout(function () { // Hide preloader overlay when content is loaded
        $('#iot-preloader,.card-preloader').fadeOut();
        $("#wrapper").removeClass("hidden");

        // Check for Main contents scrollbar visibility and set right position for FAB button
        iot.positionFab();

    }, 800);

});

// Apply necessary changes if window resized
$(window).on('resize', function () { // Modal reposition when the window is resized
    $('.modal.centered:visible').each(iot.centerModal);

    // Check for Main contents scrollbar visibility and set right position for FAB button
    iot.positionFab();
});

