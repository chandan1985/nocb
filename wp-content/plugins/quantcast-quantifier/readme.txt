=== Quantcast Quantifier ===
Contributors: James Turner
Donate link: http://www.jamesturner.co.nz/other/donate/
Tags: tracking, stats, statistics, quantcast, quantify
Requires at least: 2.5
Tested up to: 3.1.3
Stable tag: 1.5.2

Allows you to easily add the necessary JavaScript code to enable Quantcast on your blog.


== Description ==

Quantcast Quantifier adds the necessary JavaScript code to enable Quantcast logging on any WordPress blog. This eliminates the need to edit your template code to begin logging.

http://www.jamesturner.co.nz/other/wordpress/plugin-quantcast-quantifier/


**Features**

Quantcast Quantifier Has the Following Features:

- Inserts tracking code on all pages WordPress manages.
- Easy install: only need to know your Quantcast tag.
- Option to disable tracking of WordPress administrators.
- Can include tracking code in the header or footer, speeding up load times.
- Complete control over options; disable any feature if needed.

**Usage**

In your WordPress administration page go to Options > Quantcast Quantifier. From there enter your Quantcast tag and enable logging. Information on how to obtain your Quantcast tag can be found on the options page.

Once you save your settings the JavaScript code should now be appearing on all of your WordPress pages.



== Installation ==

1. Upload the folder `quantcast-quantifier` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin at Settings > Quantcast Quantifier
4. You will also need a Quantcast account to have the required tracking code


== Frequently Asked Questions ==

= Where is the Quantcast Quantifier code displayed? =

The Quantcast Quantifier code is added to the <head> section of your theme by default. If you choose the footer option it should display somewhere near the bottom before the </html>.
 
= Why don't I see the Quantcast Quantifier code on my website? =

If you have switched off admin logging, you will not see the code. You can try enabling it temporarily or log out of your WordPress account to see if the code is displaying.

= Do I need a Quantcast Account =
Yes, you will need to visit Quantcast and signup to receive tracking code for your website.


== Screenshots ==

1. This is a screen shot of the settings page.


== Changelog ==
= 1.5.2 =
**2011-06-18**
BUG FIX: Cleared the setting/option that was actually printing to the screen.

= 1.5.1 =
**2011-06-18**
BUG FIX: The default settings were being loaded poorly by me.  Have now fixed and tested.  Thank you to those who reported the error.

= 1.5 =
**2011-06-13**
* EDIT: Header/footer option removed
* EDIT: Making it more translation friendly
* EDIT: updated ga_ > qq_ (Thanks Vynce)
* ADD: Advanced area added, this code is added to the head
* ADD: Added Notes at the bottom of the plugin
* ADD: Added register/activation hook and function
* ADD: nonce when saving settings (Thanks Vynce)

= 1.4 =
**2011-03-03**
* EDIT: add_option hooks updated
* EDIT: add_options_page hook updated

= 1.3 =
**2010-11-04**
* House keeping for WP3.0.1.
* More user role control over who is tracked and when.

= 1.2 =
**2009-03-10**
* EDIT: House keeping, updating a few links and wording for some things. 2.7+ screenshot.  Added Settings link to plugin page.

= 1.1 =
**2008-10-17**
* TYPO: add_action('wp_head', 'add_quantcast_uantifier'); >>> add_action('wp_head', 'add_quantcast_quantifier'); - Thanks to dlbjeff http://www.drivelineblog.com for pointing it out.

= 1.0 =
**2008**
* Released


== Upgrade Notice ==

= 1.5.2 =
If you were using version 1.5 then you may have some output at the top of your site.  This will clear that output.

= 1.5.1 =
Please deactivate and reactivate this plugin to make the changes kick in.