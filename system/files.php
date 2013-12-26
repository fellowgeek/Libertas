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

	Class files extends OObject {

		public function __construct() {

			$this->allowed_files = array('jpg','png','gif','mp3','ogg','wav','mp4','mov','webm','pdf','zip');

			$this->permissions = array(
				'object' => 'any',
				'upload' => 'any'
			);
		}

		// handle file uploads
		public function upload() {

			if(isset($_FILES['file']) == TRUE) {

				$file = basename($_FILES['file']['name']);
				$file_name = pathinfo($file, PATHINFO_FILENAME);
				$file_extention = strtolower(pathinfo($file, PATHINFO_EXTENSION));

				$params = array('text' => $file_name);
				$file_name_slug = $this->route('/sys/utilities/slugify/', $params)->data["slug"];
				$file = $file_name_slug . '.' . $file_extention;
				$uploadfile = './files/' . $file;

				if(in_array($file_extention, $this->allowed_files) == TRUE) {
					if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
						// create response
						$this->data["files"] = $_FILES;
						$this->data["file_name"] = $file;
						$this->data["file_extention"] = $file_extention;
						if(in_array($file_extention, array('jpg','png','gif')) == TRUE) { $this->data["wiki_code"] = '[Image:' . $file . ']'; }
						if(in_array($file_extention, array('mp3','ogg','wav')) == TRUE) { $this->data["wiki_code"] = '[Audio:' . $file . ']'; }
						if(in_array($file_extention, array('mp4','mov','webm')) == TRUE) { $this->data["wiki_code"] = '[Video:' . $file . ']'; }
						if(in_array($file_extention, array('pdf','zip')) == TRUE) { $this->data["wiki_code"] = '[File:' . $file . ']'; }
					} else {
						$this->throwError('Unable to move uploaded file.', 200);
					}
				} else {
					$this->throwError('File type is not allowed.', 200);
				}

			} else {
				$this->throwError('Unable to move uploaded file.', 200);
			}
		}

	}
?>