<?php

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
          <h3 class="widgettitle">Announcements</h3>
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
  add_action('thematic_abovemainasides','openstate_abovemainasides');
  
  // Add mission statements to header
  function openstate_belowheader() {
    if(is_home()){
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
          <?php
            while ( $loop->have_posts() ) : $loop->the_post();
              echo '<div>';
              if (get_post_type() == 'post') {
                 echo '<a class="statement-link" href="' . get_permalink() . '">';
              }
              kd_mfi_the_featured_image( 'statement-head', 'post', 'full' ) || kd_mfi_the_featured_image( 'statement-head', 'announcement', 'full' );
              echo '<div>';
              echo '<h3 class=\'statement-title\'>';
              the_title();
              echo '</h3>';
              the_excerpt();
              echo '</div>';
              if (get_post_type() == 'post') {
                echo '</a>';
              }
              echo '</div>';
            endwhile;
          ?>
        </div>
      </div>
      <?php
    }
  }
  add_action('thematic_belowheader','openstate_belowheader');
  
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
              'name'      => 'Singel Post Head Image',
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
      
      $nextPost = get_next_post(true);
      $nextThumb = get_the_post_thumbnail($nextPost->ID, array(100,100));
      $prevPost = get_previous_post(true);
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
      <h2>Latest Entries</h2>
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