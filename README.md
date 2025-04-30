# Bluesky posts WordPress plugin

This enables a WordPress shortcode which displays posts from Bluesky according to your settings.

	[bluesky-posts]

It displays a specific users original posts only. Replies and re-posts are skipped.

By default it displays 12 posts. You can specify this with attribute `amount`.
For example to display only one post, use this shortcode.

	[bluesky-posts amount=1]

The plugin has some basic CSS, but it should use your sites font and color for the text.
You can turn off the plugin CSS if you want to style it completely yourself.

The plugin stores fetched content into transients. This means we ping Bluesky
only once every 10 minutes to avoid exceeding any connection limits.

## Install

Download this repository as a ZIP file from [github.com/kendafi/wp-bluesky-posts](https://github.com/kendafi/wp-bluesky-posts) by clicking on the green Code button and selecting Download ZIP.

Go to your WP admin > Plugins page and click on the Add New Plugin button.

Click on Upload Plugin button, and then on the Browse button to select the ZIP file.

Activate the plugin.

Go in WP admin to Settings > Bluesky post. Enter the account which posts you want to display. Save settings.

Go to any page and add the shortcode `[bluesky-posts]` to its content.

## Videos

Videos come as playlists. A JavaScript library that implements an HTTP Live Streaming client
is required for playlists to work in all modern browsers (some support it by default).
This plugin adds `hls.js` into your `<head>`.
It is loaded from [www.jsdelivr.com/package/npm/hls.js](https://www.jsdelivr.com/package/npm/hls.js)
where you can read more about `hls.js`.

You can also download a copy of it if you want to host it on your websites server.
In this case you should disable loading the JavaScript from the CDN - do this
on the plugin settings page in WP admin > Settings > Bluesky posts.

If you want to keep your webpage fast you may want to only display images
instead of the whole video embed that also requires that heavy JavaScript library mentioned above.
You can select this option on the plugin settings page.

## Screenshots

![Screenshot of the settings page.](screenshots/settings-english.png)

![Example of the output.](screenshots/users-posts.png)