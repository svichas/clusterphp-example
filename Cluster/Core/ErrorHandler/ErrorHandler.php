<?php
namespace Cluster\Core\ErrorHandler;

use Cluster\Modules\String\Random;
use Cluster\Modules\Link\Link;

class ErrorHandler {

  public function ThrowError($error_details="Fetal error") {

    if (!DEVELOPMENT_MODE) return false;

    $base = __BASE__ . "Cluster" . SEP . "Core" . SEP . "ErrorHandler" . SEP;

    $error_id = Random::string(7);
    $logname = $error_id . ".json";

    $error_log = [];
    $error_log[$error_id] = $error_details;

    //save file
    file_put_contents($base . "ErrorLogs/". $logname, json_encode($error_log));

    Link::go("/cluster_error/{$error_id}");
  }

  public function getError($error_id="") {
    
    $base = __BASE__ . "Cluster" . SEP . "Core" . SEP . "ErrorHandler" . SEP;

    if (!file_exists($base . "ErrorLogs/{$error_id}.json")) return "Error not found!";

    $error_log = json_decode(file_get_contents($base . "ErrorLogs/{$error_id}.json"),true);

    return isset($error_log[$error_id]) ? $error_log[$error_id] : "Error not found!";

  }

}
