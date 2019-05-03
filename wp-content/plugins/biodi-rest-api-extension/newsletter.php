<?php
/**
 * Add a new tab 'plants to the Admin dashboard--------------------------------
 */
function add_page_newsletter()
{
    add_menu_page('Newsletter', 'Newsletter', 'manage_options',
        'myplugin/newsletter.php', 'newsletter_page', 'dashicons-tickets', 7);
}

add_action('admin_menu', 'add_page_newsletter');

function newsletter_page()
{
	global $wpdb;
	$results = $wpdb->get_results("SELECT user_email FROM `wp_usermeta` INNER JOIN wp_users ON wp_usermeta.user_id = wp_users.ID WHERE meta_key = 'newsletter' AND meta_value = 1", OBJECT);

	foreach ($results as $key => $value) {
		echo $value->user_email;
	}
}
?>