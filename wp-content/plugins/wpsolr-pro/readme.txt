=== WPSOLR PRO ===

Contributors: wpsolr

Author: wpsolr

Current Version: 23.1

Author URI: https://www.wpsolr.com/

Tags: search, TablePress search, Solr in WordPress, wordpress search, bbPress search, WooCommerce search, ACF search, coupon search, affiliate feed search, relevance, Solr search, fast search, wpsolr, apache solr, better search, site search, category search, search bar, comment search, filtering, relevant search, custom search, filters, page search, autocomplete, post search, online search, search, spell checking, search integration, did you mean, typeahead, search replacement, suggestions, search results, search by category, multi language, seo, lucene, solr, suggest, apache lucene

Requires at least: 3.7.1

Tested up to: 6.2.2

Stable tag: 23.1

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html


Search faster. When your Wordpress search fails, when your WooCommerce search or bbPress search gets stuck, you need a change of search technology.

== Description ==

Replace your Wordpress or WooCommerce search with Apache Solr and Elasticsearch.

Test WPSOLR features on <a href="https://www.wpsolr.com/?s=WOOCOMMERCE+OR+ACF+OR+BBPRESS+OR+WPML+OR+POLYLANG+OR+GROUPS+OR+s2MEMBER&post_type=&camp=3">our documentation search page</a>: live suggestions, facets with acf, geolocation.

You definitely need WPSOLR PRO search if you agree with one of:

- My current search page, my instant (live) product suggestions, are so slow that my visitors are leaving without buying anything, without subscribing to anything

- I have too many posts, products, visitors, comments, and I cannot afford hundred of dollars on external search hosted services

- Most of my data is stored in pdf files, word files, excel files. I need to search these formats too.

- My customers are international, they speak different languages. My search should be multilingual also.

- I want a modern search with tons of features. Ajax, facets, partial match search, fuzzy match search.

- I want to filter my woocommerce search results with any taxonomies, custom fields, attributes, or variations.

- I have several sites, unrelated, but I want to give my visitors a single search page combining all their content

- My bbPress search cannot handle thousands, hundreds of thousands of topics and replies.


If not, there are plenty of great search plugins out there to help you.

But, if you're really ready to unleash the beast, visit <a href='https://www.wpsolr.com?camp=2'>wpsolr.com</a>, ask us any question, or just download WPSOLR PRO search to give it a try.


== Installation ==

1. Upload the wpsolr-pro folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Activate the license for your website: https://www.wpsolr.com/knowledgebase/how-to-activate-a-license-pack/.

Installation procedure for Apache Solr: see FAQ section.

== Changelog ==

= 23.2 =
* Tested with PHP 8.1 and WordPress 6.2.2
* (new) Index also ‘private’ media files when admin option is activated
* (fix) Crons duplicated during creation
* (fix) Category facets on category archives

= 23.1 =
* WARNING: if you are using a facet with the hierarchy option, you will be required to reindex all your data.
* Tested with PHP 8.1 and WordPress 6.2.1
* (new) Integration of the new Weaviate’s Google PaLM module
* (new) Set horizontal/vertical orientation on views’ facets. For instance, choose horizontal facets on admin search and vertical on front-end search.
* (fix) Boost categories does not work
* (fix) Wrong archive results with duplicated category names
* (fix) Filters are wrongly showing results with partial matching
* (Fix) Fix some “utf-8-middle-byte” errors with mb_substr()
* (fix) Apache Solr exclusions with taxonomy hierarchies in facets

= 23.0 =
* (fix) Tested with PHP8.1
* (fix) Apply <a href="https://weaviate.io/developers/weaviate/configuration/schema-configuration#property-tokenization">property tokenization</a> to Weaviate indices, to prevent tokenization on facets.
* (fix) <a href="https://www.wpsolr.com/forums/topic/error-in-region-field/">OpenSolr credentials error</a>.

= 22.9 =
* (new) Weaviate’s Hybrid search
* (new) Weaviate’s Question Answering OpenAI module
* (new) Ability to manage suggestions from several search engines simultaneously. For instance suggest with semantic QnA on forums, while suggesting with Elasticsearch on the global search bar.
* (new) Replace certainty with distance on Weaviate search settings.

= 22.8 =
* (new) Integration of the new <a href="https://weaviate.io/developers/weaviate/current/retriever-vectorizer-modules/text2vec-cohere.html" target="_new">Weaviate's Cohere module</a>
* (new) Integration of the new <a href="https://weaviate.io/developers/weaviate/current/retriever-vectorizer-modules/text2vec-openai.html#available-models" target="_new">Weaviate OpenAI embeddings v2</a>

= 22.7 =
* (new) Search image and text content on WooCommerce shop with Weaviate CLIP multimodal module.

= 22.6 =
* (new) New Google Retail API search engine integration
* (new) Implement Weaviate’s Huggging Face module with Huggging Face Endpoints API
* (new) Generate and send a userToken to Algolia’s personalised search
* (new) SEO permalinks with select and select2 facets
* (bug) Fix Algolia with taxonomy containing “+”
* (bug) Fix “2.2 Data cannot be opened”

= 22.5 =
* Tested with PHP 8.0 and WP 6.1
* (fix) Minor issues with PHP 8.0
* (fix) Fix Algolia indexing with null date

