<?php
/**
 * Plugin Name: Lana SEO
 * Plugin URI: https://lana.codes/product/lana-seo/
 * Description: Search Engine Optimization with automatic generation.
 * Version: 1.3.0
 * Author: Lana Codes
 * Author URI: https://lana.codes/
 * Text Domain: lana-seo
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();
define( 'LANA_SEO_VERSION', '1.3.0' );
define( 'LANA_SEO_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'LANA_SEO_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Language
 * load
 */
load_plugin_textdomain( 'lana-seo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Add plugin action links
 *
 * @param $links
 *
 * @return mixed
 */
function lana_seo_add_plugin_action_links( $links ) {

	$settings_url = esc_url( admin_url( 'options-general.php?page=lana-seo-settings.php' ) );

	/** add settings link */
	$settings_link = sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Settings', 'lana-seo' ) );
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lana_seo_add_plugin_action_links' );

/**
 * Lana SEO
 * add admin page
 */
function lana_seo_admin_menu() {
	add_options_page( __( 'Lana SEO Settings', 'lana-seo' ), __( 'Lana SEO', 'lana-seo' ), 'manage_options', 'lana-seo-settings.php', 'lana_seo_settings_page' );

	/** call register settings function */
	add_action( 'admin_init', 'lana_seo_register_settings' );
}

add_action( 'admin_menu', 'lana_seo_admin_menu' );

/**
 * Register settings
 */
function lana_seo_register_settings() {
	register_setting( 'lana-seo-settings-group', 'lana_seo_allowed_meta', array(
		'type'              => 'array',
		'sanitize_callback' => 'lana_seo_sanitize_array',
	) );
	register_setting( 'lana-seo-settings-group', 'lana_seo_allow_in_post_type', array(
		'type'              => 'array',
		'sanitize_callback' => 'lana_seo_sanitize_array',
	) );
	register_setting( 'lana-seo-settings-group', 'lana_seo_allow_in_taxonomy', array(
		'type'              => 'array',
		'sanitize_callback' => 'lana_seo_sanitize_array',
	) );
	register_setting( 'lana-seo-settings-group', 'lana_seo_automatic_generation_in', array(
		'type'              => 'array',
		'sanitize_callback' => 'lana_seo_sanitize_array',
	) );
}

/**
 * Lana SEO
 * get allowed meta
 * @return mixed
 */
function lana_seo_get_allowed_meta() {
	$allowed_meta = get_option( 'lana_seo_allowed_meta', array( 'og', 'dc' ) );

	return (array) apply_filters( 'lana_seo_allow_in_post_types', $allowed_meta );
}

/**
 * Lana SEO
 * get allow in post types
 * @return mixed
 */
function lana_seo_get_allow_in_post_types() {
	$allow_in_post_types = get_option( 'lana_seo_allow_in_post_type', array( 'post', 'page', 'attachment' ) );

	return (array) apply_filters( 'lana_seo_allow_in_post_types', $allow_in_post_types );
}

/**
 * Lana SEO
 * post types by support allow in post types
 *
 * @param $allow_in_post_types
 *
 * @return array
 */
function lana_seo_post_types_by_support_allow_in_post_types( $allow_in_post_types ) {

	/**
	 * add supported post types to allow in post types
	 * @var array $allow_in_post_types
	 */
	$allow_in_post_types = array_merge( $allow_in_post_types, get_post_types_by_support( 'lana-seo' ) );

	return $allow_in_post_types;
}

add_filter( 'lana_seo_allow_in_post_types', 'lana_seo_post_types_by_support_allow_in_post_types' );

/**
 * Lana SEO
 * get allow in taxonomies
 * @return mixed
 */
function lana_seo_get_allow_in_taxonomies() {
	$allow_in_taxonomies = get_option( 'lana_seo_allow_in_taxonomy', array( 'category', 'post_tag' ) );

	return (array) apply_filters( 'lana_seo_allow_in_taxonomies', $allow_in_taxonomies );
}

/**
 * Lana SEO
 * get post types with custom meta tags function
 * @return mixed
 */
function lana_seo_get_post_types_with_custom_meta_tags_function() {
	$post_types_with_custom_meta_tags_function = array( 'post', 'page', 'attachment' );

	return (array) apply_filters( 'lana_seo_post_types_with_custom_meta_tags_function', $post_types_with_custom_meta_tags_function );
}

/**
 * Lana SEO
 * sanitize array
 *
 * @param $values
 *
 * @return array
 */
function lana_seo_sanitize_array( $values ) {

	if ( ! is_array( $values ) ) {
		if ( empty( $values ) ) {
			return array();
		}

		$values = array( $values );
	}

	return $values;
}

/**
 * Lana SEO Settings page
 */
function lana_seo_settings_page() {
	global $wp_post_types, $wp_taxonomies;

	/**
	 * Get
	 * post types
	 */
	$post_types = get_post_types( array(
		'public' => true,
	) );

	/**
	 * Get
	 * taxonomies
	 */
	$taxonomies = get_taxonomies( array(
		'public' => true,
	) );

	unset( $taxonomies['post_format'] );

	/**
	 * Get
	 * options
	 */
	$allowed_meta = lana_seo_get_allowed_meta();

	$allow_in_post_types  = lana_seo_get_allow_in_post_types();
	$supported_post_types = get_post_types_by_support( 'lana-seo' );
	$allow_in_taxonomies  = lana_seo_get_allow_in_taxonomies();
	?>
    <div class="wrap">
        <h2><?php _e( 'Lana SEO Settings', 'lana-seo' ); ?></h2>

        <hr/>
        <a href="<?php echo esc_url( 'https://lana.codes/' ); ?>" target="_blank">
            <img src="<?php echo esc_url( LANA_SEO_DIR_URL . '/assets/img/plugin-header.png' ); ?>"
                 alt="<?php esc_attr_e( 'Lana Codes', 'lana-seo' ); ?>"/>
        </a>
        <hr/>

        <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'lana-seo-settings-group' ); ?>

            <h2 class="title"><?php _e( 'Meta Settings', 'lana-seo' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label>
							<?php _e( 'Allowed meta', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
                            <label for="lana-seo-allowed-meta-default">
                                <input type="checkbox" id="lana-seo-allowed-meta-default" value="default" checked
                                       disabled>
								<?php esc_html_e( 'Default Meta', 'lana-seo' ); ?>
                                <small>
                                    (<?php esc_html_e( 'required', 'lana-seo' ); ?>)
                                </small>
                            </label>
                            <br/>
                            <label for="lana-seo-allowed-meta-og">
                                <input type="checkbox" name="lana_seo_allowed_meta[]" id="lana-seo-allowed-meta-og"
                                       value="og" <?php checked( in_array( 'og', $allowed_meta ) ); ?>>
								<?php esc_html_e( 'Open Graph', 'lana-seo' ); ?>
                            </label>
                            <br/>
                            <label for="lana-seo-allowed-meta-dc">
                                <input type="checkbox" name="lana_seo_allowed_meta[]" id="lana-seo-allowed-meta-dc"
                                       value="dc" <?php checked( in_array( 'dc', $allowed_meta ) ); ?>>
								<?php esc_html_e( 'Dublin Core', 'lana-seo' ); ?>
                            </label>
                            <br/>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php _e( 'Post Type Settings', 'lana-seo' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label>
							<?php _e( 'Allow in', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
							<?php if ( ! empty( $post_types ) ) : ?>
								<?php foreach ( $post_types as $post_type ) : ?>
                                    <label for="<?php echo esc_attr( $post_type ); ?>">
                                        <input type="checkbox" name="lana_seo_allow_in_post_type[]"
                                               id="lana-seo-allow-in-post-type-<?php echo esc_attr( $post_type ); ?>"
                                               value="<?php echo esc_attr( $post_type ); ?>" <?php checked( in_array( $post_type, $allow_in_post_types ) ); ?>>
										<?php $post_type_object = $wp_post_types[ $post_type ]; ?>
										<?php echo esc_html( $post_type_object->labels->singular_name ); ?>
                                        <code><?php _e( 'post type', 'lana-seo' ); ?></code>
                                        <small>
                                            (<?php echo esc_html( $post_type ); ?>)
                                        </small>
										<?php if ( in_array( $post_type, $supported_post_types ) ): ?>
                                            <span title="<?php esc_attr_e( 'This checkbox is disabled because it can only be changed using PHP code', 'lana-seo' ); ?>">
											    <?php echo sprintf( __( ' - feature by <code>add_post_type_support()</code>', 'lana-seo' ) ); ?>
                                            </span>
										<?php endif; ?>
                                    </label>
                                    <br/>
								<?php endforeach; ?>
							<?php endif; ?>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-seo-automatic-generation-in-post">
							<?php _e( 'Automatic generation in', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <label for="post">
                            <input type="checkbox" name="lana_seo_automatic_generation_in[]"
                                   id="lana-seo-automatic-generation-in-post"
                                   value="post" <?php checked( in_array( 'post', get_option( 'lana_seo_automatic_generation_in', array() ) ) ); ?>>
							<?php esc_html_e( 'Post', 'lana-seo' ); ?>
                        </label>

                        <p class="description">
							<?php _e( 'If you haven\'t set a meta tag, the plugin automatically generate one from the existing data (post content, excerpt, post tag, featured image, ...)', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php _e( 'Taxonomy Settings', 'lana-seo' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label>
							<?php _e( 'Allow in', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
							<?php if ( ! empty( $taxonomies ) ) : ?>
								<?php foreach ( $taxonomies as $taxonomy ) : ?>
                                    <label for="<?php echo esc_attr( $taxonomy ); ?>">
                                        <input type="checkbox" name="lana_seo_allow_in_taxonomy[]"
                                               id="lana-seo-allow_in-taxonomy-<?php echo esc_attr( $taxonomy ); ?>"
                                               value="<?php echo esc_attr( $taxonomy ); ?>" <?php checked( in_array( $taxonomy, $allow_in_taxonomies ) ); ?>>
										<?php $taxonomy_object = $wp_taxonomies[ $taxonomy ]; ?>

										<?php
										/** get taxonomy post types */
										$taxonomy_post_type_names = array_map( function ( $taxonomy_post_type ) use ( $wp_post_types ) {
											$post_type_object = $wp_post_types[ $taxonomy_post_type ];

											return $post_type_object->labels->singular_name;
										}, $taxonomy_object->object_type );

										echo esc_html( implode( ', ', $taxonomy_post_type_names ) );
										?>
                                        <code><?php _e( 'post type', 'lana-seo' ); ?></code>
                                        - <?php echo esc_html( $taxonomy_object->labels->singular_name ); ?>
                                        <code><?php _e( 'taxonomy', 'lana-seo' ); ?></code>
                                        <small>
                                            (<?php echo esc_html( $taxonomy ); ?>)
                                        </small>
                                    </label>
                                    <br/>
								<?php endforeach; ?>
							<?php endif; ?>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary"
                       value="<?php esc_attr_e( 'Save Changes', 'lana-seo' ); ?>"/>
            </p>

        </form>
    </div>
	<?php
}

/**
 * Styles
 * load in admin
 */
function lana_seo_admin_styles() {

	wp_register_style( 'select2', LANA_SEO_DIR_URL . '/assets/libs/select2/css/select2.min.css', array(), '4.0.5' );
	wp_enqueue_style( 'select2' );

	wp_register_style( 'lana-seo', LANA_SEO_DIR_URL . '/assets/css/lana-seo-admin.css', array(), LANA_SEO_VERSION );
	wp_enqueue_style( 'lana-seo' );
}

add_action( 'admin_enqueue_scripts', 'lana_seo_admin_styles' );

/**
 * JavaScript
 * load in admin
 */
function lana_seo_admin_scripts() {

	wp_enqueue_media();

	/** select2 js */
	wp_register_script( 'select2', LANA_SEO_DIR_URL . '/assets/libs/select2/js/select2.min.js', array( 'jquery' ), '4.0.5' );
	wp_enqueue_script( 'select2' );

	/** lana seo admin js */
	wp_register_script( 'lana-seo-admin', LANA_SEO_DIR_URL . '/assets/js/lana-seo-admin.js', array(
		'jquery',
		'media-upload',
		'media-views',
		'select2',
	), LANA_SEO_VERSION, true );
	wp_enqueue_script( 'lana-seo-admin' );

	/** add l10n to lana seo admin js */
	wp_localize_script( 'lana-seo-admin', 'lana_seo_l10n', array(
		'select_image' => __( 'Select Image', 'lana-seo' ),
	) );
}

add_action( 'admin_enqueue_scripts', 'lana_seo_admin_scripts' );

/**
 * Callback to register
 * Lana SEO Metabox
 */
function lana_seo_add_meta_box() {

	$post_types = lana_seo_get_allow_in_post_types();

	add_meta_box( 'lana-seo-metabox', __( 'Search Engine Optimization', 'lana-seo' ), 'lana_seo_meta_box_render', $post_types );
}

add_action( 'add_meta_boxes', 'lana_seo_add_meta_box' );

/**
 * Render
 * Lana - SEO metabox
 *
 * @param $post
 */
function lana_seo_meta_box_render( $post ) {

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	/** check allow in option */
	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) :
		?>
        <p>
			<?php printf( __( 'Lana SEO is not allowed in this post type. Go to the <a href="%s">Settings</a> page to allow it.', 'lana-seo' ), esc_url( admin_url( 'options-general.php?page=lana-seo-settings.php' ) ) ); ?>
        </p>
		<?php
		return;
	endif;

	$allowed_meta = lana_seo_get_allowed_meta();

	wp_nonce_field( basename( __FILE__ ), 'lana_seo_nonce_field' );

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	/** default tags */
	if ( ! $tags ) {
		$tags = array(
			'lana_seo_meta_title'       => '',
			'lana_seo_meta_description' => '',
			'lana_seo_meta_keywords'    => array(),
			'lana_seo_og_title'         => '',
			'lana_seo_og_description'   => '',
			'lana_seo_og_image'         => '',
			'lana_seo_dc_title'         => '',
			'lana_seo_dc_description'   => '',
			'lana_seo_dc_subject'       => array(),
		);
	}

	if ( ! isset( $tags['lana_seo_meta_keywords'] ) || ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	if ( ! isset( $tags['lana_seo_dc_subject'] ) || ! is_array( $tags['lana_seo_dc_subject'] ) ) {
		$tags['lana_seo_dc_subject'] = array();
	}

	$meta_description_max_chars = 160;
	?>

    <h3>
		<?php _e( 'Default Meta', 'lana-seo' ); ?>
    </h3>
    <hr/>
    <div id="lana-seo-meta">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="lana-seo-meta-title">
						<?php _e( 'Meta Title', 'lana-seo' ); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="lana_seo_meta_title" id="lana-seo-meta-title" class="large-text"
                           value="<?php echo esc_attr( $tags['lana_seo_meta_title'] ); ?>"/>

                    <p class="description">
						<?php _e( 'Title on Default meta tag using Search Engine (e.g. Google)', 'lana-seo' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="lana-seo-meta-description">
						<?php _e( 'Meta Description', 'lana-seo' ); ?>
                    </label>
                </th>
                <td>
                    <textarea name="lana_seo_meta_description" id="lana-seo-meta-description" class="large-text"
                              rows="3"
                              maxlength="<?php echo esc_attr( $meta_description_max_chars ); ?>"><?php echo esc_textarea( $tags['lana_seo_meta_description'] ); ?></textarea>

                    <p class="description">
						<?php printf( __( '%s characters remaining', 'lana-seo' ), '<span id="lana-seo-meta-description-chars">' . $meta_description_max_chars . '</span>' ); ?>
                    </p>

                    <p class="description">
						<?php _e( 'Description on Default meta tag using Search Engine (e.g. Google)', 'lana-seo' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="lana-seo-meta-keywords">
						<?php _e( 'Meta Keywords', 'lana-seo' ); ?>
                    </label>
                </th>
                <td>
                    <select name="lana_seo_meta_keywords[]" id="lana-seo-meta-keywords" class="large-text"
                            data-select-type="select2" multiple>
						<?php if ( $tags['lana_seo_meta_keywords'] ) : ?>
							<?php foreach ( $tags['lana_seo_meta_keywords'] as $meta_keyword ) : ?>
                                <option value="<?php echo esc_attr( $meta_keyword ); ?>" selected>
									<?php echo $meta_keyword; ?>
                                </option>
							<?php endforeach; ?>
						<?php endif; ?>
                    </select>

                    <p class="description">
						<?php _e( 'Keywords on meta tag using Search Engine (e.g. Google). separated by a commas (,)', 'lana-seo' ); ?>
                    </p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

	<?php if ( in_array( 'og', $allowed_meta ) ): ?>
        <h3>
			<?php _e( 'Open Graph', 'lana-seo' ); ?>
        </h3>
        <hr/>
        <div id="lana-seo-og">
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="lana-seo-og-title">
							<?php _e( 'Open Graph Title', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="lana_seo_og_title" id="lana-seo-og-title" class="large-text"
                               value="<?php echo esc_attr( $tags['lana_seo_og_title'] ); ?>"/>

                        <p class="description">
							<?php _e( 'Title on Open Graph meta tag using Social Network (e.g. Facebook)', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-seo-og-description">
							<?php _e( 'Open Graph Description', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                    <textarea name="lana_seo_og_description" id="lana-seo-og-description" class="large-text"
                              rows="3"><?php echo esc_textarea( $tags['lana_seo_og_description'] ); ?></textarea>

                        <p class="description">
							<?php _e( 'Description on Open Graph meta tag using Social Network (e.g. Facebook)', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
                <tr class="image-row">
                    <th scope="row">
                        <label for="lana-seo-og-image">
							<?php _e( 'Open Graph Image', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
						<?php if ( $tags['lana_seo_og_image'] ) : ?>
                            <img class="lana-seo-og-image-preview"
                                 src="<?php echo esc_attr( $tags['lana_seo_og_image'] ); ?>" style="max-height:120px;"/>
                            <br/>
						<?php endif; ?>
                        <input type="text" name="lana_seo_og_image" id="lana-seo-og-image" class="regular-text"
                               value="<?php echo esc_attr( $tags['lana_seo_og_image'] ); ?>"/>
                        <input type="button" id="lana-seo-og-image-button" class="lana-seo-og-image-upload button"
                               value="<?php esc_attr_e( 'Upload Image', 'lana-seo' ); ?>"/>

                        <p class="description">
							<?php _e( 'Image on Open Graph meta tag using Social Network (e.g. Facebook).', 'lana-seo' ); ?>
                            <br/>
							<?php _e( 'The recommended image size is 1200 × 630px.', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
	<?php endif; ?>

	<?php if ( in_array( 'dc', $allowed_meta ) ): ?>
        <h3>
			<?php _e( 'Dublin Core', 'lana-seo' ); ?>
        </h3>
        <hr/>
        <div id="lana-seo-dc">
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="lana-seo-dc-title">
							<?php _e( 'Dublin Core Title', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" name="lana_seo_dc_title" id="lana-seo-dc-title" class="large-text"
                               value="<?php echo esc_attr( $tags['lana_seo_dc_title'] ); ?>"/>

                        <p class="description">
							<?php _e( 'Title on Dublic Core using Search Engine', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-seo-dc-description">
							<?php _e( 'Dublin Core Description', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                    <textarea name="lana_seo_dc_description" id="lana-seo-dc-description" class="large-text"
                              rows="3"><?php echo esc_textarea( $tags['lana_seo_dc_description'] ); ?></textarea>

                        <p class="description">
							<?php _e( 'Description on Dublic Core using Search Engine', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-seo-dc-subject">
							<?php _e( 'Dublin Core Subject', 'lana-seo' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_seo_dc_subject[]" id="lana-seo-dc-subject" class="large-text"
                                data-select-type="select2" multiple>
							<?php if ( $tags['lana_seo_dc_subject'] ) : ?>
								<?php foreach ( $tags['lana_seo_dc_subject'] as $dc_subject ) : ?>
                                    <option value="<?php echo esc_attr( $dc_subject ); ?>" selected>
										<?php echo $dc_subject; ?>
                                    </option>
								<?php endforeach; ?>
							<?php endif; ?>
                        </select>

                        <p class="description">
							<?php _e( 'Subject and Keywords on on Dublic Core using Search Engine. separated by a semicolon (;)', 'lana-seo' ); ?>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
	<?php endif; ?>
	<?php
}

/**
 * Lana - SEO
 * edit form field
 *
 * @param WP_Term $taxonomy
 */
function lana_seo_edit_term_form_fields( $taxonomy ) {

	$allow_in_taxonomies = lana_seo_get_allow_in_taxonomies();

	/** check allow in option */
	if ( ! in_array( $taxonomy->taxonomy, $allow_in_taxonomies ) ) {
		return;
	}

	$tags = get_option( $taxonomy->term_id . '_lana_seo_tags' );

	$allowed_meta = lana_seo_get_allowed_meta();

	/** default tags */
	if ( ! $tags ) {
		$tags = array(
			'lana_seo_meta_title'       => '',
			'lana_seo_meta_description' => '',
			'lana_seo_meta_keywords'    => array(),
			'lana_seo_og_title'         => '',
			'lana_seo_og_description'   => '',
			'lana_seo_og_image'         => '',
		);
	}

	if ( ! isset( $tags['lana_seo_meta_keywords'] ) || ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	$meta_description_max_chars = 160;
	?>

    <tr class="form-field lana-seo-title-wrap">
        <th colspan="2">
            <h2>
				<?php _e( 'Lana SEO', 'lana-seo' ); ?>
            </h2>
        </th>
    </tr>
    <tr class="form-field term-lana-seo-meta-title-wrap">
        <th scope="row">
            <label for="lana-seo-meta-title">
				<?php _e( 'Meta Title', 'lana-seo' ); ?>
            </label>
        </th>
        <td>
            <input type="text" name="lana_seo_meta_title" id="lana-seo-meta-title"
                   value="<?php echo esc_attr( $tags['lana_seo_meta_title'] ); ?>"/>

            <p class="description">
				<?php _e( 'Title on Default meta tag using Search Engine (e.g. Google)', 'lana-seo' ); ?>
            </p>
        </td>
    </tr>
    <tr class="form-field term-lana-seo-meta-description-wrap">
        <th scope="row">
            <label for="lana-seo-meta-description">
				<?php _e( 'Meta Description', 'lana-seo' ); ?>
            </label>
        </th>
        <td>
			<textarea name="lana_seo_meta_description" id="lana-seo-meta-description" rows="3"
                      maxlength="<?php echo esc_attr( $meta_description_max_chars ); ?>"><?php echo esc_textarea( $tags['lana_seo_meta_description'] ); ?></textarea>

            <p class="description">
				<?php printf( __( '%s characters remaining', 'lana-seo' ), '<span id="lana-seo-meta-description-chars">' . $meta_description_max_chars . '</span>' ); ?>
            </p>

            <p class="description">
				<?php _e( 'Description on Default meta tag using Search Engine (e.g. Google)', 'lana-seo' ); ?>
            </p>
        </td>
    </tr>
    <tr class="form-field term-lana-seo-meta-keywords-wrap">
        <th scope="row">
            <label for="lana-seo-meta-keywords">
				<?php _e( 'Meta Keywords', 'lana-seo' ); ?>
            </label>
        </th>
        <td>
            <select name="lana_seo_meta_keywords[]" id="lana-seo-meta-keywords" data-select-type="select2"
                    multiple>
				<?php if ( $tags['lana_seo_meta_keywords'] ) : ?>
					<?php foreach ( $tags['lana_seo_meta_keywords'] as $meta_keyword ) : ?>
                        <option value="<?php echo esc_attr( $meta_keyword ); ?>" selected>
							<?php echo $meta_keyword; ?>
                        </option>
					<?php endforeach; ?>
				<?php endif; ?>
            </select>

            <p class="description">
				<?php _e( 'Keywords on meta tag using Search Engine (e.g. Google). separated by a commas (,)', 'lana-seo' ); ?>
            </p>
        </td>
    </tr>

	<?php if ( in_array( 'og', $allowed_meta ) ): ?>
        <tr class="form-field term-lana-seo-og-title-wrap">
            <th scope="row">
                <label for="lana-seo-og-title">
					<?php _e( 'Open Graph Title', 'lana-seo' ); ?>
                </label>
            </th>
            <td>
                <input type="text" name="lana_seo_og_title" id="lana-seo-og-title"
                       value="<?php echo esc_attr( $tags['lana_seo_og_title'] ); ?>"/>

                <p class="description">
					<?php _e( 'Title on Open Graph meta tag using Social Network (e.g. Facebook)', 'lana-seo' ); ?>
                </p>
            </td>
        </tr>
        <tr class="form-field term-lana-seo-og-description-wrap">
            <th scope="row">
                <label for="lana-seo-og-description">
					<?php _e( 'Open Graph Description', 'lana-seo' ); ?>
                </label>
            </th>
            <td>
            <textarea name="lana_seo_og_description" id="lana-seo-og-description"
                      rows="3"><?php echo esc_textarea( $tags['lana_seo_og_description'] ); ?></textarea>

                <p class="description">
					<?php _e( 'Description on Open Graph meta tag using Social Network (e.g. Facebook)', 'lana-seo' ); ?>
                </p>
            </td>
        </tr>
        <tr class="form-field term-lana-seo-og-image-wrap image-row">
            <th scope="row">
                <label for="lana-seo-og-image">
					<?php _e( 'Open Graph Image', 'lana-seo' ); ?>
                </label>
            </th>
            <td>
				<?php if ( $tags['lana_seo_og_image'] ) : ?>
                    <img class="lana-seo-og-image-preview"
                         src="<?php echo esc_attr( $tags['lana_seo_og_image'] ); ?>" style="max-height:120px;"/>
                    <br/>
				<?php endif; ?>
                <input type="text" name="lana_seo_og_image" id="lana-seo-og-image" class="regular-text"
                       value="<?php echo esc_attr( $tags['lana_seo_og_image'] ); ?>"/>
                <input type="button" id="lana-seo-og-image-button" class="lana-seo-og-image-upload button"
                       value="<?php esc_attr_e( 'Upload Image', 'lana-seo' ); ?>"/>

                <p class="description">
					<?php _e( 'Image on Open Graph meta tag using Social Network (e.g. Facebook).', 'lana-seo' ); ?>
                    <br/>
					<?php _e( 'The recommended image size is 1200 × 630px.', 'lana-seo' ); ?>
                </p>
            </td>
        </tr>
	<?php endif; ?>
	<?php
}

add_action( 'category_edit_form_fields', 'lana_seo_edit_term_form_fields', 100 );
add_action( 'post_tag_edit_form_fields', 'lana_seo_edit_term_form_fields', 100 );

/**
 * Lana SEO
 * save tags
 *
 * @param $post_id
 * @param WP_Post $post
 */
function lana_seo_save_post( $post_id, $post ) {

	if ( ! isset( $_POST['lana_seo_nonce_field'] ) || ! wp_verify_nonce( $_POST['lana_seo_nonce_field'], basename( __FILE__ ) ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) {
		return;
	}

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	$allowed_meta = lana_seo_get_allowed_meta();

	/**
	 * Lana SEO
	 * default meta tags
	 */
	$lana_seo_meta_title       = sanitize_text_field( $_POST['lana_seo_meta_title'] );
	$lana_seo_meta_description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['lana_seo_meta_description'] ) ) );

	if ( ! isset( $_POST['lana_seo_meta_keywords'] ) || empty( $_POST['lana_seo_meta_keywords'] ) ) {
		$_POST['lana_seo_meta_keywords'] = array();
	}

	$_POST['lana_seo_meta_keywords'] = lana_seo_sanitize_array( $_POST['lana_seo_meta_keywords'] );
	$lana_seo_meta_keywords          = array_map( 'sanitize_text_field', $_POST['lana_seo_meta_keywords'] );

	/**
	 * Lana SEO
	 * og tags
	 */
	$lana_seo_og_title       = sanitize_text_field( $_POST['lana_seo_og_title'] );
	$lana_seo_og_description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['lana_seo_og_description'] ) ) );
	$lana_seo_og_image       = esc_url_raw( $_POST['lana_seo_og_image'] );

	/** not allowed - use exists */
	if ( ! in_array( 'og', $allowed_meta ) ) {
		$lana_seo_og_title       = $tags['lana_seo_og_title'];
		$lana_seo_og_description = $tags['lana_seo_og_description'];
		$lana_seo_og_image       = $tags['lana_seo_og_image'];
	}

	/**
	 * Lana SEO
	 * dc tags
	 */
	$lana_seo_dc_title       = sanitize_text_field( $_POST['lana_seo_dc_title'] );
	$lana_seo_dc_description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['lana_seo_dc_description'] ) ) );

	if ( ! isset( $_POST['lana_seo_dc_subject'] ) || empty( $_POST['lana_seo_dc_subject'] ) ) {
		$_POST['lana_seo_dc_subject'] = array();
	}

	$_POST['lana_seo_dc_subject'] = lana_seo_sanitize_array( $_POST['lana_seo_dc_subject'] );
	$lana_seo_dc_subject          = array_map( 'sanitize_text_field', $_POST['lana_seo_dc_subject'] );

	/** not allowed - use exists */
	if ( ! in_array( 'dc', $allowed_meta ) ) {
		$lana_seo_dc_title       = $tags['lana_seo_dc_title'];
		$lana_seo_dc_description = $tags['lana_seo_dc_description'];
		$lana_seo_dc_subject     = $tags['lana_seo_dc_subject'];
	}

	/**
	 * Update
	 * seo tags
	 */
	update_post_meta( $post->ID, 'lana_seo_tags', array(
		'lana_seo_meta_title'       => $lana_seo_meta_title,
		'lana_seo_meta_description' => $lana_seo_meta_description,
		'lana_seo_meta_keywords'    => $lana_seo_meta_keywords,
		'lana_seo_og_title'         => $lana_seo_og_title,
		'lana_seo_og_description'   => $lana_seo_og_description,
		'lana_seo_og_image'         => $lana_seo_og_image,
		'lana_seo_dc_title'         => $lana_seo_dc_title,
		'lana_seo_dc_description'   => $lana_seo_dc_description,
		'lana_seo_dc_subject'       => $lana_seo_dc_subject,
	) );
}

add_action( 'save_post', 'lana_seo_save_post', 10, 2 );

/**
 * Lana SEO
 * save tags
 *
 * @param $term_id
 * @param $tt_id
 * @param $taxonomy
 */
function lana_seo_save_term( $term_id, $tt_id, $taxonomy ) {

	$allow_in_taxonomies = lana_seo_get_allow_in_taxonomies();

	/** check allow in option */
	if ( ! in_array( $taxonomy, $allow_in_taxonomies ) ) {
		return;
	}

	if ( empty( $tt_id ) ) {
		return;
	}

	$tags = get_option( $term_id . '_lana_seo_tags' );

	$allowed_meta = lana_seo_get_allowed_meta();

	/**
	 * Lana SEO
	 * default meta tags
	 */
	$lana_seo_meta_title       = sanitize_text_field( $_POST['lana_seo_meta_title'] );
	$lana_seo_meta_description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['lana_seo_meta_description'] ) ) );

	if ( ! isset( $_POST['lana_seo_meta_keywords'] ) || empty( $_POST['lana_seo_meta_keywords'] ) ) {
		$_POST['lana_seo_meta_keywords'] = array();
	}

	$_POST['lana_seo_meta_keywords'] = lana_seo_sanitize_array( $_POST['lana_seo_meta_keywords'] );
	$lana_seo_meta_keywords          = array_map( 'sanitize_text_field', $_POST['lana_seo_meta_keywords'] );

	/**
	 * Lana SEO
	 * og tags
	 */
	$lana_seo_og_title       = sanitize_text_field( $_POST['lana_seo_og_title'] );
	$lana_seo_og_description = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['lana_seo_og_description'] ) ) );
	$lana_seo_og_image       = esc_url_raw( $_POST['lana_seo_og_image'] );

	/** not allowed - use exists */
	if ( ! in_array( 'og', $allowed_meta ) ) {
		$lana_seo_og_title       = $tags['lana_seo_og_title'];
		$lana_seo_og_description = $tags['lana_seo_og_description'];
		$lana_seo_og_image       = $tags['lana_seo_og_image'];
	}

	/**
	 * Update
	 * seo tags
	 */
	update_option( $term_id . '_lana_seo_tags', array(
		'lana_seo_meta_title'       => $lana_seo_meta_title,
		'lana_seo_meta_description' => $lana_seo_meta_description,
		'lana_seo_meta_keywords'    => $lana_seo_meta_keywords,
		'lana_seo_og_title'         => $lana_seo_og_title,
		'lana_seo_og_description'   => $lana_seo_og_description,
		'lana_seo_og_image'         => $lana_seo_og_image,
	) );
}

add_action( 'edit_term', 'lana_seo_save_term', 10, 3 );

/**
 * Lana SEO
 * add meta tags to wp_head()
 */
function lana_seo_add_post_meta_tags_to_head() {
	global $post;

	/** check page */
	if ( ! is_single() ) {
		return;
	}

	/** check post */
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	/** check post type */
	if ( 'post' != $post->post_type ) {
		return;
	}

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	/** check allow in option */
	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) {
		return;
	}

	/** automatic meta tag generate */
	if ( ! in_array( $post->post_type, get_option( 'lana_seo_automatic_generation_in', array() ) ) ) {
		add_filter( 'lana_seo_add_meta_tag_disable_default', '__return_true' );
	}

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	$allowed_meta = lana_seo_get_allowed_meta();

	/** validate tags */
	if ( ! is_array( $tags ) ) {

		$tags = array(
			'lana_seo_meta_title'       => '',
			'lana_seo_meta_description' => '',
			'lana_seo_meta_keywords'    => array(),
			'lana_seo_og_title'         => '',
			'lana_seo_og_description'   => '',
			'lana_seo_og_image'         => '',
			'lana_seo_dc_title'         => '',
			'lana_seo_dc_description'   => '',
			'lana_seo_dc_subject'       => array(),
		);
	}

	/**
	 * Validate
	 * tags
	 */
	if ( ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	if ( ! is_array( $tags['lana_seo_dc_subject'] ) ) {
		$tags['lana_seo_dc_subject'] = array();
	}

	/**
	 * Other
	 * vars
	 */
	$parents = get_post_ancestors( $post );
	$parent  = reset( $parents );

	$post_title = get_the_title( $post );

	$post_permalink = get_the_permalink( $post );

	$post_content_first_sentence = current( array_filter( explode( "\n", preg_replace( "/&#?[a-z0-9]+;/i", "", trim( $post->post_content ) ) ), function ( $content ) {
		return ! empty( $content );
	} ) );

	$post_content = mb_strimwidth( trim( strip_shortcodes( wp_strip_all_tags( $post_content_first_sentence ) ) ), 0, 160 );
	$post_excerpt = mb_strimwidth( $post->post_excerpt, 0, 160 );

	$post_tag_list = wp_get_post_terms( $post->ID, 'post_tag', array( 'fields' => 'names' ) );

	$medium_thumbnail_url = get_the_post_thumbnail_url( $post, 'medium' );
	$large_thumbnail_url  = get_the_post_thumbnail_url( $post, 'large' );

	$bloginfo_name     = get_bloginfo( 'name' );
	$bloginfo_language = get_bloginfo( 'language' );

	$wp_title = wp_title( '|', false );

	$locale = get_locale();


	/**
	 * Lana SEO
	 * default meta tags
	 */
	echo lana_seo_add_meta_tag( array(
		'name'    => 'description',
		'content' => $tags['lana_seo_meta_description'],
	), array( $post_content, $post_excerpt ) );

	/**
	 * Lana SEO
	 * og tags
	 */
	if ( in_array( 'og', $allowed_meta ) ) {

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:title',
			'content'  => $tags['lana_seo_og_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:description',
			'content'  => $tags['lana_seo_og_description'],
		), array(
			$tags['lana_seo_meta_description'],
			$post_excerpt,
			$post_content,
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:image',
			'content'  => $tags['lana_seo_og_image'],
		) );

		if ( $medium_thumbnail_url ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image',
				'content'  => $medium_thumbnail_url,
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:width',
				'content'  => lana_seo_get_image_width( 'medium' ),
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:height',
				'content'  => lana_seo_get_image_height( 'medium' ),
			) );
		}

		if ( $large_thumbnail_url ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image',
				'content'  => $large_thumbnail_url,
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:width',
				'content'  => lana_seo_get_image_width( 'large' ),
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:height',
				'content'  => lana_seo_get_image_height( 'large' ),
			) );
		}

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:type',
			'content'  => 'article',
		) );
		echo lana_seo_add_meta_tag( array(
			'property' => 'article:published_time',
			'content'  => get_the_date( DATE_W3C ),
		) );
		echo lana_seo_add_meta_tag( array(
			'property' => 'article:modified_time',
			'content'  => get_the_modified_date( DATE_W3C ),
		) );

		/** keywords */
		if ( ! empty( $tags['lana_seo_meta_keywords'] ) ) {
			/** use meta keywords */
			foreach ( $tags['lana_seo_meta_keywords'] as $keyword ) {
				echo lana_seo_add_meta_tag( array(
					'property' => 'article:tag',
					'content'  => $keyword,
				) );
			}
		} else {
			/** use post tag list */
			if ( ! empty( $post_tag_list ) ) {
				foreach ( $post_tag_list as $keyword ) {
					echo lana_seo_add_meta_tag( array(
						'property' => 'article:tag',
						'content'  => $keyword,
					) );
				}
			}
		}

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:updated_time',
			'content'  => get_the_modified_date( DATE_W3C ),
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:url',
			'content'  => $post_permalink,
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:site_name',
				'content'  => $bloginfo_name,
			) );
		}

		if ( $locale ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:locale',
				'content'  => $locale,
			) );
		}
	}

	/**
	 * Lana SEO
	 * dc tags
	 */
	if ( in_array( 'dc', $allowed_meta ) ) {

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.title',
			'content' => $tags['lana_seo_dc_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.description',
			'content' => $tags['lana_seo_dc_description'],
		), array( $tags['lana_seo_meta_description'], $post_content, $post_excerpt ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.subject',
			'content' => implode( '; ', $tags['lana_seo_dc_subject'] ),
		), array( implode( '; ', $tags['lana_seo_meta_keywords'] ), implode( '; ', $post_tag_list ) ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.identifier',
			'content' => $post_permalink,
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.date',
			'content' => get_the_date( 'Y-m-d', $post ),
			'scheme'  => 'W3CDTF',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.type',
			'content' => 'Text',
			'scheme'  => 'DCMIType',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.format',
			'content' => 'text/html',
			'scheme'  => 'IMT',
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.publisher',
				'content' => $bloginfo_name,
			) );
		}

		if ( $parent ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.source',
				'content' => get_the_permalink( $parent ),
			) );
		}

		if ( $bloginfo_language ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.language',
				'content' => $bloginfo_language,
				'scheme'  => 'RFC1766',
			) );
		}
	}
}

