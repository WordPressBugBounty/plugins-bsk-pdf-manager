=== BSK PDF Manager ===
Contributors: bannersky
Plugin URI: https://www.bannersky.com/bsk-pdf-manager/
Tags: PDF manager, meeting minutes tool, printable forms tool, data sheets tool, embed PDF
Requires at least: 5.3
Tested up to: 6.6.2
Stable tag: 3.6.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Manage your PDFs / documents by category, can be display in list, columns and dropdown. Easy to embed a PDF contnet into post / page.

== Description ==

This plugin was first released in 2013 and has over 10,000 active installs. Many webmasters use it to manage thousands of PDFs/documents.

Although this plugin is called "PDF Manager", it can also manage other files like: pdf, zip, gz, rar, png, jpg, jpeg, gif, tif, tiff, swf, docx, xlsx, pptx, csv, crtfsv, Pages, numbers, keynotes, ie.

It helps you easily manage PDFs/documents in WordPress and display them on the page very conveniently. You can upload and display by category or display a special PDF/file. Each PDF/document can have its own permalink, which means you can share the permalink with your clients, and you can update the PDF/document version at any time without worrying that clients can't find the file. It's easy to use, you just need to add the shortcode to the page/post you want to display. Then it will show the PDF/file link in your page/post.

Starting with version 3.5 <a href="https://bannersky.com/bsk-pdf-manager/"  target="_blank">BSK PDF Manager</a> uses <a href="https://mozilla.github.io/pdf.js/" target="_blank" rel="noopener" class="documentation-active-anchor">Mozilla's PDF.js</a> to display PDF content. When this feature is enabled, all PDF documents' content will be displayed in the browser when a visitor clicks a link to the document. This means that the PDF document can be displayed on the visitor’s screen regardless of the platform the visitor is using, PC, Mac, iPhone, iPad, Android…

We also have a pro version that provides more features for some administrators with advanced requirements. Such as featured images for PDF/document, thumbnail generation from PDF, notifications, bulk add via FTP, bulk add via media... For all features in the pro version, visit <a href="http://www.bannersky.com/bsk-pdf-manager/">https://www.bannersky.com/bsk-pdf-manager/</a> for documentation.

Check out the demo: <a href="https://demo.bannersky.com/bsk-pdf-manager-demos/" target="_blank">https://demo.bannersky.com/bsk-pdf-manager-demos/</a > , please note that the demo site uses the Pro version.

We welcome your valuable ideas and features you need for the future version. 

== Installation ==

Activate the plugin then you can use either a shortcode [bsk-pdfm-pdfs-ul id="ALL" order_by="date" order="DESC" target="_blank"] to show all PDFs / Documents in date descending order. Or use [bsk-pdfm-pdfs-ul id="8,9,10,11,12" target="_blank"] to show special PDFs / Documents. 

Check <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-specific-pdfs-in-list/" target="_blank">here for more attributes of the shortcode</a> and <a href="https://demo.bannersky.com/bsk-pdf-manager-demos/display-all-specific-pdfs/all-pdfs-in-unordered-list-in-date-descending-order-open-in-new-window-with-pagination/" target="_blank">here for demos</a> about this shortcode.

You may use [bsk-pdfm-category-ul id="1" show_cat_title="yes" order_by="date" order="DESC"] to show all PDFs / Documents under the category of id 1 or [bsk-pdfm-category-ul id="1,2,3" show_cat_title="yes" order_by="date" order="DESC"] to show all PDFs under categories of id 1, 2, 3 in date descending order.

Check <a href="https://www.bannersky.com/document/bsk-pdf-manager-documentatio-v2/display-pdfs-by-category-in-list/" target="_blank">here for more shortcode attributes</a> and <a href="https://demo.bannersky.com/bsk-pdf-manager-demos/display-pdfs-by-category/display-pdfs-by-category-in-unordered-list-with-pagination/" target="_blank">here for demos</a> about this shortcode.

Starting with version 3.5, you may use shortcode [bsk-pdfm-pdfs-embed id="52"] to embed PDF content into post / page or any area that can execute a shortcode. Check <a href="https://bannersky.com/document/bsk-pdf-manager/embed-pdf-into-post-page/display-pdf-by-embedding/" target="_blank">here for more attributes of the embedded shortcode</a> 

