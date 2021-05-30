<?php

namespace Voice_Studio;

if ( ! class_exists( __NAMESPACE__ . '\Role' ) ) {

  /**
   * Role class.
   *
   * @since 0.1.0
   */
  class Role {

    /**
     * Role.
     *
     * @since 0.1.0
     * @type  array
     * @var   $role
     */
    private $role;

    /**
     * Role class constructor.
     *
     * @access public
     * @since  0.1.0
     */
    public function __construct ( $role ) {
      $this->role = $role;
    }

    /**
     * Add role.
     *
     * @access public
     * @return void
     * @since  0.1.0
     */
    public function add () : void {
      // Bail early if role is missing indexes or already exists.
      if (
        empty( $this->role['role'] ) ||
        empty( $this->role['display_name'] ) ||
        empty( $this->role['capabilities'] ) ||
        get_role( $this->role['role'] )
      ) return;

      // Add role.
      add_role(
        $this->role['role'],
        $this->role['display_name'],
        $this->role['capabilities'],
      );
    }

    /**
     * Populate roles to users who previously had them.
     *
     * @access public
     * @return void
     * @since  0.1.0
     */
    public static function populate () : void {
      // If there are no users with a previous role, bail.
      $users = get_users( array( 'meta_key' => 'wpvs_user_role' ) );
      if ( ! $users ) return;

      // Get roles to add.
      $roles_to_add = \Voice_Studio::$roles_to_add;

      foreach ( $users as $user ) {
        // Get previous role.
        $role = get_user_meta( $user->ID, 'wpvs_user_role', true );

        // Remove fallback role(s).
        foreach( $roles_to_add as $role_to_add ) {
          if ( $role_to_add['role'] !== $role) continue;
          $fallback_role = $role_to_add['fallback_role'];
          if ( $fallback_role ) $user->remove_role( $fallback_role );
        }

        // Add previous role.
        $user->add_role( $role );

        // Remove meta.
        if ( ! delete_user_meta( $user->ID, 'wpvs_user_role' ) ) {
          error_log( 'Error while removing custom user meta.' );
        }
      }
    }

    /**
     * Remove role.
     *
     * @access public
     * @return void
     * @since  0.1.0
     */
    public function remove () : void {
      // Bail early if role is missing indexes or does not already exist.
      if (
        empty( $this->role['role'] ) ||
        empty( $this->role['fallback_role'] ) ||
        ! get_role( $this->role['role'] )
      ) return;

      // Update users with this role.
      $users = get_users(
        array(
          'role' => $this->role['role'],
        )
      );
      foreach ( $users as $user ) {
        // If user does not have meta, add meta.
        if ( ! get_user_meta( $user->ID, 'wpvs_user_role', true ) ) {
          add_user_meta( $user->ID, 'wpvs_user_role', $this->role['role'] );
        }

        // Remove role.
        $user->remove_role( $this->role['role'] );

        // Add fallback role.
        $user->add_role( $this->role['fallback_role'] );
      }

      // Remove role.
      remove_role( $this->role['role'] );
    }

    /**
     * Cleanup user role meta during uninstall.
     *
     * @access public
     * @return void
     * @since  0.1.0
     */
    public function cleanup () : void {
      // If there are no users with custom meta, bail.
      $users = get_users( array( 'meta_key' => 'wpvs_user_role' ) );
      if ( ! $users ) return;

      // Remove custom user meta.
      foreach ( $users as $user ) {
        // Remove meta.
        if ( ! delete_user_meta( $user->ID, 'wpvs_user_role' ) ) {
          error_log( 'Error while removing custom user meta.' );
        }
      }
    }
  }
}
