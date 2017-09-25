<?php
/**
 * Plugin Name: Tainacan: Short code
 * Plugin URI: http://mypluginuri.com/
 * Description: Enable shortcodes for Tainacan
 * Version: 1.0
 * Author: André Alvim
 * Author URI: Author's website
 * License: A "Slug" license name e.g. GPL12
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*Views*/
define ("ITEMS_VIEW", "views/items_view.php");
define ("COLLECTION_VIEW", "views/collection_view.php");

/*Meta types*/
define ('CATEGORY', 1);

$plug_in_dir = plugin_dir_url(__FILE__);
//CSS
wp_enqueue_style("codemirror_css", $plug_in_dir . "libs/js/codemirror-5.30.0/lib/codemirror.css", null, false, "all");
wp_enqueue_style("show_hint_css", $plug_in_dir . "libs/js/codemirror-5.30.0/addon/hint/show-hint.css", null, false, "all");
wp_enqueue_style("Bootstrap_css", 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', null, false, "all");
wp_enqueue_style("main", $plug_in_dir . "libs/css/main.css", null, false, "all");

//Javascript
wp_enqueue_script("jQuery", "https://code.jquery.com/jquery-3.2.1.min.js", null, "3.2.1", true);
wp_enqueue_script("Bootstrap_js", "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js", 'jQuery', "3.3.7", true);
wp_enqueue_script("sweetalert_js", "https://unpkg.com/sweetalert/dist/sweetalert.min.js", 'jQuery', "3.3.7", true);
wp_enqueue_script("codemirror_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/lib/codemirror.js', null, "5.30.0", true);
wp_enqueue_script("show_hint_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/addon/hint/show-hint.js', null, "", true);
wp_enqueue_script("xml_hint_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/addon/hint/xml-hint.js', null, "", true);
wp_enqueue_script("xml_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/mode/xml/xml.js', null, "", true);
wp_enqueue_script("javascript_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/mode/javascript/javascript.js', null, "", true);
wp_enqueue_script("css_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/mode/css/css.js', null, "", true);
wp_enqueue_script("htmlmixed_js", $plug_in_dir . 'libs/js/codemirror-5.30.0/mode/htmlmixed/htmlmixed.js', null, "", true);



wp_enqueue_script("main", $plug_in_dir . 'libs/js/main.js', null, "1.0", true);

/****************************************************************-----*****************************************************************************************/
add_action( 'admin_menu', 'tainacan_shortcode_submenu' );
function tainacan_shortcode_submenu() {
	add_options_page( 'Tainacan shortcode', 'Tainacan Shortcode', 'manage_options', 'tainacan-shortcode', 'get_shortcode_page' );
}

/***************************************************** FUNCTIONS *****************************************************/
function get_shortcode_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	ob_start();
	require "views/admin/shortcode-configuration-template.php";
	$page = ob_get_clean();

	echo $page;
}

function render_page($view, $content = null)
{
	ob_start();
	require ($view);
	$rendered_page = ob_get_clean();

	return $rendered_page;
}

function render_template($content, $template, $type)
{
	$template = stripslashes_deep($template);
	$page = '';
	if(strcmp($type, "items") === 0)
	{
		foreach($content as $item_metas)
		{
			$item_metas = $item_metas->item;
			$page .= replace_in_template($template, $item_metas);
		}
	}
	else if(strcmp($type, "collection") === 0)
	{
		$page .= replace_in_template($template, $content);
	}

	echo $page;
}

function replace_in_template($template, $content)
{
	$template = str_replace("{title}", $content->post_title, $template);
	$template = str_replace("{date}", $content->post_date, $template);
	$template = str_replace("{content}", $content->post_content, $template);
	$template = str_replace("{last_modified}", $content->post_modified, $template);
	$template = str_replace("{link}", $content->guid, $template);
	$template = str_replace("{comment_count}", $content->comment_count, $template);

	if($content->thumbnail)
	{
		$template = str_replace("{thumbnail}", $content->thumbnail, $template);
	}
	else {
		$template = str_replace("{thumbnail}", plugin_dir_url(__FILE__)."views/images/no_thumb.png", $template);
	}

	$template = str_replace("{cover}", $content->cover, $template);

	return $template;
}

function get_collection_info($tainacan_url, $collection_name)
{
	$collection_name = rawurlencode($collection_name);

	$url = $tainacan_url.'wp-json/tainacan/v1/collections?filter[title]='.$collection_name;
	$collection_info = json_decode(file_get_contents($url));

	if($collection_info)
	{
		return $collection_info[0];
	}else return false;
}

/*********************************************** [tainacan-show-items] ***********************************************/
function get_meta_id($url, $collection_id, $required_meta_name, $required_meta_value)
{
	$special_url = $url.'wp-json/tainacan/v1/collections/'.$collection_id."/metadata?includeMetadata=1";
	$collection_meta_tab = json_decode(file_get_contents($special_url));
	$category_types = ['checkbox', 'tree_checkbox', 'multipleselect', 'tree', 'radio', 'selectbox'];
	$result = [];
	$meta_type = false;
	foreach($collection_meta_tab as $tab_meta)
	{
		$tab_meta = $tab_meta->{'tab-properties'};
		foreach($tab_meta as $meta)
		{
			$current_meta_name = $meta->name;
			if(strcmp($required_meta_name, $current_meta_name) === 0)
			{
				//Verifica se é metadado de categoria
				$current_meta_type = $meta->type;
				if(in_array($current_meta_type, $category_types))
				{
					$categories = $meta->metadata->categories;
					foreach($categories as $category)
					{
						$category = $category->term;
						$category_name = $category->name;

						if(strcmp($required_meta_value, $category_name) === 0)
						{
							$result['meta_id'] = $meta->id;
							$result['category_id'] = $category->term_id;
							$meta_type = CATEGORY;
						}
					}
				}
				else
				{
					$result['meta_id'] = $meta->id;
					$meta_type = DATE;
				}
			}
		}
	}

	return ['result' =>$result, 'type' => $meta_type];
}

function show_items($atts)
{
	$atributos = shortcode_atts( array(
		'tainacan-url' => '',
		"collection-name" => '',
		"meta-name" => '',
		"meta-value" => '',
		"meta-operation" => 'LIKE'
	), $atts );

	if(empty($atributos['tainacan-url']) || empty($atributos['collection-name']))
	{
		return;
	}

	$collection_info = get_collection_info($atributos['tainacan-url'], $atributos['collection-name']);
	if($collection_info)
	{
		$collection_id = $collection_info->ID;
		$items = [];
		if(!empty($atributos['meta-name']) && !empty($atributos['meta-value']))
		{
			$return = get_meta_id($atributos['tainacan-url'], $collection_id, $atributos['meta-name'], $atributos['meta-value']);
			if($return['type'] === CATEGORY)
			{
				$categories_id = $return['result'];
				if(!empty($categories_id))
				{
					$url = $atributos['tainacan-url'].'/wp-json/tainacan/v1/collections/'.$collection_id."/items?filter[metadata][".$categories_id['meta_id']."][op]=".$atributos['meta-operation']."&filter[metadata][".$categories_id['meta_id']."][values][]=".$categories_id['category_id'];
				}
			}else
			{
				$ids = $return['result'];
				$atributos['meta-value'] = rawurlencode($atributos['meta-value']);
				$url = $atributos['tainacan-url'].'/wp-json/tainacan/v1/collections/'.$collection_id."/items?filter[metadata][".$ids['meta_id']."][op]=".$atributos['meta-operation']."&filter[metadata][".$ids['meta_id']."][values][]=".$atributos['meta-value'];
			}
		}else
		{
			$url = $atributos['tainacan-url'].'/wp-json/tainacan/v1/collections/'.$collection_id."/items/";
		}

		$items = json_decode(file_get_contents($url))->items;
		echo render_page(ITEMS_VIEW, $items);
	}
}

add_shortcode("tainacan-show-items", "show_items");

/********************************************* [tainacan-show-collection] ********************************************/
function show_collection($atts)
{
	$atributos = shortcode_atts( array(
		'tainacan-url' => '',
		"collection-name" => ''
	), $atts );

	if(empty($atributos['tainacan-url']) || empty($atributos['collection-name']))
	{
		return;
	}

	$collection_info = get_collection_info($atributos['tainacan-url'], $atributos['collection-name']);

	if($collection_info)
	{
		//$collection_id = $collection_info->ID;
		//$url = $atributos['tainacan-url'].'/wp-json/tainacan/v1/collections/'.$collection_id."/";
		echo render_page(COLLECTION_VIEW , $collection_info);
	}
}

add_shortcode("tainacan-show-collection", "show_collection");