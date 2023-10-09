<?php

namespace WP_HYG_Promos\Controllers;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Promos_Promo
{
	protected static $instance;

	public function __construct()
	{
		add_action( 'init', [ $this, 'add_promo_expiration_schedule' ], 10 );
		add_action( 'hyg_promos_expire_promos', [ $this, 'maybe_expire_promos' ], 10 );
		add_shortcode( 'hyg_promo_embed', [ $this, 'promo_embed_shortcode' ], 10, 1 );
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function add_promo_expiration_schedule()
	{
		$timestamp = wp_next_scheduled( 'hyg_promos_expire_promos' );
		if ( $timestamp == false ) {
			wp_schedule_event( time(), 'daily', 'hyg_promos_expire_promos' );
		}
	}

	public function maybe_expire_promos()
	{
		$promos = $this->get_promos_to_expire();
		if ( $promos ) {
			foreach ( $promos as $Promo ) {
				$Promo->expire();
			}
		}
	}
	
	public function promo_embed_shortcode( $atts )
	{
		$attributes = shortcode_atts( [
			'ID' => get_the_ID(),
		], $atts );
		
		$Promo = WP_HYG_Promos()->Promo( $attributes['ID'] );
		return $Promo->get_embed_code();
	}
	
	private function get_promos_to_expire()
	{
		$today = date('Ymd');
		$query = new \WP_Query( [
			'post_type' => 'promo',
			'posts_per_page' => -1,
			'meta_query' => [ 
				'relation' => 'AND',
				[
					'key' => 'expiration',
					'value' => '',
					'compare' => '!=',
					'type' => 'NUMERIC',
				],
				[
					'key' => 'expiration',
					'value' => $today,
					'compare' => '<',
					'type' => 'NUMERIC',
				],
			],
			'post_status' => 'publish',
			'fields' => 'ids',
		] );
		
		if ( $query->have_posts() ) {
			return array_map( function( $post_id ) {
				$Promo = WP_HYG_Promos()->Promo( $post_id );
				return $Promo;
			}, $query->posts );
		}
		
		return false;
	}

}

HYG_Promos_Promo::instance();