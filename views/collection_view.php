<?php
$collection_template = get_option("tainacan_collection_template");
if(!empty($collection_template))
{
    tainacansc_render_template($content, $collection_template, "collection");
}else{
    ?>
    <a href="<?= $content->guid; ?>" target="_blank">
        <div id="tainacan-collection">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
						<?php
						if($content->thumbnail)
						{
							$image_url = $content->thumbnail;
						}else {
							$image_url = plugin_dir_url(__FILE__)."images/no_thumb.png";
						}
						?>
                        <img src="<?php echo $image_url; ?>" class="img-responsive">
                    </div>

                    <div class="col-md-10">
                        <h1 id="post-title"><?= $content->post_title; ?></h1>
                        <p id="post-content">
							<?= $content->post_content; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <?php
}
?>