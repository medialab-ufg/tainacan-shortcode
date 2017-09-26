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

/*Refactor*/
function tainacan_shortcode_render_configuration_template()
{
	?>
	<div class="wrap">
		<form action="options.php" method="post">
			<?php
				settings_fields('tainacan_shortcode_templates_options');
				$options = get_option('tainacan_shortcode_templates');
			?>

			<div class="form-group">
				<h2>Items template</h2>
				<div>
					<h4>Informações dísponiveis</h4>
					<ul class="list-inline">
						<li>Título: <strong>{title}</strong></li>
						<li>Data do post: <strong>{date}</strong></li>
						<li>Conteúdo/Descrição: <strong>{content}</strong></li>
						<li>Data da ultima modificação: <strong>{last_modified}</strong></li>
						<li>Link: <strong>{link}</strong></li>
						<li>Quantidade de comentários: <strong>{comment_count}</strong></li>
						<li>Link da miniatura: <strong>{thumbnail}</strong></li>
						<li>Link da capa: <strong>{cover}</strong></li>
					</ul>
				</div>
				<textarea id="items-show-template" name="tainacan_shortcode_templates[items-show-template]" rows="15"><?php echo $options['items-show-template']; ?></textarea>
			</div>

			<div class="form-group">
				<h2>Collection template</h2>
				<textarea id="collection-show-template" name="tainacan_shortcode_templates[collection-show-template]" class="form-control" rows="15"><?php echo $options['collection-show-template']; ?></textarea>
			</div>
			<button type="submit" class="btn btn-primary btn-lg pull-right">Salvar</button>
		</form>

	</div>
	<?php
}

function tainacan_shortcode_get_template_configuration()
{
	register_setting("tainacan_shortcode_templates_options", "tainacan_shortcode_templates");
}

function tainacan_short_code_validate_template()
{
	return true;
}

function tainacan_shortcode_menu()
{
	add_options_page( 'Tainacan shortcode', 'Tainacan Shortcode', 'manage_options', 'tainacan-shortcode', 'tainacan_shortcode_render_configuration_template' );
}

add_action("admin_menu", "tainacan_shortcode_menu");
add_action("admin_init", "tainacan_shortcode_get_template_configuration");

require( "Tainacan_shortcode.php" );
$shortcode = new Tainacan_shortcode();