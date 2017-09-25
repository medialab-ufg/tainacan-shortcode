<?php
$items_template = get_option('tainacan_items_template');
if(!empty($items_template)){
    render_template($content, $items_template, 'items');
}else{
    ?>
    <div class="tainacan-items">
        <?php
        $i = 4;
        foreach($content as $item_metas)
        {
            if($i == 4)
            {
                echo "<div class='row'>";
            }

            $item_metas = $item_metas->item;
            if(!$item_metas->thumbnail)
            {
                $img_url = plugin_dir_url(__FILE__)."images/no_thumb.png";
            }else $img_url = $item_metas->thumbnail;

            ?>
            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                <a href="<?= $item_metas->guid ?>" target="_blank" class="thumbnail">
                    <img src="<?= $img_url?>">
                    <div class="caption text-center">
                        <h3><?= $item_metas->post_title ?></h3>
                    </div>
                </a>
            </div>
            <?php

            $i--;

            if($i == 0)
            {
                echo '</div>';
                $i = 4;
            }
        }

        if($i > 0)
        {
            echo '</div>';
        }
        ?>
    </div>
    <?php
}
?>