add_action( 'wp_head', 'lana_seo_add_post_meta_tags_to_head' );

/**
 * Lana SEO
 * add meta tags to wp_head()
 */
function lana_seo_add_page_meta_tags_to_head() {
	global $post;

	/** check page */
	if ( ! is_home() && ! is_front_page() && ! is_page() ) {
		return;
	}

	/** check post */
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	/** check post type */
	if ( 'page' != $post->post_type ) {
		return;
	}

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	/** check allow in option */
	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) {
		return;
	}

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	$allowed_meta = lana_seo_get_allowed_meta();

	/** validate tags */
	if ( ! is_array( $tags ) ) {

		$tags = array(
			'lana_seo_meta_title'       => '',
			'lana_seo_meta_description' => '',
			'lana_seo_meta_keywords'    => array(),
			'lana_seo_og_title'         => '',
			'lana_seo_og_description'   => '',
			'lana_seo_og_image'         => '',
			'lana_seo_dc_title'         => '',
			'lana_seo_dc_description'   => '',
			'lana_seo_dc_subject'       => array(),
		);
	}

	/**
	 * Validate
	 * tags
	 */
	if ( ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	if ( ! is_array( $tags['lana_seo_dc_subject'] ) ) {
		$tags['lana_seo_dc_subject'] = array();
	}

	/**
	 * Other
	 * vars
	 */
	$parents = get_post_ancestors( $post );
	$parent  = reset( $parents );

	$post_title = get_the_title( $post );

	$post_permalink = get_the_permalink( $post );

	$medium_thumbnail_url = get_the_post_thumbnail_url( $post, 'medium' );
	$large_thumbnail_url  = get_the_post_thumbnail_url( $post, 'large' );

	$bloginfo_name     = get_bloginfo( 'name' );
	$bloginfo_language = get_bloginfo( 'language' );

	$wp_title = wp_title( '|', false );

	$locale = get_locale();

	/**
	 * Lana SEO
	 * default meta tags
	 */
	echo lana_seo_add_meta_tag( array(
		'name'    => 'description',
		'content' => $tags['lana_seo_meta_description'],
	) );

	/**
	 * Lana SEO
	 * og tags
	 */
	if ( in_array( 'og', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'property' => 'og:title',
			'content'  => $tags['lana_seo_og_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:description',
			'content'  => $tags['lana_seo_og_description'],
		), array( $tags['lana_seo_meta_description'] ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:image',
			'content'  => $tags['lana_seo_og_image'],
		) );

		if ( $medium_thumbnail_url ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image',
				'content'  => $medium_thumbnail_url,
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:width',
				'content'  => lana_seo_get_image_width( 'medium' ),
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:height',
				'content'  => lana_seo_get_image_height( 'medium' ),
			) );
		}

		if ( $large_thumbnail_url ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image',
				'content'  => $large_thumbnail_url,
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:width',
				'content'  => lana_seo_get_image_width( 'large' ),
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:height',
				'content'  => lana_seo_get_image_height( 'large' ),
			) );
		}

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:type',
			'content'  => 'website',
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:updated_time',
			'content'  => get_the_modified_date( DATE_W3C ),
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:url',
			'content'  => $post_permalink,
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:site_name',
				'content'  => $bloginfo_name,
			) );
		}

		if ( $locale ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:locale',
				'content'  => $locale,
			) );
		}
	}

	/**
	 * Lana SEO
	 * dc tags
	 */
	if ( in_array( 'dc', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.title',
			'content' => $tags['lana_seo_dc_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.description',
			'content' => $tags['lana_seo_dc_description'],
		), array( $tags['lana_seo_meta_description'] ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.subject',
			'content' => implode( '; ', $tags['lana_seo_dc_subject'] ),
		), array( implode( '; ', $tags['lana_seo_meta_keywords'] ) ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.identifier',
			'content' => $post_permalink,
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.date',
			'content' => get_the_date( 'Y-m-d', $post ),
			'scheme'  => 'W3CDTF',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.type',
			'content' => 'Text',
			'scheme'  => 'DCMIType',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.format',
			'content' => 'text/html',
			'scheme'  => 'IMT',
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.publisher',
				'content' => $bloginfo_name,
			) );
		}

		if ( $parent ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.source',
				'content' => get_the_permalink( $parent ),
			) );
		}

		if ( $bloginfo_language ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.language',
				'content' => $bloginfo_language,
				'scheme'  => 'RFC1766',
			) );
		}
	}
}