= 22.4 =
* Tested with PHP 7.4
* (deprecation) The Aylien Text API is now decommissioned. WPSOLR hence removed the Aylien AI add-on.
* (new) Use OpenAI’s GPT-3 for search and question answering. To do so, Weaviate will use OpenAI’s embeddings API to compute your documents and queries embeddings (vectors).
* (new) Use HuggingFace’s inference API for search and question answering. To do so, Weaviate will use HuggingFace’s inference API to compute your documents and queries embeddings (vectors).
* (fix) ‘NOT’ operator not working anymore with Solr (Edismax) search query
* (fix) Prevent slider facets being reseted after sorting or other selecting other facets
* (fix) Call to undefined method wpsolr\core\classes\models\taxonomy\WPSOLR_Model_Meta_Type_Taxonomy::has_attachments() in /wpsolr-pro/wpsolr/core/dashboard/dashboard_settings.inc.php:662

= 22.3 =
* (New) “Views” to define sections of your site search that can be entirely configured with a specific search engine.
Search engines have their own strength, weaknesses and features.This is why using several search engines on your WordPress or WooCommerce for different purposes was a dream, at least until now…
For instance:
– Suggestions with BERT Hugging Face Q&A and SeMI Technologies #weaviate vector search
– WooCommerce shop and product search with #elasticsearch or OpenSearch Project
– blog with #solr
– bbPress forums with Algolia !
* (New) Integration of the Weaviate Vector search engine.
All text features are available:
– Any Weaviate module or vectorizer ( text2vec-contextionary, text2vec-transformers, text2vec-openai, qna-transformers)
– Suggestions with Questions & Answers (qna-transformers )
– Vector search with facets and sort (inverted index aggreagations, filters, and sort)
– Integrated with “Views” to be able to use Weavaite on some sections of your site search (for instance only on suggestions for Q &A)
- Images search (multi2vec-clip, and img2vec-neural)
* (New) Possibility to index, search and filter relations on any taxonomy or custom field for all post types. For instance, define a rating per category or tag for each product in WooCommerce.
* (New) New option “Show all results” in screen 2.1 to remove the infamous Elasticsearch 10,000 results limit.
* (New) Possibility to delete all WPSOLR settings on screen “4. Import / Export settings”
* (Fix) Fix Apache Solr 9.0 schema.xml and solrconfig.xml errors
* (Fix) Fix OpenSolr 9.0 schema.xml and solrconfig.xml errors
* (Fix) Fix Elasticsearch boost field values. They should only rescore (reorder), never filter, results
* (Fix) Fix Elasticsearch children category filters of current category archive
* (Fix) Fix Elasticsearch deprecated [order => _term] by replacing it with [order=> _key]
* (Deprecated) Removal of the temporary Elasticsearch index button until a better solution is found.
* (Fix) NOT operator not working anymore in search query
* (Fix) Skip unexplained term indexing error: Error on line 149 of file /wpsolr/core/classes/models/post/class-wpsolr-model-post.php: Trying to get property ‘name’ of non-object
* (Fix) Fix screen 2.2 to prevent saving unselected post type custom fields

= 22.2 =
* Tested with: WP 5.8.2, PHP 7.4.19, WooCommerce 5.9.0
* (New) Support of OpenSearch engine (local installation, Amazon OpenSearch Service, Aiven OpenSearch)
* (New) WP Rocket add-on to fix issue with deferred javascript loading with WP Rocket. See https://www.wpsolr.com/forums/topic/uncaught-referenceerror-wp_localize_script_autocomplete-is-not-defined/
* (New) Flatsome theme add-on supporting Flatsome infinite scroll
* (New) Add exact match with double quoted keywords (Apache Solr only). See https://www.wpsolr.com/forums/topic/exact-matches-vs-any-word-matches/
* (New) Use screen parameter "posts per page" on each admin post type search.
* (New) Extend test environment to Kinsta, WPEngine, and Cloudways internal domains.
* (New) Option to sort suggestion groups with user defined order
* (Update) Update the Solarium library to the latest version. See https://github.com/solariumphp/solarium
* (Fix) Trashed orders not indexed nor shown in admin. See https://www.wpsolr.com/forums/topic/wrong-count-of-orders-per-order-status/
* (Fix) WooCommerce showing only products on all archives when stock visibility is active. See https://www.wpsolr.com/forums/topic/only-products-show-in-search-results/
* (Fix) Add RTL styling on filters. Client's use case was Hebrew.
* (Fix) Restrict stock visibility filter to products only. See https://www.wpsolr.com/forums/topic/only-the-products-type-post-are-indexed-after-update-to-22-1
* (Fix) Remove highlighting on admin search results with Algolia.
* (Fix) Admin orders not shown when stock management is active. See https://www.wpsolr.com/forums/topic/hebrew-language-not-present/page/2/#post-27605
* (Fix) Fix SEO permalinks on special characters like ()
* (Fix) Fix Polylang not redirected to Ajax search form template page
* (Fix) Fix AI APIs image extraction from post html content

