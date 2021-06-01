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
        'roles',
      ];
      foreach ( $classes as $class ) {
        require plugin_dir_path( __FILE__ ) . 'voice-studio/' . $class . '.php';
      }

      // Initialize API class.
      $api = new Voice_Studio\Api();
      $api->init();

      // Setup user roles and capabilities to add.
      self::$roles        = [];
      self::$roles_to_add = [
        [
          'role'          => 'student',
          'fallback_role' => 'subscriber',
          'display_name'  => 'Student',
          'capabilities'  => array_merge(
            // Read.
            get_role( 'subscriber' )->capabilities,
            [ 'student' => true ],
          ),
        ],
        [
          'role'          => 'parent',
          'fallback_role' => 'subscriber',
          'display_name'  => 'Parent of Student',
          'capabilities'  => array_merge(
            // Read.
            get_role( 'subscriber' )->capabilities,
            [ 'parent' => true ],
          ),
        ],
        [
          'role'          => 'teacher',
          'fallback_role' => 'author',
          'display_name'  => 'Teacher',
          'capabilities'  => array_merge(
            // Manage & Publish own posts.
            // Upload files.
            // Create draft posts.
            // Read.
            get_role( 'author' )->capabilities,
            [ 'teacher' => true ],
          ),
        ],
        [
          'role'          => 'owner',
          'fallback_role' => 'administrator',
          'display_name'  => 'Owner',
          'capabilities'  => array_merge(
            // Manage & Publish others’ posts and pages.
            // Manage & Publish pages.
            // Manage & Read private posts and pages.
            // Manage categories and links.
            // Moderate comments.
            // Insert unfiltered HTML.
            // Manage & Publish own posts.
            // Upload files.
            // Create draft posts.
            // Read.
            get_role( 'editor' )->capabilities,
            [ 'owner' => true ],
            [
              // Manage users.
              'list_users' => true,
              'create_users' => true,
              'edit_users' => true,
              'promote_users' => true,
              'remove_users' => true,
              'delete_users' => true,
              // Manage widgets, menus, customization.
              'edit_dashboard' => true,
              'edit_theme_options' => true,
              // 'customize' => true,
              // Edit files.
              // 'edit_files' => true,
              // Manage settings.
              // 'manage_options',
              // Manage site.
              // 'update_core',
            ],
          ),
        ],
        [
          'role'          => 'developer',
          'fallback_role' => 'administrator',
          'display_name'  => 'Web Developer',
          'capabilities'  => array_merge(
            // Manage plugins.
            // Manage themes.
            // Manage users.
            // Import and export.
            // Manage widgets, menus, customization.
            // Manage settings.
            // Manage site.
            // Manage & Publish others’ posts and pages.
            // Manage & Publish pages.
            // Manage & Read private posts and pages.
            // Manage categories and links.
            // Moderate comments.
            // Insert unfiltered HTML.
            // Manage & Publish own posts.
            // Upload files.
            // Create draft posts.
            // Read.
            get_role( 'administrator' )->capabilities,
            [ 'developer' => true ],
          ),
        ],
      ];
      foreach ( self::$roles_to_add as $role ) {
        self::$roles[] = new Voice_Studio\Role( $role );
      }

      // Initialize Roles class.
      $roles = new Voice_Studio\Roles();
      $roles->init();
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
