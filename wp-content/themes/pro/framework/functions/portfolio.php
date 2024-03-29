<?php

// =============================================================================
// FUNCTIONS/GLOBAL/PORTFOLIO.PHP
// -----------------------------------------------------------------------------
// Portfolio related functions beyond custom post type setup.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Register Custom Post Type
//   02. Get first Portfolio Page
//   03. Get Parent Portfolio Link
//   04. Get Parent Portfolio Title
//   05. Output Portfolio Filters
//   06. Output Portfolio Item Project Link
//   07. Output Portfolio Item Tags
//   08. Output Portfolio Item Social
//   09. Entry Class
//   10. Portfolio Page Template Precedence
//   11. Add Thumbnails to the Admin Screen
//   12. Portfolio Filter Shortcode
// =============================================================================

// Register Custom Post Type
// =============================================================================

function x_portfolio_init() {

  $slug      = sanitize_title( x_get_option( 'x_custom_portfolio_slug' ) );
  $menu_icon = ( floatval( get_bloginfo( 'version' ) ) >= '3.8' ) ? 'dashicons-format-gallery' : NULL;


  //
  // Enable the custom post type.
  //

  $args = array(
    'labels'          => array(
      'name'               => __( 'Portfolio', '__x__' ),
      'singular_name'      => __( 'Portfolio Item', '__x__' ),
      'add_new'            => __( 'Add New Item', '__x__' ),
      'add_new_item'       => __( 'Add New Portfolio Item', '__x__' ),
      'edit_item'          => __( 'Edit Portfolio Item', '__x__' ),
      'new_item'           => __( 'Add New Portfolio Item', '__x__' ),
      'view_item'          => __( 'View Item', '__x__' ),
      'search_items'       => __( 'Search Portfolio', '__x__' ),
      'not_found'          => __( 'No portfolio items found', '__x__' ),
      'not_found_in_trash' => __( 'No portfolio items found in trash', '__x__' )
    ),
    'public'          => true,
    'show_ui'         => true,
    'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'author', 'custom-fields', 'revisions' ),
    'capability_type' => 'post',
    'hierarchical'    => false,
    'rewrite'         => array( 'slug' => $slug, 'with_front' => false ),
    'menu_position'   => 5,
    'menu_icon'       => $menu_icon,
    'has_archive'     => true
  );

  register_post_type( 'x-portfolio', apply_filters( 'portfolioposttype_args', $args ) );


  //
  // Portfolio tags taxonomy.
  //

  $taxonomy_portfolio_tag_args = array(
    'labels'           => array(
      'name'                       => __( 'Portfolio Tags', '__x__' ),
      'singular_name'              => __( 'Portfolio Tag', '__x__' ),
      'search_items'               => __( 'Search Portfolio Tags', '__x__' ),
      'popular_items'              => __( 'Popular Portfolio Tags', '__x__' ),
      'all_items'                  => __( 'All Portfolio Tags', '__x__' ),
      'parent_item'                => __( 'Parent Portfolio Tag', '__x__' ),
      'parent_item_colon'          => __( 'Parent Portfolio Tag:', '__x__' ),
      'edit_item'                  => __( 'Edit Portfolio Tag', '__x__' ),
      'update_item'                => __( 'Update Portfolio Tag', '__x__' ),
      'add_new_item'               => __( 'Add New Portfolio Tag', '__x__' ),
      'new_item_name'              => __( 'New Portfolio Tag Name', '__x__' ),
      'separate_items_with_commas' => __( 'Separate portfolio tags with commas', '__x__' ),
      'add_or_remove_items'        => __( 'Add or remove portfolio tags', '__x__' ),
      'choose_from_most_used'      => __( 'Choose from the most used portfolio tags', '__x__' ),
      'menu_name'                  => __( 'Portfolio Tags', '__x__' )
    ),
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    'rewrite'           => array( 'slug' => $slug . '-tag', 'with_front' => false ),
    'show_admin_column' => true,
    'query_var'         => true
  );

  register_taxonomy( 'portfolio-tag', array( 'x-portfolio' ), $taxonomy_portfolio_tag_args );


  //
  // Portfolio categories taxonomy.
  //

  $taxonomy_portfolio_category_args = array(
    'labels'            => array(
      'name'                       => __( 'Portfolio Categories', '__x__' ),
      'singular_name'              => __( 'Portfolio Category', '__x__' ),
      'search_items'               => __( 'Search Portfolio Categories', '__x__' ),
      'popular_items'              => __( 'Popular Portfolio Categories', '__x__' ),
      'all_items'                  => __( 'All Portfolio Categories', '__x__' ),
      'parent_item'                => __( 'Parent Portfolio Category', '__x__' ),
      'parent_item_colon'          => __( 'Parent Portfolio Category:', '__x__' ),
      'edit_item'                  => __( 'Edit Portfolio Category', '__x__' ),
      'update_item'                => __( 'Update Portfolio Category', '__x__' ),
      'add_new_item'               => __( 'Add New Portfolio Category', '__x__' ),
      'new_item_name'              => __( 'New Portfolio Category Name', '__x__' ),
      'separate_items_with_commas' => __( 'Separate portfolio categories with commas', '__x__' ),
      'add_or_remove_items'        => __( 'Add or remove portfolio categories', '__x__' ),
      'choose_from_most_used'      => __( 'Choose from the most used portfolio categories', '__x__' ),
      'menu_name'                  => __( 'Portfolio Categories', '__x__' ),
    ),
    'public'            => true,
    'show_in_nav_menus' => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_tagcloud'     => true,
    'hierarchical'      => true,
    'rewrite'           => array( 'slug' => $slug . '-category', 'with_front' => false ),
    'query_var'         => true
  );

  register_taxonomy( 'portfolio-category', array( 'x-portfolio' ), $taxonomy_portfolio_category_args );


  //
  // Flush rewrite rules if portfolio slug is updated.
  //

  if ( get_transient( 'x_portfolio_slug_before' ) != get_transient( 'x_portfolio_slug_after' ) ) {
    flush_rewrite_rules( false );
    delete_transient( 'x_portfolio_slug_before' );
    delete_transient( 'x_portfolio_slug_after' );
  }

}