= 22.1 =
* Tested with: WP 5.8, PHP 7.4.19, WooCommerce 5.5.2
* (Fix) Special characters () in filters with Apache Solr: https://www.wpsolr.com/forums/topic/error-in-filter/
* (Fix) Error with WPSOLR facets widget and WP 5.8 block widgets: https://www.wpsolr.com/forums/topic/error-in-new-wordpress-admin-widget-page-with-wpsolr-facets
* (Fix) Date cast error by replacing *_i dynamic format with long data type: https://www.wpsolr.com/forums/topic/invalid-number-n-for-field-is_excluded_s/
* (Fix) Algolia date format filters: https://www.wpsolr.com/forums/topic/custom-field-filters/
* (Fix) Algolia Geolocation sort: https://trello.com/c/ulrYvQdE/160-fix-algolia-geosearch
* (Fix) bbPress stopping WPSOLR on standard search: https://trello.com/c/oCtNRRQp/153-fix-bbpress-stopping-wpsolr-on-standard-search
* (Fix) Algolia showing WooCommerce out of stock products: https://www.wpsolr.com/forums/topic/bug-search-results-do-not-account-for-hiding-out-of-stock-products/
* (Fix) WooCommerce single result page Ajax error: https://www.wpsolr.com/forums/topic/bug-when-selecting-facet-with-one-product/
* (Fix) No results on filters containing a dot caracter: https://www.wpsolr.com/forums/topic/filter-does-not-work-if-there-is-a-dot-in-the-attributes-size-price-brands-etc/
* (Fix) Missing images on cross-domain search global search: https://www.wpsolr.com/forums/topic/problem-images-source-on-elasticsearch/
* (New) Index and search WooCommerce products downloadable files: https://www.wpsolr.com/forums/topic/index-by-attached-woocommerce/
* (New) Enable 2 facets widget on Elementor for responsive mobile/desktop setup: https://www.wpsolr.com/forums/topic/mobile-tablet-responsive-not-working/
* (Fix) WPML strings not filled with WPSOLR localizations in recent WPML versions: https://trello.com/c/npiRNlmw/141-fix-wpml-strings-not-filled-with-wpsolr-localizations-in-recent-wpml-versions
* (Fix) Filtering after paginating: https://www.wpsolr.com/forums/topic/bug-results-do-not-filter-when-selecting-a-second-filter-on-a-paginated-page/
* (Fix) Back button error in admin on WPRocket (and others) by limiting js scripts where required only: https://www.wpsolr.com/forums/topic/bug-page-keeps-refreshing-on-random-plugin-admin-pages/
* (Fix) Upgrade message showing errors: https://trello.com/c/lJx7QvIv/129-fix-upgrade-message-showing-errors
* (Fix) Multisite function wrongly called on SEO permalinks: https://www.wpsolr.com/forums/topic/bug-recent-fix-breaks-site/
* (Fix) Duplicate "Relevancy" sort item on WooCommerce product search and default WPSOLR sort not applied: https://www.wpsolr.com/forums/topic/search-sort-options/
* (New) Exclude certain facet values: https://www.wpsolr.com/forums/topic/allow-to-exclude-certain-facet-value-from-taxonomy-attributes-categories-filters/
* (New) Option No index/No Follow on all WPSolr facet links and facet results pages: https://www.wpsolr.com/forums/topic/noindex-no-follow-all-wpsolr-urls/
* (Fix) Opensolr error while creating an index with empty key/secret: https://www.wpsolr.com/forums/topic/wpsolr-integration-fails/
* (New) Add WooCommerce on sale filter: https://www.wpsolr.com/forums/topic/i-failed-to-create-a-filter-for-products-on-sale-with-wpsolrwoocommerce/#post-23157
* (Fix) Override the 50 limit of facet items that can be seen and modified in screen 2.4 "Override each facet item": https://www.wpsolr.com/forums/topic/filter-overwrites-limits-cant-see-all-of-them/
* (Fix) Clicking back & forward from pagination with Ajax: https://www.wpsolr.com/forums/topic/bug-clicking-forward/
* (New) Add a jQuery selector for the Ajax overlay on the "Theme" add-on: https://www.wpsolr.com/forums/topic/replace-the-white-overlay-of-loader-with-animated-white-gradient/
* (New) Implement Yoast SEO add-on on multisites: https://trello.com/c/zG8A3qcj/121-implement-yoast-seo-add-on-on-multisites
* (Fix) Clicking back & forward from filters with Ajax: https://www.wpsolr.com/forums/topic/bug-clicking-back-from-filters/
* (Fix) Facets with exclamation mark with Algolia: https://www.wpsolr.com/forums/topic/bug-exclamation-mark-in-filter/
* (Fix) errors with apostrophe: https://www.wpsolr.com/forums/topic/apostrophe/
* (New) Search in media library: https://trello.com/c/cTcu2mtS/102-search-in-media-library
* (New) Add Algolia cross-domain search: https://trello.com/c/XzizOYWR/106-add-algolia-cross-domain-search
* (New) Add Algolia queries to debug.log: https://trello.com/c/RgRtS27F/105-add-algolia-queries-to-debuglog
* (New) Add Solr queries to debug.log: https://trello.com/c/bUinBLPI/107-add-solr-queries-to-debuglog
* (New) Add Query Monitor extension for WPSOLR: https://trello.com/c/6M96CZ7Q/62-add-query-monitor-extension-for-wpsolr
* (New) Fix UI bug on filters showing duplicate SEO permalink fields (range and not range): https://trello.com/c/CozkQC5L/111-fix-ui-bug-on-filters-showing-duplicate-seo-permalink-fields-range-and-not-range
* (Fix) Toolset not respecting some parameters: https://www.wpsolr.com/forums/topic/wp-solr-not-respecting-toolset-views-filters/

