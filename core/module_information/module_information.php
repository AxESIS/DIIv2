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
		
		File: module_information.php
		File Version: 1.0.0.0
		Application Version: 2.0.0.0
*/

namespace APE\V2\core;

class module_information
{
	public $name;
	public $version;
	public $call_name;
	public $type;
	
	public function __construct(string $name, string $version, string $call, string $type)
	{
		$this->name = $name;
		$this->version = $version;
		$this->call_name = $call;
		$this->type = $type;	
	}
}