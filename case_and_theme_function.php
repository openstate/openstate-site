 <?php

function openstate_thematic_case_theme_belowheader() {
  if ( has_post_thumbnail() ) {
  		echo '<div class="case-header-img" style="background-image:url(\''.
  			wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' )[0].
  		'\')"></div>';
  }
}

function print_the_author(){
	if (function_exists('get_wp_user_avatar')) {
        echo '<p class="author"><br>'.
                get_wp_user_avatar( $user_id, 'thumbnail');

                echo __('Get in touch with', 'thematic-openstate')
        ?>
        <br>
        <a href="<?= get_the_author_meta( 'user_url' ) ?>"> <?= the_author_meta( 'first_name' )?>  <?= the_author_meta( 'last_name' ) ?> </a>
        <?php            
        echo    '<span class="contactlink">'.
            '</p>';
	}
}
?>