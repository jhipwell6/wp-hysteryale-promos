<?php

namespace WP_HYG_Promos\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Promos_Promo_Admin
{

	use \WP_HYG_Promos\Traits\Cacheable_Trait;
	protected static $instance;

	const API_URL = 'https://hygmarketing.wpengine.com/wp-json/hyg/v1/promos';
	const AUTHKEY = '9aa44f27-d86c-43dc-b7fe-a03e39750725';

	public function __construct()
	{
		add_action( 'admin_menu', [ $this, 'add_menu' ], 1 );
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_menu()
	{
		$page = add_submenu_page(
			'edit.php?post_type=promo',
			'Promo Settings',
			'Settings',
			'manage_options',
			WP_HYG_PROMOS_TEXT_DOMAIN . '-promo-settings',
			[ $this, 'load_admin_template' ]
		);
	}

	public function load_admin_template()
	{
		$force_update = isset( $_GET['force_update'] );
		$available_promos = $this->get_available_promos( $force_update );
		$active_promo_ids = $this->get_active_promo_ids();
		?>
		<div class="wrap">
			<h2>Promo Settings</h2>
			<form method="get" action="">
		<?php include_once WP_HYG_Promos()->plugin_path() . '/includes/views/admin/promo-settings.php'; ?>
				<input type="hidden" name="post_type" value="promo" />
				<input type="hidden" name="page" value="<?php echo WP_HYG_PROMOS_TEXT_DOMAIN . '-promo-settings'; ?>" />
			</form>
		</div>
		<?php
	}

	private function get_available_promos( $force_update = false )
	{
		$cache_key = 'wp_hyg_promos_data';
		if ( $force_update ) {
			$this->flush_cache( $cache_key );
		} else {
			// check cache
			if ( $cache = $this->get_cache( $cache_key ) ) {
				return $cache;
			}
		}

		$response = wp_remote_get( self::API_URL, [
			'body' => [
				'authkey' => self::AUTHKEY,
				'posts_per_page' => 100,
			]
		] );

		if ( is_array( $response ) && ! is_wp_error( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
			$this->set_cache( $cache_key, $body );
			return json_decode( $body, true );
		}

		return [];
	}
	
	private function get_active_promo_ids()
	{
		$query = new \WP_Query( [
			'post_type' => 'promo',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
		] );
		
		return array_map( function( $post_id ) {
			$Promo = WP_HYG_Promos()->Promo( $post_id );
			return $Promo->get_promo_id();
		}, $query->posts );
	}

	private function is_settings_action( $action )
	{
		$current_screen = get_current_screen();
		return ( is_admin() && $current_screen->id == 'promo_page_wp-hyg-promos-promo-settings' && isset( $_GET[$action] ) );
	}

}

HYG_Promos_Promo_Admin::instance();