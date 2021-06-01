<?php

namespace Voice_Studio;

if ( ! class_exists( __NAMESPACE__ . '\Roles' ) ) {

  /**
   * Roles class.
   *
   * @since 0.4.0
   */
  class Roles {

    /**
     * Roles class constructor.
     *
     * @access public
     * @since  0.4.0
     */
    public function __construct ( ) {
      // Do something.
    }

    /**
     * Initialize Roles class.
     *
     * @access public
     * @since  0.4.0
     */
    public function init () {
      add_filter(
        'editable_roles',
        [ __CLASS__, 'update_editable_roles' ],
        10,
        1
      );
    }

    /**
     * Update editable roles.
     *
     * @param  array[] $all_roles Array of arrays containing role information.
     * @return array[]            Updated editable roles information.
     * @since  0.4.0
     */
    public static function update_editable_roles ( array $all_roles ): array {
      if ( current_user_can( 'manage_options' ) ) return $all_roles;

      $editable_roles  = $all_roles;
      $roles_to_remove = [
        'administrator',
        'editor',
        'author',
        'contributor',
        'subscriber',
      ];

      // Remove roles.
      if ( ! empty( $roles_to_remove ) ) {
        foreach ( $roles_to_remove as $role_to_remove ) {
          unset( $editable_roles[ $role_to_remove ] );
        }
      }

      // Return updated editable roles.
      return $editable_roles;
    }
  }
}
