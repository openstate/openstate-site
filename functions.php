<?php
// Translate, if applicable //
load_child_theme_textdomain('thematic-openstate');

// Global ID Settings //
function openstate_opt_init() {
  /* Register childtheme setting separate from thematic */
  register_setting('thematic_opt_group', 'openstate_options', 'openstate_options_validate');
  /* Add a settings section for our openstate */
  add_settings_section('openstate_section', '', 'openstate_section_html', 'thematic_theme_opt');
  /* Add settings fields to the settings section */
  add_settings_field('openstate_home_id', __('Mission Statement Page', 'thematic-openstate'), 'openstate_do_home_id', 'thematic_theme_opt', 'openstate_section');
  add_settings_field('openstate_cases_catid', __('Cases Category', 'thematic-openstate'), 'openstate_do_cases_catid', 'thematic_theme_opt', 'openstate_section');
  add_settings_field('openstate_tools_catid', __('Tools Category', 'thematic-openstate'), 'openstate_do_tools_catid', 'thematic_theme_opt', 'openstate_section');
}
add_action ('admin_init', 'openstate_opt_init');

function openstate_section_html() {}
function openstate_options_validate( $input ) { return $input;}
function openstate_default_opt() {
  $openstate_default_opt = array(
    'openstate_home_id'   => 142,
    'openstate_cases_catid'   => 110,
    'openstate_tools_catid'   => 111
  );
  return $openstate_default_opt;
}

function openstate_do_home_id() {
  $opt = thematic_get_wp_opt( 'openstate_options', openstate_default_opt() );
  wp_dropdown_pages(array(
    'selected' => $opt['openstate_home_id'],
    'name' => 'openstate_options[openstate_home_id]'
  ));
}
function openstate_do_cases_catid() {
  $opt = thematic_get_wp_opt( 'openstate_options', openstate_default_opt() );
  wp_dropdown_categories(array(
    'selected' => $opt['openstate_cases_catid'],
    'name' => 'openstate_options[openstate_cases_catid]',
    'hierarchical'=>1
  ));
}
function openstate_do_tools_catid() {
  $opt = thematic_get_wp_opt( 'openstate_options', openstate_default_opt() );
  wp_dropdown_categories(array(
    'selected' => $opt['openstate_tools_catid'],
    'name' => 'openstate_options[openstate_tools_catid]',
    'hierarchical'=>1
  ));
}
function openstate_get_option($opt_key) {
  $theme_opt = thematic_get_wp_opt( 'openstate_options', openstate_default_opt() );
  if (isset( $theme_opt[$opt_key] )) {
    return $theme_opt[$opt_key];
  } else {
    return null;
  }
}

// Unhook default Thematic functions //
function unhook_thematic_functions() {
  remove_action('thematic_navigation_below', 'thematic_nav_below', 2);
  remove_action('thematic_navigation_above', 'thematic_nav_above', 2);
}
add_action('init','unhook_thematic_functions');


function openstate_enqueue_scripts() {
  wp_enqueue_script(
    'openstatejs',
    get_stylesheet_directory_uri() . '/scripts/openstate.js',
    false,
    true
    );
  wp_enqueue_style(
    'fontawesome',
    '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css'
    );                 
}    
add_action('wp_enqueue_scripts', 'openstate_enqueue_scripts');

// Themes Menu //
function register_themes_menu() {
  register_nav_menu( 'themes-menu', __( 'Themes Menu', 'theme-slug' ) );
}
add_action( 'after_setup_theme', 'register_themes_menu' );
function openstate_theme_navigation() {
  wp_nav_menu(array( 
    'theme_location'=>'themes-menu',
    'container_class'=>'themes-menu'
  ));
}

add_action('thematic_header','openstate_theme_navigation');
function openstate_thematic_nav_menu_args($args) {
  $args['depth'] = 1;
  return apply_filters('openstate_thematic_nav_menu_args', $args);
}
add_filter('thematic_nav_menu_args', 'openstate_thematic_nav_menu_args');

// Multilingual choice dropdown //
if (function_exists('qts_language_menu')) {
  function openstate_header() {
    ?>
    <!-- qTranslate slug dropdown -->
    <div style="position:absolute; top:0px; right:0px; margin: 7px 15px 0 0;">
      <?=qts_language_menu('dropdown'); // qTranslate Slug plugin ?>
    </div>
    <style type="text/css">
    .qtrans_language_chooser { list-style-type: none; }
    .qtrans_language_chooser li { padding:2px; }
    </style>
    <?php
  }
  add_action('thematic_header','openstate_header');
}

// Case and Tool pages //
// This means pages get categories and featured images!
function add_taxonomies_to_pages() {
  register_taxonomy_for_object_type( 'post_tag', 'page' );
  register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'add_taxonomies_to_pages' );