= 22.0 =
* Tested with: WP 5.5.1, PHP 7.4.10, WooCommerce 4.6.1
* Add language selector during index creation (Elasticsearch, Algolia)
* New Google Natural Language API add-on: Natural Language uses machine learning to reveal the structure and meaning of text. You can extract information about people, places, and events, and better understand social media sentiment and customer conversations. Natural Language enables you to analyze text and also integrate it with your document storage on Cloud Storage.
* New Amazon Comprehend API add-on: Amazon Comprehend uses natural language processing (NLP) to extract insights about the content of documents without the need of any special preprocessing. Amazon Comprehend processes any text files in UTF-8 format. It develops insights by recognizing the entities, key phrases, language, sentiments, and other common elements in a document.
* New MeaningCloud API add-on: Topics Extraction is MeaningCloud's solution for extracting the different elements present in sources of information. This detection process is carried out by combining a number of complex natural language processing techniques that allow to obtain morphological, syntactic and semantic analyses of a text and use them to identify different types of significant elements.
* New Aylien Text Analysis API add-on: Documents often contain mentions of entities such as people, places, products and organizations, which we collectively call Named Entities. Additionally they may also contain specific values or items such as links, telephone numbers, email addresses, currency amounts and percentages. To extract these entities and values from a piece of text, as well as the keywords, you can use the Entity Extraction endpoint.
* New Qwam Text Analytics API add-on: Please email your request to info@qwamci.com. A sales representative will contact you to evaluate your needs and provide you a pricing. You will be given an IP controlled endpoint to add to your configuration. A comprehensive study can be provided to consider special requests needing customization.
* Fix indexing error with PHP > 7.4.1.
* Fix WooCommerce 4.4.1 showing no products in results (shop and search)
* WooCommerce stocks: if no stocks, do not show products in results and variation attributes in facets.
* WooCommerce: fix indexing Similar Category Paths Moves Products To Other Categories
* MyListing: add recurring event dates filters.
* MyListing: fix search form dropdown filter slow query.
* MyListing: fix "Similar listings".
* MyListing speed improvements: 1 million listings site live: https://www.cartochrome.com/ (with Elasticsearch on https://www.cloudways.com/)
* MyListing 2.4.3 fix: (previous version tested with success was MyListing 2.3.1): change of lat/long internal parameter names caused all sorts of problems.
* MyListing 2.3.1 fixes: (previous version tested with success was MyListing 2.1.7): pagination, distance sort, date filter.
* Yoast: Replace sitemaps queries with Elasticsearch cursors
* Fix facet taxonomies not showing children taxonomies
* Fix category archive's facet category showing all the parents (should show only the selected category's children)
* Fix Yoast SEO extension showing permalinks on all archives. Only search permalinks are shown now
* Fix quotation mark in filters
* Enable custom complex filters
* Enable custom taxonomy archives

= 21.8 =
* Required! PHP 7.1
* New! Algolia is now supported. We’ve integrated Algolia search to WPSOLR. Create a new Algolia index with one click, and benefit from the integration with the biggest plugins and themes (WooCommerce, WPML, bbPress, ACF, Toolset ….).
* Fix “Did you mean” showing empty results with the theme’s template

= 21.7 =
* Required! PHP 7.1
* New! Toolset Views shortcodes now supported.
  Keep 100% Views features and flexibility (Ajax/Non Ajax, all post types, all filters, pagination, all sorts), but powered by Solr or Elasticsearch.


= 21.6 =
* Required! PHP 7.1
* New! Compatible with Elasticsearch 7.x
* New! Replace search in MyListing theme explore listings
* New! Replace search in admin for all post types and taxonomies
* New! (WooCommerce add-on) Replace search for all custom product taxonomies
* Show excerpts, or first sentences of content if excerpts are empty, when search snippets are empty. Select option 2.2 "Index excerpts" and reindex all your documents.
* Fixed indexing SQL too slow with a few thousand taxonomy terms (for suggestions)
* Fixed indexing taxonomies with WPML/Polylang (for suggestions)
* Fixed infiniscroll
* Fixed SQL error "missing index history table" (when no taxonomy is selected for indexing in 2.2)

= 21.5 =
* Replace all UI components with Twig templates
* Complete rewriting of suggestions. One can now define several suggestions, each with their own options and template.
* New suggestions grouped by result types (by products, by posts, by pages ...)
* Categories and tags can now be indexed, and appear in suggestions
* Add an extension for <a href="https://www.wpmapspro.com/" target="_blank">WP Google Map PRO</a>
* New option to hide facets with no choice (with none or one item)
* Add « Show more/Show less » link to facets
* Custom post types archives queries can now be replaced with Elasticsearch/Solr queries
* Fix facets sorted alphabetically always limited to 10 items
* Fix WPML not indexing new translations in real-time
* Show better error while indexing an atttachment with empty file name. See https://www.wpsolr.com/forums/topic/filename-cannot-be-empty
* Fix indexing error on malformed taxonomies. See https://www.wpsolr.com/forums/topic/wpsolr-error-on-term/
* Prevent error on pdfs that are scanned documents and contain no texts. See https://www.wpsolr.com/forums/topic/problem-to-index-pdfs-that-are-scanned-documents/
* Fix woocommerce errors on home page set as shop page

= 21.4 =
* New connectors to the following SolrCloud hosting service: <a href="https://www.searchstax.com/cloud-search-service/solr-manager/" target="_blank" rel="noopener">SearchStax</a>
* New connectors to the following Elasticsearch hosting service: <a href="https://qbox.io/" target="_blank" rel="noopener">Qbox</a>, <a href="https://aiven.io/elasticsearch" target="_blank" rel="noopener">Aiven</a>, <a href="https://www.objectrocket.com/managed-elasticsearch/" target="_blank" rel="noopener">ObjectRocket</a>

