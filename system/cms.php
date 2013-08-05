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

		// CMS output method
		public function out() {
			// calling the missing handler method
			$this->missing('', $params=array(), $direct=TRUE);
		}

		public function missing($path, $params=array(), $direct=TRUE) {
			// default object
			$post = new stdClass();
			// default output
			$output = '';
			// default protocol
			$porotocol = 'http://';
			// set the theme
			$theme = __THEME__;


			// set the content type
			$this->setContentType('text/html');

			// pre process path ( ensure all ends with "/"
			if($path[strlen($path)-1] != '/') {
				$path .= '/';
			}

			// create a MD5 hash from the current path
			$path_hash = md5($path);

			// check the database for existing post at the path
			$params = array(
				'post_path_hash' => $path_hash
			);

			// store the post into memory object
			$post = $this->route('/sys/posts/get/', $params);

			//new dBug($post->data);

			// if post exists at path
			if(isset($post->data) && count($post->data) != 0) {
				// set the theme
				$theme = __THEME__;
				if($post->data[0]->post_theme != '') {
					$theme = $post->data[0]->post_theme;
				}

				// set the layout
				$layout = __LAYOUT__;
				if($post->data[0]->post_layout != '') {
					$layout = $post->data[0]->post_layout;
				}

				// if theme / layout exists
				if(file_exists(__SELF__ . 'themes/' . $theme . '/' . $layout) == TRUE) {
					// load theme / layout in memory
					$output = file_get_contents(__SELF__ . 'themes/' . $theme . '/' . $layout);
					// fix the path of all relative href attributes
					$output = preg_replace("@href=\"(?!(http://)|(https://))(.*?)\"@i", "href=\"" . $porotocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
					// fix the path of all relative src attributes
					$output = preg_replace("@src=\"(?!(http://)|(https://))(.*?)\"@i", "src=\"" . $porotocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
					// fix for themes built on skell.js
					$output = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@i", "_skel_config.prefix = \"" . $porotocol . __SITE__ . "/themes/". $theme. "/$1\"", $output);

					// process post [P:COMMAND] commands
					$post_text = $post->data[0]->post_text;

					$output = preg_replace("@\[P\:Text\]@i", $post->data[0]->post_text, $output);
					$output = preg_replace("@\[P\:Title\]@i", $post->data[0]->post_title, $output);
					$output = preg_replace("@\[P\:Slug\]@i", $post->data[0]->slug, $output);
					$output = preg_replace("@\[P\:Link\]@i", $post->data[0]->post_path, $output);
					$output = preg_replace("@\[P\:Image\]@i", $post->data[0]->post_image, $output);
					$output = preg_replace("@\[P\:Audio\]@i", $post->data[0]->post_audio, $output);
					$output = preg_replace("@\[P\:Video\]@i", $post->data[0]->post_video, $output);
					$output = preg_replace("@\[P\:File\]@i", $post->data[0]->post_file, $output);
					$output = preg_replace("@\[P\:Author\]@i", $post->data[0]->post_author, $output);
					$output = preg_replace("@\[P\:Description\]@i", $post->data[0]->post_description, $output);

					// process timestamp
					$post_timestamp = $post->data[0]->post_timestamp;
					$output = preg_replace("@\[P\:Timestamp\]@i", date('U', $post_timestamp), $output);

					$post_timestamp_format = '';
					preg_match_all("@\[P\:Timestamp\|Format=(.*?)\]@i", $output, $matches);
					if(empty($matches[1]) == FALSE) {
						foreach($matches[1] as $post_timestamp_format) {
							$output = str_replace("[P:Timestamp|Format=" . $post_timestamp_format . "]", date($post_timestamp_format, $post_timestamp), $output);
						}
					}

					$output = preg_replace("@\[P\:Views\]@i", $post->data[0]->post_views, $output);

					$output = preg_replace("@\[P\:Tags\]@i", $post->data[0]->post_tags, $output);
					$output = preg_replace("@\[P\:Tags\|Format=List\]@i", $post->data[0]->post_tags_list, $output);

					$output = preg_replace("@\[P\:Keywords\]@i", $post->data[0]->post_keywords, $output);
					$output = preg_replace("@\[P\:Keywords\|Format=List\]@i", $post->data[0]->post_keywords_list, $output);

					$output = preg_replace("@\[P\:Categories\]@i", $post->data[0]->post_categories, $output);
					$output = preg_replace("@\[P\:Categories\|Format=List\]@i", $post->data[0]->post_categories_list, $output);

				} else {
					$this->throwError('Selected theme and layout does not exist.',500,$type='notfound');
				}

			// if post does not exist show 404 message / html
			} else {
				// set 404 layout
				$layout = '404.html';

				// if theme / layout exists
				if(file_exists(__SELF__ . 'themes/' . __THEME__ . '/' . $layout) == TRUE) {
					// load theme / layout in memory
					$output = file_get_contents(__SELF__ . 'themes/' . $theme . '/' . $layout);
					// fix the path of all relative href attributes
					$output = preg_replace("@href=\"(?!(http://)|(https://))(.*?)\"@i", "href=\"" . $porotocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
					// fix the path of all relative src attributes
					$output = preg_replace("@src=\"(?!(http://)|(https://))(.*?)\"@i", "src=\"" . $porotocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
					// fix for themes built on skell.js
					$output = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@i", "_skel_config.prefix = \"" . $porotocol . __SITE__ . "/themes/". $theme. "/$1\"", $output);
				} else {
					$this->throwError('Page not found.',404,$type='notfound');
					print("404 Not Found.");
				}
			}

		$this->html = $output;
		}
	}
?>