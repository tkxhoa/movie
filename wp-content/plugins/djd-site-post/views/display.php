<?php
/**
 * This file is used to markup the public facing aspect of the plugin.
 */

// If called from Frontpage Edit link we get a post_id
if (isset($_GET["post_id"])) { 
	$my_post = get_post(htmlspecialchars($_GET["post_id"]));
} else {
	$my_post = '';
}

// Set editor (content field) style
switch($djd_options['djd-editor-style']){
	case 'simple':
		$teeny = true;
		$show_quicktags = false;
		add_filter( 'teeny_mce_buttons', create_function ( '' , "return array('');" ) , 50 );
		break;
	case 'rich':
		$teeny = false;
		$show_quicktags = true;
		break;
	case 'visual':
		$teeny = false;
		$show_quicktags = false;
		break;
	case 'html':
		$teeny = true;
		$show_quicktags = true;
		add_filter ( 'user_can_richedit' , create_function ( '' , 'return false;' ) , 50 );
		break;
}

if ($called_from_widget == '1') {
	$teeny = true;
	$show_quicktags = false;
	add_filter( 'teeny_mce_buttons', create_function ( '' , "return array('');" ) , 50 );
//	add_filter ( 'user_can_richedit' , create_function ( '' , 'return false;' ) , 50 );
}

function myplugin_tinymce_buttons_2($buttons)
 {
	//Remove the format dropdown select and text color selector
	$remove = array('formatselect','forecolor', 'indent', 'outdent', 'charmap');

	return array_diff($buttons,$remove);
 }
//add_filter('mce_buttons_2','myplugin_tinymce_buttons_2');

function myplugin_tinymce_buttons($buttons)
 {
	//Remove the format dropdown select and text color selector
	$remove = array('link','unlink', 'blockquote', 'strikethrough', 'fullscreen', 'wp_more', 'wp_adv');

	return array_diff($buttons,$remove);
 }
//add_filter('mce_buttons','myplugin_tinymce_buttons');

?>