= 21.3 =
* PHP 7.0 is now required
* New connectors to the following Elasticsearch services: Elasticpress.io, Amazon AWS, Compose.io, Bonsai.io, Elastic.co, Cloudways
* Fix error: Document contains at least one immense term in field=xxx (whose UTF8 encoding is longer than the max length 32766), even with type text “_t” to store text length > 32K. Automated tests added to prevent this error coming back.
* Update Elastica (Elasticsearch php library) from 5.3.2 to 6.1.1 to fix AwsAuthV4 not sending header ‘Content-Type’
* Fixed (due to short tag): PHP Parse error: syntax error, unexpected end of file in layout/ion_range_slider/class-wpsolr-ui-layout-ion-range-slider.php on line 228
* Detect and prevent php files containing short tags in automated tests

= 21.2 =
* New Dates Range Slider filter with <a href="http://ionden.com/a/plugins/ion.rangeSlider/en.html" target="_blank">ion.rangeSlider javascript library</a>. Now, one can filter post types dates (creation date, modification date), and custom field dates, with the Range Slider. One can also apply local formats (English, German, French, Japanese, Chinese …) to display the oldest and newest dates on the slider.
* Tested with WP 5.0.3

= 21.1 =
* Tested with WP 5.0
* The plugin can now be downloaded and used, without any registration or activation, on non-production environments. See <a href="https://www.wpsolr.com/forums/topic/test-the-plugin-without-a-payment-method/">explanations and conditions</a>.

= 21.0 =
* New extension for Toolset Archive Views Query Builder.
  Keep 100% Archive Views features and flexibility (Ajax/Non Ajax, all archives types, all filters, pagination, all sorts), but powered by Solr or Elasticsearch. And a huge bonus: views work seamlessly with WPSOLR facets (filters) widget.
* Replace products thumbnail in search results by their variation image, if a variation attribute value matches a search filter.
  For instance, show all yellow shirts variation images instead of the default thumbnail when results are filtered by the yellow color.
* New option in sreen 2.1 to replace WP search with WPSOLR search on all archive types (search, home, year, day, month, author, category, tag)
* Add a secondary sort in screen 2.5
* Add new sorts in screen 2.5: Random, Post ID, Menu Order, Last Modified, Post Type, Author, Author ID, Title
* Add systematic php 7.2 validation with <a href="https://wordpress.org/plugins/php-compatibility-checker/">PHP Compatibility Checker By WP Engine</a>
* Update <a href="http://elastica.io/">Elastica</a> (Elasticsearch php library) from 5.3.0 to 5.3.2, to be php 7.2 compatible


= 20.9 =
* Fixed license activation issues with multisites. If you experience license or update troubles after this update, just reactivate your license.
* Indexing screen 2.2 rebuilt. Custom fields and taxonomies are now displayed within their own post type.
* Added a search exclusion list in screen 2.1
* New ACF option to index all the ACF files fields by default.
* Fixed cross-domain search not removing index documents.
* Fixed Geolocation sort by distance
* Refactored the code to prepare the next releases for indexing and searching beyond post types: users and custom tables. It will enable new plugin/theme extensions dealing with membership or BuddyPress.

= 20.8 =
* New extension for the theme <a href="https://themeforest.net/item/directory-portal-wordpress-theme/3840053" target="_blank" rel="noopener">DIRECTORY+</a>. Speed up items search, category filter, location filter, radius filter, with Solr and Elasticsearch.

= 20.7 =
* New extension for the theme <a href="https://themeforest.net/item/listable-a-friendly-directory-wordpress-theme/13398377" target="_blank" rel="noopener">Listable</a>. Speed up listing search with Solr and Elasticsearch.

= 20.6 =
* New extension for the theme <a href="https://themeforest.net/item/jobify-wordpress-job-board-theme/5247604">Jobify</a>. Speed up job search, category filter, job type filter, location filter, with Solr and Elasticsearch.
* Fix activation of sites with unconventional WordPress folders structure, like <a href="https://roots.io/bedrock/docs/folder-structure/">Bedrock</a>.

= 20.5 =
* Tested with WordPress 4.9.7, Solr 7.4.0, Elasticsearch 6.3.1
* Replace php short tag <? with <?php in 2 files

= 20.4 =
* Tested with Solr 7.3.1 and Elasticsearch 6.2.4
* (bbPress extension) Add facets on bbPress forum search urls.
* (bbPress extension) Manage private/hidden forums, and users roles.
* Fix Elasticsearch indexing error with empty post type titles. Detected first on bbPress empty titles replies.
* Fix empty select2 status and customers on WooCommerce orders edit page, when plugin "WooCommerce Attach me" is active
* Show a javascript console warning when an error occurs during a search

= 20.3 =
* Tested with Solr 7.2.1 and Elasticsearch 6.2.2
* Create your opensolr.com indexes from the plugin
* Theme extension: fix option « Search page pagination page links » not updating
* Theme extension: use only custom Ajax selectors, rather than use also default Ajax selectors
* Prevent random duplicate filters url parameters

= 20.2 =
* Fix no results in shop with WooCommerce 3.3
* Fix ACF facets labels localization

