=== Stop Spammers ===
Tags: spam, comment, registration, login, spammers,MU, StopForumSpam, Honeypot, BotScout,DNSBL, Spamhaus.org, Ubiquity Servers, HTTP_ACCEPT, disposable email
Donate link: http://www.blogseye.com/donate/
Requires at least: 3.0
Tested up to: 3.5
Contributors: Keith Graham
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Stop Spammers Plugin checks comments and logins 15 different ways to block spammers.

== Description ==
Stop Spammers uses more than 15 different ways to detect spammers. It checks Logins, Registrations, and comments for spam users and blocks them when it finds them.
Stop Spammers Eliminates 99% of spam registrations and comments. Checks all attempts to leave spam against StopForumSpam.com, Project Honeypot, BotScout, DNSBL lists such as Spamhaus.org, known spammer hosts such as Ubiquity Servers, disposable email addresses, very long email address and names, and HTTP_ACCEPT header. Checks for robots that hit your site too fast, and puts a fake comment and login screen where only spammers will find them. 

The Stop Spammers Plugin now checks for spammer IPs much earlier in the comment and registration process. When it detects a spammer IP, the plugin stops WordPress from completing any further operations and an access denied message is presented to the spammer. You control the access denied message, or you can redirect the spammer to another page or website.

How the plugin works: 

This plugin checks against StopForumSpam.com, Project Honeypot and BotScout to to prevent spammers from registering or making comments. The Stop Spammers plugin works by checking the IP address, email and user id of anyone who tries to register, login, or leave a comment. This effectively blocks spammers who try to register on blogs or leave spam. It checks a users credentials against up to three databases: Stop Forum Spam, Project Honeypot, and BotScout. Optionally checks against Akismet for Logins and Registrations. 

Optionally the plugin will also check for disposable email addresses, check for the lack of a HTTP_ACCEPT header, and check against several DNSBL lists such as Spamhaus.org. It also checks against spammer hosts like Ubiquity-Nobis, XSServer, Balticom, Everhost, FDC, Exetel, Virpus and other servers, which are a major source of Spam Comments. 

Rejects very long email addresses and very long author names since spammers can't resist putting there message everywhere. It also rejects form POST data where there is no HTTP_REFERER header, because spammers often forget to include the referring site information in their software.

The plugin will install a "Red Herring" comment form that will be invisible to normal users. Spammers will find this form and try to do their dirty deed using it. This results in the IP address being added to the deny list. This feature is turned off by default because the form might screw up your theme. Turn the option on and check your theme. If the form (a one pixel box) changes your theme presentation then turn the feature off. I highly recommend that you try this option. It stops a ton of spam. 

The plugin can check how long it takes a spammer to read the comment submit form and then post the comment. If this takes less than 5 seconds, then the commenter is a spammer. A human cannot fill out email, comment, and then submit the comment in less than 5 seconds.

Limitations: 

StopForumSpam.com limits checks to 10,000 per day for each IP so the plugin may stop validating on very busy sites. I have not seen this happen, yet. The plugin will not stop spam that has not been reported to the various databases. You will always get some comments from spammers who are not yet reported. You can help others and yourself by reporting spam. If you do not report spam, the spammer will keep hitting you. This plugin works best with Akismet. Akismet works well, but clutters the database with spam comments that need to be deleted regularly, and Akismet does not work with spammer registrations. Since Akismet does not check registrations and logins, the plugin will use the Akismet database to check these events, too. 

API Keys: 

API Keys are NOT required for the plugin to work. Stop Forum Spam does not require a key so this plugin will work immediately without a key. The API key for Stop Forum Spam is only used for reporting spam. In order to use the Project HoneyPot or BotScout spam databases you will need to register at those sites and get a free API key. 

History: 

The Stop Spammers plugin keeps a count of the spammers that it has blocked and displays this on the WordPress dashboard. It also displays the last hits on email or IP and it also shows a history of the times it has made a check, showing rejections, passing emails and errors. When there is data to display there will also be a button to clear out the data. You can control the size of the list and clear the history.
If a user tries to log in and passes all checks for spammers an icon appears next to the IP address. Only users you know should be allowed to login, so by clicking the icon, you can add the IP to your black list. 

Cache: 

