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

		public function out() {

			$params = array(
					"post_title" =>			 "Lorem Ipsum",
					"post_path" =>			 "test/example/",
					"post_path_hash" =>		 md5("test/example/"),
					"post_text"  =>			 "<p>Nesciunt cliche officia  ennui ethnic iPhone leggings, nisi banjo keytar.  Gentrify nulla  elit Schlitz kale chips shabby chic.  Bicycle rights cred artisan polaroid.  Pug semiotics pour-over, keytar Brooklyn stumptown artisan Terry Richardson tofu fingerstache.  Cupidatat  veniam keffiyeh chambray culpa.  Pour-over messenger bag Brooklyn thundercats id, sustainable ullamco.  Dreamcatcher meh typewriter sriracha, velit  forage seitan.</p>",
					"post_author" => 		 0,
					"post_categories" =>	 "stories,place holder",
					"post_keywords" =>		 "",
					"post_description" =>	 "<p>Nesciunt cliche officia  ennui ethnic iPhone leggings, nisi banjo keytar...</p>",
					"post_tags" => 			 "Lorem,Ipsum,Dolor,Sit",
					"post_layout" => 		 "",
					"post_theme" => 		 "",
					"post_views" => 		 rand(1,10000),
					"post_status" => 		 "published"
				);

			//$this->route("/sys/posts/add/",$params);

		}

	}
?>