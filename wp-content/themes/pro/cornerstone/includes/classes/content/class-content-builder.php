<?php

class Cornerstone_Content_Builder extends Cornerstone_Plugin_Component {

  public function config() {
    return array(
      'i18n' => $this->plugin->i18n_group( 'content' )
    );
  }

}
