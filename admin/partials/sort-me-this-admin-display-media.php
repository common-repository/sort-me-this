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

if ( current_user_can( 'upload_files' ) ) {
    wp_enqueue_media();
}
?>

<div class="smt-plugin-context">
    <!-- Popups -->
    <div class="smt-alert alert">
        <span class="smt-alert-closebtn">&times;</span>  
        <strong><?php _e('Warning!', 'sort-me-this'); ?></strong><span class="smt-alert-popup-text"></span>
    </div>

    <div class="smt-alert success">
        <span class="smt-alert-closebtn">&times;</span>  
        <strong><?php _e('Success!', 'sort-me-this'); ?></strong> <span class="smt-success-popup-text"></span>
    </div>
    <!--  -->
    <div class="smt-container">
        <div class="smt-main-logo" style="background-image: url<?php echo '('.plugin_dir_url( dirname( __FILE__ ) ). '/partials/img/sortmethislogo.png)';?>"></div>
        <div class="smt-main-title-section">
            <h1><?php _e('Media', 'sort-me-this'); ?></h1>
            <p><?php _e('From this page you can easily add, edit, sort and filter your media!', 'sort-me-this'); ?></p>
        
        </div>  
    </div>

    <div class="smt-container smt-top-actions">
        <div class="smt-main-actions">
            <button id="smt-add-media-btn" class="smt-button-primary" type="file" style="margin-top: 7px;"><?php _e('Add new', 'sort-me-this'); ?></button>
            <button id="smt-multiple-select-media-btn" type="button" style="margin-top: 7px; margin-left: 5px;"><?php _e('Multiple select', 'sort-me-this'); ?></button>
        </div>
        <div class="smt-delete-actions">
            <p><?php _e('Set Media Categories to bulk images','sort-me-this');?> <a href="https://www.algaweb.it/product/sortmethis-premium-version/" target="_blank"><?php _e('(Premium version)', 'sort-me-this'); ?></a></p>
            <button id="smt-delete-media-btn" class="smt-button-primary" style="margin-top: 7px;" onclick="smet_delete_media_group();"><?php _e('Delete selected', 'sort-me-this'); ?></button>      
        </div>

        <button id="smt-multiple-select-back-media-btn" type="button" style="margin-top: 7px;"><?php _e('Back', 'sort-me-this'); ?></button>
        <input type="hidden" id="smt-ids-to-bulk">
    </div>

    <div class="smt-container">
        <div class="smt-header">
            <!-- Date -->
            <input type="text" id="media-attachment-date-filters-from" placeholder="<?php _e('From...', 'sort-me-this'); ?>">
            <input type="text" id="media-attachment-date-filters-to" placeholder="<?php _e('To...', 'sort-me-this'); ?>">
            <!-- Media per page -->
            <select id="smt-attachment-filters-media-per-page" class="attachment-filters">
                <option value="25" selected="selected"><?php _e('Results per page...', 'sort-me-this'); ?></option>
                <option value="25">25</option>
                <option value="50" disabled>50-Premium version</option>
                <option value="100" disabled>100-Premium version</option>
            </select>
            <!-- Media type -->
            <select id="media-attachment-type-filters" class="attachment-filters">
                <option value="all" selected="selected"><?php _e('All media type', 'sort-me-this'); ?></option>
                <option value="image"><?php _e('Image', 'sort-me-this'); ?></option>
                <option value="audio"><?php _e('Audio', 'sort-me-this'); ?></option>
                <option value="video"><?php _e('Video', 'sort-me-this'); ?></option>
                <option value="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-word.document.macroEnabled.12,application/vnd.ms-word.template.macroEnabled.12,application/vnd.oasis.opendocument.text,application/vnd.apple.pages,application/pdf,application/vnd.ms-xpsdocument,application/oxps,application/rtf,application/wordperfect,application/octet-stream"><?php _e('Documents', 'sort-me-this'); ?></option>
                <option value="application/vnd.apple.numbers,application/vnd.oasis.opendocument.spreadsheet,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel.sheet.macroEnabled.12,application/vnd.ms-excel.sheet.binary.macroEnabled.12"><?php _e('Calc sheets', 'sort-me-this'); ?></option>
                <option value="application/x-gzip,application/rar,application/x-tar,application/zip,application/x-7z-compressed"><?php _e('Archive', 'sort-me-this'); ?></option>
            </select>
            <!-- Media Cat -->
            <select id="smt-attachment-filters-media-categories" class="smt-selectize-multiple" multiple="multiple" placeholder="<?php _e('Media Category...', 'sort-me-this'); ?>">
            <?php
                $media_cat = get_option('smt-media-categories');
                foreach ($media_cat as $key => $value) {
                    $sel = '';
                    if(isset($_GET['smt-media-cat'])) {
                        $sel = $_GET['smt-media-cat'] == $key ? 'selected="selected"' : '';
                    } 
                    echo '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
                }
            ?>
            </select>
            <button id="smt-filter-media-btn" class="smt-button-primary" type="button" style="vertical-align: bottom; display: inline-block; line-height: 14px;"><?php _e('Filter', 'sort-me-this'); ?></button>
        </div>
    </div>
    <div class="smt-container">     
        <div class="loader"><?php _e('Loading...', 'sort-me-this'); ?></div>        
        <div class="smt-body">
            <ul class="smt-media-list">

            </ul>
        </div>
    </div>
    <!-- Main Modal -->
    <div id="smt-modal" class="smt-modal">
        <div class="smt-modal-content">   
            <span class="smt-modal-close">&times;</span>
            <div class="smt-media-frame-title"><h1><?php _e('Attachment details', 'sort-me-this'); ?></h1></div>
            <div class="smt-modal-inner-content"></div>
        </div>
    </div>

    <!-- Media categories modal -->
    <div id="smt-modal-media-cat" class="smt-modal smt-modal-small">
        <div class="smt-modal-content">   
            <span class="smt-modal-close">&times;</span>
            <div class="smt-media-frame-title"><h1><?php _e('Add media categories to your file', 'sort-me-this'); ?></h1></div>
            <div class="smt-modal-inner-content"></div>

            <div class="smt-media-small-modal-cat">   
                <select id="smt-attachment-media-categories" multiple="multiple" class="smt-selectize-multiple-add-first">
                <?php
                    $media_cat = get_option('smt-media-categories');
                    foreach ($media_cat as $key => $value) {
                        echo '<option value="'.$key.'">'.$value.'</option>';
                    }
                ?>
                </select>
            <div>
            <div class="smt-media-categories-button-container">
                <button id="smt-add-media-category-only" type="button" onclick="smet_save_media_categories_only( this );"><?php _e('Save', 'sort-me-this'); ?></button>
                <div class="smt-id-value"></div>
            </div>
        </div>
    </div>

</div>