The plugin has a very easy admin page that allows you to manage categories and PDF documents.

== Frequently Asked Questions ==

Please visit <a href="http://www.bannersky.com/bsk-pdf-manager/">http://www.bannersky.com/bsk-pdf-manager/</a> for documentations or supporting.

== Screenshots ==

1. Settings interface, you may support more file types there
2. Upload your PDF / File from computer or WordPress Media library
3. Categories manager interface
4. Permalink settings

== Changelog ==

3.6.2

* Updated: Mozilla's PDF.js to version 4.6.82.

* Fixed: a Cross Site Scripting (XSS) vulnerability on settings page.

* Compatible with WordPress 6.6.2

3.6.1

* Updated: Mozilla's PDF.js to version 4.4.168.

* Fixed: Cross Site Scripting (XSS) vulnerability.

* Compatible with WordPress 6.5.5

3.6

* Added: new document status of draft. You can set a document as a draft with or without a PDF attached.

* Compatible with WordPress 6.5.4

( In addition to the above, the Pro version also does the following )

* Added: new feature of infinite loading documents when display in UL / OL and columns layout. Just add infinite_scrolling="yes" and pdfs_per_page="x" to your shortcode and it will load the document as you scroll the screen. 

* Added: shortcode to show documents count in a category. [bsk-pdfm-category-count cat_id="1"] will show the count of documents in category with id: 1 .

* Fixed: the bug of cannot load documents when display in UL / OL layout when click pagination anchor or others filter anchor.

* Fixed: the bug of cannot load documents when change "Day of week" select in "Date weekday query filter" in display by category mode.

3.5

* Added: new feature of display PDF contnet by using Mozilla's PDF.js. With this feature a PDF document can be displayed on the visitor’s screen regardless of the platform the visitor is using, PC, Mac, iPhone, iPad, Android......Once the embedded viewer is enabled, all PDF documents' content will be displayed in the browser when a visitor clicks a link to the document.

* Added: new shortcode [bsk-pdfm-pdfs-embed id="1"] for embedding PDF content into post / page. You may set the width and height of the container to display PDF content. Also can control the toolbar options of the PDF viewer. This feature has nothing to do with the global embedded viewer, you can use this shortcode with the embedded viewer enabled or disabled. 

* Fixed: the issue of cannot delete plugin.

* Compatible with PHP 8.2

* Compatible with WordPress 6.4.3

3.4.2

Fixed: possibility for authenticated attackers with contributor-level and above permissions to inject arbitrary web scripts in pages that will execute whenever a user accesses an injected page when use the shortocde: bsk-pdfm-category-dropdown.

Added: support more Autocad files ( rfa, rvt, step, stp )

Compatible with WordPress 6.3.2

3.4.1

Added: support Autocad files ( dwg, dxf, dgn, stl )

Added: new span structure to wrap PDF title, use CSS class "bsk-pdfm-pdf-title-string" to style or hide the PDF title

Compatible with WordPress 6.1.1

( In addition to the above, the Pro version also does the following )

Fixed: a bug when do capability settings

Fixed: a bug when generating title in upload by FTP screen

Fixed: the 405 error when display PDFs in dropdown


3.4


* Fixed: the trouble of invalid value for Date Picker that caused by The Events Calendar

* Compatible with WordPress 6.0.1

( In addition to the above, the Pro version also does the following )

* Added: support for adding text before to date string using the shortcode parameter date_prefix

* Added: show update count to menu if new version released

* Added: making PDF link can be track download count when use shortcode such as: [bsk-pdfm-pdfs-ul id=&quot;1&quot; link_only=&quot;yes&quot;] . Use [bsk-pdfm-pdfs-ul id=&quot;1&quot; link_only=&quot;yes&quot; link_only_no_desc=&quot;yes&quot;] to out html such as: &lt;a href=&quot;http://your_pdf&quot;&gt; to add your own description text and close tag &lt;/a&gt;. Use [bsk-pdfm-pdfs-ul id=&quot;1&quot; link_only=&quot;yes&quot; link_only_no_close=&quot;yes&quot;] to out html such as: &lt;a href=&quot;http://your_pdf&quot;&gt;PDF_Title, you can add more description and close tag &lt;a/&gt;

* Added: support for setting a default featured image for each file type

