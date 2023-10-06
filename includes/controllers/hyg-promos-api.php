<?php

namespace WP_HYG_Promos\Controllers;

use \WP_REST_Posts_Controller;
use \WP_REST_Controller;
use \WP_REST_Server;
use \WP_REST_Response;
use \WP_REST_Request;
use \WP_Error;
use \WP_Query;

if ( ! defined( 'ABSPATH' ) )
	exit;

class HYG_Promos_API extends WP_REST_Posts_Controller
{
	const AUTHKEY = '83dccd31-8d23-49b1-88ed-c893374ffdf5';

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes()
	{
		$version = '1';
		$namespace = 'hyg-promos/v' . $version;
		$base = 'promos';

		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args' => $this->get_endpoint_args_for_item_schema( true ),
			),
		) );

		register_rest_route( $namespace, '/' . $base . '/schema', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_public_item_schema' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
		) );
	}
	
	/**
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request )
	{
		$Promo = $this->prepare_item_for_database( $request );
		
		if ( $Promo ) {
			// save to the database (create or update logic handled in the Model)
			$Promo = $Promo->save();
			if ( $Promo->get_id() > 0 ) {
				return new WP_REST_Response( $Promo->to_array(), 200 );
			} else {
				return new WP_Error( 'rest_cannot_create', __( 'Unable to create promo.', WP_HYG_PROMOS_TEXT_DOMAIN ), array( 'status' => 500 ) );
			}
		}

		return new WP_Error( 'rest_cannot_create', __( 'Unable to create promo.', WP_HYG_PROMOS_TEXT_DOMAIN ), array( 'status' => 500 ) );
	}

	/**
	 * Check if a given request has access to create a new item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function create_item_permissions_check( $request )
	{
		return $this->request_is_authenticated( $request );
	}

	/**
	 * Check if a given request has access
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	private function request_is_authenticated( $request )
	{
		$headers = $request->get_headers();

		if ( isset( $headers['authkey'] ) && in_array( self::AUTHKEY, $headers['authkey'] ) ) {
			return true;
		}

		//For self-submission/CORS issues, allow authkey in body also
		$params = $request->get_params();

		if ( isset( $params['authkey'] ) && self::AUTHKEY == $params['authkey'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request )
	{
		$Promo = $this->get_item_from_request( $request );
		if ( is_wp_error( $Promo ) ) {
			return $Promo;
		}
		return $Promo && $Promo->exists() ? $Promo->to_array() : false;
	}
	
	/**
	 * Prepare the item for create or update operation
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database( $request )
	{
		if ( is_a( $request, 'WP_REST_Request' ) ) {
			$params = $request->get_params();
		} else {
			$params = $request;
		}
		$Promo = $this->get_item_from_request( $request );

		// never pass an id in the body
		unset( $params['id'] );

		// set the properties
		$Promo->make( $params );

		return $Promo;
	}

	private function get_item_from_request( $request )
	{
		if ( ( is_array( $request ) || is_a( $request, 'WP_REST_Request' ) ) && isset( $request['id'] ) ) {
			$id = $request['id'];
			$post = $this->is_request_by_external_id( $request ) ? $this->get_post_by_external_id( $id ) : $this->get_post( $id );
		}

		if ( is_a( $request, 'WP_Post' ) ) {
			$id = $request->ID;
			$post = $this->get_post( $id );
		}
		
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		return WP_HYG_Promos()->Promo( $post );
	}

	private function get_post_by_external_id( $id )
	{
		$Empty_Promo = WP_HYG_Promos()->Promo();
		$Promo = $Empty_Promo->get_by_unique_key( $id );
		return $Promo->get_id();
	}

	private function is_request_by_external_id( $request )
	{
		$params = $request->get_params();
		return isset( $params['externalId'] );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params()
	{
		return array(
			'page' => array(
				'description' => 'Current page of the collection.',
				'type' => 'integer',
				'default' => 1,
				'sanitize_callback' => 'absint',
			),
			'posts_per_page' => array(
				'description' => 'Maximum number of items to be returned in result set.',
				'type' => 'integer',
				'default' => 10,
				'sanitize_callback' => 'absint',
			),
			'search' => array(
				'description' => 'Limit results to those matching a string.',
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

}
