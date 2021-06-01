<?php

namespace Voice_Studio;

if ( ! class_exists( __NAMESPACE__ . '\Api' ) ) {

  /**
   * API class.
   *
   * @since 0.1.0
   */
  class Api {

    /**
     * Rest namespace.
     *
     * @access private
     * @since  0.1.0
     * @var    string
     */
    private static $rest_namespace;

    /**
     * API class constructor.
     *
     * @access public
     * @since  0.1.0
     */
    public function __construct() {
      self::$rest_namespace = 'voice-studio/v1';
    }

    /**
     * Initialize API class.
     *
     * @access public
     * @since  0.1.0
     */
    public function init () {
      // Register rest endpoints.
      add_action(
        'rest_api_init',
        [ __CLASS__, 'register_rest_endpoints' ],
        10,
        1
      );
    }

    /**
     * Get rest namespace.
     *
     * @access private
     * @return string  Rest namespace.
     */
    private static function get_rest_namespace () {
      return self::$rest_namespace;
    }
     
    /**
     * Register rest endpoints.
     *
     * @access public
     * @param  \WP_REST_Server $wp_rest_server Server object.
     * @since  0.1.0
     */
    public static function register_rest_endpoints (
      \WP_REST_Server $wp_rest_server
    ) {
      $get_routes = [
        'hello',
      ];

      foreach ( $get_routes as $get_route ) {
        register_rest_route(
          self::get_rest_namespace(),
          '/' . $get_route,
          array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ __CLASS__, 'get_' . $get_route ],
            'permission_callback' => [ __CLASS__, 'authorize' ],
          ),
          false,
        );
      }
    }

    /**
     * Get hello.
     *
     * @access public
     * @param  \WP_REST_Request  $request The request.
     * @return \WP_REST_Response          The response.
     * @since  0.1.0
     */
    public static function get_hello (
      \WP_REST_Request $request
    ) : \WP_REST_Response {
      return rest_ensure_response( 'hello' );
    }
     
    /**
     * Authorize.
     *
     * @access public
     * @return boolean|\WP_Error True or an error if not authorized.
     * @since  0.1.0
     */
    public static function authorize () {
      if ( ! current_user_can( 'manage_options' ) ) {
        return new \WP_Error(
          'rest_forbidden',
          esc_html__( 'You do not have access.', 'wpvs' ),
          array( 'status' => 401 ),
        );
      }

      return true;
    }
  }
}
