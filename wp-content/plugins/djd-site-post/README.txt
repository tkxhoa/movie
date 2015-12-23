=== Plugin Name ===
Contributors: djarzyna
Donate link: http://www.djdesign.de/djd-site-post-plugin-en/
Tags: quick post, frontend, front end, insert post, post, front end post, guest post
Requires at least: 3.3.1
Tested up to: 3.6.1
Stable tag: 0.9.3
License: GPLv2 or later

Write and edit a post at the front end without leaving your site. Supports guest posts.

== Description ==

Add a (responsive) form to your site to write a post without having to go into the admin section. It allows for 'anonymous' or 'guest' posting (not logged in users). This makes DJD Site Post a perfect plugin for user generated content. 

After installation and activation you can display a form on your site via shortcode.

DJD Site Post is translation ready. Languages already included: English and German.

Now the plugin has a widget to include the form in a sidebar. 

Upcoming Features:

* Edit or delete existing posts from front end.
* Some "skins" (css)
* Captcha for guest posts 

== Installation ==

1. Unzip djd-site-post.zip
2. Upload all the files to the `/wp-content/plugins/djd-site-post` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Customize the plugin. To do so click `DJD Site Post` in the WordPress `Settings` menu.
5. Include the shortcode [djd-site-post] in the post or page were you want to display the form.

== Frequently Asked Questions ==

No questions asked so far.

== Screenshots ==

1. screenshot-1.jpg DJD Site Post in action (Theme: 'TwentyTwelve'): inserted in an ordinary page using the shortcode.
2. screenshot-1.jpg DJD Site Post Settings Panel in common WordPress style.

== Changelog ==

= 0.9.3 =

* Bug fix: Dropping tags when editing existing post in front end.
* Bug fix: Keeping old post data when clicking 'New Post' after editing existing post in front end.

= 0.9.2 =

* Media upload should work now for non-admin users. So if you tried something in the way I described below you should undo it.
* Fixed a minor bug in quiz.js (jquery inside script-tags). Thanks for telling me Spencer Labadie.
* I've implemented a function that redirects users back to the home page after login. If you want to use it you have to uncomment line 84 in djd-site-post.php. I am going to make this an option in the options/setting panel. But first I will redesign the options panel since it became to crowded.
* Fixed some minor cosmetic css stuff (height of input fields, border around input fields).
* Special thanks for participating in early tests to Dietrich Koch. This I should have said when releasing the 0.9 version. But better late than never.

= 0.9.1 =

* With the release 0.9 I uploaded a wrong copy. I am wondering why I did't receive any comments so far because Ajax was not working at all. Anyway, here is the correct copy now.
* There is a problem with media uploads by users that have other roles than 'Administrator'. It is not only this plugin that experiences an error when uploading media files from the front end.
* Other plugins and themes, the P2 theme for instance, that use the WP default media uploader to upload files from the front end seem to get this error too.
* So it looks like a more general problem in core. I am lookin into this right now. It is the function wp_ajax_upload_attachment inside ajax-actions.php in wp-admin/includes that triggers the error.
* You get it to work if you comment out two lines as I did in the code snippet below. But I don't recommend doing this because it might open a security hole.
* I just mention it to give someone else who might be interested to think about a permanent and secure solution a place to start looking.
*
*    if ( isset( $_REQUEST['post_id'] ) ) {
*        $post_id = $_REQUEST['post_id'];
*        //if ( ! current_user_can( 'edit_post', $post_id ) )
*        //    wp_die();
*    } else {
*        $post_id = null;
*    }
*
* So keep in mind that this is not a 'fix' and it will be overwritten by the next WP core update anyway.

= 0.9 =

* Improved version of 0.7 release. Got rid of all the xml stuff. All those dreaded xml errors should be a thing from the past now.
* The plugin uses Ajax now. While including this I´ve decided to drop redirection after post in order to keep things simple. Users will stay at the page where the form is included and just receive a success or failure message after posting.
* Admins, Editors and Authors now can edit existing posts at the front end. Once they are logged in they will see a 'Frontend Edit' link right beside the regular WP 'Edit' link. For this front end editing to work in the field 'Edit Page ID' you have to enter the page-id of the page where you display the edit form. For example: If you entered the plugin shortcode on a page with page-id 427 you just enter 427.
* In the plugin options you can select not to display the regular WP 'Edit' link if you wish to display the 'Frontend Edit' Link only. This applies to posts only.
* The publish status you select in the plugin options´ field 'Publish Status' applies to all users, but you can change it in the form at the front end if you are an Administrator or Editor.
* Users can select a post format (Standard, Aside, Link...) now. You can specify the default post format in plugin options. You can enable/disable this feature in plugin options.
* I´ve included a simple anti spam quiz that applies to all users not logged in if activated in the plugin options. It´s not sophisticated but should do a basic job.
* To support Ajax and the new anti spam quiz the plugin needs JavaScript support. The plugin checks to see if JavaScript is enabled in the user´s browser. If JS is not enabled the user just receives a 'Turn on JavaScript' message and is not permitted to use the form. In fact he doesn´t even see the form.

