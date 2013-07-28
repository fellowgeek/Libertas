<?php

    /***********************************************************************

    Obray - Super lightweight framework.  Write a little, do a lot, fast.
    Copyright (C) 2013  Nathan A Obray

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    ***********************************************************************/

	if (!class_exists( "OObject" )) { die(); }

	/********************************************************************************************************************

		missing:

	********************************************************************************************************************/

	Class cms extends OObject{

		public function __construct(){

			$this->permissions = array(
				"object"=>"any",
				"out"=>"any"
			);

		}

		public function out(){
			$this->missing("",$params=array(),$direct=TRUE);
		}

		public function missing($path,$params=array(),$direct=TRUE){

			$this->setContentType("text/html");

			$pageArray = explode("/",$path);
			$pageName = $pageArray[0];

			new dBug(explode("/",$path));

			$templateName = 'html5up-txt';
			if($pageName == "simple") {
				$templateLayout = "simple.html";
			} else {
				$templateLayout = "index.html";
			}

			$template = file_get_contents(__SELF__ . 'templates/' . $templateName . '/' . $templateLayout);

			// fix the path of all relative href attributes
			$template = preg_replace("@href=\"(([^http://]|[^https://])(.*?))\"@", "href=\"" . __SITE__ . "/templates/". $templateName. "/$1\"",$template);
			// fix the path of all relative src attributes
			$template = preg_replace("@src=\"(([^http://]|[^https://])(.*?))\"@", "src=\"" . __SITE__ . "/templates/". $templateName. "/$1\"",$template);


			$template = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@", "_skel_config.prefix = \"" . __SITE__ . "/templates/". $templateName. "/$1\"",$template);


			$this->html = $template;

		}

	}
?>