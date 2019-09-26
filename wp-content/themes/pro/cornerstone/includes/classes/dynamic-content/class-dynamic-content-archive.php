<?php

class Cornerstone_Dynamic_Content_Archive extends Cornerstone_Plugin_Component {

  public function setup() {
    add_filter('cs_dynamic_content_archive', array( $this, 'supply_field' ), 10, 3 );
    add_action('cs_dynamic_content_setup', array( $this, 'register' ) );
  }

  public function register() {

    cornerstone_dynamic_content_register_group(array(
      'name'  => 'archive',
      'label' => csi18n('app.dc.group-title-archive')
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'title',
      'group' => 'archive',
      'label' => 'Title',
      'controls' => array( 'term' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'description',
      'group' => 'archive',
      'label' => 'Description',
      'controls' => array( 'term' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'url',
      'group' => 'archive',
      'label' => 'URL',
      'controls' => array( 'term' )
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'meta',
      'group' => 'archive',
      'label' => 'Meta',
      'controls' => array(
        'term',
        array(
          'key' => 'key',
          'type' => 'text',
          'label' => 'Key',
          'options' => array( 'placeholder' => 'Meta Key')
        )
      ),
      'options' => array(
        'supports' => array( 'image' ),
        'always_customize' => true
      ),
    ));

    cornerstone_dynamic_content_register_field(array(
      'name'  => 'id',
      'group' => 'archive',
      'label' => 'ID',
      'controls' => array( 'term' )
    ));

  }

  public function supply_field( $result, $field, $args = array() ) {

    $term = CS()->component('Dynamic_Content')->get_term_from_args( $args );

    if ( ! $term ) {
      return $result;
    }

    switch ( $field ) {
      case 'title': {
        $result = $term->name;
        break;
      }
      case 'description': {
        $result = $term->description;
        break;
      }
      case 'url': {
        $result = get_term_link( $term );
        break;
      }
      case 'meta': {
        if ( isset( $args['key'] ) ) {
          $result = get_term_meta( $term->term_id, $args['key'], true );
        }
        break;
      }
      case 'id': {
        $result = "$term->term_id";
        break;
      }
    }

    return $result;
  }
}