add_action( 'wp_head', 'lana_seo_add_page_meta_tags_to_head' );

/**
 * Lana SEO
 * add meta tags to wp_head()
 */
function lana_seo_add_attachment_meta_tags_to_head() {
	global $post;

	/** check post */
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	/** check post type */
	if ( ! is_attachment() ) {
		return;
	}

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	/** check allow in option */
	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) {
		return;
	}

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	$allowed_meta = lana_seo_get_allowed_meta();

	/** validate tags */
	if ( ! is_array( $tags ) ) {

		$tags = array(
			'lana_seo_meta_title'       => '',
			'lana_seo_meta_description' => '',
			'lana_seo_meta_keywords'    => array(),
			'lana_seo_og_title'         => '',
			'lana_seo_og_description'   => '',
			'lana_seo_og_image'         => '',
			'lana_seo_dc_title'         => '',
			'lana_seo_dc_description'   => '',
			'lana_seo_dc_subject'       => array(),
		);
	}

	/**
	 * Validate
	 * tags
	 */
	if ( ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	if ( ! is_array( $tags['lana_seo_dc_subject'] ) ) {
		$tags['lana_seo_dc_subject'] = array();
	}

	/**
	 * Other
	 * vars
	 */
	$post_title   = get_the_title( $post );
	$post_content = mb_strimwidth( trim( strip_shortcodes( wp_strip_all_tags( $post->post_content ) ) ), 0, 160 );

	$post_permalink = get_the_permalink( $post );

	$post_mime_type = get_post_mime_type( $post );

	$bloginfo_name     = get_bloginfo( 'name' );
	$bloginfo_language = get_bloginfo( 'language' );

	$wp_title = wp_title( '|', false );

	$locale = get_locale();

	/**
	 * Lana SEO
	 * default meta tags
	 */
	echo lana_seo_add_meta_tag( array(
		'name'    => 'description',
		'content' => $tags['lana_seo_meta_description'],
	), array( $post_content ) );

	/**
	 * Lana SEO
	 * og tags
	 */
	if ( in_array( 'og', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'property' => 'og:title',
			'content'  => $tags['lana_seo_og_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:description',
			'content'  => $tags['lana_seo_og_description'],
		), array( $tags['lana_seo_meta_description'], $post_content ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:image',
			'content'  => $tags['lana_seo_og_image'],
		) );

		if ( wp_attachment_is_image( $post ) ) {

			$image_metadata = wp_get_attachment_metadata( $post->ID );

			$attachment_url           = wp_get_attachment_url( $post->ID );
			$attachment_thumbnail_url = wp_get_attachment_thumb_url( $post->ID );

			if ( $attachment_url ) {

				echo lana_seo_add_meta_tag( array(
					'property' => 'og:image',
					'content'  => $attachment_url,
				) );
				echo lana_seo_add_meta_tag( array(
					'property' => 'og:image:width',
					'content'  => $image_metadata['width'],
				) );
				echo lana_seo_add_meta_tag( array(
					'property' => 'og:image:height',
					'content'  => $image_metadata['height'],
				) );
			}

			if ( $attachment_thumbnail_url ) {

				echo lana_seo_add_meta_tag( array(
					'property' => 'og:image',
					'content'  => $attachment_thumbnail_url,
				) );
				echo lana_seo_add_meta_tag( array(
					'property' => 'og:image:width',
					'content'  => $image_metadata['sizes']['thumbnail']['width'],
				) );
				echo lana_seo_add_meta_tag( array(
					'property' => 'og:image:height',
					'content'  => $image_metadata['sizes']['thumbnail']['height'],
				) );
			}
		}

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:type',
			'content'  => $post_mime_type,
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:updated_time',
			'content'  => get_the_modified_date( DATE_W3C ),
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:url',
			'content'  => $post_permalink,
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:site_name',
				'content'  => $bloginfo_name,
			) );
		}

		if ( $locale ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:locale',
				'content'  => $locale,
			) );
		}
	}

	/**
	 * Lana SEO
	 * dc tags
	 */
	if ( in_array( 'dc', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.title',
			'content' => $tags['lana_seo_dc_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.description',
			'content' => $tags['lana_seo_dc_description'],
		), array( $tags['lana_seo_meta_description'], $post_content ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.subject',
			'content' => implode( '; ', $tags['lana_seo_dc_subject'] ),
		), array( implode( '; ', $tags['lana_seo_meta_keywords'] ) ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.identifier',
			'content' => $post_permalink,
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.date',
			'content' => get_the_date( 'Y-m-d', $post ),
			'scheme'  => 'W3CDTF',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.type',
			'content' => 'Text',
			'scheme'  => 'DCMIType',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.format',
			'content' => 'text/html',
			'scheme'  => 'IMT',
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.publisher',
				'content' => $bloginfo_name,
			) );
		}

		if ( $bloginfo_language ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.language',
				'content' => $bloginfo_language,
				'scheme'  => 'RFC1766',
			) );
		}
	}
}

