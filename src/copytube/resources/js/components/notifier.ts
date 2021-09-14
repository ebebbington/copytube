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
    const $notifierContainer = document.querySelector<HTMLDivElement>(
      "#notifier-container"
    );
    if (messageType && title && message) {
      $notifierContainer.classList.remove(
        classNames.toString().replace(",", " ")
      );
      $notifierContainer.classList.add(messageType);
      $notifierContainer.style.visibility = "visible";
      $notifierContainer.querySelector("p#notifier-title").textContent = title;
      $notifierContainer.querySelector("p#notifier-message").textContent =
        message;
      setTimeout(function () {
        $notifierContainer.classList.remove("error warning success");
        $notifierContainer.style.visibility = "hidden";
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
