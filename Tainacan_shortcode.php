<?php
class Tainacan_shortcode {
	private $plug_in_dir;
	/*Views*/
	private $ITEMS_VIEW = 'views/items_view.php';
	private $COLLECTION_VIEW = 'views/collection_view.php';

	/*Types*/
	private $CATEGORY = 1;

	/*Cache of collections id*/
	private $COL_ID_CACHE_INDEX = 'TAINACAN_SHORTCODE_CACHE_COLLECTION_ID_LOCATION';//Cache dos id das coleções, realizado SEMPRE

	/*Cache of all collection*/
	private $COLLECTION_CACHE_INDEX = 'TAINACAN_SHORTCODE_CACHE_FULL_COLLECTION_LOCATION';//Indice da option onde está o cache de coleções
	private $ITEMS_CACHE_INDEX = 'TAINACAN_SHORTCODE_CACHE_ITEMS_SET_LOCATION';//Indice da option onde está o cache de itens
	private $CREATION_TIME_COLLECTION_CACHE_INDEX = "TAINACAN_SHORTCODE_COLLECTIONS_CREATION_TIME_CACHE_LOCATION";//Indice da option dos tempos de criação do cache das coleções
	private $CREATION_TIME_ITEMS_CACHE_INDEX = "TAINACAN_SHORTCODE_ITEMS_CREATION_TIME_CACHE_LOCATION";//Indice da option dos tempos de criação do cache dos itens

	/*Caches time*/
	private $COLLECTION_CACHE_TIME_INDEX = 'TAINACAN_SHORTCODE_COLLECTION_CACHE_TIME_LOCATION';//Localização do tempo que o cache de coleção deve durar
	private $ITEMS_CACHE_TIME_INDEX = 'TAINACAN_SHORTCODE_ITEMS_CACHE_TIME_LOCATION';//Localização do tempo que o cache de itens deve durar;
	private $DEFAULT_CACHE_TIME = 24; //Tempo padrão de duração de cache em HORAS

	function __construct()
	{
		$this->plug_in_dir = plugin_dir_url(__FILE__);
		add_action("admin_enqueue_scripts", array($this, 'init_css'));
		add_action("admin_enqueue_scripts", array($this, 'init_js'));

		add_shortcode("tainacan-show-collection", array($this,"show_collection"));
		add_shortcode("tainacan-show-items", array($this, "show_items"));
	}

