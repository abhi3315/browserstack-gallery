<?php
/**
 * File to register custom setting page.
 *
 * @package Browserstack_Gallery
 */

namespace Browserstack_Gallery;

/**
 * Class to register custom setting page.
 */
class Settings {

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public static $menu_slug = 'browserstack-gallery';

	/**
	 * Get instance.
	 *
	 * @return Settings
	 */
	public static function get_instance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Initialize the class.
	 */
	public function init() {
		add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
	}

	/**
	 * Register settings page.
	 */
	public function register_settings_page() {
		add_menu_page(
			__( 'Browserstack Settings', 'browserstack-gallery' ),
			__( 'Browserstack', 'browserstack-gallery' ),
			'manage_options',
			self::$menu_slug,
			[ $this, 'render_settings_page' ],
			'dashicons-format-gallery',
			20
		);
	}

	/**
	 * Render settings page.
	 */
	public function render_settings_page() {
		wp_enqueue_media();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Browserstack Settings', 'browserstack-gallery' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( self::$menu_slug );
				do_settings_sections( self::$menu_slug );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings.
	 */
	public function register_settings() {
		register_setting(
			self::$menu_slug,
			self::$menu_slug,
			[ $this, 'sanitize_settings' ]
		);

		add_settings_section(
			self::$menu_slug . '-section',
			__( 'Gallery Settings', 'browserstack-gallery' ),
			[ $this, 'render_settings_section' ],
			self::$menu_slug
		);
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	public function sanitize_settings( $settings ) {
		return $settings;
	}

	/**
	 * Render settings section.
	 */
	public function render_settings_section() {
		?>
		<button id="create-gallery-btn" type="button" class="button"><?php __( 'Create New Gallery', 'browserstack-gallery' ); ?></button>
		<input id="setting-field" name="<?php echo esc_attr( self::$menu_slug ); ?>" value="<?php echo esc_attr( get_option( self::$menu_slug ) ); ?>" />
		<?php

		$gallery_settings = self::get_settings();

		if ( empty( $gallery_settings ) ) {
			return;
		}

		foreach ( $gallery_settings as $gallery_id => $images ) {
			/* Translators: %s: Gallery ID */
			$title = sprintf( __( 'Gallery (ID:%s)', 'browserstack-gallery' ), $gallery_id );
			?>
			<div class="gallery-container">
				<div class="gallery-container__header">
					<h3><?php echo esc_html( $title ); ?></h3>
					<div class="gallery-container__header--cta">
						<button type="button" class="button delete-gallery-btn" data-gallery-id="<?php echo esc_attr( $gallery_id ); ?>">
							<?php esc_html_e( 'Delete Gallery', 'browserstack-gallery' ); ?>
						</button>
						<button type="button" class="button add-gallery-image-btn" data-gallery-id="<?php echo esc_attr( $gallery_id ); ?>">
							<?php esc_html_e( 'Add Image', 'browserstack-gallery' ); ?>
						</button>
					</div>
				</div>
				<div class="gallery-container__content">
					<div class="gallery-container__images">
						<?php
						foreach ( $images as $image ) {
							?>
							<div class="gallery-container__image">
								<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
								<button type="button" class="delete-img-btn">
									<span class="dashicons dashicons-trash"></span>
								</button>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Get settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = get_option( self::$menu_slug, [] );

		if ( empty( $settings ) ) {
			return [];
		}

		$settings = json_decode( $settings, true );

		if ( empty( $settings ) ) {
			return [];
		}

		return $settings;
	}

	/**
	 * Get gallery.
	 *
	 * @param string $gallery_id Gallery ID.
	 *
	 * @return array
	 */
	public static function get_gallery( string $gallery_id ) {

		$gallery_settings = self::get_settings();

		if ( empty( $gallery_settings ) || empty( $gallery_settings[ $gallery_id ] ) ) {
			return [];
		}

		return $gallery_settings[ $gallery_id ];
	}
}