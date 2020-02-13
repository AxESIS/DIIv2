<?php
/*
		AxESIS PHP ENGINE (APE) - Core installation for most AxESIS Projects (This is a closed sourced project)
		Copyright (C) 2020 Mitchell Reynolds & AxESIS

		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.

		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <https://www.gnu.org/licenses/>.
		
		File: index.php
		File Version: 1.0.0.0
		Application Version: 2.0.0.0
*/

namespace APE\V2\core;
//start a session.
session_start();

//set locale. 
setlocale(LC_ALL,'en_US.UTF-8');

//set datetime. 
date_default_timezone_set("Australia/Melbourne");

//add the mysql namespace to here.
use APE\V2\modules\database\mysql\mysql as mysql;
use APE\V2\modules\rest\rest as rest;
use APE\V2\modules\encryption\crypt as APEcrypt;

//static variables
require __DIR__ . "/core/static_variables.php";
$core_vars = new vars();

//load the module information classes (templates)
require __DIR__ . "/core/module_information/module_information.php";

//load the updater class files.
require __DIR__ . "/core/updater/updater.php";

//bundling moduler
require __DIR__ . "/core/bundler/absbundler.php";
require __DIR__ . "/core/bundler/bundler.php";
$bundler = new bundler($core_vars);

//load the config loader.
require __DIR__ . "/core/config/config_loader.php";
new config_loader($core_vars);

$mysql = new mysql($core_vars::$config["rst"]["database"]["hostname"], $core_vars::$config["rst"]["database"]["username"], $core_vars::$config["rst"]["database"]["password"], $core_vars::$config["rst"]["database"]["database"]);

$crypt = new APEcrypt($core_vars);

//we need to setup a new update class.
$update = new update($core_vars);
$update_needed = $update->check();
if($update_needed && (filter_input(INPUT_GET, "update") == 1)){
		echo $update->do_update();
		die();
}
elseif(!$update_needed && (filter_input(INPUT_GET, "update") == 1)){
	echo "Update not needed";
	die();
}
	

