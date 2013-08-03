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

	if (!class_exists( 'OObject' )) { die(); }

	/********************************************************************************************************************

		missing:

	********************************************************************************************************************/

	Class cms extends OObject {

		public function __construct() {

			$this->permissions = array(
				'object' => 'any',
				'out' => 'any'
			);
		}

		public function out() {
			$this->missing('', $params=array(), $direct=TRUE);
		}

		public function missing($path, $params=array(), $direct=TRUE) {

			$this->setContentType('text/html');

			$pageArray = explode('/', $path);
			$pageName = $pageArray[0];

			if($path[strlen($path)-1] != "/") {
				$path.="/";
			}

			$params = array(
				"post_path_hash" => md5($path)
			);

			$response = $this->route('/sys/posts/get/', $params);

			//new dBug($response->data);


			/*
			$params = array(
					"post_title" =>			 "Lorem Ipsum",
					"post_path" =>			 $path,
					"post_path_hash" => 	 md5($path),
					"post_text"  =>			 "<p>Nesciunt cliche officia  ennui ethnic iPhone leggings, nisi banjo keytar.  Gentrify nulla  elit Schlitz kale chips shabby chic.  Bicycle rights cred artisan polaroid.  Pug semiotics pour-over, keytar Brooklyn stumptown artisan Terry Richardson tofu fingerstache.  Cupidatat  veniam keffiyeh chambray culpa.  Pour-over messenger bag Brooklyn thundercats id, sustainable ullamco.  Dreamcatcher meh typewriter sriracha, velit  forage seitan.</p>",
					"post_author" => 		 0,
					"post_categories" =>	 "stories,place holder",
					"post_keywords" =>		 "",
					"post_description" =>	 "<p>Nesciunt cliche officia  ennui ethnic iPhone leggings, nisi banjo keytar...</p>",
					"post_tags" => 			 "Lorem,Ipsum,Dolor,Sit",
					"post_views" => 		 rand(1,10000),
					"post_status" => 		 "published"
				);

			$this->route("/sys/posts/add/",$params);
			*/

			//print($path);

			//print($pageArray[count($pageArray)-1]);

			$themeName = 'bootstrap';
			$themeLayout = 'index.html';

			$theme = file_get_contents(__SELF__ . 'themes/' . $themeName . '/' . $themeLayout);

			// fix the path of all relative href attributes
			$theme = preg_replace("@href=\"(([^http://]|[^https://])(.*?))\"@", "href=\"" . __SITE__ . "/themes/". $themeName. "/$1\"", $theme);

			// fix the path of all relative src attributes
			$theme = preg_replace("@src=\"(([^http://]|[^https://])(.*?))\"@", "src=\"" . __SITE__ . "/themes/". $themeName. "/$1\"", $theme);

			// fix for themes built on skell.js
			$theme = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@", "_skel_config.prefix = \"" . __SITE__ . "/themes/". $themeName. "/$1\"", $theme);

/*
[P:Title] 							// returns the current post's title
[P:Slug]							// returns the current post's slug ( The-Awesome-Post )
[P:Link]							// returns the current post's link ( http://www.example.com/The-Awesome-Post/ )
[P:Image]							// returns the current post's first image link ( http://www.example.com/AS2DFGDG3432RE2D2F.jpg )
[P:Video]							// returns the current post's first video link ( http://www.example.com/AS2DFGDG3432RE2D2F.mp4 )
[P:Audio]							// returns the current post's first audio link ( http://www.example.com/AS2DFGDG3432RE2D2F.mp3 )
[P:Text]							// returns the current post's text
[P:Author]							// returns the current post's author
[P:Description]						// returns the current post's description
[P:Timestamp]						// returns the current post's timestamp in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
[P:Timestamp|Format=FORMAT]			// returns the current post's timestamp formatted used php Date(FORMAT,Timestamp) function
[P:Views]							// returns the current post's views
[P:Tags]							// returns the current post's list of tags in <ul></li>TAG</li></ul> format
[P:Keywords]						// returns the current post's list of keywords in <ul></li>KEYWORDS</li></ul> format
[P:Categories]						// returns the current post's list of tags in <ul></li>CATEGORY</li></ul> format
*/

			if(isset($response->data) &&  count($response->data) != 0) {
				$theme = preg_replace("@\[P\:Title\]@", $response->data[0]->post_title, $theme);
				$theme = preg_replace("@\[P\:Link\]@", $response->data[0]->post_path, $theme);
				$theme = preg_replace("@\[P\:Text\]@", $response->data[0]->post_text, $theme);
			} else {
				$theme = preg_replace("@\[P\:Title\]@", "Title", $theme);
				$theme = preg_replace("@\[P\:Link\]@", "#", $theme);
				$theme = preg_replace("@\[P\:Text\]@", "Text", $theme);
			}



			$this->html = $theme;
		}
	}
?>