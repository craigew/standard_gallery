<div class="wrap">

<h2>Standard Gallery</h2>

<h3>Administration</h3>

<?php
$postId="";
if (isset($_POST['Add_New_Gallery'])) {
    $optional = array();
    show_gallery_form($optional);
} elseif (isset($_POST['save-gallery-settings'])) {
    add_gallery();
    show_gallery_list();
} elseif (isset($_POST['deleteImage'])) {
    delete_gallery_image($_POST['manage_gallery_image']);
    show_gallery_form(array("postId" => $_POST['post_id']));
} elseif (isset($_POST['delete_gallery'])) {
    delete_gallery($_POST['manage_gallery']);
    show_gallery_list();
} elseif (isset($_POST['edit_gallery'])) {
    $optional = array("postId" => $_POST['manage_gallery']);
    show_gallery_form($optional);
}
elseif (isset($_POST['attachment_id']) && isset($_POST['post_id'])) {
    associate_image_as_attachment($_POST['attachment_id'], $_POST['post_id']);
    $optional = array("postId" => $_POST['post_id']);
    show_gallery_form($optional);
} else {
    show_gallery_list();
}

?>



<?php
function show_gallery_list()
{
    ?>
    <form id="gallery_list" method="post">
        <table class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th>
                    Title
                </th>
                <th>
                    Description
                </th>
                <th>
                    Short Code
                </th>
                <th width="60px">
                    Manage
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $type = 'standard_gallery';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish');

            $my_query = null;
            $my_query = new WP_Query($args);
            if ($my_query->have_posts()) {
                $alt = 0;
                while ($my_query->have_posts()) : $my_query->the_post();
                    if ($alt % 2 == 0) {
                        $style = "alternate";

                    } else {
                        $style = "";
                    }
                    $alt += 1;
                    ?>
                    <tr valign="top" class="hentry <?php echo $style ?>">
                        <td><?php the_title(); ?></td>
                        <td><?php the_content(); ?></td>
                        <td>[crafted-software-standard-gallery id=<?php the_ID(); ?>]</td>
                        <td><input id="manage_gallery" type="radio" name="manage_gallery"
                                   value=<?php the_ID(); ?>/></td>
                    </tr>
                <?php
                endwhile;
            }
            wp_reset_query();
            ?>
            </tbody>
        </table>
        <div class="tablenav bottom">
            <input type="submit" id="Add_New_Gallery" name="Add_New_Gallery" value="Add New Gallery"
                   class="button"/>
            <input type="submit" id="editGallery" name="edit_gallery" value="Edit Gallery"
                   class="button"/>
            <input type="submit" id="deleteGallery" name="delete_gallery" value="Delete Gallery"
                   class="button"/>
        </div>
    </form>
<?php
}

function show_gallery_form($optional)
{
    $post = null;
    $hide_add_image_button="hide";
    if ((isset($optional["postId"])) && ($optional["postId"]!="")) {
        $post = get_post($optional["postId"]);
        $title = $post->post_title;
        $content = $post->post_content;
        $hide_add_image_button="";
    } else {
        $title = "";
        $content = "";
    }

    ?>
    <form id="gallery_detail" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">Gallery Name</th>
                <td><input type="text" id="gallery_name" name="gallery_name" value=" <?php echo $title ?>"/></td>
            </tr>
            <tr>
                <th scope="row">Gallery Description</th>
                <td><input type="text" id="gallery_desc" name="gallery_desc" value=" <?php echo $content ?>"/></td>

            <tr>
                <td colspan="2"><?php submit_button('Save Settings', 'primary', 'save-gallery-settings'); ?></td>
            </tr>
            <?php
            if ($post != null) {
                $images =& get_children(array(
                    'post_parent' => $post->ID,
                    'post_type' => 'attachment',
                    'post_mime_type' => 'image'
                ));

                if (!empty($images)) {
                    foreach ($images as $attachment_id => $attachment) {
                        ?>
                        <tr>
                            <td colspan="2">
                                <table class="edit_gallery_image_list_table">
                                    <tr>
                                        <td><?php echo wp_get_attachment_image($attachment_id, 'full'); ?></td>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td>Title</td>
                                                    <td><?php echo $attachment->post_title?></td>
                                                </tr>
                                                <tr>
                                                    <td>Caption</td>
                                                    <td><?php echo $attachment->post_excerpt?></td>
                                                </tr>
                                                <tr>
                                                    <td>Description</td>
                                                    <td><?php echo $attachment->post_content?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td>
                                            <input id="manage_gallery_image" type="radio" name="manage_gallery_image"
                                                   value=<?php echo($attachment_id)?>/>
                                        </td>
                                    </tr>
                                </table>


                            </td>
                        </tr>
                    <?php
                    }
                }
            }
            ?>

            <tr>
                <td colspan="2">
                    <input type="hidden" name="post_id" value="<?php echo $optional["postId"] ?>" id="post_id"/>
                    <input type="hidden" name="attachment_id" id="attachment_id"/>
                    <input type="button" id="addGalleryImage" name="addGalleryImage" value="Add New Image" class="button <?echo $hide_add_image_button?>"/>
                    <input type="submit" id="deleteImage" name="deleteImage" value="Delete Image" class="button <?echo $hide_add_image_button?>"/>
                </td>
            </tr>
        </table>
    </form>
<?php
}

function add_gallery()
{
    if ((isset($_POST['post_id']))&&($_POST['post_id']!="")) {
        wp_update_post(array('ID' => str_replace("/","",$_POST['post_id']),
            'post_title' => $_POST['gallery_name'],
            'post_content' => $_POST['gallery_desc']));
    } else {
        $post_id = wp_insert_post(array(
            'post_type' => 'standard_gallery',
            'post_title' => $_POST['gallery_name'],
            'post_content' => $_POST['gallery_desc'],
            'post_status' => 'publish',
            'comment_status' => 'closed', // if you prefer
            'ping_status' => 'closed', // if you prefer
        ));
    }

}

function associate_image_as_attachment($image_id, $gallery_id)
{
    echo("associate");
    $attachment = array(
        'ID' => $image_id,
        'post_parent' => $gallery_id
    );

    wp_update_post($attachment);
}

function delete_gallery($postId)
{
    wp_delete_post($postId);
}

function delete_gallery_image($postId)
{
    wp_delete_post($postId);
}

?>

</div>