add_action( 'wp_head', 'lana_seo_add_attachment_meta_tags_to_head' );

/**
 * Lana SEO
 * add meta tags to wp_head()
 */
function lana_seo_add_custom_post_type_meta_tags_to_head() {
	global $post;

	/** check page */
	if ( ! is_single() ) {
		return;
	}

	/** check post */
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return;
	}

	$post_types_with_custom_meta_tags_function = lana_seo_get_post_types_with_custom_meta_tags_function();

	/** check that the post type has no custom function */
	if ( in_array( $post->post_type, $post_types_with_custom_meta_tags_function ) ) {
		return;
	}

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	/** check allow in option */
	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) {
		return;
	}

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	$allowed_meta = lana_seo_get_allowed_meta();

	/** validate tags */
	if ( ! is_array( $tags ) ) {

		$tags = array(
			'lana_seo_meta_title'       => '',
			'lana_seo_meta_description' => '',
			'lana_seo_meta_keywords'    => array(),
			'lana_seo_og_title'         => '',
			'lana_seo_og_description'   => '',
			'lana_seo_og_image'         => '',
			'lana_seo_dc_title'         => '',
			'lana_seo_dc_description'   => '',
			'lana_seo_dc_subject'       => array(),
		);
	}

	/**
	 * Validate
	 * tags
	 */
	if ( ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	if ( ! is_array( $tags['lana_seo_dc_subject'] ) ) {
		$tags['lana_seo_dc_subject'] = array();
	}

	/**
	 * Other
	 * vars
	 */
	$parents = get_post_ancestors( $post );
	$parent  = reset( $parents );

	$post_title = get_the_title( $post );

	$post_permalink = get_the_permalink( $post );

	$medium_thumbnail_url = get_the_post_thumbnail_url( $post, 'medium' );
	$large_thumbnail_url  = get_the_post_thumbnail_url( $post, 'large' );

	$bloginfo_name     = get_bloginfo( 'name' );
	$bloginfo_language = get_bloginfo( 'language' );

	$wp_title = wp_title( '|', false );

	$locale = get_locale();

	/**
	 * Lana SEO
	 * default meta tags
	 */
	echo lana_seo_add_meta_tag( array(
		'name'    => 'description',
		'content' => $tags['lana_seo_meta_description'],
	) );

	/**
	 * Lana SEO
	 * og tags
	 */
	if ( in_array( 'og', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'property' => 'og:title',
			'content'  => $tags['lana_seo_og_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:description',
			'content'  => $tags['lana_seo_og_description'],
		), array( $tags['lana_seo_meta_description'] ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:image',
			'content'  => $tags['lana_seo_og_image'],
		) );

		if ( $medium_thumbnail_url ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image',
				'content'  => $medium_thumbnail_url,
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:width',
				'content'  => lana_seo_get_image_width( 'medium' ),
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:height',
				'content'  => lana_seo_get_image_height( 'medium' ),
			) );
		}

		if ( $large_thumbnail_url ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image',
				'content'  => $large_thumbnail_url,
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:width',
				'content'  => lana_seo_get_image_width( 'large' ),
			) );
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:image:height',
				'content'  => lana_seo_get_image_height( 'large' ),
			) );
		}

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:type',
			'content'  => 'website',
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:updated_time',
			'content'  => get_the_modified_date( DATE_W3C ),
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:url',
			'content'  => $post_permalink,
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:site_name',
				'content'  => $bloginfo_name,
			) );
		}

		if ( $locale ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:locale',
				'content'  => $locale,
			) );
		}
	}

	/**
	 * Lana SEO
	 * dc tags
	 */
	if ( in_array( 'dc', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.title',
			'content' => $tags['lana_seo_dc_title'],
		), array( $tags['lana_seo_meta_title'], $post_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.description',
			'content' => $tags['lana_seo_dc_description'],
		), array( $tags['lana_seo_meta_description'] ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.subject',
			'content' => implode( '; ', $tags['lana_seo_dc_subject'] ),
		), array( implode( '; ', $tags['lana_seo_meta_keywords'] ) ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.identifier',
			'content' => $post_permalink,
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.date',
			'content' => get_the_date( 'Y-m-d', $post ),
			'scheme'  => 'W3CDTF',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.type',
			'content' => 'Text',
			'scheme'  => 'DCMIType',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.format',
			'content' => 'text/html',
			'scheme'  => 'IMT',
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.publisher',
				'content' => $bloginfo_name,
			) );
		}

		if ( $parent ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.source',
				'content' => get_the_permalink( $parent ),
			) );
		}

		if ( $bloginfo_language ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.language',
				'content' => $bloginfo_language,
				'scheme'  => 'RFC1766',
			) );
		}
	}
}