= 20.1 =
* Add a "copy to text field" option to enable text analysers on boosted search (screen 2.3). Requires re-indexing.
* Improve boost search integration with Toolset types. Support Toolset types post types, taxonomies, and custom fields
* Fix index creation with Elasticsearch 6.x: "[include_in_all] is not allowed for indices created on or after version 6.0.0 as [_all] is deprecated"
* Add field type text "_t" to store text length > 32K. Fix error: Document contains at least one immense term in field=xxx (whose UTF8 encoding is longer than the max length 32766)
* Fix index creation after upgrading from WPSOLR (free) : "Cannot use string offset as an array in /wp-content/plugins/wpsolr-pro/wpsolr/core/classes/extensions/indexes/admin_options.inc.php:69"
* Prevent javascript conflict error with Toolset views
* Remove javascript console warning "Found elements with non-unique id"

= 19.9 =
* Security fix
* New Range Slider filter with <a href="http://ionden.com/a/plugins/ion.rangeSlider/en.html">ion.rangeSlider javascript library</a>
* New Select box filter with <a href="https://select2.org/">Select2 javascript library</a>
* New Select box filter
* Remove SQL syntax error in debug.log when uploading attachment types not selected in screen 2.2
* Fix filters/facets not responding with IE11

= 19.8 =
* New ACF field types are supported: layout (tab, clone).
* Fix metabox "Do not search" checkbox not removing posts from the index.
* Prevent real-time indexing of post types not selected in screen 2.2
* New WP action to activate/deactivate real-time indexing. Much faster to import data, and use cron indexing, without the real-time. Ex: add_action(WPSOLR_Events::WPSOLR_ACTION_OPTION_SET_REALTIME_INDEXING, true ).

= 19.7 =
* WooCommerce extension: fix sorting results.

= 19.6 =
* New extension to manage external crons.
* Fix an indexing error due to a watermark format change in recent versions
* Remove Listify extension dependency to PHP 7
* Tested with Solr 7.0.0
* Tested with Elasticsearch 5.6.2

= 19.5 =
* New extension for the theme <a href="https://themeforest.net/item/listify-wordpress-directory-theme/9602611">Listify - WordPress Directory Theme</a>. Speed up listing search, category filter, label filter, geolocation radius filter, with WPSOLR.
* 123 tests, 7218 assertions (phpunit + Selenium2)

= 19.4 =
* New extension for the free plugin <a href="https://wordpress.org/plugins/yith-woocommerce-ajax-search/">YITH WooCommerce Ajax Search</a>. Use WPSOLR search to retrieve the products shown in the plugin search box suggestions.
* 119 tests, 6872 assertions (phpunit + Selenium2)

= 19.3 =
* Compatibility with WPML Multilingual CMS 3.7.1 and WPML String Translation 2.5.4 (You might need to upgrade WPML)
* Compatibility with Polylang 2.2.1 (You might need to upgrade Polylang)
* Fix facet hierarchies bad display when the option Theme extension's "Collapse facet hierarchies" is off
* 117 tests, 6780 assertions (phpunit + Selenium2)

= 19.2 =
* Add parameters "shards" and "replicas" to the Elasticsearch indexes form
* 113 tests, 5766 assertions (phpunit + Selenium2)

= 19.1 =
* Fully automated SolrCloud indexes creation (no need to access SolrCloud admin UI or server filesystem)
* Semi-automated Solr indexes creation (detailed instructions with server filesystem commands to add configuration files)

= 19.0 =
* (Elasticsearch attachments) Use of https://www.elastic.co/guide/en/elasticsearch/plugins/current/ingest-attachment.html, instead of deprecated https://www.elastic.co/guide/en/elasticsearch/plugins/current/mapper-attachments.html
* Fix non clickable facets containing single quotes
* Tests: 101, Assertions: 5211 (phpunit + Selenium2)

= 18.8 =
* Test Elasticsearch indexes. With a push on a button, create a hosted Elasticsearch index, ready to use with your search.

= 18.7 =
* Fix to work with Elasticsearch 5.5.1.
* Upgrade Elastica library from 5.2.1 to 5.3.0.
* Remove automatically file Null.php from Elastica library, which caused PHP 7 sniffers fail.

= 18.6 =
* Fix Solr indexing errors when some post data contains control characters.

= 18.5 =
* New "Yoast SEO" extension - Build beautiful search urls with meta descriptions
* New "All In One SEO Pack" extension - Build beautiful search urls with meta descriptions
* New Advanced scoring extension (Elasticsearch only) - Boost recent results while keeping relevancy
* New import/export settings
* Improve the creation of a test Solr index inside the plugin
* Add sort on multi-value fields (requires downloading the new Solr schema, then restart Solr or reload the index)
* s2Member extension is now compatible with Elasticsearch
* Fix warnings on upgrade version preview
* Verify 4000 checkpoints with automatic user acceptance tests (phpunit + Selenium2).

= 18.3 =
* (WP All Import) First release of the "WP All Import" extension. Fix posts not deleted from the search engine index while deleted by import.
* Fix the auto-update when called by wp-cli.

= 18.2 =
* (SEO extension) In preparation. Generate beautiful SEO urls and metas for the facets.
* (Premium extension) Fix the Ajax Infinite scroll always showing the first page.
* (Premium extension) Display attachments in the current theme results.

