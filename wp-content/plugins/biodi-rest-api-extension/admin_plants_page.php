<?php
/**
 * Add a new tab 'plants to the Admin dashboard--------------------------------
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
<script>
    function hey(id) {
        var b = document.getElementsByClassName("groupPlants");
        for (i = 0; i < b.length; i++) {
            b.item(i).style.display = "none";
        }
        var x = id;
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

</script>
<h1>Gestion des plantes :</h1>
<ul class="list-group groupPlantsNav">
    <a href="#" onclick="hey(Planteenpot)">
        <li class="list-group-item">Plante en pot</li>
    </a>
    <a href="#" onclick="hey(Plantemurale)">
        <li class="list-group-item">Plante murale</li>
    </a>
    <a href="#" onclick="hey(Plantesuspendue)">
        <li class="list-group-item">Plante suspendue</li>
    </a>
    <a href="#" onclick="hey(Construction)">
        <li class="list-group-item">Construction</li>
    </a>
</ul>
<?php
    insertForm(1,'Plante en pot');
    insertForm(2,'Plante murale');
    insertForm(3,'Plante suspendue');
    insertForm(4,'Construction');
}

function insertForm($arrangement, $title)
{
    wp_register_script('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');
    wp_register_style('prefix_bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
    ?>
<div id=<?php echo str_replace(' ','',$title); ?> class="groupPlants" style="display:none;">
    <h2><?php echo $title; ?> </h2>
    <div>
        <h3>Création
        </h3>
        <form id="plantForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="plant-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nom</label>
                <input class="form-control" id="name" name="name" type="text" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input class="form-control" id="description" name="description" type="text">
            </div>
            <div class="form-group">
                <label for="maintain">Entretien</label>
                <input class="form-control" id="maintain" name="maintain" type="text">
            </div>
            <div class="form-group col-sm-6">
                <label for="initialBudget">Budget initial</label>
                <select class="form-control" id="initialBudget" name="initialBudget">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="exploitationBudget">Budget exploitation</label>
                <select class="form-control" id="exploitationBudget" name="exploitationBudget">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="initialTime">Temps d'installation</label>
                <select class="form-control" id="initialTime" name="initialTime">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="exploitationTime">Temps d'exploitation</label>
                <select class="form-control" id="exploitationTime" name="exploitationTime">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="size">Taille</label>
                <select class="form-control" id="size" name="size">
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="sunlight">Ensoleillement</label>
                <select class="form-control" id="sunlight" name="sunlight">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="favorising">Espèces animales attirées</label>
                <select class="form-control" id="favorising" name="favorising">
                    <option value="bugs">Insectes</option>
                    <option value="birds">Oiseaux</option>
                </select>
            </div>
            <div class="form-group col-sm-6">
                <label for="plantPictures">Photo de la plante/construction</label>
                <input class="form-control-file" type="file" name="plantPicture" id="plantPicture" size="25" />
                <input class="form-control" type="hidden" id="arrangement" name="arrangement" value="<?php echo $arrangement; ?>" hidden>
            </div>
            <input type="hidden" name="action" value="newPlant">
            <input type="submit" id="plantSubmit" name="plantSubmit" value="Créér" class="btn btn-success btn-lg validForm" style="margin-left:25%; width:50%;">
        </form>

    </div>

    <?php
    retrieveContents($arrangement);
    ?>
</div>
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
    uploadPicture();
    var_dump($_POST);
    global $wpdb;
    $wpdb->show_errors();
    $inserted = $wpdb->insert('contents', array(
        'name'               => mres ($_POST['name']),
        'description'        => mres ($_POST['description']),
        'maintain'           => mres ($_POST['maintain']),
        'initialBudget'      => $_POST['initialBudget'],
        'exploitationBudget' => $_POST['exploitationBudget'],
        'initialTime'        => $_POST['initialTime'],
        'exploitationTime'   => $_POST['exploitationTime'],
        'size'               => $_POST['size'],
        'sunlight'           => $_POST['sunlight'],
        'favorising'         => $_POST['favorising'],
        'img'                => $_POST['urlImg'],
        '_arrangement'       => ((int)$_POST['arrangement'])
    ));
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));
    if ($inserted === false) {
        echo 'error during creation';
    } else { 
        wp_redirect($current_url
            . "/wp-admin/admin.php?page=myplugin%2Fplants-page.php");
    }
}

add_action('admin_post_newPlant', 'newPlant');
/**
 * Add Images to Worpdress Media Library
 */
