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

	Class pages extends ODBO {

		public function __construct() {

			parent::__construct();

			$this->table = 'pages';
			$this->table_definition = array(
				'page_id' =>				array('primary_key' => TRUE),
				'page_title' =>				array('data_type' => 'varchar(512)',		'required' => TRUE,			'slug_key' => TRUE),
				'page_path' =>				array('data_type' => 'varchar(512)',		'required' => TRUE),
				'page_hash' =>				array('data_type' => 'varchar(256)',		'required' => TRUE),
				'page_text' =>				array('data_type' => 'text',				'required' => FALSE),
				'page_author' =>			array('data_type' => 'integer',				'required' => FALSE),
				'page_publish_date' =>		array('data_type' => 'varchar(30)',			'required' => FALSE),
				'page_tags' =>				array('data_type' => 'varchar(512)',		'required' => FALSE),
				'page_keywords' =>			array('data_type' => 'varchar(512)',		'required' => FALSE),
				'page_description' =>		array('data_type' => 'text',				'required' => FALSE),
				'page_categories' =>		array('data_type' => 'varchar(512)',		'required' => FALSE),
				'page_layout' =>			array('data_type' => 'varchar(256)',		'required' => FALSE),
				'page_theme' =>				array('data_type' => 'varchar(256)',		'required' => FALSE),
				'page_visibility' =>		array('data_type' => 'varchar(30)',			'required' => FALSE),
				'page_ssl' =>				array('data_type' => 'boolean',				'required' => FALSE),
				'page_views' =>				array('data_type' => 'integer',				'required' => FALSE),
				'page_status' =>			array('data_type' => 'varchar(30)',			'required' => FALSE),
				'slug' =>					array('data_type' => 'varchar(256)',		'required' => FALSE,		'slug_value' => TRUE)

			);

			$this->permissions = array(
				'object' => 'any',
				'get' => 1,
				'add' => 3,
				'update' => 3,
				'delete' => 2,
				'out' => 'any',
				'getParsed' => 1,
                'pageExists' => 1,
                'getTags' => 1,
                'getCategories' => 1,
                'suggestSlug' => 1
			);

		}

		public function add($params=array()) {

			/*
			$this->setContentType('text/html');
			$this->html = '';
			new dBug($params);
			*/

            // create page data from params submitted using the interface
            if(empty($params['page_title']) == TRUE) {
            	$this->throwError('Title, is required.', 200);
            	$this->throwError('page_title', 200, 'fields');
            }

            if(empty($params['page_path']) == TRUE) {
            	$this->throwError('Page path, is required.', 200);
            	$this->throwError('page_path', 200, 'fields');
            } else {
                $params['page_hash'] = md5($params['page_path']);
            }

            $params['page_author'] = $_SESSION['ouser']->ouser_id;

            if(empty($params['page_publish_date']) == TRUE) {
                $params['page_publish_date'] = date('m/d/Y');
            }

            if(empty($params['page_visibility']) == TRUE) {
                $params['page_visibility'] = 'everyone';
            }

            if(empty($params['page_ssl']) == TRUE) {
				$params['page_ssl'] = FALSE;
            }

            if(empty($params['page_status']) == TRUE) {
                $params['page_status'] = 'draft';
            }

            // create page data from commands embed in page text


            // if there are no errors proceed with add/update
            if(isset($this->errors) == FALSE) {

                if(empty($params["page_id"]) == TRUE) {
                    // add new page
                    $params['page_views'] = 0;
                    parent::add($params);
                    // success message
                    $this->success = 'Page, successfully created.';
                } else {
                    // update existing page
                    parent::update($params);
                    // success message
                    $this->success = 'Page, successfully updated.';
                }

            }

		}

		public function get($params=array()) {

			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {
				for($item = 0; $item < count($this->data); $item++) {

					// add new line to the begining and end of page text
					$this->data[$item]->page_text = "\n" . $this->data[$item]->page_text . "\n";
					$text = $this->data[$item]->page_text;

					// process image at channel [P:Image|Channel=A]
					if(isset($params['channel']) == TRUE && $params['channel'] != '' && isset($params['item']) == TRUE && $params['item'] == 'Image') {
						$image_at_channel = '';

						preg_match_all("@\[image:((.*?)\.(jpg|png|gif))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $text, $matches);

						if(empty($matches[1]) == FALSE && empty($matches[5]) == FALSE) {
							$i = 0;
							foreach($matches[5] as $matched_channel) {
								if($matched_channel == $params['channel']) {
									$image_at_channel = $matches[1][$i];
								}
							$i++;
							}
						}

						$this->data[$item]->image_at_channel = $image_at_channel;
					}
					unset($matches);

					// process audio at channel [P:Audio|Channel=A]
					if(isset($params['channel']) == TRUE && $params['channel'] != '' && isset($params['item']) == TRUE && $params['item'] == 'Audio') {
						$audio_at_channel = '';

						preg_match_all("@\[Audio:((.*?)\.(mp3|ogg|wav))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $text, $matches);

						if(empty($matches[1]) == FALSE && empty($matches[5]) == FALSE) {
							$i = 0;
							foreach($matches[5] as $matched_channel) {
								if($matched_channel == $params['channel']) {
									$audio_at_channel = $matches[1][$i];
								}
							$i++;
							}
						}

						$this->data[$item]->audio_at_channel = $audio_at_channel;
					}
					unset($matches);

					// process video at channel [P:Video|Channel=A]
					if(isset($params['channel']) == TRUE && $params['channel'] != '' && isset($params['item']) == TRUE && $params['item'] == 'Video') {
						$video_at_channel = '';

						preg_match_all("@\[Video:((.*?)\.(mp4|mov|webm))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $text, $matches);

						if(empty($matches[1]) == FALSE && empty($matches[5]) == FALSE) {
							$i = 0;
							foreach($matches[5] as $matched_channel) {
								if($matched_channel == $params['channel']) {
									$video_at_channel = $matches[1][$i];
								}
							$i++;
							}
						}

						$this->data[$item]->video_at_channel = $video_at_channel;
					}
					unset($matches);

					// process file at channel [P:File|Channel=A]
					if(isset($params['channel']) == TRUE && $params['channel'] != '' && isset($params['item']) == TRUE && $params['item'] == 'File') {
						$file_at_channel = '';

						preg_match_all("@\[File:(.*?)\|((.*?)\|)?channel=(.*?)(\|(.*?))?\]@i", $text, $matches);

						if(empty($matches[1]) == FALSE && empty($matches[4]) == FALSE) {
							$i = 0;
							foreach($matches[4] as $matched_channel) {
								if($matched_channel == $params['channel']) {
									$file_at_channel = $matches[1][$i];
								}
							$i++;
							}
						}

						$this->data[$item]->file_at_channel = $file_at_channel;
					}
					unset($matches);

					// process the text for default image
					$image = '';
					if(preg_match("@\[Image:(.*?)\.(jpg|png)\|(.*?)\]@i", $text, $matches) == 0) {
						preg_match("@\[Image:(.*?)\.(jpg|png)\]@i", $text, $matches);
					}
					if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
						$image = $matches[1] . '.' . $matches[2];
					}
					unset($matches);
					$this->data[$item]->image = $image;

					// process the text for default audio
					$audio = '';
					if(preg_match("@\[Audio:(.*?)\.(mp3|ogg)\|(.*?)\]@i", $text, $matches) == 0) {
						preg_match("@\[Audio:(.*?)\.(mp3|ogg)\]@i", $text, $matches);
					}
					if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
						$audio = $matches[1] . '.' . $matches[2];
					}
					unset($matches);
					$this->data[$item]->audio = $audio;

					// process the text for default video
					$video = '';
					if(preg_match("@\[Video:(.*?)\.(mp4|mov)\|(.*?)\]@i", $text, $matches) == 0) {
						preg_match("@\[Video:(.*?)\.(mp4|mov)\]@i", $text, $matches);
					}
					if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
						$video = $matches[1] . '.' . $matches[2];
					}
					$matches = array();
					$this->data[$item]->video = $video;

					// process the text for default file
					$file = '';
					if(preg_match("@\[File:(.*?)\.(.*?)\|(.*?)\]@i", $text, $matches) == 0) {
						preg_match("@\[File:(.*?)\.(.*?)\]@i", $text, $matches);
					}
					if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
						$file = $matches[1] . '.' . $matches[2];
					}
					unset($matches);
					$this->data[$item]->file = $file;

					// process the page for author full name
					$author = '';

					$params = array(
						'ouser_id' => $this->data[$item]->page_author
					);

					$user = $this->route('/sys/users/get/', $params);
					if(isset($user->data) == TRUE && count($user->data) !=0) {
						$author = $user->data[0]->ouser_first_name . ' ' . $user->data[0]->ouser_last_name;
					}
					$this->data[$item]->page_author = $author;

					// process timestamp
					$timestamp =  strtotime($this->data[$item]->OCDT); // replace with OCDT
					$this->data[$item]->timestamp = $timestamp;

					// process tags
					$tags = '';
					$tags_array = explode(',', $this->data[$item]->page_tags);

					if(count($tags_array) != 0) {
						$tags = '<ul>';
						foreach($tags_array as $tag) {
							$tags .= '<li>' . trim($tag) . '</li>';
						}
						$tags .= '</ul>';
					}
					$this->data[$item]->tags_list = $tags;

					// process keywords
					$keywords = '';
					$keywords_array = explode(',', $this->data[$item]->page_keywords);

					if(count($keywords_array) != 0) {
						$keywords = '<ul>';
						foreach($keywords_array as $keyword) {
							$keywords .= '<li>' . trim($keyword) . '</li>';
						}
						$keywords .= '</ul>';
					}
					$this->data[$item]->keywords_list = $keywords;

					// process categories
					$categories = '';
					$categories_array = explode(',', $this->data[$item]->page_categories);

					if(count($categories_array) != 0) {
						$categories = '<ul>';
						foreach($categories_array as $category) {
							$categories .= '<li>' . trim($category) . '</li>';
						}
						$categories .= '</ul>';
					}
					$this->data[$item]->categories_list = $categories;
				}
			}

		}

		public function getParsed($params=array()) {

			// default protocol
			$protocol = 'http://';

			// update the protocol if page is being accessed with ssl_enabled
			if(empty($_SERVER['HTTPS']) == FALSE) {
				$protocol = 'https://';
			}

			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {

				$this->data[0]->page_text = "\n" . $this->data[0]->page_text . "\n";
				$text = $this->data[0]->page_text;

				// process [C:COMPONENT|Param1=VALUE|Param2=VALUE|...] command
				$text = $this->route('/sys/cms/')->processComponents($text, '', $protocol);

				// process wiki markup
				$text = $this->route('/sys/cms/')->parser($text, '', $protocol);

				// process snippet [S:Title|COMMAND] commands (loop trough snippets 7 times to catch all nested snippets
				for($loop=1;$loop<=7;$loop++) {
					$text = $this->route('/sys/cms/')->processSnippets($text, '', $protocol);
				}

				$this->data[0]->parsed = $text;
			}

		}

		// check if a page already exists
        public function pageExists($params = array()) {

            if(isset($params['path']) == TRUE) {
                $params['path_hash'] = md5($params['path']);

                // store the page into memory object
                $page = $this->route('/sys/pages/get/', $params);

                // if page exists at path
                if(isset($page->data) && count($page->data) != 0) {
                    // if page exists return page ID
                    return $page->data[0]->page_id;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }

        }

		// get all tags form all pages
		public function getTags($params = array()) {

			$tags = '';
			$tagsArray = array();

			// get all the pages and separate tags
			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {
				for($item = 0; $item < count($this->data); $item++) {
					$tags .= $this->data[$item]->page_tags . ',';
				}
			}

			// create unique array from tags and sort
			$tagsArray = explode(',', $tags);
			$tagsArray = array_unique($tagsArray);
			$tagsArray = array_filter($tagsArray);
			sort($tagsArray);

			// rebuid tags
			$tags = '';
			foreach($tagsArray as $tag) {
				$tags .= $tag . ',';
			}
			$tags = substr($tags, 0, -1);

			// prepare output
			$this->data = new stdClass();
			$this->data->tags = $tags;
			$this->data->tagsArray = $tagsArray;

		}


		// get all categories form all pages
		public function getCategories($params = array()) {

			$categories = '';
			$categoriesArray = array();

			// get all the pages and separate categories
			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {
				for($item = 0; $item < count($this->data); $item++) {
					$categories .= $this->data[$item]->page_categories . ',';
				}
			}

			// create unique array from categories and sort
			$categoriesArray = explode(',', $categories);
			$categoriesArray = array_unique($categoriesArray);
			$categoriesArray = array_filter($categoriesArray);
			sort($categoriesArray);

			// rebuid categories
			$categories = '';
			foreach($categoriesArray as $category) {
				$categories .= $category . ',';
			}
			$categories = substr($categories, 0, -1);

			// prepare output
			$this->data = new stdClass();
			$this->data->categories = $categories;
			$this->data->categoriesArray = $categoriesArray;

		}

		// suggest page slug based on title
		public function suggestSlug($params = array()) {

			$i = 0;
			$slug = '';
			$found = FALSE;

			$this->data = new stdClass();

			if(isset($params["page_title"]) == TRUE) {

				$params["text"] = $params["page_title"];
				$slug = $this->route('/sys/utilities/slugify/', $params)->data->slug;

				while($found == FALSE) {
					$i++;
					$pages = $this->route('/sys/pages/get/?slug=' . $slug)->data;
					if(count($pages) == 0) {
						$found = TRUE;
					} else {
						$slug = $slug . '-' . $i;
					}
				}
			}

			$this->data->slug = $slug;

		}

	}
?>