=== AskApache RewriteRules Viewer ===
Contributors: AskApache, cduke250
Donate link: http://www.askapache.com/donate/
Tags: htaccess, mod_rewrite, askapache, rewriterules, permalinks, links, perma, rewrite, structure, permalink, function, category, search, property, endpoint, request, author, private, attachment, permastruct, replace, redirect, matches, generate, struct, comment, default, permalinks, comments, endpoints, queries, pagename, feedregex, tagname, attachments, trackback, verbose, rewritecode, postname, robots, queryreplace, permastructs, htaccess, feedquery, categories, subfeedquery, rewriterule, rewritereplace, feedname, trackbackregex, rewritecond, regexes, redirects, authors, withcomments, feedindex, trackbacks, trackbackquery, trackbackmatch, trackbackindex, taxonomy, postid, pageregex, pagequery, pagematch
Requires at least: 2.5
Tested up to: 3.0-beta1
Stable tag: 3.2

Displays Most Everything about your WordPress Rewrites.

== Description ==

Displays Most Everything about your WordPress Rewrites, Permalinks, URI's, in a very detailed and raw way.  Informational plugin only... Nothing is modified or changed.




Most Every Bit of Internal Link Rewriting

 * year
 * monthnum
 * day
 * hour
 * minute
 * second
 * postname
 * post_id
 * category
 * tag
 * author
 * pagename
 * search


See the Internal WordPress Rewrites

 index.php?&feed=$matches[1]&withcomments=1
 index.php?&paged=$matches[1]
 index.php?s=$matches[1]&feed=$matches[2]
 index.php?tag=$matches[1]&feed=$matches[2]
 index.php?tag=$matches[1]
 index.php?author_name=$matches[1]&feed=$matches[2]
 index.php?author_name=$matches[1]&paged=$matches[2]


See All the Permalink Info

 * date permastruct (permalink)	
 * year permastruct (permalink)	
 * month permastruct (permalink)	
 * day permastruct (permalink)	
 * category permastruct (permalink)	
 * tag permastruct (permalink)	
 * author permastruct (permalink)	
 * search permastruct (permalink)	
 * page permastruct (permalink)	
 * feed permastruct (permalink)	
 * comment_feed permastruct (permalink)	
 

View the .htaccess Mod_Rewrite Rules

 <IfModule mod_rewrite.c>
 RewriteEngine On
 RewriteBase /
 RewriteCond %{REQUEST_FILENAME} !-f
 RewriteCond %{REQUEST_FILENAME} !-d
 RewriteRule . /index.php [L]
 </IfModule>




== Installation ==

This section describes how to install the plugin and get it working.

1. Extract zip to wp-content/plugins
2. Activate the Plugin
3. Setup plugin options


== Frequently Asked Questions ==

Where can I learn more about Permalinks

http://codex.wordpress.org/Using_Permalinks


== Other Notes ==

If you have a question about .htaccess, see: [.htaccess Tutorial](http://www.askapache.com/htaccess/htaccess.html "AskApache .htaccess File Tutorial")


== Screenshots ==

1. Your Permalink, Rewriting, and Misc. Settings
2. The RewriteRules and Mod_Rewrite .htaccess
3. Permalinks and Permastruct Info
4. Feed Links, Query Replacements ...
5. Page Rewrite URI's
6. Version 3.2 - Lots more Rewrites
