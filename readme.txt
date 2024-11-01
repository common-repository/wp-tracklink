=== Plugin Name ===
Contributors: Jules81 
Donate link: https://ispire.me/donate/
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: backlink, referrer, seo, SEO, robots, widget, wp-tracklink, tracklink, pagerank, backlink-network
Requires at least: 3.3
Tested up to: 4.9
Stable tag: 0.4.9

Tracks referrer links, clicks, pagerank and displays backlinks on widget sidebar. Increase your page rank due backlinking.

== Description ==

* Tracks referrer links plus how often they have clicked and displays them on your Wordpress widget sidebar. This will increase your page ranking due to backlink and faster crawler indexing. Best pageranked and most clicked links gets an higher position in sidebar. Disabling specific links is possible via the settings tab. Visit official site at https://ispire.me/wp-tracklink for more infos.


== Installation ==

* Install wp-tracking either via Wordpress.org plugin directory, or by manually uploading to your server
* Activate the plugin through the 'Plugins' menu in WordPress
* Change your settings in the admin Settings tab
* Add WP-Tracklink to your widget tab

== Frequently Asked Questions ==

= Will the database and settings drop on uninstall? =

* Yes they will! If you want to keep the tracked data just upgrade or manually delete the plugin from the plugins folder.

== Screenshots ==

None yet.

== Changelog ==

= 0.4.9 =

* updated: search engine pattern #3

= 0.4.8 =

* updated: search engine pattern #2

= 0.4.7 =

* updated: search engine pattern

= 0.4.6 =

* updated: moved ob_start/flush 

= 0.4.5 =

* fixed: admin tab enable/disable refresh


= 0.4.4 =

* fixed: bypass self refer counting

= 0.4.3 =

* updated: mysql backlink lookup optimized

= 0.4.2 =

* fixed: Widget pagerank link order, best pr is first

= 0.4.1 =

* fixed: checkpr logic

= 0.4.0 =

* fixed: some more mysql escapes

= 0.3.9 =

* updated: code cleanup

= 0.3.8 =

* fixed: pagerank check 

= 0.3.7 =

* fixed: db table exists check

= 0.3.6 =

* fixed: deprecated db escapes

= 0.3.5 =

* fixed: db create logic

= 0.3.4 =

* fixed: php7 compability
* fixed: mysq initial db create

= 0.3.3 =

* removed: google API pagerank check († 12.07.2016)
* added: Alexa Api

= 0.3.2 =
* fixed: Cannot use object of type WP_Error as array in.

= 0.3.1 =
* added: some more search engine excludes.

= 0.3.0 =
* fixed: some more preg_match fixes.

= 0.2.9 =
* fixed: wrong preg_match entry.

= 0.2.8 =
* added: check if referer link really exists and contains content linking to your blog. else bypass adding this referer.

= 0.2.7 =
* fixed: check if return value is numeric (greater zero) on pagerank. means also if pagerank is 0 do not add this referer.

= 0.2.6 =
* fixed: if isset added.

= 0.2.5 =
* updated: pagerank re-check every 32 days

= 0.2.4 =
* updated: add only source links that returns a pagerank value

= 0.2.3 =
* fixed: widget sql error

= 0.2.2 =
* fixed: empty pagerank value

= 0.2.1 =
* added: pagerank check feature
* fixed: database upgrade to v1.1
* updated: DB duplicated title content. If you encountered errors on duplicate content, you have to delete your duplicated wp-tracklink title rows or delete plugin and reinstall to drop your old database scheme

= 0.2.0 =
* fixed: cannot modify header 

= 0.1.9 =
* added DB cleanup logic: If one tracked link have not hit TOP x of max showed links, erase it out of db after last click date has reached > 30 days.

= 0.1.8 =
* added: output buffer
* added: table tabs for a cleaner view

= 0.1.7 =
* added: more ignore expressions

= 0.1.6 =
* updated: readme

= 0.1.5 =
* added: widget title modify function
* added: seperate show max count change on widget and admin page

= 0.1.4 =
* added: backward compatible uninstall script

= 0.1.3 =
* fixed: insert example data

= 0.1.2 =
* fixed: table delete on uninstall
* fixed: table datetime

= 0.1.1 =
* fixed: cannot modify header

= 0.1.0 =
* Initial release

== Upgrade Notice ==

* None yet.

== Links ==

Useful wp-tracklink Plugin links

* wp-tracklink Plugin Page: https://ispire.me/wp-tracklink/
