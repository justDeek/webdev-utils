<?php

//------ toastr ------

/**
 * Shows the pop-up immediately on call. Use defer_popup() if the website might reload after calling this.
 *
 * @param     $type = info|success|warning|error
 * @param     $message = what message to display inside the popup
 * @param int $delay = (optional) delay between page being loaded and displaying the popup
 */
function show_popup($type, $message, $delay = 0) {
  if ($delay == 0) {
    echo "<script>show_popup('$type', '$message');</script>";
  } else {
    echo "<script>setTimeout(function() {
      show_popup('$type', '$message');
    }, $delay);</script>";
  }

  echo "<noscript style=\"color: red\">$type: $message</noscript>";
}

/**
 * Shows the pop-up after the page has been reloaded. Useful for events that occur while editing entries, as the page
 * usually reloads afterwards and wouldn't display the pop-up long enough.
 *
 * @param $type = info|success|warning|error
 * @param $message = what message to display inside the popup
 */
function defer_popup($type, $message) {
  if (!has_session()) {
    logm("Calling defer_popup() outside of a valid session!");

    return;
  }

  if ($type != "info" && $type != "success" && $type != "warning" && $type != "error") {
    logm("Invalid type passed into defer_popup(): \"$type\". Valid options are \"info\", \"success\", \"warning\" or \"error\"");
  }

  $_SESSION["{$type}_msg"] = $message;
}

/** [Internal] Used to display any queued popup by checking for set session-variables */
function check_messages() {
  if (!has_session()) {
    return;
  }
  $delay = 350; //default delay; shouldn't be set too low as the fade in might lag during page load

  //debug
  //  logm($_SESSION["success_msg"] ?? "", $_SESSION["info_msg"] ?? "",
  //       $_SESSION["warning_msg"] ?? "", $_SESSION["error_msg"] ?? "");

  while (isset($_SESSION["success_msg"]) || isset($_SESSION["info_msg"])
         || isset($_SESSION["warning_msg"])
         || isset($_SESSION["error_msg"])) {
    if (isset($_SESSION["success_msg"])) {
      show_popup("success", $_SESSION["success_msg"], $delay);
      unset($_SESSION["success_msg"]);
    }
    if (isset($_SESSION["info_msg"])) {
      show_popup("info", $_SESSION["info_msg"], $delay);
      unset($_SESSION["info_msg"]);
    }
    if (isset($_SESSION["warning_msg"])) {
      show_popup("warning", $_SESSION["warning_msg"], $delay);
      unset($_SESSION["warning_msg"]);
    }
    if (isset($_SESSION["error_msg"])) {
      show_popup("error", $_SESSION["error_msg"], $delay);
      unset($_SESSION["error_msg"]);
    }

    $delay += 50;
  }
}