add_action( 'init', 'x_portfolio_init' );



// Get the Page Link to First Portfolio Page
// =============================================================================

function x_get_first_portfolio_page() {

  $results = get_pages( array(
    'meta_key'    => '_wp_page_template',
    'meta_value'  => 'template-layout-portfolio.php',
    'sort_order'  => 'ASC',
    'sort_column' => 'ID'
  ) );

  if ( count($results) > 0 && is_a( $results[0], 'WP_Post' ) ) {
    return $results[0];
  }

  return NULL;

}



// Get Parent Portfolio Link
// =============================================================================

function x_get_parent_portfolio_link() {

  $portfolio_parent_id = get_post_meta( get_the_ID(), '_x_portfolio_parent', true );

  if ( $portfolio_parent_id && $portfolio_parent_id !== 'Default' ) {
    return get_permalink( $portfolio_parent_id );
  }

  $first_portfolio_page = x_get_first_portfolio_page();

  if ( $first_portfolio_page ) {
    return get_page_link( $first_portfolio_page );
  }

  return '';

}



// Get Parent Portfolio Title
// =============================================================================

function x_get_parent_portfolio_title() {

  $portfolio_parent_id = get_post_meta( get_the_ID(), '_x_portfolio_parent', true );

  if ( $portfolio_parent_id && $portfolio_parent_id !== 'Default' ) {
    return get_the_title( $portfolio_parent_id );
  }

  $first_portfolio_page = x_get_first_portfolio_page();

  if ( $first_portfolio_page ) {
    return $first_portfolio_page->post_title;
  }

  return '';

}



// Output Portfolio Filters
// =============================================================================