= 0.8 =

This is a rollback to version 0.6 because version 0.7 had to many bugs. Sorry for any inconviniences I may have cause with this 0.7 release.

= 0.7 =

New features and changes

* Major rewrite. Got rid of all the xml stuff. All those dreaded xml errors should be a thing from the past now.
* Admins, Editors and Authors now can edit existing posts at the front end. Once they are logged in they will see a 'Frontend Edit' link right beside the regular WP 'Edit' link. For this front end editing to work in the field 'Edit Page ID' you have to enter the page-id of the page where you display the edit form. For example: If you entered the plugin shortcode on a page with page-id 427 you just enter 427.
* In The plugin options you can select not to display the regular WP 'Edit' link if you wish to display the 'Frontend Edit' Link only.
* The publish status you select in the field 'Publish Status' does not apply to Administrators and Editors. It only applies to Authors, Contributors and obviously to Guests.

Bug fixes:

* Redirect after the post should work now. Enter the full url to redirect to in the field 'Redirect to'. You can also specify the url or page-id inside the shortcode: [djd-site-post success_url="url"] or [djd-site-post success_page_id="id"]

= 0.6 =

New features and changes

* After posting the user will be redirected to home_url. You can overwrite this default redirect by entering an url in the field 'Redirect To' in the plugin's settings panel. You could also specify the url to redirect to in the shortcode: [djd-site-post success_url='your url']. It might be a good idea to redirect to your blog page so that the user can see his post immediately (provided you permit publishing). Otherwise you could build a success page and redirect to that. Maybe there you should write something like "Thank you for your contribution. We will review your post and publish it if appropriate".
* By selecting Droplist as the method to display categories you will now get child categories below their parent categories.
* Since this question came up a couple of times: The plugin supports featured images for quite a long time now. You just have to find your way in WordPress' standard media uploader ...
* The widget displays an editor similar to WordPress' QuickPress. So you don't get all those fancy buttons of the visual editor there. I had to do it this way because - I admit it - I couldn't get this buttons to work nicely in Chrome. Long term solution might be to switch editors ...
* Extended the max lenght of the fields name (of the guest) and email to 40 characters.
* To do the same as WordPress does the plugin now permits posts without titles. The default still requires the user to enter a title though. If you wish to disable this enforcement just uncheck 'Require a Titel' in the plugin's settings panel.

Bug fixes:

* Long form title breaking container in IE.

= 0.5 =

This release comes with a couple of new features and some important changes.

* Guests get some capabilities similar to the user role of subscriber plus the right to publish (pending) posts. Guests are not allowed to upload media.
* On the plugin's settings page you can specify that the site-post form requires guests to enter their email and name. The information is stored in two custom fields: guest_name and guest_email.  
* On the plugin's settings page you can specify a default category for guest posts or give guests the freedom to select categories themselves.
* Media upload works for logged-in users only. That means users have to register first. I had to do this for security and management reasons.
* The plugin adds the capability to upload media to the user role of contributor. So if you want to grant users the right to upload media you have to assign as minimum the contributor role to them. You can do this during user registration (in WP's Settings->General just set New user Default Role to Contributor).
* Since the media upload works for logged-in users only the plugin (on the plugin's settings page) gives you the ability to hide the WordPress adminbar (now called toolbar).
* On the plugin's settings page you can specify to display a link to WP's login form right inside the site-post form. After login the user will be redirected to the original page again. 

Bug fixes:

Media uploads (attachments) are now assigned to the post they belong to and not to the page were the site-post form resides.

What I didn't come around to yet is implementing a functions to block the loading of the widget and the form on the same page. Both on the same page will not work. So for now be careful not to load the form on pages where the widget exists already (or the other way around).)    

= 0.4 =

A couple of minor bug fixes.

= 0.3 =

New features:

* Included a widget to put the form into the sidebar.

Bug fixes:

* Fixed an issue with a coditional statement that caused an error when running on PHP prior version 5.3.

= 0.2 =

Bug fixes:

* With guest posts the field "author" was left empty. Now it displays the author info out of account you've selected to use with guest posts.
* Fixed an issue with register_uninstall_hook that caused a warning when debug was enabled.

= 0.1 =
The initial release thrown into public.

== Upgrade Notice ==

Nothing yet.