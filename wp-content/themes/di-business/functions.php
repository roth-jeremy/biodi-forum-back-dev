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
 * Add a new tab 'Plants' to the Admin dashboard
 */

function my_admin_menu()
{
    add_menu_page('Plantes', 'Plantes', 'manage_options',
        'myplugin/plants-page.php', 'plants_page', 'dashicons-tickets', 6);
}

add_action('admin_menu', 'my_admin_menu');

/**
 * HTML form to insert a new plant
 */
function plants_page()
{
    ?>
    <h3>Ajout d'une nouvelle plante</h3>
    <form id="plantForm"
          action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
          method="post">
        <label for="name">Nom</label>
        <input id="name" name="name" type="text" required>

        <label for="description">Description</label>
        <input id="description" name="description" type="text">

        <br>
        <label for="initialBudget">Budget initial</label>
        <select id="initialBudget" name="initialBudget">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <label for="exploitationBudget">Budget exploitation</label>
        <select id="exploitationBudget" name="exploitationBudget">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br>
        <label for="initialTime">Temps d'installation</label>
        <select id="initialTime" name="initialTime">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <label for="exploitationTime">Temps d'exploitation</label>
        <select id="exploitationTime" name="exploitationTime">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br>
        <label for="size">Taille</label>
        <select id="size" name="size">
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
        </select>
        <br>
        <label for="sunlight">Ensoleillement</label>
        <select id="sunlight" name="sunlight">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <label for="favorising">Espèces animales attirées</label>
        <select id="favorising" name="favorising">
            <option value="bugs">insectes</option>
            <option value="birds">oiseaux</option>
        </select>
        <br>
        <input type="hidden" name="action" value="newPlant">
        <input type="submit" name="plantSubmit" value="Créér">
    </form>
    <?php
}

/**
 * Creation of a new plant in the database.
 * Verify data and do SQL INSERT
 * Fire when the form @plantForm is submit.
 */
function newPlant()
{
    if (!isset($_POST['plantSubmit'])) {
        return;
    }

    foreach ($_POST as $name => $value) {
        if (empty($value)) {
            echo "Le champ -" . $name . "- est vide";
            return;
        }
    }

    global $wpdb;
    //$wpdb->query("INSERT INTO plants(name,description) VALUES("        . $_POST['name'] . "," . $_POST['description'] . ")");
    echo $wpdb->insert('plants', array(
        'name'               => $_POST['name'],
        'description'        => $_POST['description'],
        'initialBudget'      => $_POST['initialBudget'],
        'exploitationBudget' => $_POST['exploitationBudget'],
        'initialTime'        => $_POST['initialTime'],
        'exploitationTime'   => $_POST['exploitationTime'],
        'size'               => $_POST['size'],
        'sunlight'           => $_POST['sunlight'],
        'favorising'         => $_POST['favorising']
    ));
}

add_action('admin_post_newPlant', 'newPlant');
