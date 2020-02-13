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
		
		File: updater.php
		File Version: 1.0.0.1
		Application Version: 2.0.0.0
*/
namespace APE\V2\core;

use APE\V2\modules\rest\rest as rest;

class update{
	private $conf;
	private $conf_current_version;
	private $current_version;
	private $current_dir;
	private $token;
	
	public function __construct(&$core_vars)
	{
		$this->conf                 = $core_vars::$config["update"]["api"];
		$this->conf_current_version = explode(".",trim($core_vars::$config["version"]));
		$this->current_dir          = $core_vars::$config["update"]["site"]["path"];
		
		$this->current_version["major"]    = $this->conf_current_version[0];
		$this->current_version["minor"]    = $this->conf_current_version[1];
		$this->current_version["revision"] = $this->conf_current_version[2];
		
		//get the api token.
		$rest = new rest("post", $this->conf["url"] . "auth/json/", 
						 array(
							 "username" => $this->conf["user"], 
							 "password" => $this->conf["pass"]
						 ), $this->conf["proxy"]);
		$r = $rest->_call();

		$this->token = json_decode($r, true);
		$this->token = $this->token["data"];
	}
	
	public function check() : bool
	{
		//now that we have the token, let's see all the versions of the software.
		$check_ver_url = "updates/json/$this->token/diif/";
		$rest = new rest("get", $this->conf["url"] . $check_ver_url, array(), $this->conf["proxy"]);
		$r = $rest->_call();
		
		//same as before, let's strip the response.
		$r = json_decode($r, true)["data"];
		
		$return = false;
		foreach($r as $nv)
		{
			$new_v = explode(".", $nv["version"]);
			$new_version["major"] = $new_v[0];
			$new_version["minor"] = $new_v[1];
			$new_version["revision"] = $new_v[2];

			$download = $this->check_version($this->current_version, $new_version);
			if($download) $return = true;
		}
		
		return $return;
	}
	
	public function do_update()
	{
		//now that we have the token, let's see all the versions of the software.
		$check_ver_url = "updates/json/$this->token/diif/";
		$rest = new rest("get", $this->conf["url"] . $check_ver_url, array(), $this->conf["proxy"]);
		$r = $rest->_call();
		
		//same as before, let's strip the response.
		$r = json_decode($r, true)["data"];
		
		foreach($r as $nv)
		{
			$new_v = explode(".", $nv["version"]);
			$new_version["major"] = $new_v[0];
			$new_version["minor"] = $new_v[1];
			$new_version["revision"] = $new_v[2];

			$download = $this->check_version($this->current_version, $new_version);

			//let's try an auto download...
			if($download)
			{
				$version = $nv["version"];
				$download_url  = $this->conf["url"] . "updates/json/$this->token/diif/download/$version/";
				$download_file = $_SERVER['DOCUMENT_ROOT'] . "../data/updates/". $nv["file"];
				$this->download($download_url, $this->conf["proxy"], $download_file);
				$checksum = md5_file($download_file);

				if(strtolower($checksum) !== strtolower($nv["checksum"])){
					//the file is baaaaddddd!
					unlink($download_file);
					die("Update checksum failed!");
				}

				$extract_dir = $_SERVER['DOCUMENT_ROOT'] . "../data/updates/temp/";
				//let's extract this update?
				@mkdir($extract_dir);
				$phar = new \PharData($download_file);
				$phar->extractTo($extract_dir, null, true);

				//move the files to the new path.
				$move_to = $_SERVER["DOCUMENT_ROOT"] . $this->current_dir;
				$this->recurse_copy($extract_dir, $move_to);

				//let's just unlink all the files here.
				$this->delete_directory($extract_dir);
				unlink($download_file);
				
				return "Update Succeeded.";
			}

		}
	}
	
	function check_version($cv, $nv)
	{
		$download_now = false;
		//version numbers will always go up. check with the revision first.
		if($nv["revision"] > $cv["revision"] && $nv["minor"] == $cv["minor"] && $nv["major"] == $cv["major"])
			//new revision has been released.
			$download_now = true;

		if($nv["minor"] > $cv["minor"] && $nv["major"] == $cv["major"])
			//new minor patch has been released.
			$download_now = true;

		if($nv["major"] > $cv["major"])
			$download_now = true;

		return $download_now;
	}
	
	private function download($url, $proxy = null, $download_dir = __DIR__ . "/update.tar")
	{
		$fp = fopen ($download_dir, 'w+');
		$ch = curl_init(str_replace(" ","%20", $url));
		curl_setopt($ch, CURLOPT_TIMEOUT, 50);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_exec($ch); 
		curl_close($ch);
		fclose($fp);
	}

	private function delete_directory($dir)
	{
		$it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new \RecursiveIteratorIterator($it,
					 \RecursiveIteratorIterator::CHILD_FIRST);
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
		rmdir($dir);
	}

	private function recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ($file = readdir($dir))) 
			if (( $file != '.' ) && ( $file != '..' )) 
				if ( is_dir($src . '/' . $file) ) 
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				else 
					rename($src . '/' . $file,$dst . '/' . $file);
		closedir($dir); 
	} 

}