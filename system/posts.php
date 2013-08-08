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

		posts:

	********************************************************************************************************************/

	Class posts extends ODBO {

		public function __construct() {

			parent::__construct();

			$this->table = 'posts';
			$this->table_definition = array(
				'post_id' =>				array('primary_key' => TRUE),
				'post_title' =>				array('data_type' => 'varchar(255)',		'required' => TRUE,			'slug' => TRUE),
				'post_path' =>				array('data_type' => 'varchar(512)',		'required' => TRUE),
				'post_path_hash' =>			array('data_type' => 'varchar(512)',		'required' => TRUE),
				'post_text' =>				array('data_type' => 'text',				'required' => FALSE),
				'post_author' =>			array('data_type' => 'integer',				'required' => FALSE),
				'post_categories' =>		array('data_type' => 'varchar(255)',		'required' => FALSE),
				'post_keywords' =>			array('data_type' => 'varchar(255)',		'required' => FALSE),
				'post_description' =>		array('data_type' => 'text',				'required' => FALSE),
				'post_tags' =>				array('data_type' => 'varchar(255)',		'required' => FALSE),
				'post_layout' =>			array('data_type' => 'varchar(255)',		'required' => FALSE),
				'post_theme' =>				array('data_type' => 'varchar(255)',		'required' => FALSE),
				'post_views' =>				array('data_type' => 'integer',				'required' => FALSE),
				'post_status' =>			array('data_type' => 'varchar(255)',		'required' => FALSE),
			);


			$this->permissions = array(
				'object' => 'any',
				'get' => 'any',
				'add' => 'any',
				'update' => 'any',
				'delete' => 'any',
				'out' => 'any',
				'getImageAtChannel' => 'any'
			);

		}

		public function getImageAtChannel($params=array()) {
			parent::get($params);
		}

		public function get($params=array()) {

			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {
				$post_text = $this->data[0]->post_text;

				// process image at channel [P:Image|Channel=A]
				if(isset($params['channel']) == TRUE && $params['channel'] != '' && isset($params['item']) == TRUE && $params['item'] == 'Image') {
					$image_at_channel = '';

					preg_match_all("@\[image:((.*?)\.(jpg|png))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $post_text, $matches);

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

					preg_match_all("@\[Audio:((.*?)\.(mp3|ogg))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $post_text, $matches);

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

					preg_match_all("@\[Video:((.*?)\.(mp4|mov))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $post_text, $matches);

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

					preg_match_all("@\[File:((.*?)\.(.*))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $post_text, $matches);

					if(empty($matches[1]) == FALSE && empty($matches[5]) == FALSE) {
						$i = 0;
						foreach($matches[5] as $matched_channel) {
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
				$post_image = '';
				if(preg_match("@\[Image:(.*?)\.(jpg|png)\|(.*?)\]@i", $post_text, $matches) == 0) {
					preg_match("@\[Image:(.*?)\.(jpg|png)\]@i", $post_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$post_image = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->post_image = $post_image;

				// process the text for default audio
				$post_audio = '';
				if(preg_match("@\[Audio:(.*?)\.(mp3|ogg)\|(.*?)\]@i", $post_text, $matches) == 0) {
					preg_match("@\[Audio:(.*?)\.(mp3|ogg)\]@i", $post_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$post_audio = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->post_audio = $post_audio;

				// process the text for default video
				$post_video = '';
				if(preg_match("@\[Video:(.*?)\.(mp4|mov)\|(.*?)\]@i", $post_text, $matches) == 0) {
					preg_match("@\[Video:(.*?)\.(mp4|mov)\]@i", $post_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$post_video = $matches[1] . '.' . $matches[2];
				}
				$matches = array();
				$this->data[0]->post_video = $post_video;

				// process the text for default file
				$post_file = '';
				if(preg_match("@\[File:(.*?)\.(.*?)\|(.*?)\]@i", $post_text, $matches) == 0) {
					preg_match("@\[File:(.*?)\.(.*?)\]@i", $post_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$post_file = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->post_file = $post_file;

				// process the post for author full name
				$post_author = '';

				$params = array(
					'ouser_id' => $this->data[0]->post_author
				);

				$user = $this->route('/sys/users/get/', $params);
				if(isset($user->data) == TRUE && count($user->data) !=0) {
					$post_author = $user->data[0]->ouser_first_name . ' ' . $user->data[0]->ouser_last_name;
				}
				$this->data[0]->post_author = $post_author;

				// process timestamp
				$timestamp = time(); // replace with OCDT
				$this->data[0]->post_timestamp = $timestamp;

				// process tags
				$post_tags = '';
				$post_tags_array = explode(',', $this->data[0]->post_tags);

				if(count($post_tags_array) != 0) {
					$post_tags = '<ul>';
					foreach($post_tags_array as $post_tag) {
						$post_tags .= '<li>' . trim($post_tag) . '</li>';
					}
					$post_tags .= '</ul>';
				}
				$this->data[0]->post_tags_list = $post_tags;

				// process keywords
				$post_keywords = '';
				$post_keywords_array = explode(',', $this->data[0]->post_keywords);

				if(count($post_keywords_array) != 0) {
					$post_keywords = '<ul>';
					foreach($post_keywords_array as $post_keyword) {
						$post_keywords .= '<li>' . trim($post_keyword) . '</li>';
					}
					$post_keywords .= '</ul>';
				}
				$this->data[0]->post_keywords_list = $post_keywords;

				// process categories
				$post_categories = '';
				$post_categories_array = explode(',', $this->data[0]->post_categories);

				if(count($post_categories_array) != 0) {
					$post_categories = '<ul>';
					foreach($post_categories_array as $post_category) {
						$post_categories .= '<li>' . trim($post_category) . '</li>';
					}
					$post_categories .= '</ul>';
				}
				$this->data[0]->post_categories_list = $post_categories;

			}

		}

	}
?>