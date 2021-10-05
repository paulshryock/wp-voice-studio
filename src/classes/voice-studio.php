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
     * @var    array
     */
    private static $post_types;

    /**
     * Post types to add.
     *
     * @access public
     * @since  unreleased
     * @var    array
     */
    public static $post_types_to_add;

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
        'post-type',
        'role',
        'roles',
      ];
      foreach ( $classes as $class ) {
        require plugin_dir_path( __FILE__ ) . 'voice-studio/' . $class . '.php';
      }

      // Initialize API class.
      $api = new Voice_Studio\Api();
      $api->init();

      // Setup post types and taxonomies to add.
      self::$post_types        = [];
      self::$post_types_to_add = [
        [
          'slug' => 'lessons/group-classes',
          'args' => [
            'labels'             => [
              'name'                     => __( 'Group Classes', 'wpvs' ),
              'singular_name'            => __( 'Group Class', 'wpvs' ),
              'add_new_item'             => __( 'Add new Group Class', 'wpvs' ),
              'edit_item'                => __( 'Edit Group Class', 'wpvs' ),
              'new_item'                 => __( 'New Group Class', 'wpvs' ),
              'view_item'                => __( 'View Group Class', 'wpvs' ),
              'view_items'               => __( 'View Group Classes', 'wpvs' ),
              'search_items'             => __( 'Search Group Classes', 'wpvs' ),
              'not_found'                => __( 'No Group Classes found', 'wpvs' ),
              'not_found_in_trash'       => __( 'No Group Classes found in Trash', 'wpvs' ),
              'parent_item_colon'        => __( 'Parent Group Class:', 'wpvs' ),
              'all_items'                => __( 'All Group Classes', 'wpvs' ),
              'archives'                 => __( 'Group Class archives', 'wpvs' ),
              'attributes'               => __( 'Group Class attributes', 'wpvs' ),
              'insert_into_item'         => __( 'Insert into Group Class', 'wpvs' ),
              'uploaded_to_this_item'    => __( 'Uploaded to this Group Class', 'wpvs' ),
              'filter_items_list'        => __( 'Filter Group Classes list', 'wpvs' ),
              'items_list_navigation'    => __( 'Group Classes list navigation', 'wpvs' ),
              'items_list'               => __( 'Group Classes list', 'wpvs' ),
              'item_published'           => __( 'Group Class published', 'wpvs' ),
              'item_published_privately' => __( 'Group Class published privately', 'wpvs' ),
              'item_reverted_to_draft'   => __( 'Group Class reverted to draft', 'wpvs' ),
              'item_scheduled'           => __( 'Group Class scheduled', 'wpvs' ),
              'item_updated'             => __( 'Group Class updated', 'wpvs' ),
              'item_link'                => __( 'Group Class link', 'wpvs' ),
              'item_link_description'    => __( 'A link to a Group Class', 'wpvs' ),
            ],
            // 'description',
            'public'             => true,
            'hierarchical'       => false,
            'publicly_queryable' => true,
            'show_in_rest'       => true,
            'rest_base'          => 'group_classes',
            // 'rest_controller_class', // (string) REST API Controller class name. Default is 'WP_REST_Posts_Controller'.
            // 'menu_position' => 20,
            'menu_icon'          => 'dashicons-groups',
            // 'capability_type' => [
            //   'group_class',
            //   'group_classes',
            // ],
            // 'capabilities' => [
            //   'owner'
            // ],
            // 'map_meta_cap',
            'supports'           => [
              'title',
              'editor',
              'excerpt',
              'thumbnail',
            ],
            // 'register_meta_box_cb',
            // 'taxonomies',
            'has_archive'        => false,
            // 'rewrite',
            // 'query_var',
            'delete_with_user'   => false,
            // 'template',
            // 'template_lock',
          ],
        ],
      ];
      foreach ( self::$post_types_to_add as $post_type_to_add ) {
        $post_type = new Voice_Studio\Post_Type( $post_type_to_add );
        $post_type->init();
        self::$post_types[] = $post_type;
      }

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
            [
              'owner'                => true,
              // Group classes.
              // 'read_group_class'   => true,
              // 'edit_group_class'   => true,
              // 'delete_group_class' => true,
              // 'edit_group_classes' => true,
              // 'edit_others_group_classes' => true,
              // 'delete_group_classes' => true,
              // 'publish_group_classes' => true,
              // 'read_private_group_classes' => true,
            ],
            [
              // Manage users.
              'list_users'         => true,
              'create_users'       => true,
              'edit_users'         => true,
              'promote_users'      => true,
              'remove_users'       => true,
              'delete_users'       => true,
              // Manage widgets, menus, customization.
              'edit_dashboard'     => true,
              'edit_theme_options' => true,
              // 'customize' => true,
              // Edit files.
              // 'edit_files' => true,
              // Manage settings.
              'manage_options'     => true,
              // Manage site.
              // 'update_core' => true,
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
            [
              'developer'            => true,
              // Group classes.
              // 'read_group_class'   => true,
              // 'edit_group_class'   => true,
              // 'delete_group_class' => true,
              // 'edit_group_classes' => true,
              // 'edit_others_group_classes' => true,
              // 'delete_group_classes' => true,
              // 'publish_group_classes' => true,
              // 'read_private_group_classes' => true,
            ],
          ),
        ],
      ];
      foreach ( self::$roles_to_add as $role_to_add ) {
        self::$roles[] = new Voice_Studio\Role( $role_to_add );
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
      
      // Register post types and taxonomies.
      // foreach ( self::$post_types as $post_type ) {
      //   $post_type->add();
      // }

      // @todo: Flush permalinks if needed.
      // flush_rewrite_rules();
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

      // @todo: Flush permalinks.
      // flush_rewrite_rules();
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