= 18.1 =
* (Theme extension) New option to use the current theme with Ajax (screen 2.1).
* (Theme extension) New facet layout 'Color picker'. You can now filter results clicking on colored icons.
* (Theme extension) New grid selection (horizontal, 1 column, 2 columns) on facet layouts.
* (WooCommerce extension) Fix a front-end search error when the option "Replace orders search by wpsolr" is selected.
* (Premium extension) Fix the screen 2.4 (facets) freeze when a post type contains many terms.
* (Premium extension) Fix the partial match option when the search contains several keywords. For Apache Solr and Elasticsearch.
* (WPML extension) Fix taxonomies being indexed in the admin current WPML language, rather than the index WPML language.
* Fix Elasticsearch error on post deletion
* Fix Elasticsearch boosts
* Get attachments in the current theme results.

= 18.0 =
* (Theme extension) New ranges layout. Show numeric filters, like prices, as ranges.
* (Premium extension) New option to apply 'OR' on facets with multi-selection
* (Elasticsearch) Fix empty results for multi-word keywords
* Upgrade https://github.com/solariumphp/solarium from 3.4.1 to 3.8.1, to fix exclusion for interval facets
* Fix tab showing the index settings collapsed, preventing the creation of the test Solr index.

= 17.9 =
* (Groups extension): prevent Solr error on groups with empty capabilities (capabilities removed from Groups but still there).
* Remove duplicated layout list on facets tab.
* Fix error while indexing post authors without display_name

= 17.7 =
* Fix tab showing the index settings collapsed, preventing the creation of the test Solr index.

= 17.6 =
* (Extension Toolset types) Add a checkbox to the wpsolr metabox. When a post contains a Toolset field of type "file", the file content is added to the post body (indexed and searched).

= 17.3 =
* First release of Elasticsearch:
 1. Install Elasticsearch
 2. Choose Elastic search for your index
 3. WPSOLR create your index, and setup mappings and analysers. No manual action required.
 4. Enjoy all wpsolr features: full-text search, sort, facets, autocomplete.
* Elasticsearch indexes work with all extensions, but "s2Member" and "Groups". Work in progress.
* Improve indexing debugging by catching and displaying fatal php errors.
* Fix "did you mean ?" for Apache Solr > 5.3

= 17.1 =
* Fix facets with html caracters (&, >, <, ...) returning 0 results.
* Fix silent error while indexing attachments > 500 KB (files too big where not indexed).

= 17.0 =
* Fix autocomplete.
* Fix facets not responding on clicks with the Ajax template.

= 16.9 =
* Fix wrong search engine while setting up an index (Elasticsearch is set instead of Apache Solr).

= 16.8 =
* Preparation for Elasticsearch in addition to Apache Solr.
* New layout choice on facets: checkboxes, or radio boxes.
* New exclusion choice on facets: display facets count as if no other selection was made.
* Requires PHP >= 5.4 (previously 5.3).

= 16.7 =
* Premium pack: new option to filter a facet content by default.
* Fix bug in admin screen while drag&dropping the sort items (front sort items where correctly displayed).

= 16.6 =
* Fix missing documents in index when many posts have the exact same published date (imports). Re-index everything if you are concerned.

= 16.4 =
* Fix php warnings in logs.

= 16.3 =
* Theme extension: new option to customize the facets css.

= 16.2 =
* WooCommerce extension : automatic filter on catalog visibility status. Needs a full re-index.
* Speed up loading of extensions.

= 16.1 =
* Premium extension: can now localize, and translate, all facet contents in tab 2.4 (facets). For instance, the WooCommerce stock status value 'instock' in the more readable 'In stock'.
* WooCommerce extension : prevent WooCommerce product attributes with type 'Text' split on commas during indexing.
* Front: Add compatibility with WooCommerce Customizer plugin's infinite scroll (auto loading). Tested live with 140K products.
* Admin: Add better visuals to indicate which extensions are active.

= 16.0 =
* WooCommerce extension: Replace shop search with WPSOLR search.

= 15.9 =
* (New) Theme extension: Facet hierarchies collapsing option.
* Premium extension: Sort facets alphabetically option (instead of default sort count).

= 15.8 =
* Fix error with special (Solr) characters in keywords.

= 15.7 =
* Fix the Ajax InfiniteScroll for Firefox.

= 15.6 =
* Add an option to replace the Infinite Scroll javascript.
<a href="https://www.wpsolr.com/knowledgebase/infinite-scroll-search-navigation/">Documentation</a>


= 15.5 =
* Add a new filter to replace the default facets HTML with your own. Works with the Ajax shortcode, and the facets widget.
<a href="https://www.wpsolr.com/guide/actions-and-filters/search-results-replace-facets-html/">Documentation</a>

= 15.4 =
* Add two css classes to the facets html (header and list), so each facet can be styled individually.

= 15.3 =
* (WooCommerce extension) Fix the product categories search to support multilingual subcategories slugs.

= 15.2 =
* Security update.

= 15.1 =
* Fix HTML of the Ajax search form, which could cause side effects to the theme's rendering.
* Fix PHP warning on admin menu "Plugins".

= 15.0 =
* First release of WPSOLR PRO:
- includes an external license manager for paid extensions
- includes it's own automatic update manager


== Frequently Asked Questions ==

= Is there a trial for the extensions ? =

Yes, we added a 7 days trial for all packs (Premium, bbPress, Woocommerce, WPML, Polylang, S2member, Groups, Types, ACF). Download WPSOLR PRO trial, then follow https://www.wpsolr.com/knowledgebase/how-to-activate-a-license-pack/.