function x_portfolio_filters() {

  $stack           = x_get_stack();
  $filters         = get_post_meta( get_the_ID(), '_x_portfolio_category_filters', true );
  $disable_filters = get_post_meta( get_the_ID(), '_x_portfolio_disable_filtering', true );
  $one_filter      = count( $filters ) == 1;
  $all_categories  = in_array( 'All Categories', $filters );

  //
  // 1. If one filter is selected and that filter is "All Categories."
  // 2. If more than one category filter is selected.
  //

  if ( $one_filter && $all_categories ) { // 1

    $terms = get_terms( 'portfolio-category' );

  } else { // 2

    $terms = array();
    foreach ( $filters as $filter ) {
      $parent   = array( $filter );
      $children = get_term_children( $filter, 'portfolio-category' );
      $terms    = array_merge( $parent, $terms );
      $terms    = array_merge( $children, $terms );
    }
    $terms = get_terms( 'portfolio-category', array( 'include' => $terms ) );

  }


  //
  // Main filter button content.
  //

  if ( $stack == 'integrity' ) {
    $button_content = '<i class="x-icon-sort" data-x-icon-s="&#xf0dc;"></i> <span>' . x_get_option( 'x_integrity_portfolio_archive_sort_button_text' ) . '</span>';
  } elseif ( $stack == 'ethos' ) {
    $button_content = '<i class="x-icon-chevron-down" data-x-icon-s="&#xf078;"></i>';
  } else {
    $button_content = '<i class="x-icon-plus" data-x-icon-s="&#xf067;"></i>';
  }


  //
  // Filters.
  //

  if ( $disable_filters != 'on' ) {
    if ( $stack != 'ethos' ) {

    ?>

      <ul class="option-set unstyled" data-option-key="filter">
        <li>
          <a href="#" class="x-portfolio-filters"><?php echo $button_content; ?></a>
          <ul class="x-portfolio-filters-menu unstyled">
            <li><a href="#" data-option-value="*" class="x-portfolio-filter selected"><?php _e( 'All', '__x__' ); ?></a></li>
            <?php foreach ( $terms as $term ) { ?>
              <li><a href="#" data-option-value=".x-portfolio-<?php echo md5( $term->slug ); ?>" class="x-portfolio-filter"><?php echo $term->name; ?></a></li>
            <?php } ?>
          </ul>
        </li>
      </ul>

    <?php } elseif ( $stack == 'ethos' ) { ?>

      <ul class="option-set unstyled" data-option-key="filter">
        <li>
          <a href="#" class="x-portfolio-filters cf">
            <span class="x-portfolio-filter-label"><?php _e( 'Filter by Category', '__x__' ); ?></span>
            <?php echo $button_content; ?>
          </a>
          <ul class="x-portfolio-filters-menu unstyled">
            <li><a href="#" data-option-value="*" class="x-portfolio-filter selected"><?php _e( 'All', '__x__' ); ?></a></li>
            <?php foreach ( $terms as $term ) { ?>
              <li><a href="#" data-option-value=".x-portfolio-<?php echo md5( $term->slug ); ?>" class="x-portfolio-filter"><?php echo $term->name; ?></a></li>
            <?php } ?>
          </ul>
        </li>
      </ul>

    <?php

    }
  }

}



// Output Portfolio Item Project Link
// =============================================================================

function x_portfolio_item_project_link() {

  $project_link  = get_post_meta( get_the_ID(), '_x_portfolio_project_link', true );
  $launch_title  = x_get_option( 'x_portfolio_launch_project_title' );
  $launch_button = x_get_option( 'x_portfolio_launch_project_button_text' );

  if ( $project_link ) :

  ?>

  <h2 class="h-extra launch"><?php echo $launch_title; ?></h2>
  <a href="<?php echo $project_link; ?>" title="<?php echo $launch_button; ?>" class="x-btn x-btn-block" target="_blank"><?php echo $launch_button; ?></a>

  <?php

  endif;

}



// Output Portfolio Item Tags
// =============================================================================

function x_portfolio_item_tags() {

  $stack     = x_get_stack();
  $tag_title = x_get_option( 'x_portfolio_tag_title' );

  if ( has_term( NULL, 'portfolio-tag', NULL ) ) :

    echo '<h2 class="h-extra skills">' . $tag_title . '</h2>';
    call_user_func( 'x_' . $stack . '_portfolio_tags');

  endif;

}



// Output Portfolio Item Social
// =============================================================================

