<?php

/**
 * Side Cart
 *
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Side Cart Class.
 *
 */
class Merchant_Side_Cart extends Merchant_Add_Module {

	/**
	 * Module ID.
	 */
	const MODULE_ID = 'side-cart';

	/**
	 * Is module preview.
	 *
	 */
	public static $is_module_preview = false;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		// Module id.
		$this->module_id = self::MODULE_ID;

		// WooCommerce only.
		$this->wc_only = true;

		// Parent construct.
		parent::__construct();

		// Module default settings.
		$this->module_default_settings = array(
			'show_after_add_to_cart'                => 1,
			'show_after_add_to_cart_single_product' => 0,
			'show_on_cart_url_click'                => 1,
		);

		// Module data.
		$this->module_data                = Merchant_Admin_Modules::$modules_data[ self::MODULE_ID ];
		$this->module_data['preview_url'] = $this->set_module_preview_url( array( 'type' => 'shop' ) );

		// Module section.
		$this->module_section = $this->module_data['section'];

		// Module options path.
		$this->module_options_path = MERCHANT_DIR . 'inc/modules/' . self::MODULE_ID . '/admin/options.php';
		if ( Merchant_Modules::is_module_active( self::MODULE_ID ) && is_admin() ) {
			// Init translations.
			$this->init_translations();
		}
		// Is module preview page.
		if ( is_admin() && parent::is_module_settings_page() ) {
			self::$is_module_preview = true;

			// Enqueue admin styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_css' ) );

			// Enqueue admin scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Admin preview box.
			add_filter( 'merchant_module_preview', array( $this, 'render_admin_preview' ), 10, 2 );

			// Custom CSS.
			add_filter( 'merchant_custom_css', array( $this, 'admin_custom_css' ) );
		}
	}

	public function init_translations() {
		$settings = $this->get_module_settings();
		if ( ! empty( $settings['checkout_btn_text'] ) ) {
			Merchant_Translator::register_string( $settings['checkout_btn_text'], esc_html__( 'Side cart checkout button text', 'merchant' ) );
		}
		if ( ! empty( $settings['view_cart_btn_text'] ) ) {
			Merchant_Translator::register_string( $settings['view_cart_btn_text'], esc_html__( 'Side cart view cart button text', 'merchant' ) );
		}
		if ( ! empty( $settings['upsells_title'] ) ) {
			Merchant_Translator::register_string( $settings['upsells_title'], esc_html__( 'Side cart upsells title', 'merchant' ) );
		}
		if ( ! empty( $settings['upsells_add_to_cart_text'] ) ) {
			Merchant_Translator::register_string( $settings['upsells_add_to_cart_text'], esc_html__( 'Side cart upsells add to cart text', 'merchant' ) );
		}
	}

	/**
	 * Admin enqueue CSS.
	 *
	 * @return void
	 */
	public function admin_enqueue_css() {
		if ( $this->is_module_settings_page() ) {
			wp_enqueue_style( 'merchant-' . self::MODULE_ID, MERCHANT_URI . 'assets/css/modules/' . Merchant_Floating_Mini_Cart::MODULE_ID . '/floating-mini-cart.min.css', array(),
				MERCHANT_VERSION );
			wp_enqueue_style( 'merchant-admin-' . self::MODULE_ID,
				MERCHANT_URI . 'assets/css/modules/' . Merchant_Floating_Mini_Cart::MODULE_ID . '/admin/preview.min.css',
				array(),
				MERCHANT_VERSION );

			wp_enqueue_style( 'merchant-admin-preview-' . self::MODULE_ID,
				MERCHANT_URI . 'assets/css/modules/' . self::MODULE_ID . '/admin/preview.min.css',
				array(),
				MERCHANT_VERSION
			);
		}
	}

	/**
	 * Admin Enqueue scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		// Register and enqueue the main module script.
		wp_enqueue_script( 'merchant-' . self::MODULE_ID,
			MERCHANT_URI . 'assets/js/modules/' . Merchant_Floating_Mini_Cart::MODULE_ID . '/floating-mini-cart.min.js',
			array(),
			MERCHANT_VERSION,
			true );

		wp_enqueue_script( 'merchant-preview-' . self::MODULE_ID,
			MERCHANT_URI . 'assets/js/modules/' . self::MODULE_ID . '/admin/preview.min.js',
			array(),
			MERCHANT_VERSION,
			true );

		wp_localize_script( 'merchant-preview-' . self::MODULE_ID, 'merchant_side_cart_params', array(
			'keywords' => array(
				'multi_categories'     => esc_html__( 'Multi Categories', 'merchant' ),
				'category_trigger'     => esc_html__( 'Category Trigger:', 'merchant' ),
				'no_cats_selected'     => esc_html__( 'No Categories Selected', 'merchant' ),
				'no_products_selected' => esc_html__( 'No Products Selected', 'merchant' ),
				'multi_products'       => esc_html__( 'Multi Products', 'merchant' ),
				'all_products'         => esc_html__( 'All Products', 'merchant' ),
			),
		) );
	}


	/**
	 * Render admin preview
	 *
	 * @param Merchant_Admin_Preview $preview
	 * @param string                 $module
	 *
	 * @return Merchant_Admin_Preview
	 */
	public function render_admin_preview( $preview, $module ) {
		if ( $module === self::MODULE_ID ) {
			ob_start();
			self::admin_preview_content();
			$content = ob_get_clean();

			// HTML.
			$preview->set_html( $content );
		}

		return $preview;
	}

	/**
	 * Admin preview content.
	 *
	 * @return void
	 */
	public function admin_preview_content() {
		?>

        <div class="mrc-preview-single-product-elements">
            <div class="mrc-preview-left-column">
                <div class="mrc-preview-product-image-wrapper">
                    <div class="mrc-preview-product-image"></div>
                    <div class="mrc-preview-product-image-thumbs">
                        <div class="mrc-preview-product-image-thumb"></div>
                        <div class="mrc-preview-product-image-thumb"></div>
                        <div class="mrc-preview-product-image-thumb"></div>
                    </div>
                </div>
            </div>
            <div class="mrc-preview-right-column">
                <div class="mrc-preview-text-placeholder"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-70"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-30"></div>
                <div class="mrc-preview-text-placeholder"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-70"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-30"></div>
                <div class="mrc-preview-text-placeholder"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-70"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-30"></div>
                <div class="mrc-preview-text-placeholder"></div>
                <div class="mrc-preview-text-placeholder mrc-mw-70"></div>
                <div class="mrc-preview-addtocart-placeholder"></div>
            </div>
        </div>

        <div class="merchant-floating-side-mini-cart-show">
            <div class="merchant-floating-side-mini-cart-overlay merchant-floating-side-mini-cart-toggle"></div>
            <div class="merchant-floating-side-mini-cart">
                <div class="merchant-floating-side-mini-cart-body">
                    <a href="#" class="merchant-floating-side-mini-cart-close-button merchant-floating-side-mini-cart-toggle"
                        title="<?php
						echo esc_attr__( 'Close the side mini cart', 'merchant' ); ?>">
						<?php
						echo wp_kses( Merchant_SVG_Icons::get_svg_icon( 'icon-cancel' ), merchant_kses_allowed_tags( array(), false ) ); ?>
                    </a>

                    <div class="merchant-floating-side-mini-cart-widget">
                        <div class="merchant-floating-side-mini-cart-widget-title"><?php
							echo esc_html__( 'Your Cart', 'merchant' ); ?></div>
                        <div class="widget_shopping_cart_content">
                            <ul class="woocommerce-mini-cart cart_list product_list_widget">
                                <li class="woocommerce-mini-cart-item mini_cart_item">
                                    <a href="#" class="remove remove_from_cart_button">×</a>
                                    <a href="#">
                                        <span class="mrc-product-image"></span>
										<?php
										echo esc_html__( 'Product Sample Title', 'merchant' ); ?>
                                    </a>
                                    <span class="quantity">1 ×
										<span class="woocommerce-Price-amount amount">
											<bdi><span class="woocommerce-Price-currencySymbol">$</span>12.00 </bdi>
										</span>
									</span>
                                </li>
                                <li class="woocommerce-mini-cart-item mini_cart_item">
                                    <a href="#" class="remove remove_from_cart_button">×</a>
                                    <a href="#">
                                        <span class="mrc-product-image"></span>
										<?php
										echo esc_html__( 'Product Sample Title', 'merchant' ); ?>
                                    </a>
                                    <span class="quantity">1 ×
										<span class="woocommerce-Price-amount amount">
											<bdi><span class="woocommerce-Price-currencySymbol">$</span>12.00 </bdi>
										</span>
									</span>
                                </li>
                            </ul>

                            <p class="woocommerce-mini-cart__total total">
                                <strong><?php
									echo esc_html__( 'Subtotal:', 'merchant' ); ?></strong>
                                <span class="woocommerce-Price-amount amount">
									<bdi><span class="woocommerce-Price-currencySymbol">$</span>12.00 </bdi>
								</span>
                            </p>
                            <p class="woocommerce-mini-cart__buttons buttons">
                                <a href="#" class="button wc-forward"><?php echo esc_html__( 'View cart', 'merchant' ); ?></a>
                                <a href="#" class="button checkout wc-forward"><?php echo esc_html__( 'Checkout', 'merchant' ); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

		<?php
	}

	/**
	 * Admin custom CSS.
	 *
	 * @param string $css The custom CSS.
	 *
	 * @return string $css The custom CSS.
	 */
	public function admin_custom_css( $css ) {
		$css .= Merchant_Floating_Mini_Cart::get_module_custom_css();

		return $css;
	}
}

// Initialize the module.
add_action( 'init', function () {
	Merchant_Modules::create_module( new Merchant_Side_Cart() );
} );
