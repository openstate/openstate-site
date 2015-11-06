<?php
/*
Template Name: theme-page
*/

function widget_area_primary_aside_theme(){
    echo get_post_meta($post->ID, 'tweet', true);
}

add_action('widget_area_primary_aside', 'widget_area_primary_aside_theme');

function openstate_thematic_theme_belowheader() {
    if ( has_post_thumbnail() ) {
        echo '<div class="case-header-img" style="background-image:url(\''.
            wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' )[0].
        '\')"></div>';
    }
}
add_action('thematic_belowheader', 'openstate_thematic_theme_belowheader');

function openstate_thematic_belowcontainer() {
    ?>
    <div class="theme-subs">
        <h1>Cases</h1>
        <?php
            $cases_category_id = openstate_get_option('openstate_cases_catid');
            openstate_page_list($cases_category_id, get_the_ID(), 4);
        ?>
        
        <h1>Tools</h1>
        <?php
            $tools_category_id = openstate_get_option('openstate_tools_catid');
            openstate_page_list($tools_category_id, get_the_ID(), 4);
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
    <?php
        $facts = get_post_meta($post->ID, 'fact', false);
        foreach ($facts as $fact){
            echo '<div class="fact-container">';
            echo apply_filters('thematic_post', apply_filters('the_content', $fact));
            echo '</div>';
        }
        
        ?>
        <div id="author-box" class="case-page-box">
        <?php
        print_the_author();

        function print_the_author(){
            if (function_exists('get_wp_user_avatar')) {
                echo '<p class="author">'.
                        __('contact', 'thematic-openstate').
                        '<br>'.
                        get_wp_user_avatar( $user_id, 'thumbnail');
                ?>
                <a href="<?= get_the_author_meta( 'user_url' ) ?>"> <?= the_author_meta( 'first_name' )?>  <?= the_author_meta( 'last_name' ) ?> </a>
                <?php
                echo '</p>';
            }
        }?>
    </div>
</div>


<?php
    thematic_belowcontainer();
    // calling the standard sidebar 
    //thematic_sidebar();
    
    // calling footer.php
    get_footer();
?>