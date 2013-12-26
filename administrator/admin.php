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

		// load a PHP view script ( using output buffering )
		public function load_view($filename, $data = array()) {

		    if(is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return false;
		}

		// generate admin section
		public function out($path, $params, $direct=TRUE) {

			//new dBug($path);
			//new dBug($params);

			// set the content type
			$this->setContentType('text/html');

			// default protocol
			$protocol = 'http://';

			// set the theme
			$theme = 'bootstrap';

			// default layout
			$layout = 'dashboard.html';


			$data = array();

			// set the layout based on the path
			if($path == '/admin/content/new/') { $layout = 'content.html'; }
			//if($path == '/admin/?/') { $layout = '?.html'; }
			//if($path == '/admin/?/') { $layout = '?.html'; }
			//if($path == '/admin/?/') { $layout = '?.html'; }
			//if($path == '/admin/?/') { $layout = '?.html'; }
			//if($path == '/admin/?/') { $layout = '?.html'; }
			//if($path == '/admin/?/') { $layout = '?.html'; }

			// load theme / layout
			if(file_exists(__SELF__ . 'administrator/theme/default/' . $layout) == TRUE) {
				// load theme / layout in memory
				$output = file_get_contents(__SELF__ . 'administrator/theme/default/' . $layout);
				// fix the path of all relative href attributes
				$output = preg_replace("@href=\"(?!(http://)|(https://))(.*?)\"@i", "href=\"" . $protocol . __SITE__ . "/administrator/theme/default/$3\"", $output);
				// fix the path of all relative src attributes
				$output = preg_replace("@src=\"(?!(http://)|(https://))(.*?)\"@i", "src=\"" . $protocol . __SITE__ . "/administrator/theme/default/$3\"", $output);
			}

			// content switcher for different views in admin section

			// general
			$output = str_ireplace("[A:Navigation]",	$this->load_view( __SELF__ . 'administrator/views/navigation.php'),				$output);

			// dashboard
			$output = str_ireplace("[A:Tips]",		 	$this->load_view( __SELF__ . 'administrator/views/dashboard/tips.php'),			$output);
			$output = str_ireplace("[A:Shortcuts]",	 	$this->load_view( __SELF__ . 'administrator/views/dashboard/shortcuts.php'),	$output);
			$output = str_ireplace("[A:Stats]", 		$this->load_view( __SELF__ . 'administrator/views/dashboard/stats.php'),		$output);
			$output = str_ireplace("[A:Pending]", 		$this->load_view( __SELF__ . 'administrator/views/dashboard/pending.php'),		$output);
			$output = str_ireplace("[A:Drafts]", 		$this->load_view( __SELF__ . 'administrator/views/dashboard/drafts.php'),		$output);

			// content
			if($path == '/admin/content/new/') {
                
                $data = array(
					'contentMode' => 'new',
                    'contentTitle' => '',
					'contentText' => '',
					'contentDescription' => '',
					'contentStatus' => 'draft',
					'contentPublish' => date('m/d/Y'),
					'contentPath' => '',
					'contentSSL' => '',
					'contentCategories' => '',
					'contentTags' => '',
					'contentLayout' => '',
					'contentTheme' => '',
					'contentVisibility' => 'everyone'
				);
			}

			$output = str_ireplace("[A:Editor]",	 	$this->load_view( __SELF__ . 'administrator/views/content/editor.php', $data),			$output);
			$output = str_ireplace("[A:Options]",	 	$this->load_view( __SELF__ . 'administrator/views/content/options.php', $data),		$output);

			// components

			// users

			// settings

			//$output = str_ireplace("[A:Images]", $this->load_view( __SELF__ . 'administrator/views/images.php'), $output);

			$this->html = $output;
		}
	}















?>