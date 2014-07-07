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

		admin:

	********************************************************************************************************************/

	Class admin extends OObject {

		public function __construct() {

			$this->permissions = array(
				'object' => 'any',
				'getThemes' => 'any',
				'getLayouts' => 'any',
				'out' => 'any'
			);
		}

		// load a PHP view script ( using output buffering )
		private function load_view($filename, $params = array()) {

		    if(is_file($filename)) {
		        ob_start();
		        include $filename;
		        return ob_get_clean();
		    }
		    return FALSE;
		}

		// scan /themes folder for list of all themes and layouts
		public function getThemes() {
			$themes = array();
			$i = 0;
			$j = 0;
			$dir = __SELF__ . 'themes';
			if(is_dir($dir)) {
				if($dh_theme = opendir($dir)) {
					while(($file_theme = readdir($dh_theme)) !== FALSE) {
						if(is_dir($dir . '/' . $file_theme) == TRUE && $file_theme != '.' && $file_theme != '..') {
							// scan for theme file / name
							$matches = '';
							if(file_exists($dir . '/' . $file_theme . '/theme.txt') == TRUE) {
								$theme_txt = file_get_contents($dir . '/' . $file_theme . '/theme.txt');
								preg_match("@\[THEME:(.*?)\]@i", $theme_txt, $matches);
							}
							$themes[$i] = new stdClass();
							$themes[$i]->file = $file_theme;
							$themes[$i]->name = $file_theme;
							if(isset($matches[1]) == TRUE) {
								$themes[$i]->name = $matches[1];
							}
							$themes[$i]->layouts = [];
							if($dh_layout = opendir($dir . '/' . $file_theme)) {
								// scan for layout file / name
								$j = 0;
								while(($file_layout = readdir($dh_layout)) !== FALSE) {
									if(is_file($dir . '/' . $file_theme . '/' . $file_layout) == TRUE && preg_match('@^(.*?)\.html?$@', $file_layout) == 1) {
										$themes[$i]->layouts[$j] = new stdClass();
										$themes[$i]->layouts[$j]->file = $file_layout;
										$themes[$i]->layouts[$j]->name = $file_layout;
										$matches = '';
										$pattern = '';
										$pattern = '@\[LAYOUT:' . str_replace('.', '\.', $file_layout) . '\|NAME=(.*?)\]@i';
										preg_match($pattern, $theme_txt, $matches);
										if(isset($matches[1]) == TRUE) {
											$themes[$i]->layouts[$j]->name = $matches[1];
										}
										$j++;
									}
								}
							}
							closedir($dh_layout);
							$i++;
						}
					}
				closedir($dh_theme);
				}
			}
			$_SESSION["cms"]["themes"] = $themes;
			$this->data = $themes;
			return $this->data;
		}

		// get layouts of a selected theme
		public function getLayouts($params = array()) {

			$this->data = new stdClass();

			$themes = $this->getThemes();
			$layouts = '';

			if(isset($params["theme"]) == TRUE) {
				foreach($themes as $theme) {
					if($theme->file == $params["theme"] || $theme->name == $params["theme"]) {
						$layouts = $theme->layouts;
					}
				}
			}

			$this->data = $layouts;

		}

		// generate admin section
		public function out($path, $params, $direct=TRUE) {

			$this->getThemes();

			// set the content type
			$this->setContentType('text/html');

			// default protocol
			$protocol = 'http://';

			// default layout
			$layout = 'dashboard.html';

			$data = array();

			// set the layout based on the path
			if(preg_match('@^\/admin\/(.*?)$@i', $path) == 1) 						{ $layout = 'dashboard.html';   		}

			if(preg_match('@^\/admin\/content\/(.*?)$@i', $path) == 1) 				{ $layout = 'contentAllPages.html'; 	}
			if(preg_match('@^\/admin\/content\/new\/$@i', $path) == 1) 				{ $layout = 'contentPage.html';   		}
			if(preg_match('@^\/admin\/content\/edit/(.*?)$@i', $path) == 1) 		{ $layout = 'contentPage.html';   		}
			if(preg_match('@^\/admin\/content\/categories\/(.*?)$@i', $path) == 1) 	{ $layout = 'contentCategories.html';   }
			if(preg_match('@^\/admin\/content\/tags\/(.*?)$@i', $path) == 1) 		{ $layout = 'contentTags.html';   		}
			if(preg_match('@^\/admin\/content\/files\/(.*?)$@i', $path) == 1) 		{ $layout = 'contentFiles.html'; 		}

			if(preg_match('@^\/admin\/login\/(.*?)$@i', $path) == 1) 				{ $layout = 'login.html';   			}

			if(preg_match('@^\/admin\/users\/(.*?)$@i', $path) == 1) 				{ $layout = 'usersAllUsers.html'; 		}

			if(preg_match('@^\/admin\/debug\/(.*?)$@i', $path) == 1) 				{ $layout = 'debug.html';   			}
			//if(preg_match('@^\/admin\/???(.*?)$@i', $path) == 1) 					{ $layout = '???.html'; 				}
			//if(preg_match('@^\/admin\/???(.*?)$@i', $path) == 1) 					{ $layout = '???.html'; 				}
			//if(preg_match('@^\/admin\/???(.*?)$@i', $path) == 1) 					{ $layout = '???.html'; 				}

			// load theme / layout
			if(file_exists(__SELF__ . 'administrator/theme/' . $layout) == TRUE) {
				// load theme / layout in memory
				$output = file_get_contents(__SELF__ . 'administrator/theme/' . $layout);
			}

			// process views without parametwers [V:/path/to/view.php]
			$count = preg_match_all("@\[V:(.*?)\]@i", $output, $matches);
			if(isset($matches[1]) == TRUE && $count > 0) {
				$i = 0;
				foreach($matches[1] as $view) {
					$output = str_ireplace("[V:" . $view . "]",	$this->load_view( __SELF__ . 'administrator/theme' . $view, $params), $output);
				}
			}


			// content
			/*
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
			*/

			$this->html = $output;
		}
	}


?>