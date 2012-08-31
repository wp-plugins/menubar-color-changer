<?php
/*
	Plugin Name: Menubar Color Changer
	Plugin URI: http://wordpress.org/extend/plugins/menubar-color-changer
	Description: Menubar Color Changer allows you to change the color of the black menubar in Twenty Eleven. It also adds the customizer to the appearance menu.
	Version: 0.0.1
	Author: Ole-Kenneth Rangnes
	Author URI: http://olekenneth.com
*/

add_action( 'wp_head', function() {
		$options = twentyeleven_get_theme_options();
		$menubar_color = $options['menubar_color'];
		$with_fade = $options['with_fade'];
?>
	<style>
		#access {
			background: <?php echo $menubar_color;
		if ($with_fade == 1) {
			?> url('<?php echo plugins_url( 'bg1.png' , __FILE__ )?>') repeat-x 0 0<?php
		} ?>;
		}
	</style>
<?php
	});

add_filter( 'twentyeleven_theme_options_validate', function($output, $input) {
		// Link color must be 3 or 6 hexadecimal characters
		if ( isset( $input['menubar_color'] ) && preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input['menubar_color'] ) ) {
			$output['menubar_color'] = '#' . strtolower( ltrim( $input['menubar_color'], '#' ) );
		}

		if ( isset( $input['with_fade'] )) {
			$output['with_fade'] = (!empty($input['with_fade'])) ? true : false;
		}

		return $output;
	}, '', 2 );


add_action('admin_notices', function() {
		if ( !function_exists( 'twentyeleven_customize_register' ) ) {
			echo '
		<div class="error">
			<p>This plugin requires the Twenty Eleven theme version 1.3 or newer to function.</p>
		</div>';
		}

	});

add_action( 'customize_register', function( $wp_customize ) {
		$wp_customize->add_setting( 'twentyeleven_theme_options[menubar_color]', array(
				'default'           => '#fff',
				'type'              => 'option',
				'sanitize_callback' => 'sanitize_hex_color',
				'capability'        => 'edit_theme_options',
			) );

		$wp_customize->add_setting( 'twentyeleven_theme_options[with_fade]', array(
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
			) );


		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'menubar_color', array(
					'label'    => __( 'Menubar Color', 'menubar_color' ),
					'section'  => 'colors',
					'settings' => 'twentyeleven_theme_options[menubar_color]',
				) ) );

		$wp_customize->add_control( 'with_fade', array(
				'label'    => __( 'Show fade in menubar', 'menubar_color' ),
				'section'  => 'colors',
				'settings' => 'twentyeleven_theme_options[with_fade]',
				'type'    => 'checkbox',
			) );
	});

/**
 * Add a customize link to the menu
 * Modifies the $submenu global to add support for a link to the WordPress customizer
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @global array $submenu
 */
add_action( 'admin_head', function() {
	global $submenu;

	$submenu['themes.php'] = array_merge( $submenu['themes.php'], array(array(
		'Customizer',
		'edit_theme_options',
		'customize.php',
	)));

	return;
});
?>