function uploadPicture()
{
    if(!isset($_FILES['plantPicture'])){
        echo "no img";
        return 'no IMG';
    }
    // WordPress environment
    require(dirname(__FILE__) . '/../../../wp-load.php');

    $wordpress_upload_dir = wp_upload_dir();
    $i=1; // number of tries when the file with the same name is already exists
    $plantPicture = $_FILES['plantPicture'];
    $new_file_path = $wordpress_upload_dir['path'] . '/'
        . $plantPicture['name'];
    $imgUrl = "";
    $new_file_mime = mime_content_type($plantPicture['tmp_name']);

    if (empty($plantPicture)) {
        die('File is not selected.');
    }

    if ($plantPicture['error']) {
        die($plantPicture['error']);
    }

    if ($plantPicture['size'] > wp_max_upload_size()) {
        die('It is too large than expected.');
    }

    if (!in_array($new_file_mime, get_allowed_mime_types())) {
        die('WordPress doesn\'t allow this type of uploads.');
    }

    while (file_exists($new_file_path)) {
        $i++;
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_'
            . $plantPicture['name'];
        $imgUrl = $wordpress_upload_dir['url'] . '/' . $i . '_'
            . $plantPicture['name'];
    }

// looks like everything is OK
    if (move_uploaded_file($plantPicture['tmp_name'], $new_file_path)) {

        $upload_id = wp_insert_attachment(array(
            'guid'           => $new_file_path,
            'post_mime_type' => $new_file_mime,
            'post_title'     => preg_replace('/\.[^.]+$/', '',
                $plantPicture['name']),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ), $new_file_path);

        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $_POST['urlImg'] = $imgUrl;
        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata($upload_id,
            wp_generate_attachment_metadata($upload_id, $new_file_path));
    }
}

/**
 * Retrieve all plants
 */
function retrieveContents($arrangement)
{
    global $wpdb;
    $wpdb->show_errors();
    $plants = $wpdb->get_results("SELECT * FROM contents WHERE _arrangement =". $arrangement, OBJECT);
    ?>
<h2>Modification</h1>
    <?php
    foreach ($plants as $data) {
        ?>
    <script>
        var myplants = <?php echo json_encode($plants); ?>;

    </script>
    <div class="card row" style="margin-left:15%;" >
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="updatePlantForm" method="post" enctype="multipart/form-data">
            <?php
                $idPlant;
                foreach ($data as $name => $value) {
                    if($name === 'img')
                    { ?>
            <img width="100" src="<?php echo $value; ?>" style="margin-left:25%; width:50%;"> <?php
                    }
                    if ($name === 'name' || $name === 'description'|| $name === 'maintain') {
                        ?>
            <div class="form-group">
                <label for=<?php echo $name; ?>><?php echo $name; ?></label>
                    <input class="form-control" name="<?php echo $name; ?>" id="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>">
            </div>
            <?php
                    }
                    ?>
            <?php
                    if ($name === 'id') {
                        $idPlant = $value;
                    }
                    if ($name === 'initialTime' || $name === 'exploitationTime'
                        || $name === 'initialBudget'
                        || $name === 'exploitationBudget'
                        || $name === 'favorising'
                        || $name === 'sunlight'
                    ) {
                        echo biodi_select($value, $name);
                    }
                }
                ?>
            <input class="form-control" id="idPlant" name="idPlant" /> <input class="form-control-file" type="file" name="plantPicture" id="plantPicture" size="25" />
            <input type="hidden" name="action" value="updatePlantFunction">
            <button name="modify" class="btn btn-primary" id="modify" type="submit" style="margin-left:15%; width:70%;">Sauvegarder les changements
            </button>
        </form>
    </div>
    <?php
    }
}

/**
 * Update a plant
 */
function updatePlantFunction()
{
    if (!isset($_POST['modify'])) {
        return;
    }
    foreach ($_POST as $name => $value) {
        if (empty($value) && $name != 'modify') {
            echo "Le champ -" . $name . "- est vide";
            return;
        }
    }
    uploadPicture();
    global $wp;
    $current_url = home_url(add_query_arg(array(), $wp->request));

    global $wpdb;
    $updated = $wpdb->update('contents', array(
        'name'               => $_POST['name'],
        'description'        => $_POST['description'],
        'maintain'           => $_POST['maintain'],
        'initialBudget'      => $_POST['initialBudget'],
        'exploitationBudget' => $_POST['exploitationBudget'],
        'initialTime'        => $_POST['initialTime'],
        'exploitationTime'   => $_POST['exploitationTime'],
        'size'               => $_POST['size'],
        'sunlight'           => $_POST['sunlight'],
        'favorising'         => $_POST['favorising'],
        'img'                => $_POST['urlImg']
    ), array('id' => $_POST['idPlant']));

    if ($updated === false) {
        echo 'error during updating';
    } else {

        wp_redirect($current_url
            . "/wp-admin/admin.php?page=myplugin%2Fplants-page.php");
    }
}

add_action('admin_post_updatePlantFunction', 'updatePlantFunction');


/**
 * Transform enum type from DB to html dropdown list with the correct field selected
 */
function biodi_select($default_value, $selectName)
{
    $select = '<label style="margin-top:3%;">' . $selectName . '</label> <select class="form-control" name="' . $selectName
        . '">';
    if ($selectName === 'favorising') {
        $options = array('oiseaux', 'insectes');
    } else {
        $options = array(1, 2, 3, 4, 5);
    }

    foreach ($options as $option) {
        $select .= write_option($option, $option, $default_value);
    }
    $select .= '</select>';
    return $select;
}

function write_option($value, $display, $default_value = '')
{
    $option = '<option value="' . $value . '"';
    $option .= ($default_value == $value) ? ' SELECTED' : '';
    $option .= '>' . $display . '</option>';
    return $option;
}
function admin_style() {
  wp_enqueue_style('admin-styles', get_template_directory_uri().'/admin.css');
}
add_action('admin_enqueue_scripts', 'admin_style');

function mres($value)
{
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $value);
}
require_once ('newsletter.php');