<?php if (!isset($_POST["djd_site_post_title"])) {

//init variables
$cf = array();
$sr = false;

if ($_COOKIE["form_ok"] == 1 ) {
	$cf['form_ok'] = true;
	$sr = true;
}

?>

<form id="site_post_form" class="djd_site_post_form bordered" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" enctype="multipart/form-data">
	<p hidden="hidden" class="form_error_message"></p>
	<input type="hidden" name="djd-our-id" <?php echo ( $my_post ? "value='".$my_post->ID."'" : "value='".$djd_post_id."'" ); ?> />
	<input type="hidden" name="djd-our-author" <?php if ( $my_post ) echo "value='".$my_post->post_author."'"; ?> />
	<?php if ($djd_options['djd-login-link']) { ?>
		<a style="float: right;" href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Login</a>
	<?php } ?>
	<div id="field-wrapper">
		<legend><?php echo ( $djd_options['djd-form-name'] ? $djd_options['djd-form-name'] : __('Frontend Post', 'djd-site-post') ); ?></legend>
		<label for="djd_site_post_titel"><?php echo ( $djd_options['djd-title'] ? $djd_options['djd-title'] : __('Title', 'djd-site-post') ); ?></label>
		<input type="text" <?php if ( $djd_options['djd-title-required'] == "1" ) echo "required='required'"; ?> id="djd_site_post_title" name="djd_site_post_title" maxlength="255" <?php if ( $my_post ) echo "value='".$my_post->post_title."'"; ?>autofocus="autofocus"/>
		<?php if ($djd_options['djd-show-excerpt']) { ?>
			<label for="djd_site_post_excerpt"><?php echo ( $djd_options['djd-excerpt'] ? $djd_options['djd-excerpt'] : __('Excerpt', 'djd-site-post') ); ?></label>
			<textarea id="djd_site_post_excerpt" name="djd_site_post_excerpt"><?php if ( $my_post ) echo $my_post->post_excerpt; ?></textarea>
		<?php } ?>
		<label for="djdsitepostcontent"><?php echo ( $djd_options['djd-content'] ? $djd_options['djd-content'] : __('Text', 'djd-site-post') ); ?></label>
		<?php
		$settings = array(
			'media_buttons'	=> (boolean) $djd_options['djd-allow-media-upload'],
			'teeny'			=> $teeny,
			'wpautop'		=> true,
			'quicktags'		=> $show_quicktags
		);
		$editor_content = '';
		if ( $my_post ) $editor_content = $my_post->post_content;
		wp_editor($editor_content, 'djdsitepostcontent', $settings );

		if ( is_user_logged_in() || $djd_options['djd-guest-cat-select'] ){
		
			$orderby = $djd_options['djd-category-order']; //The sort order for categories.
			$active_cat=0;
			if ( $my_post ) {
				$cats=get_the_category($my_post->ID);
				if($cats[0]) $active_cat=$cats[0]->cat_ID;
			}
			switch($djd_options['djd-categories']){
				case 'none':
					break;
				case 'list':
					$args = array(
						'orderby'           => $orderby,
						'order'             => 'ASC',
						'show_count'        => 0,
						'hide_empty'        => 0,
						'child_of'          => 0,
						'echo'              => 0,
						'selected'          => $active_cat,
						'hierarchical'      => 1,
						'name'              => 'djd_site_post_select_category',
						'class'             => 'class=djd_site_post_form',
						'depth'             => 0,
						'tab_index'         => 0,
						'hide_if_empty'     => false
					); ?>
					<label for="select_post_category"><?php echo ( $djd_options['djd-categories-label'] ? $djd_options['djd-categories-label'] : __('Select a Category', 'djd-site-post') ); ?></label>
					<?php echo str_replace("&nbsp;", "&#160;", wp_dropdown_categories($args));
					break;
				case 'check':
					$args = array(
						'type'              => 'post',
						'orderby'           => $orderby,
						'order'             => 'ASC',
						'hide_empty'        => 0,
						'hierarchical'      => 0,
						'taxonomy'          => 'category',
						'pad_counts'        => false
					); ?>
					<label for="djd_site_post_cat_checklist"><?php echo ( $djd_options['djd-categories-label'] ? $djd_options['djd-categories-label'] : __('Category', 'djd-site-post') ); ?></label>
					<ul id="djd_site_post_cat_checklist">
					<?php $cats = get_categories($args);
					foreach ($cats as $cat) { ?>
						<li><input type="checkbox" name="djd_site_post_checklist_category[]" value="<?php echo ($cat->cat_ID); ?>" <?php if( in_category($cat->cat_ID, $my_post->ID) ) echo "checked='checked'"; ?> />&nbsp;<?php echo($cat->cat_name); ?></li>
					<?php } ?>
					</ul>
					<?php break;
			}
		}
		if ($djd_options['djd-allow-new-category'] && $verified_user['djd_can_manage_categories']) { ?>
			<label for="djd_site_post_new_category"><?php echo ( $djd_options['djd-create-category'] ? $djd_options['djd-create-category'] : __('New category', 'djd-site-post') ); ?></label>
			<input type="text" id="djd_site_post_new_category" name="djd_site_post_new_category" maxlength="255" />
		<?php }
		if ($djd_options['djd-show-tags']) { ?>
			<label for="djd_site_post_tags"><?php echo ( $djd_options['djd-tags'] ? $djd_options['djd-tags'] : __('Tags (comma-separated)', 'djd-site-post') ); ?></label>
			<input type="text" id="djd_site_post_tags" name="djd_site_post_tags" maxlength="255" <?php if ( $my_post ) echo "value='".implode( ', ', $my_post->tags_input )."'"; ?>/>
		<?php }

		if (current_theme_supports('post-formats') && $djd_options['djd-post-format']) {
			$post_formats = get_theme_support( 'post-formats' );
		
			if ( is_array( $post_formats[0] ) ) :
				$post_format = get_post_format( $my_post->ID );
				if ( !$post_format )
					$post_format = '0';
				// Add in the current one if it isn't there yet, in case the current theme doesn't support it
				if ( $post_format && !in_array( $post_format, $post_formats[0] ) )
					$post_formats[0][] = $post_format;
			?>
				<label for='djd-post-format'><?php _e('Post Format', 'djd-site-post'); ?></label>
				<select id='djd-post-format' name='djd-post-format'>
				<option value="0" <?php selected( $post_format, '0' ); ?> ><?php echo get_post_format_string( 'standard' ); ?></option>
				<?php foreach ( $post_formats[0] as $format ) : ?>
				<option value="<?php echo esc_attr( $format ); ?>" <?php selected( $post_format, $format ); ?> ><?php echo esc_html( get_post_format_string( $format ) ); ?></option>
				<?php endforeach; ?>
				</select>
			<?php endif;
		}

		if ( ($djd_options['djd-guest-info']) && (!is_user_logged_in()) ){ ?>
			<label for="djd_site_post_guest_name"><?php _e('Your Name', 'djd-site-post'); ?></label>
			<input type="text" required="required" id="djd_site_post_guest_name" name="djd_site_post_guest_name" maxlength="40" />

			<label for="djd_site_post_guest_email"><?php _e('Your Email', 'djd-site-post'); ?></label>
			<input type="email" required="required" id="djd_site_post_guest_email" name="djd_site_post_guest_email" maxlength="40" /><br><br>
		<?php } ?>

	<!--<span id="loading"></span>-->
	<input type="hidden" name="action" value="process_site_post_form"/>
	<?php if ( ($djd_options['djd-quiz']) && (!is_user_logged_in()) ) { ?>
		<?php $no1 = mt_rand(1, 12); $no2 = mt_rand(1, 12); ?>
		<label class="error" for="djd_quiz" id="quiz_error" style="margin: 0 0 5px 10px; display: none; color: red;"><?php _e('Wrong Quiz Answer!', 'djd-site-post'); ?></label>
		<label for="djd_quiz" id="djd_quiz_label"><?php echo $no1; ?> plus <?php echo $no2; ?> =</label>
		<input type="text" required="required" id="djd_quiz" name="djd_quiz" maxlength="2" size="2" />
		<input type="hidden" id="djd_quiz_hidden" name="djd_quiz_hidden" value="<?php echo $no1 + $no2; ?>" />
	<?php } ?>
	<?php if (is_user_logged_in()) {
		if ( $this->djd_check_user_role( 'administrator', $verified_user['djd_user_id'] ) || $this->djd_check_user_role( 'editor', $verified_user['djd_user_id'] ) ) {
			?>
			<label for="djd-priv-publish-status"><?php _e('Post Status', 'djd-site-post'); ?></label>
			<select id='djd-priv-publish-status' name='djd-priv-publish-status'>
				<option value='publish' <?php if ($djd_options['djd-publish-status'] == 'publish') echo 'selected="selected"'; ?>> <?php _e('Publish', 'djd-site-post') ?></option>
				<option value='pending' <?php if ($djd_options['djd-publish-status'] == 'pending') echo 'selected="selected"'; ?>> <?php _e('Pending', 'djd-site-post') ?></option>
				<option value='draft' <?php if ($djd_options['djd-publish-status'] == 'draft') echo 'selected="selected"'; ?>> <?php _e('Draft', 'djd-site-post') ?></option>
				<option value='private'> <?php _e('Private', 'djd-site-post') ?></option>
			</select><br><br>
		<?php }
	} ?>
	<button type="submit" class="send-button" id="submit"><?php echo ( $djd_options['djd-send-button'] ? $djd_options['djd-send-button'] : __('Publish', 'djd-site-post') ); ?></button>
	<button id="refresher" type="reset" onclick="RefreshPage()" class="send-button <?php echo ($sr && $cf['form_ok']) ? 'visible' : ''; ?>"><?php _e('New Post', 'djd-site-post'); ?></button>
	<p id="success" class="<?php echo ($sr && $cf['form_ok']) ? 'visible' : ''; ?>"><?php echo $djd_options['djd-post-confirmation']; ?></p>
	<p id="error" class="<?php echo ($sr && !$cf['form_ok']) ? 'visible' : ''; ?>"><?php echo $djd_options['djd-post-fail']; ?></p>
	</div> <!-- field-wrapper -->
</form>
<!--<div id="feedback"></div>-->
<?php } ?>
<script>
	var myForm = document.getElementById("site_post_form");
	myForm.style.display = "block";
</script>
<noscript>
	<div class="noscriptmsg">
		<p><?php _e("Seems like you don't have Javascript enabled. To use this function you need to enable JavaScript.", "djd-site-post"); ?></p>
	</div>
</noscript>
<script type="text/javascript">
	jQuery('#site_post_form').on('submit', ProcessFormAjax);
</script>
<script>
	function RefreshPage(){
		var newlocation = location.href;
		location.replace( newlocation.replace(location.search, '') );
	}
</script>