function x_portfolio_item_social() {

  $share_project_title = x_get_option( 'x_portfolio_share_project_title' );
  $enable_facebook     = x_get_option( 'x_portfolio_enable_facebook_sharing' );
  $enable_twitter      = x_get_option( 'x_portfolio_enable_twitter_sharing' );
  $enable_google_plus  = x_get_option( 'x_portfolio_enable_google_plus_sharing' );
  $enable_linkedin     = x_get_option( 'x_portfolio_enable_linkedin_sharing' );
  $enable_pinterest    = x_get_option( 'x_portfolio_enable_pinterest_sharing' );
  $enable_reddit       = x_get_option( 'x_portfolio_enable_reddit_sharing' );
  $enable_email        = x_get_option( 'x_portfolio_enable_email_sharing' );

  $share_url     = urlencode( get_permalink() );
  $share_title   = urlencode( get_the_title() );
  $share_source  = urlencode( get_bloginfo( 'name' ) );
  $share_content = urlencode( get_the_excerpt() );
  $share_image   = urlencode( x_get_featured_image_with_fallback_url() );

  $facebook    = ( $enable_facebook == '1' )    ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share\" title=\"" . __( 'Share on Facebook', '__x__' ) . "\" onclick=\"window.open('http://www.facebook.com/sharer.php?u={$share_url}&amp;t={$share_title}', 'popupFacebook', 'width=650, height=270, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-facebook-square\" data-x-icon-b=\"&#xf082;\"></i></a>" : '';
  $twitter     = ( $enable_twitter == '1' )     ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share\" title=\"" . __( 'Share on Twitter', '__x__' ) . "\" onclick=\"window.open('https://twitter.com/intent/tweet?text={$share_title}&amp;url={$share_url}', 'popupTwitter', 'width=500, height=370, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-twitter-square\" data-x-icon-b=\"&#xf081;\"></i></a>" : '';
  $google_plus = ( $enable_google_plus == '1' ) ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share\" title=\"" . __( 'Share on Google+', '__x__' ) . "\" onclick=\"window.open('https://plus.google.com/share?url={$share_url}', 'popupGooglePlus', 'width=650, height=226, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-google-plus-square\" data-x-icon-b=\"&#xf0d4;\"></i></a>" : '';
  $linkedin    = ( $enable_linkedin == '1' )    ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share\" title=\"" . __( 'Share on LinkedIn', '__x__' ) . "\" onclick=\"window.open('http://www.linkedin.com/shareArticle?mini=true&amp;url={$share_url}&amp;title={$share_title}&amp;summary={$share_content}&amp;source={$share_source}', 'popupLinkedIn', 'width=610, height=480, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-linkedin-square\" data-x-icon-b=\"&#xf08c;\"></i></a>" : '';
  $pinterest   = ( $enable_pinterest == '1' )   ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share\" title=\"" . __( 'Share on Pinterest', '__x__' ) . "\" onclick=\"window.open('http://pinterest.com/pin/create/button/?url={$share_url}&amp;media={$share_image}&amp;description={$share_title}', 'popupPinterest', 'width=750, height=265, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-pinterest-square\" data-x-icon-b=\"&#xf0d3;\"></i></a>" : '';
  $reddit      = ( $enable_reddit == '1' )      ? "<a href=\"#share\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share\" title=\"" . __( 'Share on Reddit', '__x__' ) . "\" onclick=\"window.open('http://www.reddit.com/submit?url={$share_url}', 'popupReddit', 'width=875, height=450, resizable=0, toolbar=0, menubar=0, status=0, location=0, scrollbars=0'); return false;\"><i class=\"x-icon-reddit-square\" data-x-icon-b=\"&#xf1a2;\"></i></a>" : '';
  $email       = ( $enable_email == '1' )       ? "<a href=\"mailto:?subject=" . urlencode( get_the_title() ) . "&amp;body=" . urlencode( __( 'Hey, thought you might enjoy this! Check it out when you have a chance:', '__x__' ) ) . " " . get_permalink() . "\" data-toggle=\"tooltip\" data-placement=\"bottom\" data-trigger=\"hover\" class=\"x-share email\" title=\"" . __( 'Share via Email', '__x__' ) . "\"><span><i class=\"x-icon-envelope-square\" data-x-icon-s=\"&#xf199;\"></i></span></a>" : '';

  if ( $enable_facebook == '1' || $enable_twitter == '1' || $enable_google_plus == '1' || $enable_linkedin == '1' || $enable_pinterest == '1' || $enable_reddit == '1' || $enable_email == '1' ) :

    ?>

    <div class="x-entry-share man">
      <div class="x-share-options">
        <p><?php echo $share_project_title; ?></p>
        <?php echo $facebook . $twitter . $google_plus . $linkedin . $pinterest . $reddit . urldecode( $email ); ?>
      </div>
    </div>

    <?php

  endif;

}



// Entry Class
// =============================================================================

if ( ! function_exists( 'x_portfolio_entry_classes' ) ) :
  function x_portfolio_entry_classes( $classes ) {

    GLOBAL $post;
    $terms = wp_get_object_terms( $post->ID, 'portfolio-category' );
    foreach ( $terms as $term ) {
      $classes[] = 'x-portfolio-' . md5( $term->slug );
    }
    return $classes;

  }
  add_filter( 'post_class', 'x_portfolio_entry_classes' );
endif;



// Portfolio Page Template Precedence
// =============================================================================

//
// Allows a user to set their Custom Portfolio Slug to the same value as their
// page slug. When the x-portfolio post type is found, it gives precedence to
// the portfolio template page instead of the archive page.
//

function x_portfolio_page_template_precedence( $request ) {
  global $wp;

  if ( array_key_exists( 'post_type', $request ) && 'x-portfolio' == $request['post_type'] ) {
    if ( x_get_option( 'x_custom_portfolio_slug' ) === $wp->request && get_page_by_path( $wp->request ) ) {
      unset( $request['post_type'] );
      $request['pagename'] = $wp->request;
    }
  }

  return $request;

}

add_filter( 'request', 'x_portfolio_page_template_precedence' );



// Portfolio Filter Shortcode
// =============================================================================

function x_portfolio_filters_shortcode( $atts ) {

  ob_start();

  if ( get_post_meta( get_the_ID(), '_x_portfolio_category_filters', true ) ) {
    x_portfolio_filters();
  }

  return ob_get_clean();

}

add_shortcode( 'x_portfolio_filters', 'x_portfolio_filters_shortcode' );
