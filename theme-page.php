<?php
/*
Template Name: theme-page
*/
include 'case_and_theme_function.php';

function widget_area_primary_aside_theme(){
    echo get_post_meta($post->ID, 'tweet', true);
}

add_action('widget_area_primary_aside', 'widget_area_primary_aside_theme');

add_action('thematic_belowheader', 'openstate_thematic_case_theme_belowheader');

function openstate_thematic_belowcontainer() {
    ?>
    <div class="theme-subs">
        
        <?php
            $cases_category_id = openstate_get_option('openstate_cases_catid');
            openstate_page_list($cases_category_id, get_the_ID(), 4, 'Cases');
        ?> 

        <?php
            $tools_category_id = openstate_get_option('openstate_tools_catid');
            openstate_page_list($tools_category_id, get_the_ID(), 4, 'Tools');
        ?>
    </div>
    <?php
}

add_action('thematic_belowcontainer', 'openstate_thematic_belowcontainer'); 

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
        <?php
        $facts = get_post_meta($post->ID, 'fact', false);
        foreach ($facts as $fact){
            echo '<div class="fact-container">';
            echo apply_filters('thematic_post', apply_filters('the_content', $fact));
            echo '</div>';
        }
        
        ?>
</div>


<?php
    thematic_belowcontainer();
    // calling the standard sidebar 
    //thematic_sidebar();
    
    // calling footer.php
    get_footer();
?>