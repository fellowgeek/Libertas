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
				'post_status' =>			array('data_type' => 'varchar(255)',		'required' => FALSE)
			);

			$this->permissions = array(
				'object' => 'any',
				'get' => 'any',
				'add' => 'any',
				'update' => 'any',
				'delete' => 'any',
				'out' => 'any'
			);

		}

		public function get($params=array()) {

			parent::get($params);
			$post_text = $this->data[0]->post_text;

			// process the text for default image
			$post_image = '';
			if(preg_match("@\[image:(.*?)\.(jpg|png)\|(.*?)\]@i", $post_text, $matches) == 0) {
				preg_match("@\[image:(.*?)\.(jpg|png)\]@i", $post_text, $matches);
			}
			if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
				$post_image = $matches[1] . '.' . $matches[2];
			}
			$matches = array();
			$this->data[0]->post_image = $post_image;

			// process the text for default audio
			$post_audio = '';
			if(preg_match("@\[Audio:(.*?)\.(mp3|ogg)\|(.*?)\]@i", $post_text, $matches) == 0) {
				preg_match("@\[Audio:(.*?)\.(mp3|ogg)\]@i", $post_text, $matches);
			}
			if(empty($matches[1]) == FALSE && empty($matches[2]) == FALSE) {
				$post_audio = $matches[1] . '.' . $matches[2];
			}
			$matches = array();
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
			$matches = array();
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
?>