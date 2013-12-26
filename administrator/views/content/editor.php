<div class="panel-body content-new-left">
	<form name="contentForm" id="contentForm" class="form-horizontal">
		<fieldset>
            <!-- content mode ( new / update ) -->
            <input type="hidden" name="contentMode" id="contentMode" value="<?php print($data['contentMode']); ?>">
			<!-- page title -->
			<div class="form-group">
				<label class="col-lg-1 control-label">Title*</label>
				<div class="col-lg-11">
					<input type="text" class="form-control" name="contentTitle" id="contentTitle" value="<?php print($data["contentTitle"]); ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-1 control-label">Content</label>
				<div class="col-lg-11">
					<div class="btn-toolbar">
						<div class="btn-group">
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','=','Heading Level 1','=\n');"><i class="icon-font"></i> Heading</a>
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
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','\'\'\'','Bold','\'\'\'');"><i class="icon-bold"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','\'\'','Italic','\'\'');"><i class="icon-italic"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','[[','Target Page Title',']]');"><i class="icon-link"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','[http://','Target Page URL',']');"><i class="icon-external-link"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','* ','Bulleted List Item','\n');"><i class="icon-list-ul"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','# ','Numbered List Item','\n');"><i class="icon-list-ol"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','<blockquote>\n','Nothing takes the taste out of peanut butter like unrequited love!','\n</blockquote>\n');"><i class="icon-quote-right"></i></a>
							<a class="btn btn-default" onclick="insertMarkupAtCaret('contentText','\n<nowiki>\n<pre>\n','my problems[98];\nfor(x=0; x<99; x++){\n\tif(problems[x] == girl){\n\t\ti.feelbadfor(you, \'son\');\n\t}\n\telse{}\n}','\n</pre>\n</nowiki>\n');"><i class="icon-code"></i></a>
							<a class="btn btn-default" onclick="insertCodeAtCaret('contentText','\n-8<-\n');"><i class="icon-cut"></i></a>
							<a class="btn btn-default"><i class="icon-picture"></i></a>
							<a class="btn btn-default"><i class="icon-music"></i></a>
							<a class="btn btn-default"><i class="icon-film"></i></a>
							<a class="btn btn-default"><i class="icon-file"></i></a>
						</div>
					</div>

					<textarea class="form-control" rows="15" name="contentText" id="contentText"><?php print($data["contentText"]); ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-1 control-label">Description</label>
				<div class="col-lg-11">
					<textarea class="form-control" rows="3" name="contentDescription" id="contentDescription"><?php print($data["contentDescription"]); ?></textarea>
					<span class="help-block">Optional summery of your article that can be used in previews.</span>
				</div>
			</div>

		</fieldset>
	</form>

    <form id="contentDropzone" name="contentDropzone" action="/sys/files/upload/" class="col-lg-11 col-lg-offset-1 dropzone"></form>
    <div class="clearfix"><br/></div>

    <div class="col-lg-11 margin-left-75px">
        <a class="btn btn-primary ui-btn" name="contentSubmit" id="contentSubmit">Submit</a>
        <a class="btn btn-default ui-btn" name="contentPreview" id="contentPreview">Preview</a>
        <a class="btn btn-default ui-btn" name="contentCancel" id="contentCancel">Cancel</a>
    </div>

</div>