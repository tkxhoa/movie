<?php
//Register our admin panel sections and fields

//TODO: Include custom fields and taxonomies


//Adding the sections and fields
//Syntax for settings:
//add_settings_section( $id, $title, $callback, $page )
//Syntax for fields:
//add_settings_field( $id, $title, $callback, $page, $section, $args ) $args are given to callback function

//Main settings and fields
add_settings_section('djd_site_post_plugin_main', __('Main Settings', 'djd-site-post'), 'djd_site_post_plugin_main_text', 'djd-site-post-plugin');
add_settings_field('djd_site_post_form_name', __('Form Title', 'djd-site-post'), 'djd_site_post_form_name', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_edit_page', __('Edit Page ID', 'djd-site-post'), 'djd_site_post_edit_page', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_publish_status', __('Publish Status', 'djd-site-post'), 'djd_site_post_publish_status', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_post_confirmation', __('Post Confirmation Message', 'djd-site-post'), 'djd_site_post_post_confirmation', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_post_fail', __('Post Failure Message', 'djd-site-post'), 'djd_site_post_post_fail', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
//add_settings_field('djd_site_post_redirect', __('Redirect to', 'djd-site-post'), 'djd_site_post_redirect', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_mail', __('Mail on New Post', 'djd-site-post'), 'djd_site_post_mail', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_login_link', __('Display Login Link in Form', 'djd-site-post'), 'djd_site_post_login_link', 'djd-site-post-plugin', 'djd_site_post_plugin_main');

if ( current_theme_supports('post-formats') ) {
	add_settings_field('djd_site_post_post_format_default', __('Default Post Format', 'djd-site-post'), 'djd_site_post_post_format_default', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
}


add_settings_field('djd_site_post_post_format', __('Allow selection of Post Format', 'djd-site-post'), 'djd_site_post_post_format', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_hide_toolbar', __('Hide WordPress Toolbar', 'djd-site-post'), 'djd_site_post_hide_toolbar', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_hide_edit', __('Hide regular WP Edit Link', 'djd-site-post'), 'djd_site_post_hide_edit', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
//add_settings_field('djd_site_post_no_backend', __('Deny Backend Access for Subscribers and Contributors', 'djd-site-post'), 'djd_site_post_no_backend', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_guest_posts', __('Allow guest to post', 'djd-site-post'), 'djd_site_post_allow_guest_posts', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_guest_account', __('Guest Account', 'djd-site-post'), 'djd_site_post_guest_account', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_guest_cat_select', __('Allow Guests to select Category', 'djd-site-post'), 'djd_site_post_guest_cat_select', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_guest_cat', __('Category for Guest Posts', 'djd-site-post'), 'djd_site_post_guest_cat', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
add_settings_field('djd_site_post_quiz', __('Show Guests a Spam Prevention Quiz', 'djd-site-post'), 'djd_site_post_quiz', 'djd-site-post-plugin', 'djd_site_post_plugin_main');
//Category settings and fields
add_settings_section('djd_site_post_plugin_cats', __('Category Settings', 'djd-site-post'), 'djd_site_post_plugin_cats_text', 'djd-site-post-plugin');
add_settings_field('djd_site_post_categories', __('Display Categories', 'djd-site-post'), 'djd_site_post_categories', 'djd-site-post-plugin', 'djd_site_post_plugin_cats');
add_settings_field('djd_site_post_allow_new_category', __('Create New Category', 'djd-site-post'), 'djd_site_post_allow_new_category', 'djd-site-post-plugin', 'djd_site_post_plugin_cats');
add_settings_field('djd_site_post_category_order', __('Categrory Order', 'djd-site-post'), 'djd_site_post_category_order', 'djd-site-post-plugin', 'djd_site_post_plugin_cats');
//Field settings and fields
add_settings_section('djd_site_post_plugin_fields', __('Field Settings', 'djd-site-post'), 'djd_site_post_plugin_fields_text', 'djd-site-post-plugin');
add_settings_field('djd_site_post_title-required', __('Require a Title', 'djd-site-post'), 'djd_site_post_title_required', 'djd-site-post-plugin', 'djd_site_post_plugin_fields');
add_settings_field('djd_site_post_show_excerpt', __('Show Separate Excerpt', 'djd-site-post'), 'djd_site_post_show_excerpt', 'djd-site-post-plugin', 'djd_site_post_plugin_fields');
add_settings_field('djd_site_post_editor_style', __('Content Field Style', 'djd-site-post'), 'djd_site_post_editor_style', 'djd-site-post-plugin', 'djd_site_post_plugin_fields');
add_settings_field('djd_site_post_allow_media_upload', __('Allow Media Upload', 'djd-site-post'), 'djd_site_post_allow_media_upload', 'djd-site-post-plugin', 'djd_site_post_plugin_fields');
add_settings_field('djd_site_post_show_tags', __('Allow Tags', 'djd-site-post'), 'djd_site_post_show_tags', 'djd-site-post-plugin', 'djd_site_post_plugin_fields');
add_settings_field('djd_site_post_guest_info', __('Email & Name for Guest Posts', 'djd-site-post'), 'djd_site_post_guest_info', 'djd-site-post-plugin', 'djd_site_post_plugin_fields');

//Label settings and fields
add_settings_section('djd_site_post_plugin_labels', __('Labels', 'djd-site-post'), 'djd_site_post_plugin_label_text', 'djd-site-post-plugin');
add_settings_field('djd_site_post_title', __('Title', 'djd-site-post'), 'djd_site_post_title', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');
add_settings_field('djd_site_post_excerpt', __('Excerpt', 'djd-site-post'), 'djd_site_post_excerpt', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');
add_settings_field('djd_site_post_content', __('Content', 'djd-site-post'), 'djd_site_post_content', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');
add_settings_field('djd_site_post_tags', __('Tags', 'djd-site-post'), 'djd_site_post_tags', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');
add_settings_field('djd_site_post_categories_label', __('Categories', 'djd-site-post'), 'djd_site_post_categories_label', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');
add_settings_field('djd_site_post_create_category', __('New Category', 'djd-site-post'), 'djd_site_post_create_category', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');
add_settings_field('djd_site_post_send_button', __('Send Button', 'djd-site-post'), 'djd_site_post_send_button', 'djd-site-post-plugin', 'djd_site_post_plugin_labels');

?>

<!-- Print the html that makes up our admin settings page -->

<?php /*

<?php $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'djdspp_general_settings'; ?>
<div class="wrap">
    <div class="icon32">
	<img src="<?php echo plugins_url('/images/djd-grey34x34.png', __FILE__) ?>">
    </div>
    <h2><?php _e('DJD Site Post Pro Settings', 'djd-site-post'); ?></h2>
    <a href="http://wordpress.org/support/plugin/djd-site-post/"><?php _e('Need help? Just click here!', 'djd-site-post') ?></a>
	<br />
	<a href="http://www.djdesign.de/djd-site-post-plugin-en/"><?php _e('Documentation is here!', 'djd-site-post') ?></a>
    <br/>
	<?php $this->plugin_options_tabs(); ?>
    <form id="djd_site_post_admin_form" method="post" action="options.php">
	<?php wp_nonce_field( 'update-options' ); ?>
	<?php
	// Argument in settings_fields is the option-group registered with register_setting
	settings_fields($tab);
	// Argument in do_settings_sections is same as $page in add_settings_field
	do_settings_sections($tab);
	?>

	<p class="submit"><input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes', 'djd-site-post'); ?>" /></p>
    </form>
</div>

*/ ?>

<div class="wrap">
    <div class="icon32">
	<img src="<?php echo plugins_url('/images/djd-grey34x34.png', __FILE__) ?>">
    </div>
    <h2><?php _e('DJD Site Post Settings', 'djd-site-post'); ?></h2>
    <a href="http://wordpress.org/support/plugin/djd-site-post/"><?php _e('Need help? Just click here!', 'djd-site-post') ?></a>
	<br />
	<a href="http://www.djdesign.de/djd-site-post-plugin-en/"><?php _e('Documentation is here!', 'djd-site-post') ?></a>
    <br/>
    <form id="djd_site_post_admin_form" method="post" action="options.php">
	<?php
	// Argument in settings_fields is the option-group registered with register_setting
	settings_fields('djd_site_post_template_group');
	// Argument in do_settings_sections is same as $page in add_settings_field
	do_settings_sections('djd-site-post-plugin');
	?>

	<p class="submit"><input class="button-primary" name="Submit" type="submit" value="<?php esc_attr_e('Save Changes', 'djd-site-post'); ?>" /></p>
    </form>
</div>



<?php

//The callback functions that read the settings from the db and echo the html
function djd_site_post_form_name() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-form-name' class='regular-text' name='djd_site_post_settings[djd-form-name]' value='{$options['djd-form-name']}' size='40' /> ";
}
function djd_site_post_edit_page() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-edit-page' name='djd_site_post_settings[djd-edit-page]' value='{$options['djd-edit-page']}' size='4' /> ";
	_e('Enter the ID of the page where the edit form appears (the page where you entered the plugin shortcode).', 'djd-site-post');
}
function djd_site_post_title() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-title' name='djd_site_post_settings[djd-title]' value='{$options['djd-title']}' size='40' /> ";
}
function djd_site_post_content() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-content' name='djd_site_post_settings[djd-content]' value='{$options['djd-content']}' size='40' /> ";
}
function djd_site_post_excerpt() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-excerpt' name='djd_site_post_settings[djd-excerpt]' value='{$options['djd-excerpt']}' size='40' /> ";
}
function djd_site_post_tags() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-tags' name='djd_site_post_settings[djd-tags]' value='{$options['djd-tags']}' size='40' /> ";
}
function djd_site_post_categories_label() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-categories-label' name='djd_site_post_settings[djd-categories-label]' value='{$options['djd-categories-label']}' size='40' /> ";
}
function djd_site_post_create_category() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-create-category' name='djd_site_post_settings[djd-create-category]' value='{$options['djd-create-category']}' size='40' /> ";
}
function djd_site_post_send_button() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-send-button' name='djd_site_post_settings[djd-send-button]' value='{$options['djd-send-button']}' size='40' /> ";
}
function djd_site_post_publish_status() {
    $options = get_option('djd_site_post_settings');
    ?>
    <select id='djd-publish-status' name='djd_site_post_settings[djd-publish-status]' value="<?php echo $options['djd-publish-status']; ?>" >
        <option value='publish' <?php if ($options['djd-publish-status'] == 'publish') echo 'selected="selected"'; ?>> <?php _e('Publish', 'djd-site-post') ?></option>
        <option value='pending' <?php if ($options['djd-publish-status'] == 'pending') echo 'selected="selected"'; ?>> <?php _e('Pending', 'djd-site-post') ?></option>
        <option value='draft' <?php if ($options['djd-publish-status'] == 'draft') echo 'selected="selected"'; ?>> <?php _e('Draft', 'djd-site-post') ?></option>
        <option value='private' <?php if ($options['djd-publish-status'] == 'private') echo 'selected="selected"'; ?>> <?php _e('Private', 'djd-site-post') ?></option>
    </select>
    <?php
    _e("<p class='description'>The Status assigned to the new Post (Publish, Pending, Draft, Private).</p>", "djd-site-post");
}
function djd_site_post_show_tags() {
    $options = get_option('djd_site_post_settings')
    ?>
    <input type='checkbox' id='djd-show-tags' name='djd_site_post_settings[djd-show-tags]' value='1' <?php checked(1, $options['djd-show-tags']); ?> />
    <?php
    _e('Check to display a field to enter tags.', 'djd-site-post');
}
function djd_site_post_guest_info() {
    $options = get_option('djd_site_post_settings')
    ?>
    <input type='checkbox' id='djd-guest-info' name='djd_site_post_settings[djd-guest-info]' value='1' <?php checked(1, $options['djd-guest-info']); ?> />
    <?php
    _e('Check to require email and name for guest posts.', 'djd-site-post');
}
function djd_site_post_title_required() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-title-required' name='djd_site_post_settings[djd-title-required]' value='1' <?php checked(1, $options['djd-title-required']); ?> />
    <?php
    _e('Check to enforce the user to enter a title for his post.', 'djd-site-post');
}
function djd_site_post_show_excerpt() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-show-excerpt' name='djd_site_post_settings[djd-show-excerpt]' value='1' <?php checked(1, $options['djd-show-excerpt']); ?> />
    <?php
    _e('Check to display a separate field for the excerpt.', 'djd-site-post');
}
function djd_site_post_show_content() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-show-content' name='djd_site_post_settings[djd-show-content]' value='1' <?php checked(1, $options['djd-show-content']); ?> />
    <?php
    _e('Check to display an editor field for the content.', 'djd-site-post');
}
function djd_site_post_editor_style() {
    $options = get_option('djd_site_post_settings');
    ?>
    <select id='djd-editor-style' name='djd_site_post_settings[djd-editor-style]' value="<?php echo $options['djd-editor-style']; ?>" >
        <option value='simple' <?php if ($options['djd-editor-style'] == 'simple') echo 'selected="selected"'; ?>> <?php _e('Simple - Plain Text', 'djd-site-post') ?></option>
        <option value='rich' <?php if ($options['djd-editor-style'] == 'rich') echo 'selected="selected"'; ?>> <?php _e('Rich - Visual and HTML', 'djd-site-post') ?></option>
        <option value='visual' <?php if ($options['djd-editor-style'] == 'visual') echo 'selected="selected"'; ?>> <?php _e('Visual - Visual Only', 'djd-site-post') ?></option>
		<option value='html' <?php if ($options['djd-editor-style'] == 'html') echo 'selected="selected"'; ?>> <?php _e('HTML - HTML Only', 'djd-site-post') ?></option>
    </select>
    <?php
    _e("<p class='description'>For responsive layouts it is recommended not to use the visual content field style,</p>", "djd-site-post");
    _e("<p class='description'>because some Android browsers don't support some of the tages used.</p>", "djd-site-post");
}
function djd_site_post_post_confirmation() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-post-confirmation' name='djd_site_post_settings[djd-post-confirmation]' value='{$options['djd-post-confirmation']}' size='40' /> ";
    _e('<p class="description">Your custom post-success message.</p>', 'djd-site-post');
}
function djd_site_post_post_fail() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-post-fail' name='djd_site_post_settings[djd-post-fail]' value='{$options['djd-post-fail']}' size='40' /> ";
    _e('<p class="description">Your custom post-failure message.</p>', 'djd-site-post');
}
function djd_site_post_redirect() {
    $options = get_option('djd_site_post_settings');
    echo "<input type='text' id='djd-redirect' name='djd_site_post_settings[djd-redirect]' value='{$options['djd-redirect']}' size='40' /> ";
    _e('<p class="description">The URL to redirect the user to after his post.</p>', 'djd-site-post');
    _e('<p class="description">Shortcode parameters will overwrite this setting.</p>', 'djd-site-post');
}
function djd_site_post_mail() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-mail' name='djd_site_post_settings[djd-mail]' value='1' <?php checked(1, $options['djd-mail']); ?> />
    <?php
    _e('Check to notify admin on new post.', 'djd-site-post');
}
function djd_site_post_categories() {
    $options = get_option('djd_site_post_settings');
    ?>
    <select id='djd-categories' name='djd_site_post_settings[djd-categories]' value="<?php echo $options['djd-categories']; ?>" >
        <option value='list' <?php if ($options['djd-categories'] == 'list') echo 'selected="selected"'; ?>> <?php _e('Droplist', 'djd-site-post') ?></option>
        <option value='check' <?php if ($options['djd-categories'] == 'check') echo 'selected="selected"'; ?>> <?php _e('Check boxes', 'djd-site-post') ?></option>
        <option value='none' <?php if ($options['djd-categories'] == 'none') echo 'selected="selected"'; ?>> <?php _e('No display', 'djd-site-post') ?></option>
    </select>
    <?php
    _e('How categories appear at the front end.', 'djd-site-post');
    _e('<p class="description">You can select not to display categories at all.</p>', 'djd-site-post');
}
function djd_site_post_allow_new_category() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-allow-new-category' name='djd_site_post_settings[djd-allow-new-category]' value='1' <?php checked(1, $options['djd-allow-new-category']); ?> />
    <?php
    _e('Check if user should be able to create a new category himself.', 'djd-site-post');
}
function djd_site_post_category_order() {
    $options = get_option('djd_site_post_settings');
    ?>
    <select id='djd-category-order' name='djd_site_post_settings[djd-category-order]' value="<?php echo $options['djd-category-order']; ?>" >
        <option value='id' <?php if ($options['djd-category-order'] == 'id') echo 'selected="selected"'; ?>> <?php _e('by ID', 'djd-site-post') ?></option>
        <option value='name' <?php if ($options['djd-category-order'] == 'name') echo 'selected="selected"'; ?>> <?php _e('by name', 'djd-site-post') ?></option>
    </select>
    <?php
    _e('The sort order of categories at the front end.', 'djd-site-post');
}
function djd_site_post_allow_media_upload() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-allow-media-upload' name='djd_site_post_settings[djd-allow-media-upload]' value='1' <?php checked(1, $options['djd-allow-media-upload']); ?> />
    <?php
    _e('Allow user to upload new media file (picture, video).', 'djd-site-post');
}
function djd_site_post_login_link () {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-login-link' name='djd_site_post_settings[djd-login-link]' value='1' <?php checked(1, $options['djd-login-link']); ?> />
    <?php
    _e('Display a Login Link inside the form.', 'djd-site-post');
}
function djd_site_post_post_format_default () {
    $options = get_option('djd_site_post_settings');
			$post_formats = get_theme_support( 'post-formats' );
		
			if ( is_array( $post_formats[0] ) ) : ?>
				<select id='djd-post-format-default' name='djd_site_post_settings[djd-post-format-default]' value="<?php echo $options['djd-post-format-default']; ?>">
				<option value="0" <?php selected( $options['djd-post-format-default'], '0' ); ?> ><?php echo get_post_format_string( 'standard' ); ?></option>
				<?php foreach ( $post_formats[0] as $format ) : ?>
				<option value="<?php echo esc_attr( $format ); ?>" <?php selected( $options['djd-post-format-default'], $format ); ?> ><?php echo esc_html( get_post_format_string( $format ) ); ?></option>
				<?php endforeach; ?>
				</select>
			<?php endif;
    _e('Select the default Post Format.', 'djd-site-post');
}
function djd_site_post_post_format () {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-post-format' name='djd_site_post_settings[djd-post-format]' value='1' <?php checked(1, $options['djd-post-format']); ?> />
    <?php
    _e('Allow the selection off the Post Format inside the form.', 'djd-site-post');
}
function djd_site_post_hide_toolbar () {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-hide-toolbar' name='djd_site_post_settings[djd-hide-toolbar]' value='1' <?php checked(1, $options['djd-hide-toolbar']); ?> />
    <?php
    _e('Hide the WordPress Toolbar.', 'djd-site-post');
}
function djd_site_post_no_backend () {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' name='djd_site_post_settings[djd-no-backend]' value='1' <?php checked(1, $options['djd-no-backend']); ?> />
    <?php
    _e('Deny subscribers and contributors access to the backend.', 'djd-site-post');
}
function djd_site_post_hide_edit () {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-hide-edit' name='djd_site_post_settings[djd-hide-edit]' value='1' <?php checked(1, $options['djd-hide-edit']); ?> />
    <?php
    _e('Hide the regular WordPress Edit Link.', 'djd-site-post');
}
function djd_site_post_allow_guest_posts() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-allow-guest-posts' name='djd_site_post_settings[djd-allow-guest-posts]' value='1' <?php checked(1, $options['djd-allow-guest-posts']); ?> />
    <?php
    _e('Allow guests to post.', 'djd-site-post');
}
function djd_site_post_guest_account() {
    $options = get_option('djd_site_post_settings');
    $args = array(
        'selected'                => $options['djd-guest-account'],
        'name'                    => 'djd_site_post_settings[djd-guest-account]',
        'class'                   => 'djd_site_post_droplist'
    ); ?>
    <span title="<?php _e('Dedicated account to use for guests', 'djd-site-post') ?>"><?php wp_dropdown_users($args); ?></span>
    <?php
    _e('The dedicated account that should be used for guest posts.', 'djd-site-post');
}
function djd_site_post_guest_cat_select() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-guest-cat-select' name='djd_site_post_settings[djd-guest-cat-select]' value='1' <?php checked(1, $options['djd-guest-cat-select']); ?> />
    <?php
    _e('Check if you want guests to select categories themselves. If not checked the default category you specify below will be used for guest posts.', 'djd-site-post');
}
function djd_site_post_guest_cat() {
    $options = get_option('djd_site_post_settings');
    $args = array(
		'orderby'                 => 'name',
		'order'                   => 'ASC',
        'selected'                => $options['djd-guest-cat'],
        'name'                    => 'djd_site_post_settings[djd-guest-cat]',
        'class'                   => 'djd_site_post_droplist',
		'hide_empty'              => 0,
		'hide_if_empty'           => false
    ); ?>
    <span title="<?php _e('Category used for guest posts', 'djd-site-post') ?>"><?php wp_dropdown_categories($args); ?></span>
    <?php
    _e('The category guest posts should be assigned to.', 'djd-site-post');
}
function djd_site_post_quiz() {
    $options = get_option('djd_site_post_settings');
    ?>
    <input type='checkbox' id='djd-quiz' name='djd_site_post_settings[djd-quiz]' value='1' <?php checked(1, $options['djd-quiz']); ?> />
    <?php
    _e('Display a Spam Prevention Quiz. Applies to useres not logged in.', 'djd-site-post');
}
function djd_site_post_plugin_main_text(){
    echo "<p>" . _e('Section for all the general settings for this plugin.', 'djd-site-post') . "</p>";
}
function djd_site_post_plugin_cats_text(){
    echo "<p>" . _e('Section for all the settings specific to categories.', 'djd-site-post') . "</p>";
}
function djd_site_post_plugin_fields_text(){
    echo "<p>" . _e('Specify the fields you want to include in the form generated.', 'djd-site-post') . "</p>";
    echo "<p>" . _e('Please note that the Title will always be included - No Post without Title.', 'djd-site-post') . "</p>";
}
function djd_site_post_plugin_label_text(){
    echo "<p>" . _e('You can specify your own labels for the form fields shown to the user', 'djd-site-post') . "</p>";
}