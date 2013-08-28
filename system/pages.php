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
				'page_title' =>				array('data_type' => 'varchar(255)',		'required' => TRUE,			'slug' => TRUE),
				'page_path' =>				array('data_type' => 'varchar(512)',		'required' => TRUE),
				'page_path_hash' =>			array('data_type' => 'varchar(512)',		'required' => TRUE),
				'page_text' =>				array('data_type' => 'text',				'required' => FALSE),
				'page_author' =>			array('data_type' => 'integer',				'required' => FALSE),
				'page_tags' =>				array('data_type' => 'varchar(255)',		'required' => FALSE),
				'page_keywords' =>			array('data_type' => 'varchar(255)',		'required' => FALSE),
				'page_description' =>		array('data_type' => 'text',				'required' => FALSE),
				'page_categories' =>		array('data_type' => 'varchar(255)',		'required' => FALSE),
				'page_layout' =>			array('data_type' => 'varchar(255)',		'required' => FALSE),
				'page_theme' =>				array('data_type' => 'varchar(255)',		'required' => FALSE),
				'page_visibility' =>		array('data_type' => 'varchar(10)',			'required' => FALSE),
				'page_ssl' =>				array('data_type' => 'boolean',				'required' => FALSE),
				'page_views' =>				array('data_type' => 'integer',				'required' => FALSE),
				'page_status' =>			array('data_type' => 'varchar(255)',		'required' => FALSE),
				'OCDT' =>					array()
			);


			$this->permissions = array(
				'object' => 'any',
				'get' => 1,
				'add' => 1,
				'update' => 1,
				'delete' => 1,
				'out' => 'any'
			);

		}

		public function get($params=array()) {

			parent::get($params);

			if(isset($this->data) && count($this->data) != 0) {

				// add new line to the begining and end of page text
				$this->data[0]->page_text = "\n" . $this->data[0]->page_text . "\n";
				$page_text = $this->data[0]->page_text;

				// process image at channel [P:Image|Channel=A]
				if(isset($params['channel']) == TRUE && $params['channel'] != '' && isset($params['item']) == TRUE && $params['item'] == 'Image') {
					$image_at_channel = '';

					preg_match_all("@\[image:((.*?)\.(jpg|png|gif))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $page_text, $matches);

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

					preg_match_all("@\[Audio:((.*?)\.(mp3|ogg|wav))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $page_text, $matches);

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

					preg_match_all("@\[Video:((.*?)\.(mp4|mov|webm))(.*?)\|channel=(.*?)(\|(.*?))?\]@i", $page_text, $matches);

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

					preg_match_all("@\[File:(.*?)\|((.*?)\|)?channel=(.*?)(\|(.*?))?\]@i", $page_text, $matches);

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
				$page_image = '';
				if(preg_match("@\[Image:(.*?)\.(jpg|png)\|(.*?)\]@i", $page_text, $matches) == 0) {
					preg_match("@\[Image:(.*?)\.(jpg|png)\]@i", $page_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$page_image = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->page_image = $page_image;

				// process the text for default audio
				$page_audio = '';
				if(preg_match("@\[Audio:(.*?)\.(mp3|ogg)\|(.*?)\]@i", $page_text, $matches) == 0) {
					preg_match("@\[Audio:(.*?)\.(mp3|ogg)\]@i", $page_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$page_audio = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->page_audio = $page_audio;

				// process the text for default video
				$page_video = '';
				if(preg_match("@\[Video:(.*?)\.(mp4|mov)\|(.*?)\]@i", $page_text, $matches) == 0) {
					preg_match("@\[Video:(.*?)\.(mp4|mov)\]@i", $page_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$page_video = $matches[1] . '.' . $matches[2];
				}
				$matches = array();
				$this->data[0]->page_video = $page_video;

				// process the text for default file
				$page_file = '';
				if(preg_match("@\[File:(.*?)\.(.*?)\|(.*?)\]@i", $page_text, $matches) == 0) {
					preg_match("@\[File:(.*?)\.(.*?)\]@i", $page_text, $matches);
				}
				if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
					$page_file = $matches[1] . '.' . $matches[2];
				}
				unset($matches);
				$this->data[0]->page_file = $page_file;

				// process the page for author full name
				$page_author = '';

				$params = array(
					'ouser_id' => $this->data[0]->page_author
				);

				$user = $this->route('/sys/users/get/', $params);
				if(isset($user->data) == TRUE && count($user->data) !=0) {
					$page_author = $user->data[0]->ouser_first_name . ' ' . $user->data[0]->ouser_last_name;
				}
				$this->data[0]->page_author = $page_author;

				// process timestamp
				$timestamp =  strtotime($this->data[0]->OCDT); // replace with OCDT
				$this->data[0]->page_timestamp = $timestamp;

				// process tags
				$page_tags = '';
				$page_tags_array = explode(',', $this->data[0]->page_tags);

				if(count($page_tags_array) != 0) {
					$page_tags = '<ul>';
					foreach($page_tags_array as $page_tag) {
						$page_tags .= '<li>' . trim($page_tag) . '</li>';
					}
					$page_tags .= '</ul>';
				}
				$this->data[0]->page_tags_list = $page_tags;

				// process keywords
				$page_keywords = '';
				$page_keywords_array = explode(',', $this->data[0]->page_keywords);

				if(count($page_keywords_array) != 0) {
					$page_keywords = '<ul>';
					foreach($page_keywords_array as $page_keyword) {
						$page_keywords .= '<li>' . trim($page_keyword) . '</li>';
					}
					$page_keywords .= '</ul>';
				}
				$this->data[0]->page_keywords_list = $page_keywords;

				// process categories
				$page_categories = '';
				$page_categories_array = explode(',', $this->data[0]->page_categories);

				if(count($page_categories_array) != 0) {
					$page_categories = '<ul>';
					foreach($page_categories_array as $page_category) {
						$page_categories .= '<li>' . trim($page_category) . '</li>';
					}
					$page_categories .= '</ul>';
				}
				$this->data[0]->page_categories_list = $page_categories;

			}

		}

	}
?>