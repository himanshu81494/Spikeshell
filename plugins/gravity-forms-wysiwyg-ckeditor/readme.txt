=== CKEditor WYSIWYG for Gravity Forms ===
Contributors: ovann86
Donate link: http://www.itsupportguides.com/
Tags: Gravity Forms, CKEditor, WYSIWYG, WCAG, accessibility, visual editor, online form, form, forms
Requires at least: 4.4
Tested up to: 4.5.2
Stable tag: 1.8.7
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use the CKEditor WYSIWYG in your Gravity Forms

== Description ==

> This plugin is an add-on for the <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">Gravity Forms</a>. If you don't yet own a license of the best forms plugin for WordPress, go and <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">buy one now</a>!

**What does this plugin do?**

* allows you to add the CKEditor WYSIWYG to 'Paragraph Text', 'Body' and 'Custom Field - Paragraph Text' fields
* allows you to add the CKEditor WYSIWYG to the form editor - textarea's in the field settings will use the CKEditor WYSIWYG
* options to control which buttons and features are available in the CKEditor WYSIWYG - found in the Gravity Forms -> Settings -> CKEditor menu.

> See a demo of this plugin at [demo.itsupportguides.com/gravity-forms-wysiwyg-ckeditor/](http://demo.itsupportguides.com/gravity-forms-wysiwyg-ckeditor/ "demo website")

**Why use CKEditor?**

[CKEditor](http://ckeditor.com/ "CKEditor website") is a feature packed WYSIWYG that meets the WCAG 2.0 requirements, providing the best accessibility for your users.

It provides the superior copy-and-paste support for formatted text from Microsoft Word.

This plugin is compatible with the [Gravity Forms Data Persistence Add-On Reloaded](https://wordpress.org/plugins/gravity-forms-data-persistence-add-on-reloaded/ "Gravity Forms Data Persistence Add-On Reloaded website") and [Gravity PDF](https://wordpress.org/plugins/gravity-forms-pdf-extended/ "Gravity PDF website") plugins.

**Disclaimer**

*Gravity Forms is a trademark of Rocketgenius, Inc.*

*This plugins is provided “as is” without warranty of any kind, expressed or implied. The author shall not be liable for any damages, including but not limited to, direct, indirect, special, incidental or consequential damages or losses that occur out of the use or inability to use the plugin.*

== Installation ==

1. Install plugin from WordPress administration or upload folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress administration
1. Open the Gravity Forms 'Forms' menu
1. Open the form editor for the form you want to add CKEditor to
1. Either add a new 'Paragraph Text' field or select an existing one
1. Tick the 'Enable WYSIWYG (CKEditor)' option to enable CKEditor for the field
1. CKEditor will now be used for the fields you selected
1. OPTIONAL: Open the Gravity Forms settings page, the WYSIWYG CKEditor menu to select the buttons you want used in CKEditor

== Screenshots ==

1. Shows CKEditor with the default buttons and no character limit applied. The character count appears below CKEditor, showing the number of characters typed (not including spaces).
2. Shows CKEditor with the default buttons and the character limit set to 456 characters. The character count appears below CKEditor, showing the number of characters typed (not including spaces) and the limit.
3. Shows a 'Paragraph Text' field without CKEditor applied. The Gravity Forms default character count has been modified to replicate what is used with CKEditor.
4. Shows the 'Paragraph Text' field in the form editor. The 'Enable WYSIWYG (CKEditor)' is enabled.
5. Shows the WYSIWYG CKEditor settings page where you can configure which buttons are used in CKEditor.
6. Shows the WYSIWYG CKEditor with all the buttons enabled.
7. Shows the WYSIWYG CKEditor where the maximum characters have been met. The character counter shows the limit has been reached by changing to red. The WYSIWYG CKEditor field does not allow more text to be entered when the limit has been reached.
8. Shows the WYSIWYG CKEditor in the entry editor screen.

== Frequently Asked Questions ==

= What are the default buttons =

By default a limited number of the CKEditor buttons are used. These are:

 * Bold
 * Italic
 * Underline
 * Paste as text
 * Paste from word
 * Numbered list
 * Bulleted list
 * Outdent
 * Indent
 * Link
 * Unlink
 * Format
 * Font
 * Font size

= How do I enable more buttons? =

Open the Gravity Forms settings menu and select the WYSIWYG CKEditor menu.

Here you will find a list of all the available buttons that can be added to the CKEditor.

Open the Gravity Forms settings menu and select the WYSIWYG CKEditor menu.

Here you will find a list of all the available buttons that can be added to the CKEditor.

= What version of CKEditor does this use? =

CKEditor 4.5.8

= How does the character counter work? =

The character counter works by using the 'wordcount' CKEditor plugin. 

The plugin will use the 'Maximum Characters' configured for the field in the Gravity Forms form editor.

Most importantly, the plugin **will only count characters** - not spaces and not formatting HTML markup.

= Does this support image uploading? =

No - not at this point in time.

= How to disable the CKEditor from appearing in the form editor =

As of version 1.5.0 the CKEditor is applied in the back end form editor.

textarea's in side each field edit window will use the CKEditor automatically. For example, the 'Field description' box will have the CKEditor applied.

These fields support HTML, however Gravity Forms does not provide a WYSIWYG visual editor, requiring the person creating the form to manually enter the HTML.

By using CKEditor you are making form creating much easy and providing better formatting options for your field descriptions.

There is a drawback - the CKEditor has an overhead when it needs to load, as well as when you type it needs to send the changes to the field settings.

If this is creating issues for you, or you do not need this feature, you can disable it by going to the Gravity Forms settings menu, then opening WYSIWYG ckeditor menu, unticking the 'Enable in form editor' option and saving the settings.

= Help! The HTML formatting is lost in woocommerce = 

woocommerce will automatically strip the HTML from the field data.

I can't say why they do this, maybe it's for a good reason? But I can provide this code that stops the HTML from being stripped.

`add_filter( 'woocommerce_gforms_strip_meta_html', 'configure_woocommerce_gforms_strip_meta_html' );
function configure_woocommerce_gforms_strip_meta_html( $strip_html ) {
    $strip_html = false;
    return $strip_html;
}`

== Changelog ==

= 1.8.7 =
* FIX: Change short PHP open tag to full open tag.
* MAINTENANCE: Automatically remove characters from uploaded images that may break cross-system compatibility (e.g. < > | :) 

= 1.8.6 =
* MAINTENANCE: Update CKEditor to version 4.5.8.
* MAINTENANCE: Update plugin to support PHP version 5.2.

= 1.8.5 =
* MAINTENANCE: Improve support for 'Product' type fields in the back-end form editor.
* FIX: Resolve issue with undefined variables where settings page has not been used.

= 1.8.4 =
* FIX: Add 'notification' plugin, required by wordcount plugin from version 1.14.

= 1.8.3 =
* FIX: Update CKEditor wordcount plugin to version 1.14 to resolve issue with CKEditor not loading in Greek language browsers.

= 1.8.2 =
* FIX: Better handling of character limit script for CKEditor enabled fields.

= 1.8.1 =
* FIX: Resolve 'array_key_exists() expects parameter 2 to be array' error that occurred on submitting a form.
* MAINTENANCE: Improve translation support.
* MAINTENANCE: Tidy up of PHP code, working towards using WordPress standards.

= 1.8.0 =
* FEATURE: Add support for uploading and attaching images inside the WYSIWYG. 'Image upload' needs to be enabled in the WYSIWYG CKEditor settings page. The upload process is managed through the built in CKEditor image plugin - the 'image' button.
* FEATURE: Add support for the future release of Gravity PDF 4.x
* FEATURE: Add the ability to override the Gravity Forms 'Enable No-Conflict' option.
* FEATURE: Upgrade CKEditor to version 4.5.7.

= 1.7.1 =
* FIX: Resolve issue with editing fields in the form editor when 'Enable in form editor' option is enabled. It appears that some users have the Gravity Forms 'No-Conflict Mode' enabled. This option breaks third party plugins that rely on scripts to be loaded. IF YOU HAVE THIS OPTION ENABLED THE CKEDITOR WILL NOT LOAD IN THE FORM EDITOR OR ENTRY EDITOR. 

= 1.6.6 =
* FIX: Attempt to resolve issue with editing fields in the form editor when 'Enable in form editor' option is enabled. 
* FEATURE: Added support for the 'Default Value' field option.
* FEATURE: Add 'Settings' link to plugin on the WordPress installed plugins page.

= 1.6.5 =
* FIX: Resolve issue with character counter when the 'Gravity Forms Data Persistence Add-On Reloaded' plugin is also installed.
* FIX: Resolve issue with 'File Upload' field breaking in the form editor when the 'Enable Multi-File Upload' option is used.

= 1.6.4 =
* FIX: Resolve issue with Gravity Forms 1.9.15 and above not displaying HTML field content correctly in the entry editor, gravity PDF and using GravityWiz better pre-confirmation.

= 1.6.3 =
* MAINTENANCE: Change is_wysiwyg_ckeditor() function from private to public to allow developers to check for a CKEditor enabled field.

= 1.6.2 =
* FIX: Resolved issue with CKEditor loading on number field 'enable calculation' textarea.
* MAINTENANCE: Added blank index.php file to plugin directory to ensure directory browsing does not occur. This is a security precaution.
* MAINTENANCE: Added ABSPATH check to plugin PHP files to ensure PHP files cannot be accessed directly. This is a security precaution.
* FEATURE: Added '.wysiwyg_exclude' exception to JavaScript that targets textarea fields and enables CKEditor. This allows form creators and plugin developers to use this class if they do not want the CKEditor to be applied to a textarea (paragraph) field.

= 1.6.1 =
* FIX: Resolve issue with character count not appearing in some browsers (e.g. Internet Explorer). The character count now appears in the grey box below CKEditor, previously it appeared below they grey box.

= 1.6 =
* FEATURE: Added Media (oEmbed) Plugin (can be enabled in the settings page). This also required adding the lineutils, dialog and widget plugins. More information on this plugin can be found here: http://ckeditor.com/addon/oembed
* FEATURE: Added support for text translation - uses 'gravity-forms-wysiwyg-ckeditor' text domain.
* FIX: Resolve issue with CKeditor loading on drop down field 'Bulk Add / Predefined Choices' window in form editor.
* FIX: Resolve issue with CKeditor attempting to load when viewing 'Forms' page or form settings pages in wp-admin.

= 1.5.2 =
* FIX: Resolve issue with field settings not expanding after removing a field in the form editor.

= 1.5.1 =
* FIX: Resolve issues with CKeditor not loading for single page forms.
* MAINTENANCE: Further improvements to JavaScript that handles CKeditor in the form editor.

= 1.5.0 =
* FEATURE: Add support for multisite WordPress installations.
* MAINTENANCE: Improve JavaScript that handles CKeditor in the form editor.

= 1.4.0 =
* Feature: add CKeditor support for the form editor. textarea fields which are included in the field settings will automatically use the CKeditor - this can be disabled from the CKEditor WYSIWYG settings page.
* Maintenance: resolve various PHP errors that were appearing in debug mode, but did not affect functionality.
* Maintenance: improve performance of CKeditor enabled fields by only applying CKeditor to fields on the current displayed page.
* Maintenance: improve how plugin default settings are handled - changed so settings are automatically saved to the database when first running the plugin, instead defaults are stored in an array and combined with any existing settings.

= 1.3.1 =
* Maintenance: Upgrade CKEDITOR to Version 4.5.2 - FULL (4 Aug 2015). Version 1.3.0 inadvertently included the CKEDITOR STANDARD - which does not include some of the formatting options.

= 1.3.0 =
* Fix: Resolve issue with plugin attempting to load before Gravity Forms has loaded, making this plugin not function.
* Fix: Change JavaScript to allow CKEDITOR enabled fields to work in ajax enabled multi-page forms.
* Maintenance: Upgrade CKEDITOR to Version 4.5.2 (4 Aug 2015). See CKEDITOR change log for full detail http://ckeditor.com/whatsnew 
* Maintenance: Change plugin name from 'Gravity Forms - WYSIWYG CKEditor' to 'CKEditor WYSIWYG for Gravity Forms'

= 1.2.1 =
* Fix: Make CKEditor only apply to WYSIWYG enabled fields in the entry editor screen. Was previously incorrectly applied to all paragraph (textarea) form fields.

= 1.2.0 =
* Feature: Add CKEditor support for entry editor screen - seen when editing entries with WYSIWYG enabled paragraph fields.
* Fix: Added function to remove blank lines between paragraphs when field is saved. This improves how the field is displayed in the entry editor screen and improves compatibility with GravityPDF.
* Maintenance: General tidy up of PHP layout. Use wp_enqueue_script for JS files. 

= 1.1.2 =
* Fix: Moved jQuery script to footer to resolve errors where it loaded before jQuery was ready.

= 1.1.1 =
* Fix: add space in textarea between 'data-maxlen' and 'name' attributes. All browsers worked without the space, but was technically invalid HTML.

= 1.1.0 =
* Feature: extended to work with 'Body' field (found under 'Post Fields' tab) and 'Custom Field - Paragraph Text'  field (found under 'Post Fields' tab).

= 1.0.1 =
* Fix: resolve issue that caused the 'Strength Indicator' in the Gravity Forms 'Password' field to not work.

= 1.0 =
* First public release.