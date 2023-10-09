<?php

namespace WP_HYG_Promos\Models;

use \WP_HYG_Promos\Models\Abstracts\Post_Model;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Promo extends Post_Model
{
	const POST_TYPE = 'promo';
	const UNIQUE_KEY = 'promo_id';
	const WP_PROPS = array(
		'post_title' => 'title',
		'post_content' => 'description',
		'post_date' => 'date',
	);
	const ALIASES = array(
	);
	const HIDDEN = array(
	);
	
	// Stored
	protected $title;
	protected $description;
	protected $date;
	protected $promo_id;
	protected $embed_code;
	protected $expiration;
	
	/*
	 * Getters
	 */

	public function get_title()
	{
		return $this->get_post_title();
	}

	public function get_description( $apply_filters = false )
	{
		return $this->get_post_content( $apply_filters );
	}

	public function get_date( $format = 'Y-m-d h:i:s' )
	{
		return $this->get_post_date( $format );
	}

	public function get_promo_id()
	{
		return $this->get_prop( 'promo_id' );
	}
	
	public function get_embed_code()
	{
		return $this->get_prop( 'embed_code' );
	}
	
	public function get_expiration()
	{
		return $this->get_prop( 'expiration' );
	}
	
	/*
	 * Setters
	 */

	public function set_title( $value )
	{
		return $this->set_prop( 'title', $value );
	}

	public function set_description( $value )
	{
		return $this->set_prop( 'description', $value );
	}

	public function set_date( $value, $format = 'Y-m-d h:i:s' )
	{
		return $this->set_prop( 'date', $this->to_datetime( $value, $format ) );
	}
	
	public function set_promo_id( $value )
	{
		return $this->set_prop( 'promo_id', $value );
	}
	
	public function set_embed_code( $value )
	{
		return $this->set_prop( 'embed_code', $value );
	}
	
	public function set_expiration( $value )
	{
		return $this->set_prop( 'expiration', $value );
	}
	
	/*
	 * Savers
	 */

	public function save_title_meta( $value )
	{
		return $this->save_post_title( $value );
	}

	public function save_description_meta( $value )
	{
		if ( is_array( $value ) || ! $value ) {
			$value = ' ';
		}
		return $this->save_post_content( $value );
	}

	public function save_date_meta( $value, $return_format = '' )
	{
		return $this->save_post_date( $this->to_datetime( $value ), $return_format );
	}
	
	/*
	 * Helpers
	 */
	
	public function expire()
	{
		return wp_update_post( [
			'ID' => $this->get_id(),
			'post_status' => 'draft',
		] );
	}
}