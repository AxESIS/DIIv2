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
		
		File: bundler.php
		File Version: 1.0.0.0
		Application Version: 2.0.0.0
*/
namespace APE\V2\core;

/**
* Bundler Class
*
* Allows for the detection and addition of plugin / modules.
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 2.0.0.0
*/ 
class bundler extends abstract_bundler
{	
	/**
	 * Get Files Function
	 * 
	 * Returns an array of file locations from the specified directory
	 *
	 * @param string  $dir A directory path / file and folder names
	 * 
	 * @throws Nil. Returns empty array.
	 * @author Mitchell Reynolds
	 * @return Array of full file directory paths
	 */ 
	public static function get_files($dir)
	{
		return self::glob_recursive($dir);
	}
	
	/**
	 * Constructor Function
	 * 
	 * Initiates this functions when called upon a new.
	 *
	 * @param Reference Address  $core_vars Static variables used throughout this application
	 * @param string	$module_dir Directory path where modules are located.
	 * 
	 * @author Mitchell Reynolds
	 */ 
	public function __construct(&$core_vars, $module_dir = __DIR__ . '/../../modules/*.mod.json')
	{
		$this->register_files($core_vars, $module_dir);
	}
}



?>