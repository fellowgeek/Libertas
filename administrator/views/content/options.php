<div class="panel-body content-new-right">
	<form name="contentOptionsForm" id="contentOptionsForm" class="form-horizontal">
		<fieldset>
			<legend>Publishing</legend>
			<div class="form-group">
				<label class="col-lg-4 control-label">Status</label>
				<div class="col-lg-8">
					<select class="form-control" name="contentStatus" id="contentStatus">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Publish at</label>
				<div class="col-lg-8">
					<input type="text" class="form-control datepicker" name="contentPublish" id="contentPublish" value="<?php print($data["contentPublish"]); ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-4 control-label">Path*</label>
				<div class="col-lg-8">
                    <input type="text" class="form-control" name="contentPath" id="contentPath" value="<?php print($data["contentPath"]); ?>" <?php if($data['contentPath'] == '') { print('disabled'); } ?>>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="contentSSL" id="contentSSL" value="1" <?php if($data["contentSSL"] != '') { print("checked"); } ?>> SSL Enabled
						</label>
					</div>
				</div>
			</div>

			<legend>Category & Tags</legend>
			<div class="form-group">
				<label class="col-lg-4 control-label">Categories</label>
				<div class="col-lg-8">
					<input type="text" class="form-control" name="contentCategories" id="contentCategories" value="<?php print($data["contentCategories"]); ?>">
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-4 control-label">Tags</label>
				<div class="col-lg-8">
					<input type="text" class="form-control" name="contentTags" id="contentTags" value="<?php print($data["contentTags"]); ?>">
				</div>
			</div>

			<legend>Appearance</legend>
			<div class="form-group">
				<label class="col-lg-4 control-label">Layout</label>
				<div class="col-lg-8">
					<select class="form-control" name="contentLayout" id="contentLayout">
                        <option value="">Default</option>
                        <option value="Layout 1">Layout 1</option>
                        <option>Layout 2</option>
                        <option>Layout 3</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Theme</label>
				<div class="col-lg-8">
					<select class="form-control" name="contentTheme" id="contentTheme">
                        <option value="">Default</option>                        
                        <option value="Theme 1">Theme 1</option>
                        <option>Theme 2</option>
                        <option>Theme 3</option>
					</select>
				</div>
			</div>

			<legend>Access</legend>
			<div class="form-group">
				<label class="col-lg-4 control-label">Visibility</label>
				<div class="col-lg-8">
					<select class="form-control" name="contentVisibility" id="contentVisibility">
                        <option value="everyone">Everyone</option>
                        <option value="user">User</option>
                        <option value="author">Author</option>
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
					</select>
				</div>
			</div>
		</fieldset>
	</form>
</div>