	public function init_css()
	{
		/**************************** CSS ****************************/
		//Codemirror
		wp_enqueue_style("codemirror_css", $this->plug_in_dir . "libs/js/codemirror-5.30.0/lib/codemirror.css", null, false, "all");
		wp_enqueue_style("show_hint_css", $this->plug_in_dir . "libs/js/codemirror-5.30.0/addon/hint/show-hint.css", null, false, "all");

		//Bootstrap
		wp_enqueue_style("Bootstrap_css", 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', null, false, "all");

		//Meu css princiapal
		wp_enqueue_style("main", $this->plug_in_dir . "libs/css/main.css", null, false, "all");
	}

	public function init_js()
	{
		/**************************** JavaScript ****************************/
		//Codemirror
		wp_enqueue_script("codemirror_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/lib/codemirror.js', null, "5.30.0", true);
		wp_enqueue_script("show_hint_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/addon/hint/show-hint.js', null, "", true);
		wp_enqueue_script("xml_hint_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/addon/hint/xml-hint.js', null, "", true);
		wp_enqueue_script("xml_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/mode/xml/xml.js', null, "", true);
		wp_enqueue_script("javascript_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/mode/javascript/javascript.js', null, "", true);
		wp_enqueue_script("css_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/mode/css/css.js', null, "", true);
		wp_enqueue_script("htmlmixed_js", $this->plug_in_dir . 'libs/js/codemirror-5.30.0/mode/htmlmixed/htmlmixed.js', null, "", true);

		//jQuery
		wp_enqueue_script("jQuery","https://code.jquery.com/jquery-3.2.1.min.js", null, "3.2.1", true);

		//Bootstrap
		wp_enqueue_script("Bootstrap_js", "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js", 'jQuery', "3.3.7", true);

		//Sweetalert
		wp_enqueue_script("sweetalert_js", "https://unpkg.com/sweetalert/dist/sweetalert.min.js", 'jQuery', "3.3.7", true);

		//JS principal
		wp_enqueue_script("main", $this->plug_in_dir . 'libs/js/main.js', null, "1.0", true);
	}
	/********************************************* [tainacan-show-collection] ********************************************/
	public function show_collection($atts)
	{
		$atributos = shortcode_atts( array(
			'tainacan-url' => '',
			"tainacan-query-url" => '',
			"enable-cache" => 'false',
			"cache-time" => $this->DEFAULT_CACHE_TIME,
			"collection-name" => ''
		), $atts );

		if(empty($atributos['tainacan-url']) || empty($atributos['collection-name']))
		{
			if(!empty($atributos['tainacan-query-url']))
			{
				$url = str_replace("{", "[", $atributos['tainacan-query-url']);
				$url = str_replace("}", "]", $url);

				$items = json_decode(file_get_contents($url))->items;
				echo $this->render_page($this->ITEMS_VIEW, $items);
			}
			return;
		}

		//Cache
		if(strcmp(strtolower($atributos['enable-cache']), "true") === 0 )
		{
			$cache_time = get_option($this->COLLECTION_CACHE_TIME_INDEX);
			if($cache_time != $atributos['cache-time'])
			{
				update_option($this->COLLECTION_CACHE_TIME_INDEX, $atributos['cache-time']);
				$cache_time = $atributos['cache-time'];
			}


			$cache = get_option($this->COLLECTION_CACHE_INDEX);
			if(!empty($cache))
			{
				$collection_info = $cache[$atributos['tainacan-url']][$atributos['collection-name']];

				if($collection_info)
				{
					$created_cache_collection_time = get_option($this->CREATION_TIME_COLLECTION_CACHE_INDEX);
					$creation_time = $created_cache_collection_time[$collection_info->ID];
					$diff_time = $this->diff_dates_hours($creation_time, new DateTime());

					if($diff_time > $cache_time)
					{
						$collection_info = false;
					}
				}
			}

			if(!$collection_info)
			{
				$collection_info = $this->get_collection_info($atributos['tainacan-url'], $atributos['collection-name']);
				$cache[$atributos['tainacan-url']] = [$atributos['collection-name'] => $collection_info];

				update_option($this->COLLECTION_CACHE_INDEX, $cache);

				$date = new DateTime();
				$created_cache_collection_time = get_option($this->CREATION_TIME_COLLECTION_CACHE_INDEX);
				$created_cache_collection_time[$collection_info->ID] = $date;
				update_option($this->CREATION_TIME_COLLECTION_CACHE_INDEX, $created_cache_collection_time);
			}
		}else
		{
			$collection_info = $this->get_collection_info($atributos['tainacan-url'], $atributos['collection-name']);
		}

		if($collection_info)
		{
			echo $this->render_page($this->COLLECTION_VIEW , $collection_info);
		}
	}

	/*********************************************** [tainacan-show-items] ***********************************************/
	public function show_items($atts)
	{
		$atributos = shortcode_atts( array(
			'tainacan-url' => '',
			"tainacan-query-url" => '',
			"collection-name" => '',
			"meta-name" => '',
			"meta-value" => '',
			"enable-cache" => 'false',
			"cache-time" => $this->DEFAULT_CACHE_TIME,
			"meta-operation" => 'LIKE'
		), $atts );

		if(empty($atributos['tainacan-url']) || empty($atributos['collection-name']))
		{
			if(!empty($atributos['tainacan-query-url']))
			{
				$url = str_replace("{", "[", $atributos['tainacan-query-url']);
				$url = str_replace("}", "]", $url);

				$items = json_decode(file_get_contents($url))->items;
				echo $this->render_page($this->ITEMS_VIEW, $items);
			}
			return;
		}


		$collection_id = $this->get_collection_id($atributos['tainacan-url'], $atributos['collection-name']);
		if(!$collection_id) return;

		$partial_url = 'wp-json/tainacan/v1/collections/';
		if(!empty($atributos['meta-name']) && !empty($atributos['meta-value']))
		{
			$return = $this->get_meta_id($atributos['tainacan-url'], $collection_id, $atributos['meta-name'], $atributos['meta-value']);
			if($return['type'] === $this->CATEGORY)
			{
				$categories_id = $return['result'];
				if(!empty($categories_id))
				{
					$url = $atributos['tainacan-url'].$partial_url.$collection_id."/items?filter[metadata][".$categories_id['meta_id']."][op]=".$atributos['meta-operation']."&filter[metadata][".$categories_id['meta_id']."][values][]=".$categories_id['category_id'];
				}
			}else
			{
				$ids = $return['result'];
				$atributos['meta-value'] = rawurlencode($atributos['meta-value']);
				$url = $atributos['tainacan-url'].$partial_url.$collection_id."/items?filter[metadata][".$ids['meta_id']."][op]=".$atributos['meta-operation']."&filter[metadata][".$ids['meta_id']."][values][]=".$atributos['meta-value'];
			}
		}else
		{
			$url = $atributos['tainacan-url'].$partial_url.$collection_id."/items/";
		}

		//Cache
		if(strcmp(strtolower($atributos['enable-cache']), "true") === 0 )
		{
			$cache_time = get_option($this->ITEMS_CACHE_TIME_INDEX);
			if($cache_time != $atributos['cache-time'])
			{
				update_option($this->ITEMS_CACHE_TIME_INDEX, $atributos['cache-time']);
				$cache_time = $atributos['cache-time'];
			}

			$cache = get_option($this->ITEMS_CACHE_INDEX);
			if(!empty($cache))
			{
				$items = $cache[$atributos['tainacan-url']][$atributos['collection-name']][$atributos['meta-name']][$atributos['meta-value']][$atributos['meta-operation']];

				if($items)
				{
					$created_cache_items_time = get_option($this->CREATION_TIME_ITEMS_CACHE_INDEX);
					$creation_time = $created_cache_items_time[$atributos['tainacan-url']][$atributos['collection-name']][$atributos['meta-name']][$atributos['meta-value']][$atributos['meta-operation']];
					$diff_time = $this->diff_dates_hours($creation_time, new DateTime());

					if($diff_time > $cache_time)
					{
						$items = false;
					}
				}
			}

			if(!$items)
			{
				$items = json_decode(file_get_contents($url))->items;
				$cache[$atributos['tainacan-url']][$atributos['collection-name']][$atributos['meta-name']][$atributos['meta-value']][$atributos['meta-operation']] = $items;

				update_option($this->ITEMS_CACHE_INDEX, $cache);

				$date = new DateTime();
				$created_cache_items_time = get_option($this->CREATION_TIME_ITEMS_CACHE_INDEX);
				$created_cache_items_time[$atributos['tainacan-url']][$atributos['collection-name']][$atributos['meta-name']][$atributos['meta-value']][$atributos['meta-operation']] = $date;
				update_option($this->CREATION_TIME_ITEMS_CACHE_INDEX, $created_cache_items_time);
			}
		}else
		{
			$items = json_decode(file_get_contents($url))->items;
		}

		echo $this->render_page($this->ITEMS_VIEW, $items);
	}

	public function get_meta_id($url, $collection_id, $required_meta_name, $required_meta_value)
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
								$meta_type = $this->CATEGORY;
							}
						}
					}
					else
					{
						$result['meta_id'] = $meta->id;
					}
				}
			}
		}

		return ['result' =>$result, 'type' => $meta_type];
	}

