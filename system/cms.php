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

		// parse lists ( handler )
		function parse_lists_handler($matches,$close=false) {
			$this->listtypes = array("*" => "ul", "#" => "ol");
			$output='';

			if($close == true) {
				$newlevel = 0;
			} else {
				$newlevel = strlen($matches[1]);
			}
			while ($this->list_level != $newlevel) {
				$listchar = substr($matches[1], -1);
				if(empty($listchar) == FALSE) {
					$listtype = $this->listtypes[$listchar];
				}
				if ($this->list_level > $newlevel) {
					$listtype = '/' . array_pop($this->list_level_types);
					$this->list_level--;
				} else {
					$this->list_level++;
					array_push($this->list_level_types, $listtype);
				}
				$output .= "<{$listtype}>\n";
			}
			if ($close) return $output;
			$output .= "<li>" . $matches[2] . "</li>";
			return $output;
		}

		// process lists ( line )
		function parse_lists_line($line) {
			$line_regexes=array("list" => "^([\*\*]+)(.*?)$");
			$this->stop = FALSE;
			$this->stop_all = FALSE;
			$called["list"] = FALSE;
			foreach($line_regexes as $func => $regex) {
				if(preg_match("/$regex/i", $line, $matches)) {
					$called[$func] = TRUE;
					$line = $this->parse_lists_handler($matches);
					if ($this->stop || $this->stop_all) break;
				}
			}
			if (($this->list_level > 0) && !$called["list"]) $line = $this->parse_lists_handler(FALSE, TRUE) . $line;
			return $line;
		}


		// process lists ( main )
		public function parse_lists($text) {
			$output="";
			$this->list_level_types = array();
			$this->list_level = 0;
			$lines = explode("\n", $text);
			foreach ($lines as $line) {
				$line = $this->parse_lists_line($line);
				$output .= "\n" . $line;
			}
	    return $output;
		}

		public function parser($text, $protocol, $paragraphs=TRUE) {

			// process <nowiki></nowiki> tags
			preg_match_all("@<nowiki>(.*?)<\/nowiki>@s", $text, $matches);
			if(empty($matches[1]) == FALSE) {
				foreach($matches[1] as $nowiki) {
					$text = str_ireplace("<nowiki>" . $nowiki . "</nowiki>", "[Base64:" . base64_encode($nowiki) . "]", $text);
				}
			}
			unset($matches);

			// paragraphs
			if($paragraphs == TRUE) {
				// platform-independent newlines
			    $text=preg_replace("@(\r\n|\r)@","\n",$text);

		   		// remove excess newlines
		    	$text=preg_replace("@\n\n+@","\n\n",$text);

		    	// make paragraphs, including one at the end
		    	//$text = preg_replace("@\n?(.+?)(?:\n\s*\n|\z)@s", "<p>$1</p>\n\n", $text);
				$text = preg_replace("@\n([A-Za-z0-9](.*?))\n@s", "\n<p>$1</p>", $text);

		    	// remove paragraphs if they contain only whitespace
			    $text = preg_replace("@<p>\s*?<\/p>@","",$text);
			}

			// horizontal line
			$text = preg_replace("@---\n@", "<hr />\n", $text);

			// bold
			$text = preg_replace("@'''(.*?)'''@", "<b>$1</b>", $text);

			// italic
			$text = preg_replace("@''(.*?)''@", "<i>$1</i>", $text);

			// headings H1 - H6
			$text = preg_replace("@======(.*?)======\n@", "<h6>$1</h6>\n", $text);
			$text = preg_replace("@=====(.*?)=====\n@", "<h5>$1</h5>\n", $text);
			$text = preg_replace("@====(.*?)====\n@", "<h4>$1</h4>\n", $text);
			$text = preg_replace("@===(.*?)===\n@", "<h3>$1</h3>\n", $text);
			$text = preg_replace("@==(.*?)==\n@", "<h2>$1</h2>\n", $text);
			$text = preg_replace("@=(.*?)=\n@", "<h1>$1</h1>\n", $text);

			// internal links ( renamed )
			preg_match_all("@\[\[(.*?)\|(.*?)\]\]@", $text, $matches);

			if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
				$i = 0;
				foreach($matches[1] as $page_title) {

					$page_renamed_title = $matches[2][$i];
					$link_protocol = 'http://';

					$params = array(
						'page_title' => $page_title
					);

					$page = $this->route('/sys/pages/get/', $params);

					// if page exists at path
					if(isset($page->data) && count($page->data) != 0) {
						if(isset($page->data[0]->page_ssl) == TRUE && $page->data[0]->page_ssl == TRUE) {
							$link_protocol = 'https://';
						}
					}

					$slug = removeSpecialChars(str_replace("-",'',$page_title),'-','and');
					$text =	str_ireplace("[[" . $page_title . "|" . $page_renamed_title . "]]", "<a href=\"" . $link_protocol . __SITE__ . "/" . $slug . "/\">" . $page_renamed_title . "</a>", $text);
					$i++;
				}
			}
			unset($matches);

			// internal links
			preg_match_all("@\[\[(.*?)\]\]@", $text, $matches);

			if(empty($matches[1]) == FALSE) {
				foreach($matches[1] as $page_title) {
					$link_protocol = 'http://';

					$params = array(
						'page_title' => $page_title
					);

					$page = $this->route('/sys/pages/get/', $params);

					// if page exists at path
					if(isset($page->data) && count($page->data) != 0) {
						if(isset($page->data[0]->page_ssl) == TRUE && $page->data[0]->page_ssl == TRUE) {
							$link_protocol = 'https://';
						}
					}

					$slug = removeSpecialChars(str_replace("-",'',$page_title),'-','and');
					$text =	str_ireplace("[[" . $page_title . "]]", "<a href=\"" . $link_protocol . __SITE__ . "/" . $slug . "/\">" . $page_title . "</a>", $text);
				}
			}
			unset($matches);

			// external links ( renamed )
			$text = preg_replace("@\[(https?://)(.*?)\|(.*?)\]\n@i", "<a target=\"_blank\" href=\"$1$2\">$3</a>", $text);
			// external links
			$text = preg_replace("@\[(https?://)(.*?)\]\n@i", "<a target=\"_blank\" href=\"$1$2\">$1$2</a>", $text);

			// images
			preg_match_all("@\[Image:((.*?)\.(jpg|png|gif))\|(.*?)\]@i", $text, $matches);

			if(isset($matches[1]) == TRUE && isset($matches[4]) == TRUE) {
				$i = 0;
				foreach($matches[1] as $image) {
					$image_params = explode("|", $matches[4][$i]);

					$image_width =  '';
					$image_height = '';
					$image_alt = '';
					$image_hidden = '';
					$image_alignment = '';
					$image_description = '';

					foreach($image_params as $image_param) {
						// width
						preg_match("@^Width=(.*?)$@i", $image_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $image_width = $matched_param[1]; }
						unset($matched_param);
						// height
						preg_match("@^Height=(.*?)$@i", $image_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $image_height = $matched_param[1]; }
						unset($matched_param);
						// alt
						preg_match("@^Alt=(.*?)$@i", $image_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $image_alt = $matched_param[1]; }
						unset($matched_param);
						// hidden
						preg_match("@^Hidden=(.*?)$@i", $image_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $image_hidden = strtoupper($matched_param[1]); }
						unset($matched_param);
						// alignment
						preg_match("@^Alignment=(.*?)$@i", $image_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $image_alignment = $matched_param[1]; }
						unset($matched_param);
						// description
						preg_match("@^Description=(.*?)$@i", $image_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $image_description = $matched_param[1]; }
						unset($matched_param);
					}

					$image_html = '';
					$image_html .= "\n" . '<div class="cms-image" ';
					if($image_hidden == 'YES') { $image_html .= 'style="display: none;"'; } else { if($image_alignment != '') { $image_html .= 'style="float: ' . $image_alignment . ';"'; } }
					$image_html .= '>';
					$image_html .= '<img src="' . $protocol . __SITE__ . '/files/' . $image . '" ';
					if($image_alt != '') { $image_html .= 'alt="' . $image_alt . '" '; }
					if($image_width != '') { $image_html .= 'width="' . $image_width . '" '; }
					if($image_height != '') { $image_html .= 'height="' . $image_height . '" '; }
					$image_html .= '/>';
					if($image_description != '') { $image_html .= '<p>' . $image_description . '</p>'; }
					$image_html .= '</div>' . "\n";

					$text = str_ireplace($matches[0][$i], $image_html, $text);
					$i++;
				}
			}
			unset($matches);

			$text = preg_replace("@\[Image:((.*?)\.(jpg|png|gif))\]@i", "\n<div class=\"cms-image\"><img src=\"" . $protocol . __SITE__ . "/files/$1\" /></div>\n", $text);

			// audios
			preg_match_all("@\[Audio:((.*?)\.(mp3|ogg|wav))\|(.*?)\]@i", $text, $matches);

			if(isset($matches[1]) == TRUE && isset($matches[4]) == TRUE) {
				$i = 0;
				foreach($matches[1] as $audio) {
					$audio_params = explode("|", $matches[4][$i]);

					$audio_width =  '';
					$audio_height = '';
					$audio_autoplay = '';
					$audio_controls = '';
					$audio_loop = '';
					$audio_hidden = '';
					$audio_alignment = '';
					$audio_description = '';

					foreach($audio_params as $audio_param) {
						// width
						preg_match("@^Width=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_width = $matched_param[1]; }
						unset($matched_param);
						// height
						preg_match("@^Height=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_height = $matched_param[1]; }
						unset($matched_param);
						// autoplay
						preg_match("@^Autoplay=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_autoplay = $matched_param[1]; }
						unset($matched_param);
						// controls
						preg_match("@^controls=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_controls = $matched_param[1]; }
						unset($matched_param);
						// loop
						preg_match("@^Loop=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_loop = $matched_param[1]; }
						unset($matched_param);
						// hidden
						preg_match("@^Hidden=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_hidden = strtoupper($matched_param[1]); }
						unset($matched_param);
						// alignment
						preg_match("@^Alignment=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_alignment = $matched_param[1]; }
						unset($matched_param);
						// description
						preg_match("@^Description=(.*?)$@i", $audio_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $audio_description = $matched_param[1]; }
						unset($matched_param);
					}

					$audio_html = '';
					$audio_html .= "\n" . '<div class="cms-audio" ';
					if($audio_hidden == 'YES') { $audio_html .= 'style="display: none;"'; } else { if($audio_alignment != '') { $audio_html .= 'style="float: ' . $audio_alignment . ';"'; } }
					$audio_html .= '>';
					$audio_html .= '<audio src="' . $protocol . __SITE__ . '/files/' . $audio . '" ';
					if($audio_width != '') { $audio_html .= 'width="' . $audio_width . '" '; }
					if($audio_height != '') { $audio_html .= 'height="' . $audio_height . '" '; }
					if($audio_autoplay != '') { $audio_html .= 'autoplay="' . $audio_autoplay . '" '; }
					if($audio_controls != '') { $audio_html .= 'controls="' . $audio_controls . '" '; }
					if($audio_loop != '') { $audio_html .= 'loop="' . $audio_loop . '" '; }
					$audio_html .= '/>';
					if($audio_description != '') { $audio_html .= '<p>' . $audio_description . '</p>'; }
					$audio_html .= '</div>' . "\n";

					$text = str_ireplace($matches[0][$i], $audio_html, $text);
					$i++;
				}
			}
			unset($matches);

			$text = preg_replace("@\[Audio:((.*?)\.(mp3|ogg|wav))\]@i", "\n<div class=\"cms-audio\"><audio src=\"" . $protocol . __SITE__ . "/files/$1\" /></div>\n", $text);

			// videos
			preg_match_all("@\[Video:((.*?)\.(mp4|mov|webm))\|(.*?)\]@i", $text, $matches);

			if(isset($matches[1]) == TRUE && isset($matches[4]) == TRUE) {
				$i = 0;
				foreach($matches[1] as $video) {
					$video_params = explode("|", $matches[4][$i]);

					$video_width =  '';
					$video_height = '';
					$video_autoplay = '';
					$video_controls = '';
					$video_loop = '';
					$video_hidden = '';
					$video_alignment = '';
					$video_description = '';

					foreach($video_params as $video_param) {
						// width
						preg_match("@^Width=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_width = $matched_param[1]; }
						unset($matched_param);
						// height
						preg_match("@^Height=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_height = $matched_param[1]; }
						unset($matched_param);
						// autoplay
						preg_match("@^Autoplay=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_autoplay = $matched_param[1]; }
						unset($matched_param);
						// controls
						preg_match("@^controls=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_controls = $matched_param[1]; }
						unset($matched_param);
						// loop
						preg_match("@^Loop=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_loop = $matched_param[1]; }
						unset($matched_param);
						// hidden
						preg_match("@^Hidden=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_hidden = strtoupper($matched_param[1]); }
						unset($matched_param);
						// alignment
						preg_match("@^Alignment=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_alignment = $matched_param[1]; }
						unset($matched_param);
						// description
						preg_match("@^Description=(.*?)$@i", $video_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $video_description = $matched_param[1]; }
						unset($matched_param);
					}

					$video_html = '';
					$video_html .= "\n" . '<div class="cms-video" ';
					if($video_hidden == 'YES') { $video_html .= 'style="display: none;"'; } else { if($video_alignment != '') { $video_html .= 'style="float: ' . $video_alignment . ';"'; } }
					$video_html .= '>';
					$video_html .= '<video src="' . $protocol . __SITE__ . '/files/' . $video . '" ';
					if($video_width != '') { $video_html .= 'width="' . $video_width . '" '; }
					if($video_height != '') { $video_html .= 'height="' . $video_height . '" '; }
					if($video_autoplay != '') { $video_html .= 'autoplay="' . $video_autoplay . '" '; }
					if($video_controls != '') { $video_html .= 'controls="' . $video_controls . '" '; }
					if($video_loop != '') { $video_html .= 'loop="' . $video_loop . '" '; }
					$video_html .= '/>';
					if($video_description != '') { $video_html .= '<p>' . $video_description . '</p>'; }
					$video_html .= '</div>' . "\n";

					$text = str_ireplace($matches[0][$i], $video_html, $text);
					$i++;
				}
			}
			unset($matches);

			$text = preg_replace("@\[Video:((.*?)\.(mp4|mov|webm))\]@i", "\n<div class=\"cms-video\"><video src=\"" . $protocol . __SITE__ . "/files/$1\" /></div>\n", $text);

			// files
			preg_match_all("@\[File:((.*?)\.(.*?))\|(.*?)\]@i", $text, $matches);

			if(isset($matches[1]) == TRUE && isset($matches[4]) == TRUE) {
				$i = 0;
				foreach($matches[1] as $file) {
					$file_params = explode("|", $matches[4][$i]);

					$file_hidden = '';
					$file_alignment = '';
					$file_description = '';
					$file_download = '';

					foreach($file_params as $file_param) {
						// hidden
						preg_match("@^Hidden=(.*?)$@i", $file_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $file_hidden = strtoupper($matched_param[1]); }
						unset($matched_param);
						// alignment
						preg_match("@^Alignment=(.*?)$@i", $file_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $file_alignment = $matched_param[1]; }
						unset($matched_param);
						// description
						preg_match("@^Description=(.*?)$@i", $file_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $file_description = $matched_param[1]; }
						unset($matched_param);
						// HTML5 download
						preg_match("@^Download=(.*?)$@i", $file_param, $matched_param);
						if(isset($matched_param[1]) == TRUE) { $file_download = $matched_param[1]; }
						unset($matched_param);
					}

					$file_html = '';
					$file_html .= "\n" . '<div class="cms-file" ';
					if($file_hidden == 'YES') { $file_html .= 'style="display: none;"'; } else { if($file_alignment != '') { $file_html .= 'style="float: ' . $file_alignment . ';"'; } }
					$file_html .= '>';
					$file_html .= '<a href="' . $protocol . __SITE__ . '/files/' . $file . '" ';
					if($file_download != '') { $file_html .= 'download'; }
					$file_html .= '>' . $file . '</a>';
					if($file_description != '') { $file_html .= '<p>' . $file_description . '</p>'; }
					$file_html .= '</div>' . "\n";

					$text = str_ireplace($matches[0][$i], $file_html, $text);
					$i++;
				}
			}
			unset($matches);

			$text = preg_replace("@\[File:(.*?)\]@i", "\n<div class=\"cms-file\"><a href=\"" . $protocol . __SITE__ . "/files/$1\">$1</a></div>\n", $text);

			// page break after in print
			$text = preg_replace("@-8<-\n@","\n<div style=\"page-break-after: always;\"></div>\n",$text);


			$text = $this->parse_lists($text);

			return $text;
		}

		public function missing($path, $params=array(), $direct=TRUE) {

			// set the content type
			$this->setContentType('text/html');

			// break down the path
			$path_array = explode('/', $path);

			// route to administrator panel
			if(isset($path_array[1]) == TRUE && $path_array[1] == 'admin') {
				//new dBug($path_array);
				$oobject_admin = $this->route('/cmd/administrator/admin/');
				$oobject_admin->out($path, $params, $direct);
				$this->html = $oobject_admin->html;
			} else {
				// default object
				$page = new stdClass();
				$snippet = new stdClass();
				$item = new stdClass();

				// default output
				$output = '';

				// fix for Obray bug
				if($path == '//') { $path = '/'; }
				//print($path);

				// default protocol
				$protocol = 'http://';

				// update the protocol if page is being accessed with SSL
				if(empty($_SERVER['HTTPS']) == FALSE) {
					$protocol = 'https://';
				}

				// set the theme
				$theme = __THEME__;

				// create a MD5 hash from the current path
				$path_hash = md5($path);

				// check the database for existing page at the path
				$params = array(
					'page_path_hash' => $path_hash
				);

				// store the page into memory object
				$page = $this->route('/sys/pages/get/', $params);

				// if page exists at path
				if(isset($page->data) && count($page->data) != 0) {

					// redirect to HTTPS version if the page_ssl is TRUE
					if(isset($page->data[0]->page_ssl) == TRUE && $page->data[0]->page_ssl == TRUE) {
						if(empty($_SERVER['HTTPS']) == TRUE) {
							$redirect = 'https://' . __SITE__ . $path;
							header('Location: ' . $redirect);
							exit();
						}
					}

					// set the theme
					$theme = __THEME__;
					if($page->data[0]->page_theme != '') {
						$theme = $page->data[0]->page_theme;
					}

					// set the layout
					$layout = __LAYOUT__;
					if($page->data[0]->page_layout != '') {
						$layout = $page->data[0]->page_layout;
					}

					// if theme / layout exists
					if(file_exists(__SELF__ . 'themes/' . $theme . '/' . $layout) == TRUE) {
						// load theme / layout in memory
						$output = file_get_contents(__SELF__ . 'themes/' . $theme . '/' . $layout);
						// fix the path of all relative href attributes
						$output = preg_replace("@href=\"(?!(http://)|(https://))(.*?)\"@i", "href=\"" . $protocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
						// fix the path of all relative src attributes
						$output = preg_replace("@src=\"(?!(http://)|(https://))(.*?)\"@i", "src=\"" . $protocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
						// fix for themes built on skell.js
						$output = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@i", "_skel_config.prefix = \"" . $protocol . __SITE__ . "/themes/". $theme. "/$1\"", $output);

						// process page [P:COMMAND] commands
						$page_text = $page->data[0]->page_text;
						// process wiki markup
						$page_text = $this->parser($page_text, $protocol);

						$output = str_ireplace("[P:Text]", $page_text, $output);

						// process snippet [S:Title|COMMAND] commands
						$snippet = '';
						preg_match_all("@\[S:(.*?)(\|(.*?))?\]@i", $output, $matches);

						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $snippet_title) {

								$params = array(
									'page_title' => $snippet_title
								);

								$snippet = $this->route('/sys/pages/get/', $params);

								// if snippet exists at path
								if(isset($snippet->data) && count($snippet->data) != 0) {

									$snippet_text = $snippet->data[0]->page_text;

									// process wiki markup
									$snippet_text = $this->parser($snippet_text, $protocol);

									$output = str_ireplace("[S:" . $snippet_title . "]", $snippet_text, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Text]", $snippet_text, $output);

									$output = str_ireplace("[S:" . $snippet_title . "|Title]", $snippet->data[0]->page_title, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Slug]", $snippet->data[0]->slug, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Link]", $snippet->data[0]->page_path, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Image]", $snippet->data[0]->page_image, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Audio]", $snippet->data[0]->page_audio, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Video]", $snippet->data[0]->page_video, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|File]", $snippet->data[0]->page_file, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Author]", $snippet->data[0]->page_author, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Description]", $snippet->data[0]->page_description, $output);

									// process timestamp
									$snippet_timestamp = $snippet->data[0]->page_timestamp;
									$output = str_ireplace("[S:" . $snippet_title . "|Timestamp]", date('U', $snippet_timestamp), $output);

									$snippet_timestamp_format = '';
									preg_match_all("@\[S\:(.*?)\|Timestamp\|Format=(.*?)\]@i", $output, $matches);
									if(empty($matches[2]) == FALSE) {
										foreach($matches[2] as $snippet_timestamp_format) {
											$output = str_ireplace("[S:" . $snippet_title . "|Timestamp|Format=" . $snippet_timestamp_format . "]", date($snippet_timestamp_format, $snippet_timestamp), $output);
										}
									}
									unset($matches);

									$output = str_ireplace("[S:" . $snippet_title . "|Views]", $snippet->data[0]->page_views, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Tags]", $snippet->data[0]->page_tags, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Tags|Format=List]", $snippet->data[0]->page_tags_list, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Keywords]", $snippet->data[0]->page_keywords, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Keywords|Format=List]", $snippet->data[0]->page_keywords_list, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Categories]", $snippet->data[0]->page_categories, $output);
									$output = str_ireplace("[S:" . $snippet_title . "|Categories|Format=List]", $snippet->data[0]->page_categories_list, $output);
								}
							}
						}
						unset($matches);

						// process [S:Title|Image|Channel=A]
						$i = 0;
						$image_at_channel = '';
						preg_match_all("@\[S:(.*?)\|Image\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
							foreach($matches[2] as $channel) {
								$snippet_title = $matches[1][$i];
								$params = array(
									'page_title' => $snippet_title,
									'item'			 => 'Image',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$image_at_channel = $item->data[0]->image_at_channel;
									$output = str_ireplace("[S:" . $snippet_title . "|Image|Channel=" . $channel . "]", $image_at_channel, $output);
								}
							$i++;
							}
						}
						unset($matches);

						// process [S:Title|Audio|Channel=A]
						$i = 0;
						$audio_at_channel = '';
						preg_match_all("@\[S:(.*?)\|Audio\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
							foreach($matches[2] as $channel) {
								$snippet_title = $matches[1][$i];
								$params = array(
									'page_title' => $snippet_title,
									'item'			 => 'Audio',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$audio_at_channel = $item->data[0]->audio_at_channel;
									$output = str_ireplace("[S:" . $snippet_title . "|Audio|Channel=" . $channel . "]", $audio_at_channel, $output);
								}
							$i++;
							}
						}
						unset($matches);

						// process [S:Title|Video|Channel=A]
						$i = 0;
						$video_at_channel = '';
						preg_match_all("@\[S:(.*?)\|Video\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
							foreach($matches[2] as $channel) {
								$snippet_title = $matches[1][$i];
								$params = array(
									'page_title' => $snippet_title,
									'item'			 => 'Video',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$video_at_channel = $item->data[0]->video_at_channel;
									$output = str_ireplace("[S:" . $snippet_title . "|Video|Channel=" . $channel . "]", $video_at_channel, $output);
								}
							$i++;
							}
						}
						unset($matches);

						// process [S:Title|File|Channel=A]
						$i = 0;
						$file_at_channel = '';
						preg_match_all("@\[S:(.*?)\|File\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
							foreach($matches[2] as $channel) {
								$snippet_title = $matches[1][$i];
								$params = array(
									'page_title' => $snippet_title,
									'item'			 => 'File',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$file_at_channel = $item->data[0]->file_at_channel;
									$output = str_ireplace("[S:" . $snippet_title . "|File|Channel=" . $channel . "]", $file_at_channel, $output);
								}
							$i++;
							}
						}
						unset($matches);

						// process [P:Image|Channel=A]
						$image_at_channel = '';
						preg_match_all("@\[P:Image\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $channel) {

								$params = array(
									'page_path_hash' => $path_hash,
									'item'			 => 'Image',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$image_at_channel = $item->data[0]->image_at_channel;
									$output = str_ireplace("[P:Image|Channel=" . $channel . "]", $image_at_channel, $output);
								}
							}
						}
						unset($matches);

						// process [P:Audio|Channel=A]
						$audio_at_channel = '';
						preg_match_all("@\[P:Audio\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $channel) {

								$params = array(
									'page_path_hash' => $path_hash,
									'item'			 => 'Audio',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$audio_at_channel = $item->data[0]->audio_at_channel;
									$output = str_ireplace("[P:Audio|Channel=" . $channel . "]", $audio_at_channel, $output);
								}
							}
						}
						unset($matches);

						// process [P:Video|Channel=A]
						$video_at_channel = '';
						preg_match_all("@\[P:Video\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $channel) {

								$params = array(
									'page_path_hash' => $path_hash,
									'item'			 => 'Video',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$video_at_channel = $item->data[0]->video_at_channel;
									$output = str_ireplace("[P:Video|Channel=" . $channel . "]", $video_at_channel, $output);
								}
							}
						}
						unset($matches);

						// process [P:File|Channel=A]
						$file_at_channel = '';
						preg_match_all("@\[P:File\|Channel=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $channel) {

								$params = array(
									'page_path_hash' => $path_hash,
									'item'			 => 'File',
									'channel'		 => $channel
								);

								$item = $this->route('/sys/pages/get/', $params);

								// if item exists at path and channel
								if(isset($item->data) && count($item->data) != 0) {
									$file_at_channel = $item->data[0]->file_at_channel;
									$output = str_ireplace("[P:File|Channel=" . $channel . "]", $file_at_channel, $output);
								}
							}
						}
						unset($matches);


						$output = str_ireplace("[P:Title]", $page->data[0]->page_title, $output);
						$output = str_ireplace("[P:Slug]", $page->data[0]->slug, $output);
						$output = str_ireplace("[P:Link]", $page->data[0]->page_path, $output);
						$output = str_ireplace("[P:Image]", $page->data[0]->page_image, $output);



						$output = str_ireplace("[P:Audio]", $page->data[0]->page_audio, $output);
						$output = str_ireplace("[P:Video]", $page->data[0]->page_video, $output);
						$output = str_ireplace("[P:File]", $page->data[0]->page_file, $output);
						$output = str_ireplace("[P:Author]", $page->data[0]->page_author, $output);
						$output = str_ireplace("[P:Description]", $page->data[0]->page_description, $output);

						// process timestamp
						$page_timestamp = $page->data[0]->page_timestamp;
						$output = str_ireplace("[P:Timestamp]", date('U', $page_timestamp), $output);

						$page_timestamp_format = '';
						preg_match_all("@\[P\:Timestamp\|Format=(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $page_timestamp_format) {
								$output = str_ireplace("[P:Timestamp|Format=" . $page_timestamp_format . "]", date($page_timestamp_format, $page_timestamp), $output);
							}
						}
						unset($matches);

						$output = str_ireplace("[P:Views]", $page->data[0]->page_views, $output);
						$output = str_ireplace("[P:Tags]", $page->data[0]->page_tags, $output);
						$output = str_ireplace("[P:Tags|Format=List]", $page->data[0]->page_tags_list, $output);
						$output = str_ireplace("[P:Keywords]", $page->data[0]->page_keywords, $output);
						$output = str_ireplace("[P:Keywords|Format=List]", $page->data[0]->page_keywords_list, $output);
						$output = str_ireplace("[P:Categories]", $page->data[0]->page_categories, $output);
						$output = str_ireplace("[P:Categories|Format=List]", $page->data[0]->page_categories_list, $output);

						// process [C:COMPONENT|Param1=VALUE|Param2=VALUE|...] command

						// process [Base64:STRING], ( used for system level tasks, and <nowiki> )
						$base64_encoded_string = '';
						$base64_decoded_string = '';
						preg_match_all("@\[Base64:(.*?)\]@i", $output, $matches);
						if(empty($matches[1]) == FALSE) {
							foreach($matches[1] as $base64_encoded_string) {
								$base64_decoded_string = base64_decode($base64_encoded_string);
								$output = str_ireplace("[Base64:" . $base64_encoded_string . "]", $base64_decoded_string, $output);
							}
						}
						unset($matches);

					} else {
						$this->throwError('Selected theme and layout does not exist.',500,$type='notfound');
					}

				// if page does not exist show 404 message / html
				} else {
					// set 404 layout
					$layout = '404.html';

					// if theme / layout exists
					if(file_exists(__SELF__ . 'themes/' . __THEME__ . '/' . $layout) == TRUE) {
						// load theme / layout in memory
						$output = file_get_contents(__SELF__ . 'themes/' . $theme . '/' . $layout);
						// fix the path of all relative href attributes
						$output = preg_replace("@href=\"(?!(http://)|(https://))(.*?)\"@i", "href=\"" . $protocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
						// fix the path of all relative src attributes
						$output = preg_replace("@src=\"(?!(http://)|(https://)(.*?)\"@i", "src=\"" . $protocol . __SITE__ . "/themes/". $theme. "/$3\"", $output);
						// fix for themes built on skell.js
						$output = preg_replace("@_skel_config\.prefix ?= ?\"(.*?)\"@i", "_skel_config.prefix = \"" . $protocol . __SITE__ . "/themes/". $theme. "/$1\"", $output);
					} else {
						$this->throwError('Page not found.',404,$type='notfound');
						print("404 Not Found.");
					}
				}

			$this->html = $output;
			}
		}
	}
?>