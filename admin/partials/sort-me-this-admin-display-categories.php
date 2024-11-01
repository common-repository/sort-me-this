<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 * 
 * @since      1.0.0
 *
 * @package    Sort_Me_This
 * @subpackage Sort_Me_This/admin/partials
 */

$media_cat = get_option('smt-media-categories');
?>

<div class="smt-plugin-context">
    <!-- Popups -->
    <div class="smt-alert alert">
        <span class="smt-alert-closebtn">&times;</span>  
        <strong><?php _e('Warning!', 'sort-me-this'); ?></strong> <span class="smt-alert-popup-text"></span>
    </div>

    <div class="smt-alert success">
        <span class="smt-alert-closebtn">&times;</span>  
        <strong><?php _e('Success!', 'sort-me-this'); ?></strong> <span class="smt-success-popup-text"></span>
    </div>
    <!--  -->
    <div class="smt-container">
        <div class="smt-main-logo" style="background-image: url<?php echo '('.plugin_dir_url( dirname( __FILE__ ) ). '/partials/img/sortmethislogo.png)';?>"></div>
        <div class="smt-main-title-section">
            <h1><?php _e('Media Categories', 'sort-me-this'); ?></h1>
            <p><?php _e('Media Categories are user created elements, used to group files that have something in common.' , 'sort-me-this'); ?></p>
        </div>  
    </div>

    <div class="smt-container" style="display: block;">
        <p><?php _e('You can create up to <b>5 Media Categories in free version</b>. Need more? check out the','sort-me-this');?> <a href="https://www.algaweb.it/product/sortmethis-premium-version/" target="_blank"><?php _e('Premium version', 'sort-me-this'); ?></a></p>
        <button id="smt-add-media-category-btn" type="file" style="margin-top: 7px;"><?php _e('Add new media category', 'sort-me-this'); ?></button>
    </div>

    <div class="smt-container">
        <div class="loader"><?php _e('Loading...', 'sort-me-this'); ?></div>    
        <div class="smt-body">
            <table class="smt-media-cat-table wp-list-table widefat fixed striped table-view-list tags">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Name', 'sort-me-this'); ?>
                        </th>
                        <th>
                            <?php _e('ID', 'sort-me-this'); ?>
                        </th>
                        <th>
                            <?php _e('Count', 'sort-me-this'); ?>
                        </th>
                        <th class="smt-little-col"></th>
                        <th class="smt-little-col"></th>
                        <th class="smt-little-col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $media_cat = get_option('smt-media-categories');                              
                        foreach ($media_cat as $key => $value) {
                            $count = 0; 
                            $media_query = new WP_Query(
                                array(
                                    'post_type' => 'attachment',
                                    'post_status' => 'inherit',
                                    'posts_per_page' => -1,
                                    'fields' => 'ids',
                                    'meta_query' => array(
                                         array(
                                            'key' => 'smet_attachment_media_categories',
                                            'value' => $key,
                                            'compare' => 'LIKE'
                                        )
                                    )
                                )
                            );
                            $count = $media_query->found_posts;     
                            
                            echo '<tr id="row-id-'.$key.'">';
                            echo '<td class="smt-table-name">'.$value.'</td>';
                            echo '<td>'.$key.'</td>';
                            echo '<td>'.$count.'</td>';
                            if($count > 0) {
                                echo '<td><a href="'.admin_url( '/admin.php?page=sort-me-this&smt-media-cat='.$key ).'">'. __('View media', 'sort-me-this') .'</a></td>';
                            }
                            else {
                                echo '<td></td>';
                            }
                            
                            echo '<td><button class="smt-edit-existent-media-cat" type="button" value="'.$key.'" onclick="smet_show_edit_media_cat_modal(this);">'. __('Edit', 'sort-me-this') .'</button></td>';
                            echo '<td><button type="button" class="smt-button-warning" onclick="smet_delete_media_cat(this, \''.$key.'\')">'. __('Delete', 'sort-me-this') .'</button></td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Media Category Modal -->
    <div id="smt-modal-add-cat" class="smt-modal smt-modal-small">
        <div class="smt-modal-content">   
            <span class="smt-modal-close">&times;</span>
            <div class="smt-media-frame-title"><h1><?php _e('Add a new Media Category', 'sort-me-this'); ?></h1></div>
            <div class="smt-modal-inner-content">
                <div class="smt-modal-inner-content-add">
                    <input id="smt-new-cat-textfield" type="text" placeholder="<?php _e('My new Media Category', 'sort-me-this'); ?>">
                    <i><?php _e('Type the name of the <b>new Media Category</b> you want to add. If you type the name of an already existent Media Category it will not be saved.', 'sort-me-this');  ?></i>
                    <p><button id="smt-add-new-media-category" type="button" class="smt-button-primary"><?php _e('Save', 'sort-me-this'); ?></button></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Media Category Modal -->
    <div id="smt-modal-edit-cat" class="smt-modal smt-modal-small">
        <div class="smt-modal-content">   
            <span class="smt-modal-close">&times;</span>
            <div class="smt-media-frame-title"><h1><?php _e('Edit Media Category', 'sort-me-this'); ?></h1></div>
            <div class="smt-modal-inner-content">
                <div class="smt-modal-inner-content-add">
                    <label for="smt-edit-cat-id" style="color: #848484;"><b><?php _e('Media Category Id', 'sort-me-this'); ?></b></label>
                    <input id="smt-edit-cat-id" type="text" disabled value="" style="margin-bottom: 15px;">
                    <input id="smt-edit-cat-textfield" type="text" placeholder="<?php _e('Type a new name', 'sort-me-this'); ?>">
                    <i><?php _e('Type the name of the <b>new Media Category</b> you want to edit. If you type the name of an already existent Media Category it will not be saved.', 'sort-me-this');  ?></i>
                    <p><button id="smt-edit-media-category" type="button" class="smt-button-primary"><?php _e('Edit', 'sort-me-this'); ?></button></p>
                </div>
            </div>
        </div>
    </div>

</div> 