NOTES ON MOBILE IPHONE READER APP INTERGRATION

PLEASE READ

the PHP files in this /rpc folder originate from Spark Design (formerly web3mavens). they are used as a proxy between the iphone reader app and the feed built in the dmc-google-news-feed plugin. 

As of 6/17/2011, these files are no longer used. The methods that return json to the app are now inside the dmc-google-news-feed, accessible in this manner..

[site_url]/subscriber_feed/?json

[site_url]/subscriber_feed_pages/?json

*Without 'json' in url the url will return the XML feed with full content.

6/16/2011 - To Dos
* Validate token passed in URL
