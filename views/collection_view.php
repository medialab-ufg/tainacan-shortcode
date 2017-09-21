<a href="<?= $content->guid; ?>" target="_blank">
	<div id="tainacan-collection">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<?php
						if($content->thumnail)
						{
							$image_url = $content->thumbnail;
						}else {
							$image_url = plugin_dir_url(__FILE__)."images/no_thumb.png";
						}
					?>
					<img src="<?php echo $image_url; ?>" class="img-responsive">
				</div>

				<div class="col-md-9">
					<h1 id="post-title"><?= $content->post_title; ?></h1>
					<p id="post-content">
						<?= $content->post_content; ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</a>