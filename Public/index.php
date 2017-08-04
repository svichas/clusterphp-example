<?php
session_start();

define("SEP", DIRECTORY_SEPARATOR);
define("__BASE__", dirname(__FILE__).SEP."..".SEP);

require __BASE__ . "Cluster".SEP."Require".SEP."autoload.php";

use Cluster\Kernel\App\Cluster;

define("DEVELOPMENT_MODE", true);

/*
Cluster::setup() will run setup method in all models if DEVELOPMENT_MODE is true.
*/
//Cluster::setup();

$Cluster_app = new Cluster();