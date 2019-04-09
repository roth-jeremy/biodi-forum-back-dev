<?php

if (!defined('ABSPATH')) {
    die("No Direct access");
}

// Load init.php file.
require_once get_template_directory() . '/inc/init.php';


/**
 * Add new fields above 'Update' button.
 *
 * @param WP_User $user User object.
 */
function biodi_additional_profile_fields($user)
{
    $neighbourhoods = array('', 'nord', 'sud', 'ouest', 'est', 'centre');
    $user_neighbourhood = get_the_author_meta('neighbourhood', $user->ID);
    ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="neighbourhood">Neighbourhood</label></th>
            <td>
                <select id="neighbourhood" name="neighbourhood"><?php
                    for ($i = 0; $i <= 5; $i++) {
                        if ($neighbourhoods[$i] == $user_neighbourhood) {
                            print("<option value='" . $neighbourhoods[$i]
                                . "' selected='selected'>" . $neighbourhoods[$i]
                                . "</option>");
                        } else {
                            print("<option value='" . $neighbourhoods[$i] . "'>"
                                . $neighbourhoods[$i] . "</option>");
                        }
                    }

                    ?></select>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'biodi_additional_profile_fields');
add_action('edit_user_profile', 'biodi_additional_profile_fields');


/**
 * Save additional profile fields.
 *
 * @param int $user_id Current user ID.
 */
function biodi_save_profile_fields($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    if (empty($_POST['neighbourhood'])) {
        return false;
    }
    update_usermeta($user_id, 'neighbourhood', $_POST['neighbourhood']);
}

add_action('personal_options_update', 'biodi_save_profile_fields');
add_action('edit_user_profile_update', 'biodi_save_profile_fields');

/**
 * Add some new plants
 */
function plants_page()
{
    ?>
    <h3>Add a new plant</h3>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
          method="post">
        <label for="name">Nom</label>
        <input id="name" name="name" type="text" required>

        <label for="description">Description</label>
        <input id="description" name="description" type="text" required>

        <input type="hidden" name="action" value="newPlant">
        <input type="submit" name="plantSubmit" value="Créér">
    </form>
    <?php

}

add_action('admin_menu', 'my_admin_menu');

function my_admin_menu()
{
    add_menu_page('Plantes', 'Plantes', 'manage_options',
        'myplugin/plants-page.php', 'plants_page', 'dashicons-tickets', 6);
}

add_action('admin_post_newPlant', 'newPlant');
/**
 * Database insert
 */
function newPlant()
{
    var_dump($_POST);
    if (!isset($_POST['plantSubmit'])) {
        return;
    }

    if (empty($_POST['name']) || empty($_POST['description'])) {
        return;
    }

    global $wpdb;
    //$wpdb->query("INSERT INTO plants(name,description) VALUES("        . $_POST['name'] . "," . $_POST['description'] . ")");
    echo $wpdb->insert('plants',array(
            'name' => $_POST['name'],
            'description' => $_POST['description']
    ));
}
