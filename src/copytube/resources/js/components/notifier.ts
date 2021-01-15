"use strict";
/**
 * Have the following HTML:
 * <div id="notifier-container">
 *  <p id="notifier-title"></p>
 *  <p id="notifier-message"></p>
 * </div>
 */

type MessageTypes = "success" | "warning" | "error";

const Notifier = (function () {
    const classNames: string[] = ["success", "warning", "error"];

    const FADE_OUT_DELAY: number = 4000;

    function show(
        messageType: MessageTypes,
        title: string,
        message: string
    ): void {
        const $notifierContainer = $("#notifier-container");
        if (messageType && title && message) {
            $notifierContainer.removeClass(
                classNames.toString().replace(",", " ")
            );
            $notifierContainer.addClass(messageType);
            $notifierContainer.css("visibility", "visible");
            $notifierContainer.find("p#notifier-title").text(title);
            $notifierContainer.find("p#notifier-message").text(message);
            setTimeout(function () {
                $notifierContainer.removeClass("error warning success");
                $notifierContainer.css("visibility", "hidden");
            }, FADE_OUT_DELAY);
        }
    }

    function success(title: string, message: string): void {
        show("success", title, message);
    }

    function warning(title: string, message: string): void {
        show("warning", title, message);
    }

    function error(title: string, message: string) {
        show("error", title, message);
    }

    return {
        success: success,
        warning: warning,
        error: error,
    };
})();

export default Notifier;
