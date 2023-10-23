# Bluesky posts WordPress plugin

This enables a WordPress shortcode which displays posts from Bluesky according to your settings.

	[bluesky-posts]

It displays a specific users original posts only. Replies and re-posts are skipped.

By default it displays 12 posts. You can specify this with attribute `amount`.
For example to display only one post, use this shortcode.

	[bluesky-posts amount=1]

The plugin has some basic CSS, but it should use your sites font and color for the text.
You can turn off the plugin CSS if you want to style it completely yourself.