The Stop Spammers plugin keeps track of a number of spammer emails and IP addresses in a cache to avoid pinging databases more often than necessary. The results are saved and displayed. You can control the length of the cache list and clear it at any time. The plugin caches IP addresses that do not fail, assuming that they may be valid users. In order to prevent re-checking these IP addresses, the plugin stores the last two IP addresses that passed all tests.

Reporting Spam: 

On the comments moderation page, the plugin adds extra options to check comments against the various databases and to report to the Stop Forum Spam database. You will need a Stop Forum Spam API key in order to report spam/ 

Network MU Installation Option: 

If you are running a networked WPMU system of blogs, you control this plugin from the network admin dashboard. By checking the "Networked ON" radio button, the individual blogs will not see the options page. The API keys will only have to entered in one place and the history will only appear in one place, making the plugin easier to use for administrating many blogs. The comments, however, still must be maintained from each blog. The Network radio button only appear if you have a Networked installation.

Requirements: 

The plugin uses the WP_Http class to query the spam databases. Normally, if WordPress is working, then this class can access the databases. If, however, the system administrator has turned off the ability to open a URL, then the plugin will not work. Sometimes placing a php.ini file in the blog’s root directory with the line "allow_url_fopen=On" will solve this. 
There is a button that allows you check access to the StopForumSpam database from the plugin Options page. This will tell you if the host allows opening of remote URL addresses.


 
== Installation ==
1. Download the plugin.
2. Upload the plugin to your wp-content/plugins directory.
3. Activate the plugin.
4. Under the settings, add the appropriate API keys (optional). Update the white list. Set any of the optional items and limits.

== Changelog ==

= 1.0 =
* initial release 

= 1.2 =
 * renumber releases due to typo
 
= 1.3 =
 * Check the IP address whenever email is checked.
 
= 1.4 =
 * Checks the user name. Cache failed attempts with option to clear cache. Cleans up after itself when uninstalled. 

= 1.5 =
* fixed a bug where the the admin user was cached in error.

= 1.6 =
* Improved caching to help stop false rejections.
 
= 1.7 =
* Included signup form, that I forgot to add before. Cached data is automatically expired after 24 hours.
 
= 1.8 =
* fixed the cache cleanup (again). Changed the name in the titles and menus of the plugin to reflect that it does more than stop registrations.

= 1.9 =
* Added link to report spam to StopForumSpam.com database.

= 1.10 =
* Improved the access to StopForumSpam.com database. Fixed white space at end of plugin.
 
= 1.11 =
* Stored the StopForumSpam API Key. Fixed a possible security hole on the settings page.
 
= 1.12 =
* Fixed typo error.
 
= 1.13 =
* Changed Evidence field to spam URL or content

= 1.14 =
* Changes suggested by Paul at StopForumSpam. Fix bug in zero history data. There has been much interest in the plugin so there has been lots of feedback. I am sorry for all the updates, but they are all good stuff.

= 1.15 =
* Options added. 1) Reject if Accept header not found. Spammers use some kind of lazy approach that does not send the HTTP_ACCEPT header. All real browsers have this header. 2) Check on BL Blacklist. If for some reason the IP and email pass on the StopForumSpam db you can have a second check on Project Honeypot. 3) Added a white list in case there are IPs or emails that have problems. 4) Stopped checking for Usernames because of too many false positives. 4) Made checking for emails optional. Most spammers use bogus or random emails anyway. 5) Ability to recheck comments against the HoneyPot db from the comments admin form.

= 1.16 =
* Added RoboScout.com spam check to IP address. Added limits to checking to allow know spammers who are not recent spammers or do not have many spam reported. Added a complete list of passed and rejected login attempts. Fixed a bug introduced in 1.15. Fixed check on accept headers that prevented it from working.

= 1.17 =
* Fixed another bad bug. Added a warning if the host does not allow URL fopens. Reduced memory requirements. Cache less information.
This has some functions partially complete, but I had to release as is to fix the bugs that appear on new install. It's my own fault, because last time I did not test from a clean WP install.

= 2.0 =
* Made the plugin WPMU aware. Streamlined some of the code. Limited the cached spam sizes to reduce memory overhead. Changed the way that the plugin decides when to check an IP and email. This will help it when working with other plugins. It also checks in multiple places in case the is_email() function is not called. It allows admins to change the minimum requirements for spam, forgiving spammers who have few incidents or have not spammed for a period of time.

