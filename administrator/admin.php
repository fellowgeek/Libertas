<?php

    /***********************************************************************

	The MIT License (MIT)

	Copyright (c) 2013 Erfan Reed <erfan.reed@gmail.com>

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.

    ***********************************************************************/

	if (!class_exists( 'OObject' )) { die(); }

	/********************************************************************************************************************

		pages:

	********************************************************************************************************************/

	Class admin extends OObject {

		public function __construct() {

			$this->permissions = array(
				'object' => 'any',
				'out' => 'any'
			);
		}

		// admin output method
		public function out($path, $params, $direct=TRUE) {

			//new dBug($path);
			//new dBug($params);


			// set the content type
			$this->setContentType('text/html');

			// default protocol
			$protocol = 'http://';

			// set the theme
			$theme = 'bootstrap';

			// set the layout
			$layout = 'dashboard.html';

			// if theme / layout exists
			if(file_exists(__SELF__ . 'administrator/themes/' . $theme . '/' . $layout) == TRUE) {
				// load theme / layout in memory
				$output = file_get_contents(__SELF__ . 'administrator/themes/' . $theme . '/' . $layout);
				// fix the path of all relative href attributes
				$output = preg_replace("@href=\"(?!(http://)|(https://))(.*?)\"@i", "href=\"" . $protocol . __SITE__ . "/administrator/themes/". $theme. "/$3\"", $output);
				// fix the path of all relative src attributes
				$output = preg_replace("@src=\"(?!(http://)|(https://))(.*?)\"@i", "src=\"" . $protocol . __SITE__ . "/administrator/themes/". $theme. "/$3\"", $output);
				// fix for themes built on skell.js
				$output = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@i", "_skel_config.prefix = \"" . $protocol . __SITE__ . "/administrator/themes/". $theme. "/$1\"", $output);
			}

			// process admin dashboard

			$output = str_ireplace("[A:Shortcuts]", include_view( __SELF__ . 'administrator/views/shortcuts.php'), $output);
			$output = str_ireplace("[A:Stats]", include_view( __SELF__ . 'administrator/views/stats.php'), $output);
			$output = str_ireplace("[A:Pending]", include_view( __SELF__ . 'administrator/views/pending.php'), $output);
			$output = str_ireplace("[A:Pending|Count]", rand(1,100), $output);
			$output = str_ireplace("[A:Drafts]", include_view( __SELF__ . 'administrator/views/drafts.php'), $output);
			$output = str_ireplace("[A:Drafts|Count]", rand(1,100), $output);
			$output = str_ireplace("[A:Images]", include_view( __SELF__ . 'administrator/views/images.php'), $output);

			$this->html = $output;
		}
	}
?>