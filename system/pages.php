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
				'id' =>					array('primary_key' => TRUE),
				'title' =>				array('data_type' => 'varchar(512)',		'required' => TRUE,			'slug' => TRUE),
				'path' =>				array('data_type' => 'varchar(512)',		'required' => TRUE),
				'path_hash' =>			array('data_type' => 'varchar(256)',		'required' => TRUE),
				'text' =>				array('data_type' => 'text',				'required' => FALSE),
				'author' =>				array('data_type' => 'integer',				'required' => FALSE),
				'publish_date' =>		array('data_type' => 'varchar(30)',			'required' => FALSE),
				'tags' =>				array('data_type' => 'varchar(512)',		'required' => FALSE),
				'keywords' =>			array('data_type' => 'varchar(512)',		'required' => FALSE),
				'description' =>		array('data_type' => 'text',				'required' => FALSE),
				'categories' =>			array('data_type' => 'varchar(512)',		'required' => FALSE),
				'layout' =>				array('data_type' => 'varchar(256)',		'required' => FALSE),
				'theme' =>				array('data_type' => 'varchar(256)',		'required' => FALSE),
				'visibility' =>			array('data_type' => 'varchar(30)',			'required' => FALSE),
				'ssl_enabled' =>		array('data_type' => 'boolean',				'required' => FALSE),
				'views' =>				array('data_type' => 'integer',				'required' => FALSE),
				'status' =>				array('data_type' => 'varchar(30)',			'required' => FALSE),
				'OCDT' =>				array('data_type' => 'datetime',			'required' => FALSE)

			);

			$this->permissions = array(
				'object' => 'any',
				'get' => 1,
				'add' => 3,
				'update' => 3,
				'delete' => 2,
				'out' => 'any',
				'abcd' => 'any',
                'page_exists' => 'any'
			);

		}

		public function add($params=array()) {

            // create page data from params submitted using the interface
            if(isset($params['contentTitle']) == TRUE && $params['contentTitle'] != '') {
                $data['title'] = $params['contentTitle'];
            } else { $this->throwError('Title, is required.', 200); }


            if($params['contentMode'] == 'new') {
                $params['contentPath'] = 'tmp-' . rand(100000,10000000);
            }

            if(isset($params['contentPath']) == TRUE && $params['contentPath'] != '') {
                $data['path'] = $params['contentPath'];
                $data['path_hash'] = md5($params['contentPath']);
            } else { $this->throwError('Page path, is required.', 200); }

            if(isset($params['contentText']) == TRUE) {
                $data['text'] = $params['contentText'];
            }

            $data['author'] = $_SESSION['ouser']->ouser_id;

            if(isset($params['contentPublish']) == TRUE && $params['contentPublish'] != '') {
                $data['publish_date'] = $params['contentPublish'];
            } else {
                $data['publish_date'] = date('m/d/Y');
            }

            if(isset($params['contentTags']) == TRUE && $params['contentTags'] != '') {
                $data['tags'] = $params['contentTags'];
            }

            if(isset($params['contentDescription']) == TRUE && $params['contentDescription'] != '') {
                $data['description'] = $params['contentDescription'];
            }

            if(isset($params['contentCategories']) == TRUE && $params['contentCategories'] != '') {
                $data['categories'] = $params['contentCategories'];
            }

            if(isset($params['contentLayout']) == TRUE && $params['contentLayout'] != '') {
                $data['layout'] = $params['contentLayout'];
            }

            if(isset($params['contentTheme']) == TRUE && $params['contentTheme'] != '') {
                $data['theme'] = $params['contentTheme'];
            }

            if(isset($params['contentVisibility']) == TRUE && $params['contentVisibility'] != '') {
                $data['visibility'] = $params['contentVisibility'];
            } else {
                $data['visibility'] = 'everyone';
            }

            if(isset($params['contentSSL']) == TRUE && $params['contentSSL'] != FALSE) {
                $data['ssl_enabled'] = $params['contentSSL'];
            } else {
                $data['ssl_enabled'] = FALSE;
            }

            if(isset($params['contentStatus']) == TRUE && $params['contentStatus'] != '') {
                $data['status'] = $params['contentStatus'];
            } else {
                $data['status'] = 'draft';
            }

            // create page data from commands embed in page text



            // if there are no errors proceed with add/update
            if($this->isError() == FALSE) {
                $page_exists = $this->page_exists( array('path' => $params['contentPath']) );
                if($page_exists == FALSE) {
                    // add new page
                    $data['views'] = 0;
                    parent::add($data);
                    // success message
                    $this->success = 'Page, successfully created.';
                } else {
                    // update existing page
                    $data['id'] = $page_exists;
                    parent::update($data);
                    // success message
                    $this->success = 'Page, successfully updated.';
                }

            }

		}

		public function get($params=array()) {

			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {

				// add new line to the begining and end of page text
				$this->data[0]->text = "\n" . $this->data[0]->text . "\n";
				$text = $this->data[0]->text;

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

					$this->data[0]->image_at_channel = $image_at_channel;
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

					$this->data[0]->audio_at_channel = $audio_at_channel;
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

					$this->data[0]->video_at_channel = $video_at_channel;
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

					$this->data[0]->file_at_channel = $file_at_channel;
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
				$this->data[0]->image = $image;

				// process the text for default audio
				$audio = '';
				if(preg_match("@\[Audio:(.*?)\.(mp3|ogg)\|(.*?)\]@i", $text, $matches) == 0) {
					preg_match("@\[Audio:(.*?)\.(mp3|ogg)\]@i", $text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$audio = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->audio = $audio;

				// process the text for default video
				$video = '';
				if(preg_match("@\[Video:(.*?)\.(mp4|mov)\|(.*?)\]@i", $text, $matches) == 0) {
					preg_match("@\[Video:(.*?)\.(mp4|mov)\]@i", $text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$video = $matches[1] . '.' . $matches[2];
				}
				$matches = array();
				$this->data[0]->video = $video;

				// process the text for default file
				$file = '';
				if(preg_match("@\[File:(.*?)\.(.*?)\|(.*?)\]@i", $text, $matches) == 0) {
					preg_match("@\[File:(.*?)\.(.*?)\]@i", $text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$file = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->file = $file;

				// process the page for author full name
				$author = '';

				$params = array(
					'ouser_id' => $this->data[0]->author
				);

				$user = $this->route('/sys/users/get/', $params);
				if(isset($user->data) == TRUE && count($user->data) !=0) {
					$author = $user->data[0]->ouser_first_name . ' ' . $user->data[0]->ouser_last_name;
				}
				$this->data[0]->author = $author;

				// process timestamp
				$timestamp =  strtotime($this->data[0]->OCDT); // replace with OCDT
				$this->data[0]->timestamp = $timestamp;

				// process tags
				$tags = '';
				$tags_array = explode(',', $this->data[0]->tags);

				if(count($tags_array) != 0) {
					$tags = '<ul>';
					foreach($tags_array as $tag) {
						$tags .= '<li>' . trim($tag) . '</li>';
					}
					$tags .= '</ul>';
				}
				$this->data[0]->tags_list = $tags;

				// process keywords
				$keywords = '';
				$keywords_array = explode(',', $this->data[0]->keywords);

				if(count($keywords_array) != 0) {
					$keywords = '<ul>';
					foreach($keywords_array as $keyword) {
						$keywords .= '<li>' . trim($keyword) . '</li>';
					}
					$keywords .= '</ul>';
				}
				$this->data[0]->keywords_list = $keywords;

				// process categories
				$categories = '';
				$categories_array = explode(',', $this->data[0]->categories);

				if(count($categories_array) != 0) {
					$categories = '<ul>';
					foreach($categories_array as $category) {
						$categories .= '<li>' . trim($category) . '</li>';
					}
					$categories .= '</ul>';
				}
				$this->data[0]->categories_list = $categories;
			}

		}

        public function page_exists($params) {

            if(isset($params['path']) == TRUE) {
                $params['path_hash'] = md5($params['path']);

                // store the page into memory object
                $page = $this->route('/sys/pages/get/', $params);

                // if page exists at path
                if(isset($page->data) && count($page->data) != 0) {
                    // if page exists return page ID
                    return $page->data[0]->id;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }

        }

	}
?>