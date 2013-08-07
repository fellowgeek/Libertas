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
			$snippet = new stdClass();

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

					$output = str_ireplace("[P:Text]", $post->data[0]->post_text, $output);

					// process snippets [S:Title]
					$snippet = '';
					preg_match_all("@\[S:(.*?)\]@i", $output, $matches);
					if(empty($matches[1]) == FALSE) {
						foreach($matches[1] as $snippet_title) {
							$snippet_path_hash = md5($snippet_title);

							$params = array(
								'post_path_hash' => $snippet_path_hash
							);

							$snippet = $this->route('/sys/posts/get/', $params);

							// if snippet exists at path
							if(isset($snippet->data) && count($snippet->data) != 0) {
								$output = str_ireplace("[S:" . $snippet_title . "]", $snippet->data[0]->post_text, $output);

								$output = str_ireplace("[S:" . $snippet_title . "|Text]", $snippet->data[0]->post_text, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Title]", $snippet->data[0]->post_title, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Slug]", $snippet->data[0]->slug, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Link]", $snippet->data[0]->post_path, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Image]", $snippet->data[0]->post_image, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Audio]", $snippet->data[0]->post_audio, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Video]", $snippet->data[0]->post_video, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|File]", $snippet->data[0]->post_file, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Author]", $snippet->data[0]->post_author, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Description]", $snippet->data[0]->post_description, $output);

								// process timestamp
								$snippet_timestamp = $snippet->data[0]->post_timestamp;
								$output = str_ireplace("[S:" . $snippet_title . "|Timestamp]", date('U', $snippet_timestamp), $output);

								$snippet_timestamp_format = '';
								preg_match_all("@\[S\:(.*?)\|Timestamp\|Format=(.*?)\]@i", $output, $matches);
								if(empty($matches[2]) == FALSE) {
									foreach($matches[2] as $snippet_timestamp_format) {
										$output = str_ireplace("[S:" . $snippet_title . "|Timestamp|Format=" . $snippet_timestamp_format . "]", date($snippet_timestamp_format, $snippet_timestamp), $output);
									}
								}
								unset($matches);

								$output = str_ireplace("[S:" . $snippet_title . "|Views]", $snippet->data[0]->post_views, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Tags]", $snippet->data[0]->post_tags, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Tags|Format=List]", $snippet->data[0]->post_tags_list, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Keywords]", $snippet->data[0]->post_keywords, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Keywords|Format=List]", $snippet->data[0]->post_keywords_list, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Categories]", $snippet->data[0]->post_categories, $output);
								$output = str_ireplace("[S:" . $snippet_title . "|Categories|Format=List]", $snippet->data[0]->post_categories_list, $output);
							}
						}
					}
					unset($matches);


					// process [P:Image|Channel=A]
					preg_match_all("@\[P:Image\|Channel=(.*?)\]@i", $output, $matches);
					if(empty($matches[1]) == FALSE) {
						foreach($matches[1] as $channel) {
							$output = str_ireplace("[P:Image|Channel=" . $channel . "]", "--". $channel ."--", $output);
						}
					}
					//new dBug($matches);


					$output = str_ireplace("[P:Title]", $post->data[0]->post_title, $output);
					$output = str_ireplace("[P:Slug]", $post->data[0]->slug, $output);
					$output = str_ireplace("[P:Link]", $post->data[0]->post_path, $output);
					$output = str_ireplace("[P:Image]", $post->data[0]->post_image, $output);



					$output = str_ireplace("[P:Audio]", $post->data[0]->post_audio, $output);
					$output = str_ireplace("[P:Video]", $post->data[0]->post_video, $output);
					$output = str_ireplace("[P:File]", $post->data[0]->post_file, $output);
					$output = str_ireplace("[P:Author]", $post->data[0]->post_author, $output);
					$output = str_ireplace("[P:Description]", $post->data[0]->post_description, $output);

					// process timestamp
					$post_timestamp = $post->data[0]->post_timestamp;
					$output = str_ireplace("[P:Timestamp]", date('U', $post_timestamp), $output);

					$post_timestamp_format = '';
					preg_match_all("@\[P\:Timestamp\|Format=(.*?)\]@i", $output, $matches);
					if(empty($matches[1]) == FALSE) {
						foreach($matches[1] as $post_timestamp_format) {
							$output = str_ireplace("[P:Timestamp|Format=" . $post_timestamp_format . "]", date($post_timestamp_format, $post_timestamp), $output);
						}
					}
					unset($matches);

					$output = str_ireplace("[P:Views]", $post->data[0]->post_views, $output);
					$output = str_ireplace("[P:Tags]", $post->data[0]->post_tags, $output);
					$output = str_ireplace("[P:Tags|Format=List]", $post->data[0]->post_tags_list, $output);
					$output = str_ireplace("[P:Keywords]", $post->data[0]->post_keywords, $output);
					$output = str_ireplace("[P:Keywords|Format=List]", $post->data[0]->post_keywords_list, $output);
					$output = str_ireplace("[P:Categories]", $post->data[0]->post_categories, $output);
					$output = str_ireplace("[P:Categories|Format=List]", $post->data[0]->post_categories_list, $output);

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