<?php

class Cornerstone_Footer_Builder extends Cornerstone_Plugin_Component {

  public function config() {
    return array(
      'i18n' => $this->plugin->i18n_group( 'footers' ),
      'assign_contexts' => $this->plugin->component( 'Footer_Assignments' )->get_assign_contexts()
    );

  }

}
