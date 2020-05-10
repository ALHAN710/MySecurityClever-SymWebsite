/* @charset "UTF-8" */
/*
 * author: Jozef Dvorský
 */

var iot;

(function ($) {
  'use strict';

  var namespace;

  namespace = {

    // Vertical align center - Modal
    centerModal: function () {
      var modal = $(this),
        dialog = modal.find('.modal-dialog');
      modal.css('display', 'block');

      // Dividing by two centers the modal exactly, but dividing by three 
      // or four works better for larger screens.
      dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
    },

    //
    armingSecuritySystem: function (el, status) {
      // Show "EXIT NOW" only if Security system is activated
      if (status) {
        iot.armingModal(el);
      }
      // Change basic appearance
      $('[data-unit="' + el + '"]').toggleClass("active");
      $('#' + el).closest("label").toggleClass("checked", status);

    },

    // Switch ON/OFF (checkbox) elemet "on change" functionality
    switchSingle: function (el, status) {

      // Check switch statuses
      var switchValues = JSON.parse(localStorage.getItem('switchValues')) || {};

      // Change basic appearance
      /*
      $('[data-unit="' + el + '"]').toggleClass("active");
      $('#' + el).closest("label").toggleClass("checked", status);
      */

      var str = String(el);
      console.log('element id as string : ' + str);

      if (str.indexOf('switch-motion-') >= 0) {
        iot.armingSecuritySystem(el, status);
        str = tabId[el];
        //$('#switch-flood-1234567890').closest("label").toggleClass("checked", status);
      }
      else if (str.indexOf('switch-fire-') >= 0) {
        //iot.armingSecuritySystem(el, status);
        $('[data-unit="' + el + '"]').toggleClass("active");
        $('#' + el).closest("label").toggleClass("checked", status);
        str = tabId[el];
      }
      else if (str.indexOf('switch-flood-') >= 0) {
        //iot.armingSecuritySystem(el, status);
        $('[data-unit="' + el + '"]').toggleClass("active");
        $('#' + el).closest("label").toggleClass("checked", status);
        str = tabId[el];
      }
      else if (str.indexOf('switch-house-lock') >= 0) {
        iot.armingSecuritySystem(el, status);
        str = "house";
      }
      else if (str.indexOf('switch-camera-') >= 0) {
        // ON status - connect cam
        if (status) {
          iot.connectCam(el);
          // OFF status - disconnect cam
        } else {
          $('[data-unit="' + el + '"] video')[0].pause();
        }
        $('[data-unit="' + el + '"]').toggleClass("active");
        $('#' + el).closest("label").toggleClass("checked", status);
        str = "";
      }
      else {
        $('[data-unit="' + el + '"]').toggleClass("active");
        $('#' + el).closest("label").toggleClass("checked", status);

      }

      // Apply extended functionality to unique units
      /*
      switch (el) {
        // Security system
        case 'switch-house-lock':
          // Show "EXIT NOW" only if Security system is activated
          if (status) {
            iot.armingModal(el);
          }
          break;
        // Wash-machine system
        case 'switch-wash-machine':
          // Show "EXIT NOW" only if Security system is activated
          if (status) {
            iot.armingModal(el);
          }
          break;
        
        // Cameras - ON/OFF
        case 'switch-camera-1':
        case 'switch-camera-2':
          // ON status - connect cam
          if (status) {
            iot.connectCam(el);
            // OFF status - disconnect cam
          } else {
            $('[data-unit="' + el + '"] video')[0].pause();
          }
          break;
      }
      */

      //var $urlUpdateState = "/security/state";
      if (str !== "") {
        var $data = JSON.stringify({
          "moduleId": str,
          "state": status
        });

        console.log($data);

        $.ajax({
          type: "POST",//method type
          contentType: "application/json; charset=utf-8",
          url: $urlUpdateState,///Target function that will be return result
          data: $data,//parameter pass data is parameter name param is value 
          dataType: "json",
          success: function (data) {
            //alert("Success");
            console.log(data);

          },
          error: function (result) {
            console.log("Error");
            console.log(result);
          }
        });
      }

      // Update localStorage
      if (localStorage) {
        switchValues[el] = status;
        localStorage.setItem("switchValues", JSON.stringify(switchValues));
      }

    },

    // Switch ON/OFF all unit elements in group
    switchGroup: function (parent, action) {

      // Group
      var el = '[data-unit-group="' + parent + '"]',

        // Get last stored states
        switchValues = JSON.parse(localStorage.getItem('switchValues')) || {};

      // Apply changes based on action
      switch (action) {

        case 'all-on':
          $(el + ' [data-unit]').each(function () {

            var key = $(this).data('unit');
            $(this).addClass("active");
            $("#" + key).prop('checked', true);
            $("#" + key).closest("label").addClass("checked");

            switchValues[key] = true;

          });
          break;
        case 'all-off':
          $(el + ' [data-unit]').each(function () {
            var key = $(this).data('unit');
            $(this).removeClass("active");
            $("#" + key).prop('checked', false);
            $("#" + key).closest("label").removeClass("checked");

            switchValues[key] = false;
          });
          break;
      }

      // Save current states in localStorage
      if (localStorage) {
        localStorage.setItem("switchValues", JSON.stringify(switchValues));
      }

    },

    // EXIT NOW - contdown modal
    armingModal: function (unit) {

      // Check if "alarm unit" has class "active" - Android native browser bug
      /*if (!$('[data-unit="' + unit + '"]').hasClass("active")) {
        $('[data-unit="' + unit + '"]').addClass("active");
      }*/

      $('#armModal').modal('show');

      // Activate countdown
      $('#armTimer .timer').timer({
        countdown: true,
        format: '%s',
        duration: '10s', // Here you can set custom time to exit
        callback: function () {
          $('#armModal').modal('hide'); // Automaticaly hide modal on countdown end

          // Check if "alarm unit" has class "active" - Android native browser bug
          if (!$('[data-unit="' + unit + '"]').hasClass("active")) {
            $('[data-unit="' + unit + '"]').addClass("active");
          }

        }
      });

    },

    // Open/close garage doors
    /*garageDoors: function (parent, action) {
  
      var el = '[data-unit="' + parent + '"]';
      switch (action) {
        case 'open':
          $(el + ' .timer').timer({
            attribute: 'aria-valuenow',
            style: 'width',
            duration: $(el + ' .timer').attr('aria-valuemax') + 's',
            callback: function () {
              $(el + ' .timer').timer('remove');
              $(el + ' .status').removeClass('text-warning');
              $(el + ' .status').html('Open');
              $(el + ' [data-action="pause"]').hide();
              $(el + ' [data-action="close"]').show();
            }
          });
          $(el + ' .status').toggleClass('text-danger text-warning');
          $(el + ' .status').html('In progress');
          $(el + ' [data-action="open"]').hide();
          $(el + ' [data-action="pause"]').show();
          break;
        case 'pause':
          $(el + ' .timer').timer('pause');
          $(el + ' .status').html('Paused');
          $(el + ' [data-action="pause"]').hide();
          $(el + ' [data-action="resume"]').show();
          break;
        case 'resume':
          $(el + ' .timer').timer('resume');
          $(el + ' .status').html('In progress');
          $(el + ' [data-action="resume"]').hide();
          $(el + ' [data-action="pause"]').show();
          break;
        case 'close':
          $(el + ' .timer').timer({
            countdown: true,
            attribute: 'aria-valuenow',
            style: 'width',
            duration: $(el + ' .timer').attr('aria-valuemax') + 's',
            callback: function () {
              $(el + ' .timer').timer('remove');
              $(el + ' .status').toggleClass('text-danger text-warning');
              $(el + ' .status').html('Closed');
              $(el + ' [data-action="pause"]').hide();
              $(el + ' [data-action="open"]').show();
            }
          });
          $(el + ' .status').toggleClass('text-warning');
          $(el + ' .status').html('In progress');
          $(el + ' [data-action="close"]').hide();
          $(el + ' [data-action="pause"]').show();
          break;
      }
    },*/

    // Pause/resume Wash machine Program
    /*washMachine: function (parent, action) {
  
      var el = '[data-unit="' + parent + '"]';
      switch (action) {
        case 'pause':
          $('#wash-machine').timer('pause');
          $(el + ' .status').html('Paused');
          $(el + ' .status').addClass('text-warning');
          $(el + ' [data-action="pause"]').hide();
          $(el + ' [data-action="resume"]').show();
          break;
        case 'resume':
          $('#wash-machine').timer('resume');
          $(el + ' .status').html('ON');
          $(el + ' .status').removeClass('text-warning');
          $(el + ' [data-action="resume"]').hide();
          $(el + ' [data-action="pause"]').show();
          break;
      }
    },
    */


    // FAB button position base on scrollbar visibility
    positionFab: function () {

      var main = $('#main'),
        sis = main.get(0) ? main.get(0).scrollHeight > main.innerHeight() : false;

      if (sis) {
        $('#info-toggler').css('right', '40px');
      }
    },
    // Connect to camera with preloader. Use ajax request instead of timeout function.
    connectCam: function (cam) {
      $('[data-unit="' + cam + '"] .card-preloader').css("display", "block");
      setTimeout(function () {
        $('[data-unit="' + cam + '"] .card-preloader').fadeOut();
        $('[data-unit="' + cam + '"] video')[0].play();
      }, 800);
    },
    // Clear active countdown 
    clearCountdown: function () {

      $('#' + this.id + '  .timer').timer('remove');

    }
  };

  window.iot = namespace;

}(this.jQuery));
