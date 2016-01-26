jQuery(function() {
var noThumbPosts;

noThumbPosts = jQuery(".home .post, .category .post").filter(function(post) {
  return jQuery('.entry-thumb', this).length === 0
});

jQuery(".entry-title a", noThumbPosts).css('padding-left', '0px');
jQuery(".entry-date", noThumbPosts).css('margin-left', '0px');

// adds name fields to MailChimp widget in sidebar
jQuery('input[name="mc_mv_EMAIL"]').attr("placeholder", "Email Address");
jQuery('input[name="mc_mv_FNAME"]').attr("placeholder", "Name");

});