<?php
namespace WP_HYG_Promos\Core;

if ( ! defined('ABSPATH') )
    exit;

class Promo_Factory extends Abstracts\Factory
{
    private $found = array();
	
    /**
     * Get an item from the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */	
	public function get( $Promo = false, $default = null )
    {		
		$promo_id = $this->get_promo_id( $Promo );
		if ( $promo_id && $this->contains( 'id', $promo_id ) && $promo_id != 0 && ! in_array( $promo_id, $this->found ) ) {
			$this->found[] = $promo_id;
            return $this->where( 'id', $promo_id );
        }
		
		$Promo = new \WP_HYG_Promos\Models\Promo( $promo_id );
		$this->add( $Promo );
		
        return $this->last();
    }
	
	/**
	 * Get the promo ID depending on what was passed.
	 *
	 * @return int|bool false on failure
	 */
	private function get_promo_id( $Promo )
	{
		global $post;

		if ( false === $Promo && isset( $post, $post->ID ) && 'promo' === get_post_type( $post->ID ) ) {
			return absint( $post->ID );
		} elseif ( is_numeric( $Promo ) ) {
			return $Promo;
		} elseif ( $Promo instanceof \WP_HYG_Promos\Models\Promo ) {
			return $Promo->get_id();
		} elseif ( ! empty( $Promo->ID ) ) {
			return $Promo->ID;
		} else {
			return false;
		}
	}
}