= 2.10 =
* Fixed the way the cache is sorted. Added DNSBL support for spamhaus, dsbl, sorbs, spamcop, ordb, and njabl. These are email spam databases and they get only a small portion of the comment spam, but some is better than none. Added a list of common disposable email sites so that users who use disposable sites can be blocked. The list is only popular sites and is not exhaustive. Real commentators probably won't use the disposable sites, but some bloggers may be nervous about blocking them, so it is optional. Divided the options into a stats and a parameters wp_option array. Something in spam, probably a foreign language character, has been breaking the options causing the blog to "forget" when the stored array is broken. Now, when the stats array breaks, the configuration items will still be available. Rewrote the MU options, although it is not tested on subdomain installations.

= 2.20 =
* Fixed several networked blog issues. Added a dummy email address so that pingbacks can be reported. Added Multisite Maintenance. Fixed a few minor bugs. Testing use of X-Forwarded-For HTTP IP address when the blog is behind a proxy. I cannot test this because I don't have access to a site behind a proxy. Please report if the X-forwarded-for header handling is broken.

= 3.0 =
* Restructured the Plugin completely, changing many of the ways it works. Changed the points and places where spam is checked. Spam is now being checked for much earlier. Added an Access denied screen. Optionally block Ubiquity Servers. Use AJAX to report Spam so that there is no need to open a new window.

= 3.1 =
* Changed access to SFS db to stop false positives

= 3.2 =
* Added automatic addition of admins to IP white list. Added ability to specify where plugin actions work. Added WP API key update for those who don't use Akismet. Added checks for long names and emails. Added HTTP_REFERER checks. Added a check so users can see if they have access to the StopForumSpam database. Added a long list of known Spam Hosting company IP addresses. 

= 3.3 =
* Changed way arrays are searched. It was possible that IP addresses were not found in lists. Added a "Red Herring" bogus comments form that stops a huge amount of spam. Repaired delete option.

= 3.4 =
* Fixed an issue with Red Herring inserting invalid data into feeds. Added a list of spam robot user agents. Added a timeout to the comment submission forms to ban spammers who take less than 5 seconds to fill out and submit a form. Changed the way the plugin loads, speeding up WordPress. Most functions do not load unless the plugin is processing a form. There is no need to check for spammers unless they are actually in the process of leaving a comment or logging in. Mail and XMLRPC checks load all the time. Akismet may get the spammer before this plugin does resulting in more spam in the Akismet spam queue, but it doesn't matter as long as the spammer is stopped. Added an optional JavaScript trap to the comment form. Users who do not have JavaScript enabled will be marked as spammers. Disable this if you have a blog for paranoids.

= 3.5 =
* Fixed typo. Although I tested for a week in 5 different sites, this bug didn't come up.

= 3.6 =
* Fixed issue with some web servers that did not set server variables such as SCRIPT_URI and REQUEST_URI. These were troubling to those with hosting software that ignored these variables. Fixed an issue on saving of parameters. Added a hook to 404 errors so that missed hits on wp-login can be considered malicious. Removed default doubleclick link that was causing problems.

= 3.7 =
* fixed several bugs in Options page. Reformatted Options page to make it easier to view.

= 3.8 =
* fixed options page bug with the Check SFS checkbox. 
* Fixed blacklist options issue. 
* White listed PayPal IPs to stop interference with PayPal callbacks (not optional).
* added ability to reject by TLD in email (users can stop .ru or .cn if they want).
* made options and history options non-autoload to preserve memory usage.
* changed the way the network checkbox works. Users must be able to manage the network to set the feature and see the network options when set. When the network box is checked the only way to admin the network is through the network admin dashboard.
* compensates for a bug in Apple Safari that does not sent HTTP_REFERER from the iPhone and iPad. Disables the HTTP_REFERER check if the user agent appears to be from an iphone or ipad.
* corrected link to options from admin panel (again). I hope I have it right at last.

= 4.0 =
* Removed functions that caused issues with Buddy Press
* Reorganized and simplified the plugin. It is a more streamlined now. It checks for spam only on form submission (POST) as soon as WP is initialized. It no longer does any checks in the register and login functions so it should be more compatible with other plugins. It only does checks when a form is submitted so it should have less impact on WordPress resources.
* Removed email validation hooks. 
* Added a spam event type summary to the history page.
* changed the order of spam checks. Cache check first, then most likely or simple checks, database access last.
* fixed a bug in cache checking.
* added an activation check to see if the current user is reported as a spammer. Plugin will not install unless the user passes all spam tests. 
* added a button to the options screen to test if current user appears to be a spammer.
* fixed bug in log file cleanup.
* fixed autoload options issue. Change to autoload=false only happens once.
* added ability to add reason and IP to deny message.
* Removed the "loop_start" hook and replaced it with a before comment form hook. This will mean that some themes will not use the red herring forms if they do not comply with WP standards.

