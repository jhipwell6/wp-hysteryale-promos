<?php

namespace WP_HYG_Promos\Controllers;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Promos_Promo
{
	protected static $instance;

	public function __construct()
	{
		add_action( 'init', array( $this, 'maybe_expire_promos' ), 10 );
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function maybe_expire_promos()
	{
		// should this be done via cron?
	}

}