add_action( 'wp_head', 'lana_seo_add_custom_post_type_meta_tags_to_head' );

/**
 * Lana SEO
 * meta title tag to wp title
 *
 * @param $title
 *
 * @return mixed
 */
function lana_seo_add_meta_tags_to_title( $title ) {
	global $post;

	/** check page */
	if ( ! is_home() && ! is_front_page() && ! is_single() && ! is_page() ) {
		return $title;
	}

	/** check feed */
	if ( is_feed() ) {
		return $title;
	}

	/** check post */
	if ( ! is_a( $post, 'WP_Post' ) ) {
		return $title;
	}

	$allow_in_post_types = lana_seo_get_allow_in_post_types();

	/** check allow in option */
	if ( ! in_array( $post->post_type, $allow_in_post_types ) ) {
		return $title;
	}

	$tags = get_post_meta( $post->ID, 'lana_seo_tags', true );

	/** meta title */
	if ( isset( $tags['lana_seo_meta_title'] ) && ! empty( $tags['lana_seo_meta_title'] ) ) {
		$title = $tags['lana_seo_meta_title'];
	}

	return $title;
}

add_filter( 'wp_title', 'lana_seo_add_meta_tags_to_title' );

/**
 * Lana SEO
 * add meta tags to wp_head()
 */