* Fixed: the bug of PDF/document cannot be open when using default_cat_id parameter in selector shortcode, eg: [bsk-pdfm-selector-dropdown cat_id=&quot;ALL&quot; default_cat_id=&quot;1&quot;]

* Fixed: the bug of tag listed in parent category dropdown when add new category

3.3


* Added: displays file size information.

* Added: screen options to support setting the default sort columns on the categories list page.

* Added: a guide to help users migrate all PDFs/documents from the current site to the a site.

* Compatible with WordPress 5.9.3

( In addition to the above, the Pro version also does the following )

* Added: notifications feature, which can send notifications to specified emails, all users of selected roles and selected users. It supports sending notifications manually, and also supports sending notifications automatically according to events, status, and category. Provide merge tags to include PDF/documents links or edit links in emails. The body of the email can be in html format. Multiple notifications can be created to automatically send notifications to different users, for example, if a pending document is added, the administrator will be notified to view it. For another example, add the news letter of the current month and send it to a specific user group for viewing.

* Added: settings to redirect permalinks to PDF/document file links. Provides global settings to redirect all permalinks to file links of PDFs/documents or redirect settings for individual PDFs/documents. For some huge PDF files, such as those with a size of more than 100M, using permanent links is convenient for users to access the files, and with this function, it can speed up the speed of users to open files and save the CPU resources of the host.

* Fixed: wrong capability for edit category, wrong publish capability for uploading by FTP / Media Library.

* Fixed: Potential file save error when set custom upload folder site root

* Fixed: unable to open or download file on IOS device( iPhone, iPad ) when display PDF / documents in dropdown.


3.2.1

* Fixed: PHP warning

* Updated: Pro tips text

3.2

* Updated: adjusting menu items order

* Fixed: PHP warning on PDFs / documents list page

* Compatible with WordPress 5.9

( The following changed done to the Pro version )

* Added: new capability settings to let different users have different capability to add / manage PDFs / documents in backend

* Added: enable contributor users to add / manage pending PDFs / Document.

* Added: users with bsk_pdfm_publish and bsk_pdfm_edit_others capabilities can publish the pending PDFs / documents.

* Added: enable custom role to access backend.

* Added: assign available categories to user. Users with available categories can only add / edit PDFs / Documents in the assigned categories.

* Added: keywords with exactly match type in search bar. In exactly match type mode, only file name or PDF / Document title / description same as the keywords will be listed.


3.1.3

* Fixed: update codes to secure output content

3.1.2

* Fixed: the codes of order_by in sql that may cause trouble

3.1.1

* Fixed: the bug when set widget in backend and display widget in front

3.1

* Added: support documents( PDFs ) Permalink feature

* Added: support documents( PDFs ) to be put in trash

* Added: new category filter option to show  documents( PDFs ) that have no category assigned.

* Added: document status link to list all, published, scheduled, expired, trash

* Added: screen options to be applied to documents list page

* Added: more parameters to the hook bsk_pdfm_after_document_insert and bsk_pdfm_after_document_update

* Updated: don't delete documents( PDFs ) in a category when delete the category.

* Updated: backend PDFs list page, add more status and view

* Fixed: the All label cannot be translated in tags filter

* Fixed: the bug of invalid file path when move document out from Media Library

* Compatible with WordPress 5.8.1


3.0.1

* Fixed the bug of target="_blank" not work

* Fixed typo

* Compatible with WordPress 5.8


3.0

* Add new feature of generating featured image from the selected page of the PDF

* Support deleting featured image when delete PDF / document

* Remove old shortcodes

* Add new action of bsk_pdfm_after_document_insert

* Add new action of bsk_pdfm_after_document_update

* Add new action of bsk_pdfm_after_bulk_add_by_ftp

* Add new action of bsk_pdfm_after_bulk_add_by_media_lib

* Compatible with WordPress 5.7.2


2.9.3

* Change to use WordPress' new date function.

* Fixed the bug of html description cannot be saved and shown for document( PDF ).

* Fixed the bug of html description cannot be saved and shown for category.

* Fixed the bug of html description cannot be saved and shown for tag.

* Fixed the bug of causing NextGEN Gallery cannot edit thumb.

* Compatible with WordPress 5.7


2.9.2

* Support WordPress that installed to its own directory

* Support WordPress that changed default content directory

* Support WordPress that changed default uploads directory

