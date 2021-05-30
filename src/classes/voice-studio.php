<?php

if ( ! class_exists( 'Voice_Studio' ) ) {

  /**
   * Voice Studio class.
   *
   * @since 0.1.0
   */
  class Voice_Studio {

    /**
     * Roles.
     *
     * @access private
     * @since  0.1.0
     * @var    array
     */
    private static $roles;

    /**
     * Roles to add.
     *
     * @access public
     * @since  0.1.0
     * @var    array
     */
    public static $roles_to_add;

    /**
     * Post types.
     *
     * @access private
     * @since  0.1.0
     * @var    Voice_Studio\Post_Types
     */
    private $post_types;

    /**
     * API.
     *
     * @access private
     * @since  0.1.0
     * @var    Voice_Studio\Api
     */
    private $api;

    /**
     * Voice Studio class constructor.
     *
     * @access public
     * @since  0.1.0
     */
    public function __construct() {
      // Require classes.
      $classes = [
        'api',
        'role',
      ];
      foreach ( $classes as $class ) {
        require plugin_dir_path( __FILE__ ) . 'voice-studio/' . $class . '.php';
      }

      // Setup user roles and capabilities to add.
      self::$roles        = [];
      self::$roles_to_add = [
        [
          'role'          => 'student',
          'fallback_role' => 'subscriber',
          'display_name'  => 'Student',
          'capabilities'  => [
            'student',
          ],
        ],
        [
          'role'          => 'parent',
          'fallback_role' => 'subscriber',
          'display_name'  => 'Parent of Student',
          'capabilities'  => [
            'parent',
          ],
        ],
        [
          'role'          => 'teacher',
          'fallback_role' => 'editor',
          'display_name'  => 'Teacher',
          'capabilities'  => [
            'teacher',
          ],
        ],
        [
          'role'          => 'owner',
          'fallback_role' => 'administrator',
          'display_name'  => 'Owner',
          'capabilities'  => [
            'owner',
          ],
        ],
      ];
      foreach ( self::$roles_to_add as $role ) {
        self::$roles[] = new Voice_Studio\Role( $role );
      }

      // Initialize API class.
      $api = new Voice_Studio\Api();
      $api->init();
    }

    /**
     * Activate Voice Studio plugin.
     *
     * @access public
     * @since  0.1.0
     */
    public static function activate () {
      // Add roles and capabilities.
      foreach ( self::$roles as $role ) {
        $role->add();
      }

      // Populate roles to users who previously had them.
      Voice_Studio\Role::populate();
      
      // @todo: Register post types and taxonomies.
      // @todo: Flush Permalinks (flush_rewrite_rules())
    }

    /**
     * Deactivate Voice Studio plugin.
     *
     * @access public
     * @since  0.1.0
     */
    public static function deactivate () {
      // Remove roles and capabilities.
      foreach ( self::$roles as $role ) {
        $role->remove();
      }
      
      // @todo: Unregister post types and taxonomies. (unregister_post_type())
      // @todo: Flush Cache/Temp
      // @todo: Flush Permalinks (flush_rewrite_rules())
    }

    /**
     * Uninstall Voice Studio plugin.
     *
     * @access public
     * @since  0.1.0
     */
    public static function uninstall () {
      // Remove user meta.
      foreach ( self::$roles as $role ) {
        $role->cleanup();
      }

      // @todo: Remove Options from {$wpdb->prefix}_options
      // @todo: Remove Tables from wpdb
    }
  }
}