	/***************************************************** FUNCTIONS *****************************************************/
	public function render_page($view, $content = null)
	{
		ob_start();
		require ($view);
		$rendered_page = ob_get_clean();

		return $rendered_page;
	}

	public function render_template($content, $template, $type)
	{
		$template = stripslashes_deep($template);
		$page = '';
		if(strcmp($type, "items") === 0)
		{
			foreach($content as $item_metas)
			{
				$item_metas = $item_metas->item;
				$page .= $this->replace_in_template($template, $item_metas);
			}
		}
		else if(strcmp($type, "collection") === 0)
		{
			$page .= $this->replace_in_template($template, $content);
		}

		echo $page;
	}

	public function replace_in_template($template, $content)
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

		if(isset($content->cover))
		{
			$template = str_replace("{cover}", $content->cover, $template);
		}

		return $template;
	}

	public function get_collection_id($tainacan_url, $collection_name)
	{
		$cache = get_option($this->COL_ID_CACHE_INDEX);
		$collection_id = false;
		if(!empty($cache))
		{
			$collection_id = $cache[$tainacan_url][$collection_name];
		}

		if(!$collection_id)
		{
			$collection_info = $this->get_collection_info($tainacan_url, $collection_name);
			if($collection_info)
			{
				$collection_id = $collection_info->ID;
			}else return false;
		}

		return $collection_id;
	}

	public function get_collection_info($tainacan_url, $collection_name)
	{
		$collection_name = rawurlencode($collection_name);

		$url = $tainacan_url.'wp-json/tainacan/v1/collections?filter[title]='.$collection_name;
		$collection_info = json_decode(file_get_contents($url));

		if($collection_info)
		{
			$tainacan_shortcode_cache = get_option($this->COL_ID_CACHE_INDEX);
			$tainacan_shortcode_cache[$tainacan_url] = [$collection_name => $collection_info[0]->ID];
			update_option($this->COL_ID_CACHE_INDEX, $tainacan_shortcode_cache);

			return $collection_info[0];
		}else return false;
	}

	public function diff_dates_hours($before, $now)
	{
		$diff = date_diff($before, $now);

		$years = $diff->y * (365 * 30 * 24);
		$months = $diff->m * (30 * 24);
		$days = $diff->d * 24;
		$hours = $diff->h;
		$minutes = $diff->i / 60;

		return $years + $months + $days + $hours + $minutes;
	}
}
