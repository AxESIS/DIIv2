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
		
		File: absbundler.php
		File Version: 1.0.0.0
		Application Version: 2.0.0.0
*/

namespace APE\V2\core;

/**
* Abstract Bundler Class
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 2.0.0.0
*/ 
abstract class abstract_bundler
{
	/**
	 * Glob Recursive Function
	 * 
	 * Gathers an array of files within a specific pattern (directory)
	 *
	 * @param string  $pattern A directory path / file and folder names
	 * 
	 * @throws Nil. Returns empty array.
	 * @author Mitchell Reynolds
	 * @return Array of full file directory paths
	 */ 
	protected static function glob_recursive($pattern)
	{
		$files = glob($pattern);

		foreach(glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
			$files = array_merge($files, self::glob_recursive($dir.'/'.basename($pattern)));

		return $files;
	}
	
	/**
	 * File Name Helper Function
	 * 
	 * Grabs and returns the directory name (and thus the file name)
	 *
	 * @param string  $file A file path of the module
	 * 
	 * @throws Nil.
	 * @author Mitchell Reynolds
	 * @return String - The directory/file root name
	 */ 
	private function file_name_helper(string $file) : string
	{
		$d = explode('/',dirname($file));
		$c = count($d);
		$n = $d[$c - 1];
		return $n;
	}
	
	/**
	 * File Exists Function
	 * 
	 * Checks to see if the corresponding module file to the json file exists.
	 *
	 * @param string  $file A file path of the module
	 * 
	 * @throws Nil.
	 * @author Mitchell Reynolds
	 * @return Bool - True if exists.
	 */ 
	private function check_file_exists(string $file) : bool
	{
		$n = $this->file_name_helper($file);
		return file_exists(dirname($file) . '/' . $n . '.module.php');
	}
	
	private function register_module(&$core_vars, $json, $file)
	{
		$json = $json["mod"];
		$pi = new module_information($json["name"], $json["version"], $json["call_name"], $json["type"]);
		$n = $this->file_name_helper($file);
		
		//load the file.
		require dirname($file) . '/' . $n . '.module.php';
		
		//check if the class exists.
		if(!class_exists($pi->call_name)) throw new \Exception("Class doesn't exist.", 500);
		
		//now let's register this module.
		foreach($core_vars::$modules as $p)
			if($p->name == $pi->name) throw new \Exception("Duplicate module detected.", 500);
		
		//drop the module in.
		$core_vars::$modules[] = $pi;
	}
	
	/**
	 * Register Files Function
	 * 
	 * Scrolls through, verifies and registers (loads and add's to static variables)
	 * all the modules in the modules folder.
	 *
	 * @param Reference Class (vars) - $core_vars An address of where the static variables for the program is.
	 * @param string                 - $dir directory to look into for the modules to register them
	 * 
	 * @throws Nil. Returns empty array.
	 * @author Mitchell Reynolds
	 * @return bool - True if completed successfully.
	 */ 
	protected function register_files(vars &$core_vars, string $dir) 
	{
		$module_files = self::glob_recursive($dir);
		//just gotta do a little bit of a cleanup...
		foreach($module_files as $k=>$module_file)
			$module_files[$k] = realpath($module_file);
		
		//cycle through to register each file
		foreach($module_files as $module_file)
		{
			$json = json_decode(file_get_contents($module_file),true);
			if(!$this->check_file_exists($module_file)) throw new \Exception("Module file doesn't exist.", 404);
			else $this->register_module($core_vars, $json, $module_file);
		}
	}
}
?>