add_theme_support( 'post-thumbnails', array( 'post', 'page' ) );
if ( ! is_admin() ) {
  function category_and_tag_archives( $wp_query ) {
    $my_post_array = $wp_query->get( 'post_type' );
    if ( $wp_query->get( 'category_name' ) || $wp_query->get( 'cat' ) ) {
      $wp_query->set( 'post_type', $my_post_array );
    }
    if ( $wp_query->get( 'tag' ) ) {
      $wp_query->set( 'post_type', $my_post_array );
    }
  }
  add_action( 'pre_get_posts', 'category_and_tag_archives' );
}
function add_extra_thumbnail_support() {
  // Featured images for pages
  add_theme_support( 'post-thumbnails', array( 'post', 'page' ) );
}
add_action( 'after_setup_theme', 'add_extra_thumbnail_support' );

function openstate_page_list($cat, $parent=null, $limit=5) {
  // Print a list of pages with thumbnails based on category
  $the_query = new WP_Query( array(
    'cat' => $cat, 
    'post_type' => 'page',
    'meta_query' => array(array('key' => '_thumbnail_id')),
    'posts_per_page' => $limit,
    'post_parent' => $parent,
  ) );
  // The Loop
  if ( $the_query->have_posts() ) {
    echo '<ul class="page-thumbs">';
    while ( $the_query->have_posts() ) {
      $the_query->the_post();
      if ( has_post_thumbnail() ) {
        $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' );
        $ratio = $thumb_url[1] / ($thumb_url[2]+1);
        $height = 160;
        echo sprintf('<li style="height:%spx; min-width:%spx;">', 
          $height, min(max(160 * $ratio, $height),240));
        echo sprintf('<a class="back" style="background-image:url(\'%s\');" href="%s">&nbsp;</a>',
          $thumb_url[0],
          get_permalink()
        );
        $this_parent = wp_get_post_parent_id( get_the_ID() );
        echo '<div class="title">';
        if ($this_parent != $parent) {
          echo '<a href="'.get_permalink($this_parent).'">'.get_the_title($this_parent).'</a>';
        } else {
          echo '<div>&nbsp;</div>';
        }
        echo sprintf('<h2><a href="%s">', get_permalink()) . get_the_title() . '</a></h2>';
        echo '</div></li>';
      }
    }
    echo '</ul>';
  } else {
    // no posts found
  }
  // Restore original Post Data 
  wp_reset_postdata();
}


function remove_blogdescription() {
  remove_action('thematic_header','thematic_blogdescription',5);
}
add_action('init','remove_blogdescription');

// Custom Front Page //
// Show the mission statement and latest cases
function openstate_thematic_belowheader() {
  if (is_home() || is_front_page()) {
    ?>
    <div id="home-statement">
      <?php
      // Use one specific page for home page
      $home_id = openstate_get_option('openstate_home_id');
      echo apply_filters('the_content', get_post($home_id)->post_content); 
      ?>
    </div>
    <div id="home-cases">
      <?php
      $cases_category_id = openstate_get_option('openstate_cases_catid');
      openstate_page_list($cases_category_id, null, 2);
      ?>
    </div>
    <?php
    remove_action('thematic_navigation_above','thematic_nav_above', 2);

    global $query_string;
    query_posts( $query_string . '&posts_per_page=4' );
  }
}
add_action('thematic_belowheader', 'openstate_thematic_belowheader');

function openstate_thematic_navigation_below() {
  if (is_home() || is_front_page()) {
    ?>
    <div id="home-allposts">
      <a href="/search/+/"><?=__('All posts', 'thematic')?> ≫</a>
    </div>
    <?php
  }
}
add_action('thematic_navigation_below', 'openstate_thematic_navigation_below');

// Show excerpt instead of full posts on front page
function openstate_thematic_content($post) {
  if (is_home() || is_front_page()) {
    $post = 'excerpt';
  }
  return apply_filters('openstate_thematic_content', $post);
}
add_filter('thematic_content', 'openstate_thematic_content');

// Filter author and seperators from post-meta block
function openstate_thematic_postmeta_entrydate() {

  $entrydate .= '<span class="entry-date"><abbr class="published" title="';
  $entrydate .= get_the_time(thematic_time_title()) . '">';
  $entrydate .= get_the_time(thematic_time_display());
  $entrydate .= '</abbr></span>';

  return apply_filters('thematic_post_meta_entrydate', $entrydate);  
}   
function openstate_thematic_postheader_postmeta($postmeta) {
  if(is_single()){
    $postmeta;
  }
  else {
    $postmeta = '<div class="entry-meta">';
    $postmeta .= openstate_thematic_postmeta_entrydate();
    $postmeta .= '<span class="cat-list">';
    $postmeta .= get_the_category_list(', ');
    $postmeta .= '</span>';
    $postmeta .= "</div><!-- .entry-meta -->\n";

  }
  return apply_filters('openstate_thematic_postheader_postmeta',$postmeta);     
}    
add_filter('thematic_postheader_postmeta','openstate_thematic_postheader_postmeta');