function lana_seo_add_term_meta_tags_to_head() {

	/** check page */
	if ( ! is_tax() && ! is_category() && ! is_tag() ) {
		return;
	}

	/** @var WP_Term $term */
	$term = get_queried_object();

	if ( ! is_a( $term, 'WP_Term' ) ) {
		return;
	}

	$allow_in_taxonomies = lana_seo_get_allow_in_taxonomies();

	/** check allow in option */
	if ( ! in_array( $term->taxonomy, $allow_in_taxonomies ) ) {
		return;
	}

	$tags = get_option( $term->term_id . '_lana_seo_tags', array(
		'lana_seo_meta_title'       => '',
		'lana_seo_meta_description' => '',
		'lana_seo_meta_keywords'    => array(),
		'lana_seo_og_title'         => '',
		'lana_seo_og_description'   => '',
		'lana_seo_og_image'         => '',
	) );

	$allowed_meta = lana_seo_get_allowed_meta();

	/**
	 * Validate
	 * tags
	 */
	if ( ! is_array( $tags['lana_seo_meta_keywords'] ) ) {
		$tags['lana_seo_meta_keywords'] = array();
	}

	/**
	 * Other
	 * vars
	 */
	$parents = get_term( $term->parent );
	$parent  = reset( $parents );

	$term_title = $term->name;

	$term_permalink = get_term_link( $term, $term->taxonomy );

	$term_description = mb_strimwidth( trim( strip_shortcodes( wp_strip_all_tags( $term->description ) ) ), 0, 160 );

	$bloginfo_name     = get_bloginfo( 'name' );
	$bloginfo_language = get_bloginfo( 'language' );

	$wp_title = wp_title( '|', false );

	$locale = get_locale();

	/**
	 * Lana SEO
	 * default meta tags
	 */
	echo lana_seo_add_meta_tag( array(
		'name'    => 'description',
		'content' => $tags['lana_seo_meta_description'],
	), array( $term_description ) );

	/**
	 * Lana SEO
	 * og tags
	 */
	if ( in_array( 'og', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'property' => 'og:title',
			'content'  => $tags['lana_seo_og_title'],
		), array( $term_title, $tags['lana_seo_meta_title'], $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:description',
			'content'  => $tags['lana_seo_og_description'],
		), array(
			$tags['lana_seo_meta_description'],
			$term_description,
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:image',
			'content'  => $tags['lana_seo_og_image'],
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:updated_time',
			'content'  => get_the_modified_date( DATE_W3C ),
		) );

		echo lana_seo_add_meta_tag( array(
			'property' => 'og:url',
			'content'  => $term_permalink,
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:site_name',
				'content'  => $bloginfo_name,
			) );
		}

		if ( $locale ) {
			echo lana_seo_add_meta_tag( array(
				'property' => 'og:locale',
				'content'  => $locale,
			) );
		}
	}

	/**
	 * Lana SEO
	 * dc tags
	 */
	if ( in_array( 'dc', $allowed_meta ) ) {
		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.title',
			'content' => $tags['lana_seo_meta_title'],
		), array( $term_title, $wp_title ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.description',
			'content' => $tags['lana_seo_meta_description'],
		), array( $term_description ) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.subject',
			'content' => implode( '; ', $tags['lana_seo_meta_keywords'] ),
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.identifier',
			'content' => $term_permalink,
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.type',
			'content' => 'Text',
			'scheme'  => 'DCMIType',
		) );

		echo lana_seo_add_meta_tag( array(
			'name'    => 'DC.format',
			'content' => 'text/html',
			'scheme'  => 'IMT',
		) );

		if ( $bloginfo_name ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.publisher',
				'content' => $bloginfo_name,
			) );
		}

		if ( $parent && is_a( $parent, 'WP_Term' ) ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.source',
				'content' => get_term_link( $term, $term->taxonomy ),
			) );
		}

		if ( $bloginfo_language ) {
			echo lana_seo_add_meta_tag( array(
				'name'    => 'DC.language',
				'content' => $bloginfo_language,
				'scheme'  => 'RFC1766',
			) );
		}
	}
}