* Fixed the issue of "missing file" on WordPress that installed in sub directory

* Fixed the bug of showing date wrong format in PDF title

2.9.1

* IMPORTANT: from version 3.0 we'll remove the shortcakes [bsk-pdf-manager-pdf id="1"] and [bsk-pdf-manager-list-category id="1"]. Please change to use [bsk-pdfm-pdfs-ul id="1"] and [bsk-pdfm-category-ul id="1"]. For more attributes of the two shortocdes, please check documents form https://www.bannersky.com/document/bsk-pdf-manager/ 

* Sanitize user entered data before process

* Support ordering by ID for categories and tags 

* Support linking category to all document that assoicated to it

* Fixed the bug of cannot delete documents when delete category

* Improve admin interface

* Compatible with WordPress 5.6.1

2.9

* Supports date&time for tag and order by date when display tags in front

* Add options to display widget title in h3 or h4 tag instead of always h2 

* Fixed the warning message when add category widget in backend and display in front

* Fixed the bug of time for category cannot be updated

* Fixed the bug of tag title, description cannot be updated

* Fixed the bug of tags displayed in categories list screen

* Updated some Dutch translations thank Hans van der Brugh for providing translations.

* Compatible with WordPress 5.6

2.8

* Support tags for PDFs / documents, you may assign one or more tags to a single document

* Add tags filter in front, dynamic load PDFs / documents by Ajax when click filter anchor

* Support move PDFs / documents from Media Library to BSK PDF Manage's upload folder

* Support visit PDF / document directly by ID

* Add option of moving documents out of Media Library

* Add new language of Swedish, thank Anders Olofsson providing Swedish translations

* Fixed the bug of no documents loaded when day of week changed

* Fixed the bug of two Monday displayed in date weekday query filter

* Compatible with WordPress 5.5.3


2.7.2

* Support translating date / day in week according to WordPress language

* Updated codes to be compatible with creating custom folders on hosts such as wordpress.com that has a special Wordpress installation folder.

* Fixed the bug of category cannot be listed ( in selector ) if only child / grand categories have PDFs

* Change PDF description to accept large text / html

* Fixed the warning message with PHP 7.3

* Fixed the bug of some labels not shown in widget

* Fixed the bug of warning message in the file of category-functions.php with some hosting

* Compatible with Wordpress 5.5.1

2.7.1

* Fixed the warning message on edit documents screenshot with default plugin settings.

* Include jQuery UI css file to make date picker work better.

* Compatible with Wordpress 5.5

2.7

* Support more file formats such as .mp3, .mp4, .wmv... 

* Add a filter to switch extension in Dashboard to let users manage documents easier

* Support adding rel="noopener noreferrer" to documents link

* Make category dropdown on PDF / document adding page more clear

* Remove warning message when edit PDF / document

* Fix the issue of showing wrong notice when edit PDF / document

* Fixed warning on setting page on some hosting

* Compatible with Wordpress 5.4.2


Pro version

* Support year month day weekday query filter, this filter load documents by date from database when date change

* Add extension attribute to shortcode

* Improving the interface when use file name as title, support remove all - and all _ in title to space

* Change JavaScript codes to make tracking download count accurately


2.6

* Change script library for uploading directory setting

* Compatible with some special hosting such as Flywheel and crazydomains.com

* Support multiple languages of English, German, Italian, Portuguese, French and Spanish. Also support set to use English always

* Compatible with Wordpress 5.4

Pro version

* Fixed a bug in bulk changing title

* Fixed a bug in bulk changing date

* Fixed a bug of sorting in the case with pagination

* Fixed a bug of category description shown in display with selector mode

* Fixed the bug of extension filter doesn't work for documents from WordPress Media Library

* Fixed a bug when use extension filter with display in category and selector mode

* Fixed a bug when use title start filter with display in category and selector mode

* Fixed the bug of pagination error on some case

* Fixed the bug of query most recent doesn't work for display by category

* Fixed the bug of date cannot be shown correctly when display in dropdown with category selector mode

* Support overwritten search button text by shortcode attribute

* Support attribute to order PDFs/documents across multiple categories


2.5

* Fixed the bug of target="_blank" doesn't work for category shortcodes

* Fixed the bug of wrong link for no category

* Backend interface improving

* Compatible with Wordpress 5.2.4

Pro version

