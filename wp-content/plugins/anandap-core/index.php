<?php
/*
Plugin Name: AnandaProfessional Core
Description: Important functionalities of the AnandaProfessional WordPress site
Author: Lancelot Gordon
Version: 0.1
*/

define("ANANDAP_CORE_PLUGIN_PATH", dirname(__FILE__));
define("ANANDAP_CORE_PLUGIN_URL", plugins_url('', __FILE__));

require_once "wc-gateway-customer-history.php";
require_once "SalesforceSDK.php";
require_once "functions.php";