add_action( 'wp_head', 'lana_seo_add_term_meta_tags_to_head' );

/**
 * Lana SEO
 * meta title tag to wp title
 *
 * @param $title
 *
 * @return mixed
 */
function lana_seo_add_meta_tags_in_term_to_title( $title ) {

	if ( ! is_tax() && ! is_category() && ! is_tag() ) {
		return $title;
	}

	if ( is_feed() ) {
		return $title;
	}

	/** @var WP_Term $term */
	$term = get_queried_object();

	if ( ! is_a( $term, 'WP_Term' ) ) {
		return $title;
	}

	$allow_in_taxonomies = lana_seo_get_allow_in_taxonomies();

	/** check allow in option */
	if ( ! in_array( $term->taxonomy, $allow_in_taxonomies ) ) {
		return $title;
	}

	$tags = get_option( $term->term_id . '_lana_seo_tags' );

	/** meta title */
	if ( isset( $tags['lana_seo_meta_title'] ) && ! empty( $tags['lana_seo_meta_title'] ) ) {
		$title = $tags['lana_seo_meta_title'];
	}

	return $title;
}

add_filter( 'wp_title', 'lana_seo_add_meta_tags_in_term_to_title' );

/**
 * Add meta tag
 *
 * @param array $atts
 * @param string $default_content
 *
 * @return string
 */