* Support bulk change documents title

* Support bulk add documents from Wordpress' media library

* Support recognize date from document file name

* Support only have category and keywords in search bar

* Fixed the bug of cannot use "add by FTP" when Wordpress run on Windows server

2.4

* Support extension filter

* Support show PDFs / Documents count description, use show_count_desc="yes" in shortcode to enable it 

* Fixed the warning message when edit a document

* Compatible with Wordpress 5.2.2

2.3

* Support .doc file

* Improve uploading file interface

* Improve documents list interface

* Fix widgets cannot sort by date correctly

* Fix duplicate entries appear when do upgrading on some server

* Remove PHP warnings

* Compatible with WordPress 5.1.1

2.2.1

* Fix the bug of removing data when upgrade to Pro version

* Fix a bug of causing failed uploading

* Compatible with WordPress 5.0.3

2.2

* Fix bug when create new directory to upload PDFs

* Update database structure to compatible with Pro version

* Admin interface improved

* Compatible with WordPress 5.0.2

2.1

* Fix bug when upload PDF

* Admin interface improved

* Compatible with Wordpress 5.0.1

2.0

* Support more file types

* Support order by PDFs id sequence

* Support show all categories in one shortcode

* Improve dashboard user interface

* Remove category password feature from free version as it cannot work on some hosting

* Compatible with Wordpress 4.9.8

* Compatible with PHP 5 & PHP 7

1.8.2

* Ready for version 2.0

* Fix PHP warnings

* Compatible with Wordpress 4.9.8

* Compatible with PHP 5 & PHP 7

1.8.1

* Add new shortcode parameter of nolist, set to yes will only output html a element.

* Improve parameters compatibility

* Compatible with Wordpress 4.9.5

1.8

* Fix small bug in widget

* Use new singleton design pattern

1.7.5

* Improve user interface

* Compatible with Wordpress 4.9.4

1.7.4

* Add the feature to show all PDFs when use shortcode [bsk-pdf-manager-pdf showall="yes"]

* Compatible with Wordpress 4.9.2

1.7.3

* Fixed the bug of unclosed list tag when using widgets that for unordered lists

1.7.2

* Add show category title option to widget

* Fixed a small bug

1.7.1

* Fixed the bug of bulk delete triggered when change category filter drop down


1.7

* Change PDF widget title to use h2 tag.

* Add open new target parameter to PDF widget.

* Add PDF Category widget to show all PDF within given category.

* Compatible with Wordpress 4.7.3.


1.6

* Fixed warning message.

* Improved backend interface.

* Add search feature on backend PDFs list page.

1.5.2

* Fixed warning message.

1.5.1

* Fixed the bug of putting an "/" slash at the end of the unordered list in widget.

1.5

* Change Datepicker to use the latest jQuery UI theme css
* Security improvement
* Support show PDF as ordered list

1.4

* Make shortcode support new attributes.
* Fix the bug that only show one category even there are multiple ids.

1.3.9

* Fix a small bug.

1.3.8

* Change PDF file name to XXXX_ID.pdf.

1.3.7

* Fix two warnings & make users who above Editor can add / edit PDFs.

1.3.6

* Fix the bug of cannot delete PDF item or category.

1.3.5

* Fixed a typo.

* Support out PDFs in a dropdown(select).

* Make date for category and PDF item editable.

* Support setting the number of most recent PDFs.

1.3.4

* Fixed a small bug.

1.3.3

* Changed order by parameter, now you may include orderby="title" or orderby="filename" or orderby="date" to order all PDF, also can be order by PDFs' id sequence.

1.3.2

* Fixed the bug of wrong output when category doesn't have a PDF.

1.3.1

* Widget supported now.

* Support wordPress site with subdirectory installation.

* Changed special PDF list from &lt;p&gt; tag to &lt;li&gt; tag, only return PDF link is possilbe. Support multiple PDF id and category id, so show all PDFs in one list is possible.

* Changed backend interface.

1.3

* Support PDF list order by Date, Title and File Name.

1.2.0

* Fixed several small bug.

* Support order by in admin dashboard.

* Hide category title when list PDF.

1.1.0

* Add option to open PDF document in new window.

* Show shortcode in Categories page and PDF Documents page. For convenience user just need copy and paste it to where you want to show the PDF documents.

1.0.0 First version.

