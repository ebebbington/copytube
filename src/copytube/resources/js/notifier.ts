"use strict";
/**
 * Have the following HTML:
 * <div id="notifier-container">
 *  <p id="notifier-title"></p>
 *  <p id="notifier-message"></p>
 * </div>
 */
const Notifier = (function () {

    const classNames: string[] = [
        'success',
        'warning',
        'error'
    ];

    const FADE_OUT_DELAY: number = 4000;

    function show (messageType: string, title: string, message: string): void {
      if (messageType && title && message) {
        $('#notifier-container').removeClass(classNames.toString().replace(',', ' '));
        $('#notifier-container').addClass(messageType);
        $('#notifier-container').css('display', 'block');
        $('#notifier-container > p#notifier-title').text(title);
        $('#notifier-container > p#notifier-message').text(message);
        setTimeout(function () {
          $('#notifier-container').removeClass('error warning success');
        }, FADE_OUT_DELAY);
      }
    }

    function success(title: string, message: string): void {
      show('success', title, message)
    }

    function warning(title: string, message: string): void {
      show('warning', title, message)
    }

    function error(title: string, message: string) {
      show('error', title, message)
    }

    return {
        success: success,
        warning: warning,
        error: error
    };

})();

export default Notifier