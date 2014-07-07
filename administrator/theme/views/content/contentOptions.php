<?php

	// if we are in edit mode, get the page information
	if(isset($params["page"]) == TRUE && $params["page"] != '') {
		$page = $this->route('/sys/pages/get/?page_id=' . $params["page"])->getFirst();
	}
?>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">Options</h3>
	</div>
	<div class="panel-body content-new-right">
		<form name="page_options" id="page_options" class="form-horizontal">
			<fieldset>
				<legend>Publishing</legend>
				<div class="form-group">
					<label class="col-lg-4 control-label">Status</label>
					<div class="col-lg-8">
						<select class="form-control" name="page_status" id="page_status">
							<?php if(isset($page->page_status) == TRUE) { print('<option value="' . $page->page_status . '" selected>' . ucfirst($page->page_status) . '</option>');  } ?>
	                        <option value="draft">Draft</option>
	                        <option value="published">Published</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label">Publish Date</label>
					<div class="col-lg-8">
						<input type="text" class="form-control datepicker" name="page_publish_date" id="page_publish_date" value="<?php if(isset($page->page_publish_date) == TRUE) { print($page->page_publish_date);  } ?>">
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-4 control-label">Path*</label>
					<div class="col-lg-8">
	                    <input type="text" class="form-control" name="page_path" id="page_path" value="<?php if(isset($page->page_path) == TRUE) { print($page->page_path);  } ?>">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="page_ssl" id="page_ssl" value="1" <?php if(isset($page->page_ssl) == TRUE && $page->page_ssl == TRUE) { print('checked');  } ?>> SSL Enabled
							</label>
						</div>
					</div>
				</div>

				<legend>Category & Tags</legend>
				<div class="form-group">
					<label class="col-lg-4 control-label">Categories</label>
					<div class="col-lg-8">
						<input type="text" class="form-control" name="page_categories" id="page_categories" value="<?php if(isset($page->page_categories) == TRUE) { print($page->page_categories);  } ?>">
					</div>
				</div>

				<div class="form-group">
					<label class="col-lg-4 control-label">Tags</label>
					<div class="col-lg-8">
						<input type="text" class="form-control" name="page_tags" id="page_tags" value="<?php if(isset($page->page_tags) == TRUE) { print($page->page_tags);  } ?>">
					</div>
				</div>

				<legend>Appearance</legend>
				<div class="form-group">
					<label class="col-lg-4 control-label">Theme</label>
					<div class="col-lg-8">
						<select class="form-control" name="page_theme" id="page_theme">
						<?php
						foreach($_SESSION["cms"]["themes"] as $theme) {
							if(isset($page->page_theme) == TRUE && $page->page_theme != '' && $theme->file == $page->page_theme) {
								print('<option value="' . $theme->file . '" selected>' . $theme->name . '</option>');
							} else {
								print('<option value="' . $theme->file . '">' . $theme->name . '</option>');
							}
						}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label">Layout</label>
					<div class="col-lg-8">
						<select class="form-control" name="page_layout" id="page_layout" data-layout="<?php if(isset($page->page_layout) == TRUE) { print($page->page_layout);  } ?>">
						</select>
					</div>
				</div>

				<legend>Access</legend>
				<div class="form-group">
					<label class="col-lg-4 control-label">Visibility</label>
					<div class="col-lg-8">
						<select class="form-control" name="page_visibility" id="page_visibility">
							<?php if(isset($page->page_visibility) == TRUE) { print('<option value="' . $page->page_visibility . '" selected>' . ucfirst($page->page_visibility) . '</option>');  } ?>
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
</div>

<script type="text/javascript">
	$('#page_status').select2({minimumResultsForSearch: -1});
	$('#page_theme').select2({minimumResultsForSearch: -1});
	$('#page_layout').select2({minimumResultsForSearch: -1});
	$('#page_visibility').select2({minimumResultsForSearch: -1});

	$('#page_categories').select2({
		tags: '<?php print($this->route('/sys/pages/getCategories/')->data->categories); ?>'.split(','),
		tokenSeparators: [',']
	});
	$('#page_tags').select2({
		tags: '<?php print($this->route('/sys/pages/getTags/')->data->tags); ?>'.split(','),
		tokenSeparators: [',']
	});
</script>