= 4.1 =
* Made changes to help with bbPress. Use the bbPress fix spam plugin to force this plugin to load before bbPress.
* Fixed bug in the 404 processor.
* Added separate sizes for the email and ip caches.
* Added option for sleep time. That is the time plugin waits after denying a spammer. Default 10 seconds.
* Added option for session timing seconds - default 4.
* Added option for the Good ip cache size - default 2.
* Checking for HTTP-X-FORWARDED-FOR in all cases. Aggressively looks for forwarding headers to resolve real IP.
* Checks for any POST field with EMAIL, USER or LOGIN in field name. This accommodates plugins that use non-standard comment and login/register field names.
* Show password used in spammer login attempts - helps identify dictionary attacks.
* Does not log attacks by DUKANG2004. This idiot was filling up my logs with failed attempts. If total spam appears larger then logs would indicate then blame him. This must be a default value in some badly written root kit.
* Option to disable IP checking - this cripples the plugin, but allows it to continue checking for many types of spam. Not recommended.
* added option check credentials on logins before the plugin does its check. This opens Wordpress to dictionary attacks so it should be unchecked as soon as possible.
* added ability to remove individual IP or email addresses from the cache.
* added warning to options page if user name is admin.
* removed main hook to prevent recursion after executing once.
* added routine to log passes. Commented in production. Use to check why some spam still gets through.
* Added checks for accept headers LANGUAGE and ENCODING to monitor if these are good for checking spam.
* Fixed bugs in the stats summary and summary clear.
* Plugin writes to a permanent log file all actions (such as update) and denied spammers. Size can be set in options and viewed and cleared on the history page.
* Fixed Ubiquity Server check.
* Removed activation hook to check IP. Now, after activation, it checks to see if your IP address is valid the first time the options page is visited. The plugin reloads the IP address into the white list on every activation.
* Fixed bad bugs in Project Honeypot and DNSBL lookup. Removed slow DNSBL databases.
* Created a test page where users can check to see if an IP address results in spam detection. I does a few of the database lookups and checks headers. URL: http://www.blogseye.com/checkspam/
* Every upgrade or re-activation forces IP into the white list.

= 4.2 =
* fixed bug in network version deleting log file. 
* Changed the default actions log file behavior to not write a log. Must be selected.
* Moved location of actions log to plugin home directory.
* Removed checks on wp-cron.php. There is too much chance of a user being blacklisted by accident.
* Added the ability to use Akismet to check non-login or non-registration events. Akismet is better for comments because it marks comments as spam. This option allows you to prevent comments by blocking them with Stop Spammers, but you lose the spam queue (unless the akismet plugin fires first).
* Added option to disable checks on plugin forms. This looks for the plugins folder in the URL. This could potentially prevent blocking of eCommerce functions that use a custom php file to process orders. It may help those with ecommerce solutions, but most plugins work by hooking init or other events rather than posting directly to a custom PHP file.
* changed names of written files to have a "dot" in the beginning to hide them and prevent apache from serving them. Also added a chmod to the log file to add an extra layer of security since the file contains login ID information.
* Fixed issue with options not saving.
* Added an option to disable the plugin when executing forms in the wp_content/plugins folder to prevent interfering with some ecommerce systems.
* Changed formatting of Red Herring forms to try to prevent threading failure on bbpress and some themes. This may reduce the effectiveness of red herring forms, but will prevent threading from breaking.
* fixed issue with non-admins logging in. No longer throws an error.
* Removed "buy the book" nag messages in widget and settings.