function lana_seo_add_meta_tag( $atts = array(), $default_content = '' ) {

	/**
	 * If html5 theme
	 * remove scheme attribute
	 */
	if ( isset( $atts['scheme'] ) && current_theme_supports( 'html5' ) ) {
		unset( $atts['scheme'] );
	}

	/**
	 * Default content
	 */
	if ( empty( $atts['content'] ) ) {

		if ( apply_filters( 'lana_seo_add_meta_tag_disable_default', false, $atts, $default_content ) ) {
			return '';
		}

		if ( is_array( $default_content ) ) {
			$default_content = current( array_filter( $default_content ) );
		}

		$atts['content'] = $default_content;
	}

	$atts['content'] = trim( $atts['content'] );

	if ( empty( $atts['content'] ) ) {
		return '';
	}

	$attributes = '';

	foreach ( $atts as $attr => $value ) {
		if ( ! empty( $value ) ) {
			$value      = esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}
	}

	return '<meta' . $attributes . '>';
}

/**
 * Get size information for all currently-registered image sizes
 * @return array $sizes Data for all currently-registered image sizes
 * @uses get_intermediate_image_sizes()
 * @global $_wp_additional_image_sizes
 */
function lana_seo_get_image_sizes() {
	global $_wp_additional_image_sizes;

	$image_sizes = array();

	foreach ( get_intermediate_image_sizes() as $image_size ) {
		$size_data = array(
			'width'  => 0,
			'height' => 0,
			'crop'   => false,
		);

		if ( isset( $_wp_additional_image_sizes[ $image_size ]['width'] ) ) {
			// For sizes added by plugins and themes.
			$size_data['width'] = (int) $_wp_additional_image_sizes[ $image_size ]['width'];
		} else {
			// For default sizes set in options.
			$size_data['width'] = (int) get_option( "{$image_size}_size_w" );
		}

		if ( isset( $_wp_additional_image_sizes[ $image_size ]['height'] ) ) {
			$size_data['height'] = (int) $_wp_additional_image_sizes[ $image_size ]['height'];
		} else {
			$size_data['height'] = (int) get_option( "{$image_size}_size_h" );
		}

		if ( empty( $size_data['width'] ) && empty( $size_data['height'] ) ) {
			// This size isn't set.
			continue;
		}

		if ( isset( $_wp_additional_image_sizes[ $image_size ]['crop'] ) ) {
			$size_data['crop'] = $_wp_additional_image_sizes[ $image_size ]['crop'];
		} else {
			$size_data['crop'] = get_option( "{$image_size}_crop" );
		}

		if ( ! is_array( $size_data['crop'] ) || empty( $size_data['crop'] ) ) {
			$size_data['crop'] = (bool) $size_data['crop'];
		}

		$image_sizes[ $image_size ] = $size_data;
	}

	return $image_sizes;
}

/**
 * Get size information for a specific image size
 *
 * @param string $size The image size for which to retrieve data
 *
 * @return bool|array $size Size data about an image size or false if the size doesn't exist
 * @uses wp_get_registered_image_subsizes()
 * @uses lana_seo_get_image_sizes()
 */
function lana_seo_get_image_size( $size ) {

	if ( function_exists( 'wp_get_registered_image_subsizes' ) ) {
		$sizes = wp_get_registered_image_subsizes();
	} else {
		$sizes = lana_seo_get_image_sizes();
	}

	if ( isset( $sizes[ $size ] ) ) {
		return $sizes[ $size ];
	}

	return false;
}

/**
 * Get the width of a specific image size
 *
 * @param string $size The image size for which to retrieve data
 *
 * @return bool|string $size Width of an image size or false if the size doesn't exist
 * @uses lana_seo_get_image_size()
 */
function lana_seo_get_image_width( $size ) {

	$image_size = lana_seo_get_image_size( $size );

	if ( ! $image_size ) {
		return false;
	}

	if ( isset( $image_size['width'] ) ) {
		return $image_size['width'];
	}

	return false;
}

/**
 * Get the height of a specific image size
 *
 * @param string $size The image size for which to retrieve data
 *
 * @return bool|string $size Height of an image size or false if the size doesn't exist
 * @uses lana_seo_get_image_size()
 */
function lana_seo_get_image_height( $size ) {

	$image_size = lana_seo_get_image_size( $size );

	if ( ! $image_size ) {
		return false;
	}

	if ( isset( $image_size['height'] ) ) {
		return $image_size['height'];
	}

	return false;
}