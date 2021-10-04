<?php

namespace Voice_Studio;

if ( ! class_exists( __NAMESPACE__ . '\CustomPostType' ) ) {

  /**
   * Post type class.
   *
   * @since unreleased
   */
  class Post_Type {

    /**
     * Post type.
     *
     * @since unreleased
     * @type  array
     * @var   $post_type
     */
    private $post_type;

    /**
     * Post type class constructor.
     *
     * @access public
     * @since  unreleased
     */
    public function __construct ( $post_type ) {
      $this->post_type = $post_type;
    }

    /**
     * Initialize post type.
     *
     * @access public
     * @return void
     * @since  unreleased
     */
    public function init () : void {
      // Add post type.
      add_action( 'init', [ $this, 'add' ] );
    }

    /**
     * Add post type.
     *
     * @access public
     * @return void
     * @since  unreleased
     */
    public function add () : void {
      // Bail early if post type already exists or is missing indexes.
      if (
        post_type_exists( $this->post_type['slug'] ) ||
        empty( $this->post_type['slug'] ) ||
        empty( $this->post_type['args']['labels']['name'] ) ||
        empty( $this->post_type['args']['labels']['singular_name'] )
      ) return;

      // Add post type.
      register_post_type(
        sanitize_key( $this->post_type['slug'] ),
        $this->post_type['args']
      );

      // Flush permalinks.
      flush_rewrite_rules();
    }

    /**
     * Remove post type.
     *
     * @access public
     * @return void
     * @since  unreleased
     */
    public function remove () : void {
      // Bail early if post type does not exist.
      if ( ! post_type_exists( $this->post_type['slug'] ) ) return;

      // Remove post type.
      $removed = unregister_post_type(
        sanitize_key( $this->post_type['slug'] )
      );

      // Handle failure.
      if ( $removed !== true ) {
        // todo: Log to UI.
        error_log(
          'Post type ' . $this->post_type['slug'] . ' was not removed.'
        );
      }

      // Flush permalinks.
      flush_rewrite_rules();
    }
  }
}
