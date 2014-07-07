<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">All Pages</h3>
	</div>
	<div class="panel-body">
		<table class="table table-striped">
		    <thead>
		        <tr>
		            <th class="col-sm-6">Title</th>
		            <th class="col-sm-2">Author</th>
		            <th class="col-sm-1">Date</th>
		            <th class="col-sm-1">Status</th>
			        <th class="col-sm-1 text-right"></th>
		        </tr>
		    </thead>
		    <tbody>
			<?php
			// variables
			$itemsPerPage = 10;

			// get item count and max page number
			$itemsCount = $this->route('/sys/pages/count/')->data["count"];
			$itemsMaxPageNumber = round(($itemsCount / $itemsPerPage), 0, PHP_ROUND_HALF_UP);

			// detect page number
			if(isset($params["p"]) && $params["p"] != '') {
				$p = (int) $params["p"];
				if($p > $itemsMaxPageNumber) { $p = $itemsMaxPageNumber; }
			} else {
				$p = 1;
			}

			// calculate previous/next page
			if($p > 1) {
				$p_previous = $p - 1;
			} else {
				$p_previous = 1;
			}

			if($p < $itemsMaxPageNumber) {
				$p_next = $p + 1;
			} else {
				$p_next = $itemsMaxPageNumber;
			}

			// get pages
			$params = array(
				'start' => ($p-1) * 15,
				'rows' => $itemsPerPage
				);

			$resourcePages = $this->route('/sys/pages/get/', $params)->data;

			// display data
			foreach($resourcePages as $page) {
			?>
		        <tr>
		            <td class="col-sm-6"><?php print($page->page_title); ?></td>
		            <td class="col-sm-2"><?php print($page->page_author); ?></td>
		            <td class="col-sm-1"><?php print($page->page_publish_date); ?></td>
		            <td class="col-sm-1"><?php print(ucfirst($page->page_status)); ?></td>
			        <td class="col-sm-1 text-right">
			        	<a data-page="<?php print($page->page_id); ?>" class="btn btn-default btn-xs contentDeletePage"><i class="fa fa-trash-o"></i></a>
			        	<a data-page="<?php print($page->page_id); ?>" href="/admin/content/edit/?page=<?php print($page->page_id); ?>" class="btn btn-default btn-xs contentEditPage"><i class="fa fa-pencil"></i></a>
			        	<a data-page="<?php print($page->page_id); ?>" class="btn btn-default btn-xs contentPreviewPage"><i class="fa fa-eye"></i></a>
			        </td>
		        </tr>
			<?php
			}
			?>
		    </tbody>
		</table>
		<div class="text-center">
			<ul class="pagination">
				<li><a href="/admin/content/?p=<?php print($p_previous); ?>"><i class="fa fa-angle-double-left"></i></a></li>
			<?php
			for($i = 1; $i <= $itemsMaxPageNumber; $i++) {
				if($i == $p) {
					print('<li class="active"><a href="/admin/content/?p=' . $i . '">' . $i . '</a></li>');
				} else {
					print('<li><a href="/admin/content/?p=' . $i . '">' . $i . '</a></li>');
				}
			}
			?>
				<li><a href="/admin/content/?p=<?php print($p_next); ?>"><i class="fa fa-angle-double-right"></i></a></li>
			</ul>
		</div>
	</div>
</div>