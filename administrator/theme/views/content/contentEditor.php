<?php
	// if we are in edit mode, get the page information
	if(isset($params["page"]) == TRUE && $params["page"] != '') {
		$page = $this->route('/sys/pages/get/?page_id=' . $params["page"])->getFirst();
	}
?>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Content</h3>
	</div>
	<div class="panel-body content-new-left">
		<form name="page_editor" id="page_editor" class="form-horizontal">
			<fieldset>
			    <!-- this will keep page_id, blank if this is a new page -->
			    <input type="hidden" name="page_id" id="page_id" value="<?php if(isset($params["page"]) == TRUE && $params["page"] != '') { print($params["page"]); } ?>">
				<!-- page title -->
				<div class="form-group">
					<label class="col-lg-1 control-label">Title*</label>
					<div class="col-lg-11">
						<input type="text" class="form-control" name="page_title" id="page_title" value="<?php if(isset($page->page_title) == TRUE) { print($page->page_title);  } ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label">Content</label>
					<div class="col-lg-11">
						<div class="btn-toolbar">
							<div class="btn-group">
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','=','Heading Level 1','=\n');"><i class="fa fa-font"></i> Heading</a>
								<a class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li><a onclick="insertMarkupAtCaret('contentText','=','Heading Level 1','=\n');">Heading Level 1</a></li>
									<li><a onclick="insertMarkupAtCaret('contentText','==','Heading Level 2','==\n');">Heading Level 2</a></li>
									<li><a onclick="insertMarkupAtCaret('contentText','===','Heading Level 3','===\n');">Heading Level 3</a></li>
									<li><a onclick="insertMarkupAtCaret('contentText','====','Heading Level 4','====\n');">Heading Level 4</a></li>
									<li><a onclick="insertMarkupAtCaret('contentText','=====','Heading Level 5','=====\n');">Heading Level 5</a></li>
									<li><a onclick="insertMarkupAtCaret('contentText','======','Heading Level 6','======\n');">Heading Level 6</a></li>
								</ul>
							</div>
							<div class="btn-group">
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','\'\'\'','Bold','\'\'\'');"><i class="fa fa-bold"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','\'\'','Italic','\'\'');"><i class="fa fa-italic"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','[[','Target Page Title',']]');"><i class="fa fa-link"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','[http://','Target Page URL',']');"><i class="fa fa-external-link"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','* ','Bulleted List Item','\n');"><i class="fa fa-list-ul"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','# ','Numbered List Item','\n');"><i class="fa fa-list-ol"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','<blockquote>\n','Nothing takes the taste out of peanut butter like unrequited love!','\n</blockquote>\n');"><i class="fa fa-quote-right"></i></a>
								<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','\n<nowiki>\n<pre>\n','my problems[98];\nfor(x=0; x<99; x++){\n\tif(problems[x] == girl){\n\t\ti.feelbadfor(you, \'son\');\n\t}\n\telse{}\n}','\n</pre>\n</nowiki>\n');"><i class="fa fa-code"></i></a>
								<a class="btn btn-default" onclick="insertCodeAtCaret('contentText','\n-8<-\n');"><i class="fa fa-cut"></i></a>
							</div>
						</div>

						<textarea class="form-control" rows="20" name="page_text" id="page_text"><?php if(isset($page->page_text) == TRUE) { print($page->page_text);  } ?></textarea>
					</div>
				</div>
				<!--
				<div class="form-group">
					<label class="col-lg-1 control-label">Description</label>
					<div class="col-lg-11">
						<textarea class="form-control" rows="3" name="contentDescription" id="contentDescription"><?php if(isset($page->description) == TRUE) { print($page->description);  } ?></textarea>
						<span class="help-block">Optional summery of your article that can be used in previews.</span>
					</div>
				</div>
				-->

			</fieldset>
		</form>

		<label class="col-lg-1 control-label">Files</label>
		<form id="dropzone" name="dropzone" action="/sys/files/upload/" class="col-lg-11 dropzone"></form>
		<div class="clearfix"><br/></div>

		<div class="row">
			<div class="col-lg-10 col-lg-offset-1">
				<a class="btn btn-default small-button" id="page_cancel">Cancel</a>
				<a class="btn btn-default small-button" id="page_preview">Preview</a>
				<a class="btn btn-primary small-button" id="page_submit">Submit</a>
			</div>
		</div>

	</div>
</div>