= What is the installation procedure for Solr on Windows ? =

!!! Important: always reload the index in your Solr admin UI after each install/change of file schema.xml

A tutorial at WPSOLR: [Solr 4.x](http://wpsolr.com/installation-guide/ "Apache Solr installation, Solr 4.x")

A tutorial at Wordpress support: [Windows, Solr 5.x/6.x](https://wordpress.org/support/topic/great-software-but-needs-some-documentation "Apache Solr installation, Windows, Solr 5.x/6.x")

= What is the installation procedure for Solr on linux ? =

!!! Important: always reload the index in your Solr admin UI after each install/change of file schema.xml

A tutorial at Wordpress support: [Linux, Solr 4.x](https://wordpress.org/support/topic/no-support-for-self-hosted-solr-and-not-working-for-self-hosted "Apache Solr installation, Linux, Solr 4.x")

A tutorial at Linode: [Linux, Solr 4.x](https://www.linode.com/docs/websites/cms/turbocharge-wordpress-search-with-solr "Apache Solr installation, Linux, Solr 4.x")

For Linux, Solr 6.1.0 (tested). Replace 6.1.0 with your current Solr version.
`
wget http://archive.apache.org/dist/lucene/solr/6.1.0/solr-6.1.0.tgz
tar xvf solr-6.1.0.tgz
solr-6.1.0/bin/solr start
solr-6.1.0/bin/solr create -c wpsolr-6.1.0
(download solr 5.xx config files from https://www.wpsolr.com/kb/apache-solr/apache-solr-configuration-files)
cp solrconfig.xml schema.xml solr-6.1.0/server/solr/wpsolr-6.1.0/conf/
(reload index with solr admin UI)
(configure a new index in wpsolr admin UI:
Index name: wpsolr - local 6.1.0
Solr Protocol: http
Solr host: localhost
Solr port: 8983
Solr path: /solr/wpsolr-6.1.0
)
(index posts on wpsolr admin UI, including a pdf file)
(search in posts, retrieve the pdf)
`

= What WPSOLR PRO can do to help my search ? =
Relevanssi, Better Search, Search Everything, are really great because they do not need other external softwares or services to work.

WPSOLR, on the other hand, requires Apache Solr, the worlds's most popular search engine on the planet, to index and search your data.

If you can manage to install Solr (or to buy a hosting Solr service), WPSOLR can really help you to:

* Search in many sites for aggregated searches

* Search in thousands or millions of posts/products

* Search in attached files (pdf, word, excel....)

* Filter results with dynamic facets

* Tweak your search in many many ways with Solr solrconfig.cml and schema.xml files (language analysers, stopwords, synonyms, stemmers ...)

= Do you offer a premium version ? =
Yes. Check out our <a href="https://wpsolr.com/pricing">Premium Packs</a>.

= Can you search in several sites and show results on one site ? =
Yes, there is a multisites extension in WPSOLR PRO.

You configure the sites belonging to the network search as "local", and one or several "global" sites to show results from "local" sites consolidated, while "Local" sites continue to search their own data.

As Solr manages the whole network search, there is almost no limits to the number of "local" sites, and number of posts indexed.
Contact us for more information on this multisites feature.

= Can you manage millions of products/attributes/variations ? =
Yes (Premium for attributes/variations). WPSOLR PRO is based on the mighty Apache Solr search engine. It can easily manage millions of posts, and fast.

= Why the search page does not show up ? =
You have to select the admin option "Replace standard WP search", and verify that your urls permalinks are activated.

= Which PHP version is required ? =

WPSOLR uses a Solr client library, Solarium, which requires namespaces.

Namespaces are supported by PHP >= 5.3.0

= Which Apache Solr version is supported ? =

Solr 4.x, Solr 5.x, Solr 6.x

WPSOLR PRO was tested till Solr 6.1.0

= Can I have my Apache Solr server hosted ? =

Yes, on <a href='http://gotosolr.com/en/'>Gotosolr Solr hosting</a>.

[Gotosolr Solr hosting tutorial](http://www.gotosolr.com/en/solr-tutorial-for-wordpress/ "Gotosolr Solr hosting tutorial")

[sitepoint tutorial on Gotosolr Solr hosting with WPSOLR](https://www.sitepoint.com/enterprise-search-with-apache-solr-and-wordpress/ "sitepoint tutorial on Gotosolr Solr hosting with WPSOLR")

= How do I install and configure my own Apache Solr server ? =

Please refer to our detailed <a href='http://wpsolr.com/installation-guide/'>Installation Guide</a>.


= What version of Solr does the WPSOLR PRO plugin need? =

WPSOLR PRO plugin is <a href="https://www.wpsolr.com/kb/apache-solr/apache-solr-configuration-files"> compatible with the following Solr versions</a>. But if you were going with a new installation, we would recommend installing Solr version 3.6.x or above.


= Does WPSOLR PRO Plugin work with any version of WordPress? =

As of now, the WPSOLR PRO Plugin works with WordPress version 3.8 or above.


= Can custom post type, custom taxonomies and custom fields be added filtered search? =

Yes (Premium feature). The WPSOLR PRO plugin provides option in dashboard, to select custom post types, custom taxonomies and custom fields, to be added in filtered search.


= Do you offer support? =

You can raise a support question for our plugin from wordpress.org.
Premium users can use our zendesk support.
