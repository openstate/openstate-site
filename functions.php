<?php
// Translate, if applicable
load_child_theme_textdomain('thematic-openstate');

// Unhook default Thematic functions
function unhook_thematic_functions() {
  remove_action('thematic_navigation_below', 'thematic_nav_below', 2);
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
  ));
}
add_action('thematic_header','openstate_theme_navigation');
function openstate_thematic_nav_menu_args($args) {
  $args['depth'] = 1;
  return apply_filters('openstate_thematic_nav_menu_args', $args);
}
add_filter('thematic_nav_menu_args', 'openstate_thematic_nav_menu_args');

// Add custom post type for announcements //
add_action( 'init', 'create_my_post_types' );
function create_my_post_types() {
  register_post_type( 'announcement', 
    array(
      'labels' => array(
        'name' => 'Announcements',
        'singular_name' => 'Announcement'
        ),
      'supports' => array(  
        'title',
        'excerpt'
        ),
      'taxonomies' => array(
        'category'
        ),
      'public' => true,
      'menu_position' => 5,
      'hierarchical' => false
      )
    );
}

// Multilingual choice dropdown
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

// Use tagline (blog description) as mission statement
function childtheme_override_blogdescription() {
  // Always make it h1
  $blogdesc = '"blog-description">' . get_bloginfo('description', 'display');
  echo "\t<h1 id=$blogdesc</h1>\n\n";
}
function move_blogdescription() {
  remove_action('thematic_header','thematic_blogdescription',5);
  add_action('thematic_header','thematic_blogdescription');
}
add_action('init','move_blogdescription');



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
      // thumbnail
    $post_title = get_the_title();
    $size = apply_filters( 'thematic_post_thumb_size' , array(100,100) );
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
      global $post;
      $content = get_extended( $post->post_content );
      if (strlen($content['extended']) == 0) {
        $main = $is_index? get_the_excerpt() : '';
        $extended = get_the_content();
      } else {
        $main = strip_shortcodes($content['main']);
        $extended = $content['extended'];
      }
      ?>
      <div class="entry-content">
        <?=apply_filters('thematic_post', apply_filters('the_excerpt', $main)); ?>
      </div>
    </div>
    <?php if (!$is_index): ?>
    <div style="clear:both;"></div>
    <div class="entry-content">
      <?=apply_filters('thematic_post', apply_filters('the_content', $extended)) ?>
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
}
  // Remove thumbnail from within post content
function nope() { return false; }
add_filter('thematic_post_thumbs', 'nope'); 

// Increase post thumbnail image thumbnail size
function hdo_thematic_post_thumb_size() {
  return apply_filters('hdo_thematic_post_thumb_size', array(260, 260));
}
add_filter('thematic_post_thumb_size','hdo_thematic_post_thumb_size');

// POST NAVIGATION //
function openstate_next_post_link_args() {
  $args = array ( 
    'format'              => '%link',
    'link'                => '<span class="meta-nav">Next >></span>',
    'in_same_cat'         => FALSE,
    'excluded_categories' => ''
    );
  return $args;
}
add_filter('thematic_next_post_link_args', 'openstate_next_post_link_args');

function openstate_previous_post_link_args() {
  $args = array ( 
    'format'              => '%link',
    'link'                => '<span class="meta-nav"><< Previous</span>',
    'in_same_cat'         => FALSE,
    'excluded_categories' => ''
    );
  return $args;
}
add_filter('thematic_previous_post_link_args', 'openstate_previous_post_link_args');  

function childtheme_override_nav_below() {
  if (is_single()) {

    wp_reset_postdata();
    wp_reset_query();
    rewind_posts();

    $nextPost = get_next_post(false);
    $nextThumb = get_the_post_thumbnail($nextPost->ID, array(100,100));
    $prevPost = get_previous_post(false);
    $prevThumb = get_the_post_thumbnail($prevPost->ID, array(100,100));

    ?>

    <div id="nav-below" class="navigation">
      <div class="nav-previous"><?php
      thematic_previous_post_link(); 
      echo '<p>' . get_the_title($prevPost->ID) . '</p>';
      echo $prevThumb;
      ?></div>
      <div class="nav-next"><?php 
      thematic_next_post_link();
      echo '<p>' . get_the_title($nextPost->ID) . '</p>';
      echo $nextThumb;
      ?></div>
    </div>

    <?php
  } else { ?>
    <div id="nav-below" class="navigation">
      <?php if(function_exists('wp_pagenavi')) { ?>
      <?php wp_pagenavi(); ?>
      <?php } else { ?>  

      <div class="nav-previous"><?php next_posts_link(sprintf('<span class="meta-nav">&laquo;</span> %s', __('Older posts', 'thematic') ) ) ?></div>

      <div class="nav-next"><?php previous_posts_link(sprintf('%s <span class="meta-nav">&raquo;</span>',__( 'Newer posts', 'thematic') ) ) ?></div>

      <?php } ?>
    </div>  
    
    <?php
  }
}
add_action('thematic_abovefooter','thematic_nav_below');

function childtheme_override_nav_above() { 
  ?>
  <div id="nav-above"></div>
  <?php 
}

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

  return $content;

}
add_filter('thematic_page_title', 'openstate_page_title');  

?>
