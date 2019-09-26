<?php

class Cornerstone_Dynamic_Content_ACF extends Cornerstone_Plugin_Component {


  public function setup() {
    add_filter( 'cs_dynamic_content_acf', array( $this, 'supply_field' ), 10, 3 );
    add_action( 'cs_dynamic_content_setup', array( $this, 'register' ) );
    add_filter( 'cs_dynamic_content_before_expand', array( $this, 'legacy_acf' ) );
    add_filter( 'cs_dynamic_options_acf', array( $this, 'populate_fields' ), 10, 2 );
  }

  public function register() {
    cornerstone_dynamic_content_register_group(array(
      'name'  => 'acf',
      'label' => csi18n('app.dc.group-title-acf')
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'field',
      'group' => 'acf',
      'label' => 'Field',
      'controls' => array( 'acf-field', 'post' ),
      'options' => array(
        'supports' => array( 'image' )
      )
    ));

  }

  public function supply_field( $result, $field, $args = array() ) {

    $post = CS()->component('Dynamic_Content')->get_post_from_args( $args );

    if ( 'field' === $field && isset($args['field']) ) {
      $result = acf_shortcode( array(
        'field' => $args['field'],
        'post_id' => $post->ID,
        'raw' => true
      ) );
    }

    return $result;
  }

  public function populate_fields( $options, $args = array() ) {

    if ( isset( $args['context'] ) &&
      isset( $args['context']['mode'] ) &&
      'content' === $args['context']['mode'] &&
      isset( $args['context']['data'] ) &&
      isset( $args['context']['data']['post_id'] )
    ) {

      $fields = get_fields((int) $args['context']['data']['post_id'], false );
      if ( is_array( $fields ) ) {
        foreach ($fields as $value => $label) {
          $options[] = array( 'label' => sprintf("%s (%s)", $value, $label), 'value' => $value );
        }
      }

    }

    return $options;

  }

  /**
   * Earlier versions of Cornersone allowed for this ACF syntax: {{acf:field_name}}
   * This function continues to support it for older content even though {{dc:acf:field}} is the new syntax.
   */
  public function legacy_acf( $content ) {
    return function_exists( 'acf_shortcode' )
      ? preg_replace_callback( '/{{[aA][cC][fF]:([A-Za-z0-9_-]*?)}}/', array( $this, 'legacy_acf_expand_callback' ), $content )
      : $content;
  }

  public function legacy_acf_expand_callback( $matches ) {
    return acf_shortcode( array('field' => $matches[1]) );
  }


}
