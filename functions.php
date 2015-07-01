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
        'slidejs',
        get_stylesheet_directory_uri() . '/scripts/slides.min.jquery.js',
        array('jquery')
      );
      wp_enqueue_script(
        'openstatejs',
        get_stylesheet_directory_uri() . '/scripts/openstate.js',
        array('slidejs'),
        false,
        true
      );                
  }    
  add_action('wp_enqueue_scripts', 'openstate_enqueue_scripts');

  // Add custom post type for announcements
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


// Fix for qTranslate plugin and "Home" menu link reverting back to default language
/// *** This is now in thematic/functions.php !!! *** ///
// if (function_exists('qtrans_convertURL')) {
// function openstate_qtrans_in_nav_el($output, $item, $depth, $args) {
// $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
// $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
// $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
// // Integration with qTranslate Plugin
// $attributes .=!empty($item->url) ? ' href="' . esc_attr( qtrans_convertURL($item->url) ) . '"' : '';

// $output = $args->before;
// $output .= '<a' . $attributes . '>';
// $output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
// $output .= '</a>';
// $output .= $args->after;
// return $output;
// }
// add_filter('walker_nav_menu_start_el', 'openstate_qtrans_in_nav_el', 10, 4);
// }
  
  // Add announcements to top of sidebar
  function openstate_abovemainasides()  {  
    $args = array( 
      'post_type' => array(
        'announcement',
        'post'
      ),
      'category_name' => 'events',
      'posts_per_page' => 3 );
    $loop = new WP_Query( $args );
    ?>
    <div class="aside main-aside">
  		<ul class="xoxo">
  			<li id="announcements" class="widgetcontainer widget_announcement">
          <!--span id="announcement_icon"></span><span class="widgettitle">Announcements</span></br-->
          <h3 class="widgettitle"><?php echo _e("Announcements", 'thematic-openstate') ?></h3>
          <div class="slides_container">
            <?php 
              while ( $loop->have_posts() ) : $loop->the_post();
                echo '<div>';
                if (get_post_type() == 'post') {
                   echo '<a class="announcement-link" href="' . get_permalink() . '">';
                }
                echo '<h4 class=\'announcement-title\'>';
                the_title();
                echo '</h4>';
                the_excerpt();
                if (get_post_type() == 'post') {
                   echo '</a>';
                }
                echo '</div>';
              endwhile;
            ?>
          </div>
      </li>
  		</ul>
    </div>
    <?php
  } 
