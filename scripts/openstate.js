jQuery('#announcements').slides();
jQuery('.statements').slides();
jQuery(".menu > ul > li > ul").parent().addClass("has-sub-menu");

// adds name fields to MailChimp widget in sidebar
jQuery('input[name="mc_mv_EMAIL"]').attr("placeholder", "Email Address");
jQuery('input[name="mc_mv_FNAME"]').attr("placeholder", "Name");