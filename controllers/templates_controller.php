<?php
include("../../../../wp-config.php");

define('ITEMS_TEMPLATE', 'tainacan_items_template');
define('COLLECTION_TEMPLATE', 'tainacan_collection_template');

$operation = $_GET['operation'];

switch($operation){
	case "save_templates":
		$items_template = $_POST['items_editor_val'];
		$collection_template = $_POST['collection_editor_val'];

		update_option(ITEMS_TEMPLATE, $items_template);
		update_option(COLLECTION_TEMPLATE, $collection_template);
		echo(json_encode(true));
		break;
	case "get_templates":
		$items_template = get_option(ITEMS_TEMPLATE);
		$collection_template = get_option(COLLECTION_TEMPLATE);

		$templates = ['items_template' => $items_template, 'collection_template' => $collection_template];

		echo json_encode($templates);
		break;
}