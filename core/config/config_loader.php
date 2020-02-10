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

	File: config_loader.php
	File Version: 1.0.0.0
	Application Version: 2.0.0.0
*/

namespace APE\V2\core;

/**
* Config Loader Class
*
* Imports the configurations for the program.
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 2.0.0.0
*/ 
class config_loader
{
	public function __construct(&$core_vars, $dir = __DIR__ . "/*.json")
	{
		$core_vars::$config = $this->load_files($dir);
	}
	
	private function load_files($dir)
	{
		$configs = bundler::get_files($dir);
		//fix up the directory strings.
		foreach($configs as $k => $config)
			$configs[$k] = realpath($config);
		
		$c = array();
		
		foreach($configs as $config)
		{
			$j = json_decode(file_get_contents($config), true);
			foreach($j as $k => $v)
				$c[$k] = $v;
			
		}
		return $c;
	}
}