//  add_action('thematic_abovemainasides','openstate_abovemainasides');
  
  // Add mission statements
  function openstate_mission_statements() {
    if(is_home()){
      // Mission statements
      $args = array( 
        'post_type' => array(
          'announcement',
          'post'
        ),
        'category_name' => 'mission_statements',
        'posts_per_page' => 3 );
      $loop = new WP_Query( $args );
      ?>
      <div class='statements'>
        <div class="slides_container">
          <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
              <div>
                <?php if (get_post_type() == 'post') { ?> <a class="statement-link" href=" <?php get_permalink() ?>"> <?php } ?>
                <?php kd_mfi_the_featured_image( 'statement-head', 'post', 'full' ) || kd_mfi_the_featured_image( 'statement-head', 'announcement', 'full' ); ?>
                <div>
                  <h3 class="statement-title"> <?php the_title(); ?> </h3>
                  <?php the_excerpt(); ?>
                </div>
                <?php if (get_post_type() == 'post') { ?> </a> <?php } ?>
              </div>
            <?php endwhile; ?>
        </div>
      </div>
      <?php
    }
  }
  add_action('thematic_belowheader','openstate_mission_statements');


  //// Add big navigation ////

  function openstate_nav_menu_args($args) {
    // Only show the shallow primary menu
    $args['depth'] = 1;
    return apply_filters('openstate_nav_menu_args', $args);
  }
  add_filter('thematic_nav_menu_args', 'openstate_nav_menu_args');

  // Get a certain submenu
  // source: http://www.ordinarycoder.com/wordpress-wp_nav_menu-show-a-submenu-only/

  function submenu_get_children_ids( $id, $items ) {
    $ids = wp_filter_object_list( $items, array( 'menu_item_parent' => $id ), 'and', 'ID' );
    foreach ( $ids as $id ) {
      $ids = array_merge( $ids, submenu_get_children_ids( $id, $items ) );
    }
    return $ids;
  }
  function submenu_limit( $items, $args ) {
    if ( empty($args->submenu) )
      return $items;
    $filter_object_list = wp_filter_object_list( $items, array( 'object_id' => $args->submenu ), 'and', 'ID' );
    $parent_id = array_pop( $filter_object_list );
    $children  = submenu_get_children_ids( $parent_id, $items );
    foreach ( $items as $key => $item ) {
      if ( ! in_array( $item->ID, $children ) )
        unset($items[$key]);
    }
    return $items;
  }
  add_filter( 'wp_nav_menu_objects', 'submenu_limit', 10, 2 );

  function menu_get_ancestor( $id, $items ) {
    // Hack to get menu root
    $ancs = wp_filter_object_list( $items, array( 'object_id' => $id ), 'and', 'menu_item_parent' );
    if (empty($ancs)) {
      return array_pop(wp_filter_object_list( $items, array( 'ID' => $id ), 'and', 'object_id' ));
    } else {
      $anc = array_pop($ancs);
      return ($anc=='0')? $id : menu_get_ancestor($anc, $items);
    }
  }

  function openstate_big_nav() {
    if(is_page()){
      ?>
        <div class='statements'>
          <div id="big-navigation">
            <?php
            $id = get_the_ID();

            $menu_name = 'primary-menu';
            $locations = get_nav_menu_locations();
            $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
            $menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );
            $root = menu_get_ancestor($id, $menuitems);
            wp_nav_menu(array( 'theme_location'=>'primary-menu','submenu'=>$root, 'depth'=>1 ));

            // var_dump($menuitems);


            ?>
          </div>
        </div>
      <?php
    }
  }
  add_action('thematic_belowheader','openstate_big_nav');

  
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

  // Put the postmeta above the posttitle
  function childtheme_override_postheader() {
    global $post;
    if ( is_404() || $post->post_type == 'page') {
       $postheader = thematic_postheader_posttitle();        
    } else {
       $postheader =  thematic_postheader_postmeta() . thematic_postheader_posttitle();
    }
    echo apply_filters( 'thematic_postheader', $postheader ); // Filter to override default post header
  }
  
  // Add avatar to author link
  function childtheme_override_postmeta_authorlink(){
		global $authordata;
    
      $author_avatar = '<span class="post-author" >';
      $author_avatar .= get_avatar( get_the_author_meta('ID'), 32 );
      $author_avatar .= '</span>';  
	
	    $author_prep = '<span class="meta-prep meta-prep-author">' . __('Posted by', 'thematic') . ' </span>';
	    
	    if ( thematic_is_custom_post_type() && !current_theme_supports( 'thematic_support_post_type_author_link' ) ) {
	    	$author_info  = '<span class="vcard"><span class="fn nickname">';
	    	$author_info .= get_the_author_meta( 'display_name' ) ;
	    	$author_info .= '</span></span>';
	    } else {
	    	$author_info  = '<span class="author vcard">';
	    	$author_info .= sprintf('<a class="url fn n" href="%s" title="%s">%s</a>',
	    							get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
									/* translators: author name */
	    							sprintf( esc_attr__( 'View all posts by %s', 'thematic' ), get_the_author_meta( 'display_name' ) ),
	    							get_the_author_meta( 'display_name' ));
	    	$author_info .= '</span>';
	    }
	    
	    $author_credit = $author_avatar . $author_prep . $author_info ;
	    
	    return apply_filters('thematic_postmeta_authorlink', $author_credit);
  }
  
  // Increase post thumbnail image thumbnail size
  function hdo_thematic_post_thumb_size() {
      return apply_filters('hdo_thematic_post_thumb_size', array(260, 260));
  }
  add_filter('thematic_post_thumb_size','hdo_thematic_post_thumb_size');
  
  // Add featured image for single post header
  $singleposthead = array(
          'id' => 'single-post-head',
          'post_type' => 'post',
          'labels' => array(
              'name'      => 'Single Post Head Image',
              'set'       => 'Set image (620x410)',
              'remove'    => 'Remove image',
              'use'       => 'Use as post head',
          )
  );

  // Add featured image for single post header
  $statementhead_post = array(
          'id' => 'statement-head',
          'post_type' => 'post',
          'labels' => array(
              'name'      => 'Mission Statement Head Image',
              'set'       => 'Set image (660x310)',
              'remove'    => 'Remove image',
              'use'       => 'Use as mission statement image',
          )
  );
  
  // Add featured image for single post header
  $statementhead_announcement = array(
          'id' => 'statement-head',
          'post_type' => 'announcement',
          'labels' => array(
              'name'      => 'Mission Statement Head Image',
              'set'       => 'Set image (660x310)',
              'remove'    => 'Remove image',
              'use'       => 'Use as mission statement image',
          )
  );


/*
 * An image carrousel using the 'Multiple Features Images' wordpress plugin
 * The plugin must be installed. class_exists checks whether it's installed.
 */ 
if (class_exists( 'kdMultipleFeaturedImages' )) {
  new kdMultipleFeaturedImages( $singleposthead );
  new kdMultipleFeaturedImages( $statementhead_post );
  new kdMultipleFeaturedImages( $statementhead_announcement );
  
  // Add featured image to single posts
  function openstate_thematic_postheader_posttitle($posttitle){
    
    if(is_single()){
      $image = kd_mfi_the_featured_image( 'single-post-head', 'post', 'full' );
      $posttitle = $image . $posttitle; 
    }
    
    return apply_filters('openstate_thematic_postheader_posttitle', $posttitle);
  }
  add_filter('thematic_postheader_posttitle','openstate_thematic_postheader_posttitle');  
  
  function openstate_singlecomment_text() {
      $content = sprintf( _x( '%1$sOne%2$s Thought' , 'One Thought, where %$1s and %$2s are <span> tags', 'thematic' ), '<span>' , '</span>' );
      return apply_filters( 'openstate_singlecomment_text', $content );
  }
  add_filter('thematic_singlecomment_text','openstate_singlecomment_text');
  
  function openstate_multiplecomments_text() {
      $content = '<span>%d</span> ' . __('Thoughts', 'thematic');
      return apply_filters( 'openstate_multiplecomments_text', $content );
  }
  add_filter('thematic_multiplecomments_text','openstate_multiplecomments_text');
}
// END multiple featured images carrousel

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
    <div id="nav-above">
      <h2><?php _e("Latest Entries", 'thematic-openstate'); ?></h2>
    </div>
    
    
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