// CUSTOM POST LAYOUT
// Custom index loop post layout (thumbnail outside, meta above title)
function openstate_post($is_index) {
  ?>
  <div id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
    <?php
      global $post;
    
    // thumbnail
    $post_title = get_the_title();
    $size = apply_filters( 'thematic_post_thumb_size' , array(70,70));
    $attr = apply_filters( 'thematic_post_thumb_attr', array('title'  => sprintf( esc_attr__('Permalink to %s', 'thematic'), the_title_attribute( 'echo=0' ) ) ) );
    if ( has_post_thumbnail() ) {
      echo sprintf('<a class="entry-thumb" href="%s" title="%s">%s</a>',
        get_permalink() ,
        sprintf( esc_attr__('Permalink to %s', 'thematic'), the_title_attribute( 'echo=0' ) ),
        get_the_post_thumbnail(get_the_ID(), $size, $attr));
    }
    ?>
    <div class="entry-excerpt">
      <?php
      // skip the post header function
      echo thematic_postheader_postmeta();
      echo thematic_postheader_posttitle();    
      ?>
    </div>
    <?php if (!$is_index): ?>
    <div style="clear:both;"></div>
    <div class="entry-content">
      <?=apply_filters('thematic_post', apply_filters('the_content', $post->post_content)) ?>
      <?php wp_link_pages(array('before' => sprintf('<div class="page-link">%s', __('Pages:', 'thematic')),
      'after' => '</div>')); ?>
    </div><!-- .entry-content -->
    <?php endif; ?>
  <?php thematic_postfooter(); ?>
</div><!-- #post -->
<?php
}
// Custom post layout in single post page
function childtheme_override_single_post() { 
  thematic_abovepost();
  openstate_post(false);
  thematic_belowpost();
}
// Custom post layout in index loop
function childtheme_override_index_loop() {
  // Count the number of posts so we can insert a widgetized area
  $count = 1;
  while ( have_posts() ) : the_post();
  thematic_abovepost();
  openstate_post(true);
  thematic_belowpost();
  comments_template();
  if ( $count == thematic_get_theme_opt( 'index_insert' ) ) {
    get_sidebar('index-insert');
  }
  $count = $count + 1;
  endwhile;
}

function childtheme_override_category_loop() {
  childtheme_override_index_loop();

  add_action('thematic_navigation_below','thematic_nav_below');
}
// SEARCH PAGE //
function openstate_change_search_url_rewrite() {
  if ( is_search() && isset( $_GET['s'] ) ) {
    $s = empty( $_GET['s'] )? "%20/" : urlencode( get_query_var( 's' ) );
    wp_redirect( home_url( "/search/" ) . $s );
    exit();
  } 
}
add_action( 'template_redirect', 'openstate_change_search_url_rewrite' );
function childtheme_override_search_loop() {
  $_GET['s'] = get_search_query();
  $_GET['s'] = $_GET['s']=='%20'? "" : $_GET['s'];

  thematic_search_form();

  childtheme_override_index_loop();

  add_action('thematic_navigation_below','thematic_nav_below');
}

// Remove thumbnail from within post content
function nope() { return false; }
add_filter('thematic_post_thumbs', 'nope'); 

// POST NAVIGATION //
function openstate_page_title($content) {
  if (is_category()) {
    $content = '';
    $content .= '<h1 class="page-title">';
    $content .= ' <span>' . single_cat_title('', FALSE) .'</span>';
    $content .= '</h1>' . "\n";
    $content .= "\n\t\t\t\t" . '<div class="archive-meta">';
    if ( !(''== category_description()) ) : $content .= apply_filters('archive_meta', category_description()); endif;
    $content .= '</div>';
  }

  global $wp_query;
  if ((($pagenr = $wp_query->get("paged")) || ($pagenr = $wp_query->get("page"))) && $pagenr > 1) {
    $content = '<h1 class="page-title" style="float:right">(page ' . $pagenr . ')</h1>' . $content;
  }

  return apply_filters('openstate_page_title',$content); 
}
add_filter('thematic_page_title', 'openstate_page_title');  

function wpcodex_hide_email_shortcode( $atts , $content = null ) {
  if ( ! is_email( $content ) ) {
    return;
  }

  return '<a href="mailto:' . antispambot( $content ) . '">' . antispambot( $content ) . '</a>';
}
add_shortcode( 'email', 'wpcodex_hide_email_shortcode' );
?>
