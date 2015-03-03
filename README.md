Redirected URLs
===============

**Author:** Sam Minnée

**Author:** Stig Lindqvist

**Author:** Russ Michell


This module provides a system for users to configure arbitrary redirections in the CMS. These can be
used for legacy redirections, friendly URLs, and anything else that involves redirecting one URL to
another.

The URLs may include query-strings, and can be imported from a CSV using the "Redirects" model
admin included.

The redirection is implemented as a plug-in to the 404 handler, which means that you can't create a
redirection for a page that already exists on the site.

Installation
------------
Either:
1. Download or git clone the 'redirectedurls' directory to your webroot, or;
2. Using composer run the following in the command line:

  composer require silverstripe/redirectedurls dev-master

3. Run dev/build (http://www.mysite.com/dev/build?flush=all)

Usage
-----
 1. Click 'Redirects' in the main menu of the CMS.
 2. Click 'Add Redirected URL' to create a mapping of an old URL to a new URL on your SilverStripe website.
 3. Enter a 'From Base' which is the URL from your old website (not including the domain name). For example, "/about-us.html".
 4. Alternatively, depending on your old websites URL structure you can redirect based on a query string using the combination of 'From Base' and 'From Querystring' fields. For exmaple, "index.html" as the base and "page=about-us" as the query string.
 5. As a further alternative, you can include a trailing '/*' for a wildcard match to any file with the same stem. For example, "/about/*".
 6. Complete the 'To' field which is the URL you wish to redirect traffic to if any traffic from. For example, "/about-us".
 7. Alternatively you can terminate the 'To' field with '/*' to redirect to the specific file requested by the user. For example, "/new-about/*". Note that if this specific file is not in the target directory tree, the 404 error will be handled by the target site.
 8. Create a new Redirection for each URL mapping you need to redirect.

For example, to redirect "/about-us/index.html?item=1" to "/about-us/item/1", set:

	From Base:  /about-us/index.html
	From Querystring:  item=1
	To:  /about-us/item/1

Importing
---------
 1. Create a CSV file with the columns headings 'FromBase', 'FromQuerystring' and 'To' and enter your URL mappings.
 2. Click 'Redirects' in the main menu of the CMS.
 3. In the 'Import' section click 'Choose file', select your CSV file and then click 'Import from CSV'.
 4. Optionally select the 'Replace data' option if you want to replace the RedirectedURL database table contents with the imported data.

CSV Importer, example file format:

	FromBase, FromQuerystring, To
	/about-us/index.html, item=1, /about/item/1
	/example/no-querystring.html, ,/example/no-querystring/
	/example/two-queryparams.html, foo=1&bar=2, /example/foo/1/bar/2
	/about/*, ,/about-us

Expiry
------
You can optionally provide an expiry date for redirect rules. This allows you set up a transition period after a content restructure, for example.

The build task RedirectedURLExpiryTask can be executed to delete redirects n a have an expiry date that is in the past. If you want this to be executed periodically, it needs to be set up to execute as a cron job. Alternately, this can be executed directly by an administrator.

Editing Rules that Target a Page
--------------------------------
On sites where there are many redirect rules, it can be easier to manage the redirect rules on the pages that the rules target. By default this feature is
disabled. To enable it, simply add RedirectedURLPageExtension as an extension to Page. For example, in your site's config.yml:

    Page:
      extensions:
        - RedirectedURLPageExtension

This will add a tab "Redirections" to each page. This will list all redirection rules where the "To" field matches this page, as follows:

 *  The To field should be a relative URL. It may or may not have leading
    or trailing slashes. Home is "/". A URL containing "://" will not match.
 *  If the To field contains query fields or fragments, these are ignored
    for the matching.

Permissions work the same; the user must have the appropriate permisssions (for maintaining RedirectedURL objects) to edit the rules. It is the same underlying data, only filtered.
