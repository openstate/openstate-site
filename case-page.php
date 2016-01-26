<?php
/*
Template Name: case-page
*/

include 'case_and_theme_function.php';

function widget_area_primary_aside_case(){
	echo get_post_meta($post->ID, 'tweet', true);
}

add_action('widget_area_primary_aside', 'widget_area_primary_aside_case');

add_action('thematic_belowheader', 'openstate_thematic_case_theme_belowheader');	

?>

<?php
/**
 * Page Template
 *
 * …
 * 
 * @package Thematic
 * @subpackage Templates
 */
 
    // calling the header.php
    get_header();

    // action hook for placing content above #container
    thematic_abovecontainer();
    ?>
    	<div id="parentlink"><h1>
    	<?php 

			if ( $post->post_parent ) { ?>
			    <a href="<?php echo get_permalink( $post->post_parent ); ?>" >
			        <?php echo '≪' . get_the_title( $post->post_parent ); ?>
			    </a>
			<?php } ?>

		</h1></div>
		<div id="container">


		
			<?php



				// action hook for placing content above #content
				thematic_abovecontent();
 
				// filter for manipulating the element that wraps the content 
				echo apply_filters( 'thematic_open_id_content', '<div id="content">' . "\n" );
			
				// calling the widget area 'page-top'
	            get_sidebar('page-top');
	
	            // start the loop
	            while ( have_posts() ) : the_post();

				// action hook for placing content above #post
	            thematic_abovepost();
	        ?>
    	     		
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> > 

				<?php
	                
	                // creating the post header
	                thematic_postheader();
				?>
	                
					<div class="entry-content">
	
						<?php
	                    	the_content();
	                    
	                    	wp_link_pages( "\t\t\t\t\t<div class='page-link'>" . __( 'Pages: ', 'thematic' ), "</div>\n", 'number' );
	                    
	                    	edit_post_link( __( 'Edit', 'thematic' ), "\n\t\t\t\t\t\t" . '<span class="edit-link">' , '</span>' . "\n" );
	                    ?>

					</div><!-- .entry-content -->
					
				</div><!-- #post -->
	
			<?php
				// action hook for inserting content below #post
	        	thematic_belowpost();
	        		        
       			// action hook for calling the comments_template
       			thematic_comments_template();
        		
	        	// end loop
        		endwhile;
	        
	        	// calling the widget area 'page-bottom'
	        	get_sidebar( 'page-bottom' );
	        ?>
	
			</div><!-- #content -->
			
			<?php 
				// action hook for placing content below #content
				thematic_belowcontent(); 
			?> 
			
		</div><!-- #container -->

	<div id="primary" class="aside main-aside">
		<div id="author-box">
	    <?php
            print_the_author();
		?>

	    </div>
	    <div id="newscontainer" >
	    <?php
	    	$categories = get_the_category();
			$category_slug = $categories[0]->slug;

	    	query_posts(array(
	    		'post_type' => 'post',
	    		'tax_query' => array(
				    array(
				        'taxonomy' => 'category',
				        'terms' => array( $category_slug ),
				        'field' => 'slug',
				    )
				)
			));
			if (have_posts()):
			?>
		    <h1><?= __('News', 'thematic-openstate') ?></h1>
			<ul class="newslist">
				<?php
				while ( have_posts() ) : the_post(); ?>
			
				<li>
					
					<?php 
					  echo '<span class="entry-date"><abbr class="published" title="'.
						  get_the_time(thematic_time_title()) . '">'.
					  	  get_the_time(thematic_time_display()).
					      '</abbr></span>';
					?>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> 

				</li>
				
				<?php endwhile; 
			?>
			</ul>
			<?php endif; ?>
	    </div>
	</div>		

<?php 
    // action hook for placing content below #container
    thematic_belowcontainer();

    // calling the standard sidebar 
    //thematic_sidebar();


    
    //widget_area_primary_aside();
    
    // calling footer.php
    get_footer();
?>