= 4.3 =
* Fixed a bug in determining the ip address of pages forwarded by a proxy server.
* Modified IP code to work correctly with Opera Turbo.
* added a check for dictionary attacks against admin.
* broke out common code to a separate include file.
* fixed a bug on the settings page.
* rewrote the MU detection and loading code.
* added a white list request form to the deny message. White list request will be shown on the statistics page and the "Right Now" section of the main admin page.
* added a check for email "test@test.com" that allows users to check if the spam detection is working without black listing themselves. Try to leave a message using "test@test.com" and you'll be able to see what spammers see.
* rewrote list lookups so that the wild card '*' can be used. Now 238.3.* ip address can be white listed or black listed. The wild card only works for the end of emails and ips. '*.dn' will not work.
* fixed TLD block list. Many reports that it did not work. I rewrote and tested on different installations.
* I changed the name of the plugin to Stop Spammers. We'll see if it breaks Wordpress. I think this is the most dangerous thing I've tried.
* changed how logging works. Now, by default there is no log at all.


== Frequently Asked Questions ==
= Help, I'm locked out of my Website =
Not everyone who is marked as a spammer is actually a spammer. It is quite possible that you have been marked as a spammer on one of the spammer databases. There is no "back door", because spammers could use it.
Rename stop-spammer-registrations.php to stop-spammer-registrations.xxx and then login. Rename it back and check the history logs for the reason why your were denied access. Was your email or IP address marked as spam in one of the databases? If so, contact the website that maintains the database and ask them to remove you. 
Check off the box, "Automatically add admins to white list" in the spammer options settings. Then save your settings. This puts your IP address into the white list. You should be able to logout and then log back in.
Use the button on the Stop Spammer settings page to see if you pass. You may have to uncheck some options in order to pass. 
Unprofessional webmasters sometimes report IP address to Stop Forum Spam unnecessarily. If you are listed on SFS, there is a from at www.StopForumSpam.com. They can delete your entry.
= I have found a bug =
Please report it NOW. I fill try to fix it and incorporate the fix into the next release. I try to respond quickly to bugs that are possible to fix (all others take a few days). I keep a bleeding edge BETA test of the plugin (sometimes its very ALPHA) at my website: http://www.blogseye.com/beta-test-plugins/
If you are adventurous you can download the latest versions of some of my plugins before I release them.
= I used an older version of the plugin and it worked, but the latest version breaks my site =
You can download previous versions of the plugin at: http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/developers/
Don't forget to report to me what the problem is so I can try to fix it.
= All spammers have the same IP =
I am finding more and more plugin users on hosts that do some kind of Network Address Translation (NAT) or are behind a firewall, router, or proxy that does not pass the original IP address to the web server. If the proxy does not support X-FORWARDED-FOR (XFF) type headers then there is little that you can do. You must uncheck the "Check IP" box and rely on the plugin to use the passive methods to eliminate spammers. These are good methods and will stop most spammers, but you cannot report spam without reporting yourself, and you cannot cache bad IP addresses.
= I can't log into WordPress from my Android/iPhone app. =
Check your log files to find out exactly why the app was rejected. It usually is often the HTTP_REFERER header was not sent correctly. This is one sign of badly written spam software. It is also, unfortunately, a sign of badly written login software. Uncheck the box on the Stop Spammer settings page "Block with missing or invalid HTTP_REFERER". I white list iPhones and iPads using Safari on some checks because of bugs in the headers it sends.
= I see errors in the error listing below the cache listing =
It could be that there is something in your system that is causing errors. Copy the errors and email them to me, or paste them into a comment on the WordPress plugin page. I will investigate and try to fix these errors.
= You plugin is stopping new registrations, but how do I clean up existing spam registrations? =
Unfortunately, WordPress does not record the IP address of User registrations. This is a design flaw in WordPress. They do record the IP of comments. I cannot run a check against logins without their IP address, so you have to remove users the old fashioned way, one at a time. 
You might try listing the emails of all registered users, and then deleting them. You can then ask all users to re-register, but that would probably annoy your legitimate users.
= I have a cool idea for a feature for Stop-Spammer-Registrations-Plugin. =
Most of the features in the plugin have come from the users of the plugin. By all means stop by my website and leave a comment. I read all of them, and if the are feasible, I try to include them.
= I would like to support your programming efforts =
Try these links: http://www.blogseye.com/donate and http://www.blogseye.com/buy-the-book. Thanks for asking.


== Screenshots ==

1. A shot of the Spammer History settings page.

== Support ==
= Rate the Plugin =
This plugin is free and I expect nothing in return. Please rate the plugin at: http://wordpress.org/extend/plugins/stop-spammer-registrations-plugin/
If you find it useful, you may consider supporting my efforts.
http://www.blogseye.com/donate/


