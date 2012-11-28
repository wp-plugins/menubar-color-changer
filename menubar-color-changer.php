<?php
/*
	Plugin Name: Menubar Color Changer
	Plugin URI: http://wordpress.org/extend/plugins/menubar-color-changer
	Description: Menubar Color Changer allows you to change the color of the black menubar in Twenty Eleven. It also adds the customizer to the appearance menu.
	Version: 1.0.3
	Author: Ole-Kenneth Rangnes
	Author URI: http://olekenneth.com
*/


class menubarColorChanger {

	function menuBarColorChanger() {
		load_plugin_textdomain('menubar-color-changer', false, basename( dirname( __FILE__ ) ) . '/languages' );

		add_action( 'wp_head', array(&$this, "menubar_wp_head") );
		add_filter( 'twentyeleven_theme_options_validate', array(&$this, "menubar_validate") , '', 2 );
		add_action( 'admin_notices', array(&$this, "menubar_admin_notices") );
		add_action( 'customize_register', array(&$this, "menubar_customize_register") );
		add_action( 'admin_head', array(&$this, "menubar_admin_head_customizer"));
	}

	function menubar_wp_head() {
		$options = twentyeleven_get_theme_options();
		$menubar_color = $options['menubar_color'];
		$with_fade = $options['with_fade'];
?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<style type="text/css">
		#access {
			background: <?php echo $menubar_color;
		if ($with_fade == 1) {
			?> url('<?php echo plugins_url( 'bg1.png' , __FILE__ )?>') repeat-x 0 0<?php
		} ?>;
		}
	</style>
<?php
	}


	function menubar_validate($output, $input) {
		if ( isset( $input['menubar_color'] ) && preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input['menubar_color'] ) ) {
			$output['menubar_color'] = '#' . strtolower( ltrim( $input['menubar_color'], '#' ) );
		}

		if ( isset( $input['with_fade'] )) {
			$output['with_fade'] = (!empty($input['with_fade'])) ? true : false;
		}

		return $output;
	}



	function menubar_admin_notices() {
		if ( !function_exists( 'twentyeleven_customize_register' ) ) {
			echo '
		<div class="error">
			<p>This plugin requires the Twenty Eleven theme version 1.3 or newer to function.</p>
		</div>';
		}

	}


	function menubar_customize_register($wp_customize) {
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
				'label'    => __( 'Add fading to menubar color', 'menubar_color' ),
				'section'  => 'colors',
				'settings' => 'twentyeleven_theme_options[with_fade]',
				'type'    => 'checkbox',
			) );
	}


	/**
	 * Add a customize link to the menu
	 * Modifies the $submenu global to add support for a link to the WordPress customizer
     * Modified by Ole-Kenneth to not throw errors when debug and editor role etc.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryan@pixopoint.com>
	 * @global array $submenu
	 */
	function menubar_admin_head_customizer() {
		global $submenu;
        
        if ((is_array($submenu)) && (isset($submenu['themes.php']))) {
            $submenu['themes.php'] = @array_merge( $submenu['themes.php'], array(array(
                'Customizer',
                'edit_theme_options',
                'customize.php',
                )));
            
        }
		return;
	}


}

new menubarColorChanger();