<?php

/**
 * Plugin Name: Bluesky posts
 * Description: This enables the shortcode [bluesky-posts] that outputs a specific users Bluesky posts based on your settings.
 * Version: 2025.1.28
 * Update URI: https://github.com/kendafi/wp-bluesky-posts/
 * Author: Kenda
 * Author URI: https://kenda.fi/
 * Text Domain: wp-bluesky-posts
 * Domain Path: /languages
 **/

// Makes sure WP is loaded (no direct loading of this file is allowed).

if ( !function_exists( 'add_action' ) ) { die( 'No direct access to this file.' ); }

// Load translations.

add_action( 'plugins_loaded', 'wp_bluesky_posts_load_textdomain' );

function wp_bluesky_posts_load_textdomain() {

	load_plugin_textdomain(
		'wp-bluesky-posts',
		false,
		basename( dirname( __FILE__ ) ) . '/languages'
	);

}

// Show link on plugins list page.

add_filter( 'plugin_action_links_'.plugin_basename(__FILE__) , 'wp_bluesky_posts_settings_link' );

function wp_bluesky_posts_settings_link( $links ) {

	$settings_link = '<a href="options-general.php?page=wp-bluesky-posts">'.esc_html__( 'Settings', 'wp-bluesky-posts' ).'</a>';

	array_unshift( $links, $settings_link );

	return $links;

}

// Show links below description on plugins list page.

add_filter( 'plugin_row_meta', 'wp_bluesky_posts_custom_links', 10, 2 );

function wp_bluesky_posts_custom_links( $links, $file ) {

	if ( plugin_basename( __FILE__ ) == $file ) {

		$row_meta = array(
			'setup'    => '<a href="options-general.php?page=wp-bluesky-posts">'.esc_html__( 'Settings', 'wp-bluesky-posts' ).'</a>',
		);

		return array_merge( $links, $row_meta );

	}

	return (array) $links;

}

// Make the link appear in WP admin under Settings.

add_action( 'admin_menu', 'wp_bluesky_posts_menu' );

function wp_bluesky_posts_menu() {

	$page_title = __( 'Bluesky posts settings', 'wp-bluesky-posts' );
	$menu_title = __( 'Bluesky posts', 'wp-bluesky-posts' );
	$capability = 'manage_options';
	$menu_slug  = 'wp-bluesky-posts';
	$function   = 'wp_bluesky_posts_page_content';

	add_options_page(
		$page_title,
		$menu_title,
		$capability,
		$menu_slug,
		$function
	);

}

// Admin page content.

function wp_bluesky_posts_page_content() {

	if ( !current_user_can( 'manage_options' ) ) {

		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-bluesky-posts' ) );

	}

	echo '<div class="wrap">';

	echo '<h1>'.esc_html( get_admin_page_title() ).'</h1>';

	// Handle submitted data.

	if ( isset( $_POST[ 'wp_bluesky_author' ] ) ) {

		$wp_bluesky_posts_settings = array(
			'wp_bluesky_author' => str_replace( '@', '', trim( $_POST[ 'wp_bluesky_author' ] ) )
		);

		if ( isset( $_POST[ 'wp_bluesky_disablecss' ] ) ) {

			$wp_bluesky_posts_settings['wp_bluesky_disablecss'] = 1;

		}

		update_option( 'wp_bluesky_posts', json_encode( $wp_bluesky_posts_settings ) );

		echo '<div class="notice notice-success is-dismissible"><p>'.esc_html__( 'Settings are updated.', 'wp-bluesky-posts' ).'</p></div>';

	}

	echo '<p>'.esc_html__( 'Save settings below and use this shortcode to display Bluesky posts on any page:', 'wp-bluesky-posts' ).' <code>[bluesky-posts]</code></p>';

	$wp_bluesky_author = '';
	$wp_bluesky_disablecss = '';

	// Get any settings we may already have stored.
	$wp_bluesky_posts_settings = get_option( 'wp_bluesky_posts' );

	if ( $wp_bluesky_posts_settings != '' ) {

		$wp_bluesky_posts_settings = json_decode( $wp_bluesky_posts_settings, true );

		if ( is_array( $wp_bluesky_posts_settings ) && !empty( $wp_bluesky_posts_settings ) ) {

			$wp_bluesky_author = $wp_bluesky_posts_settings['wp_bluesky_author'];
			$wp_bluesky_disablecss = ( array_key_exists( 'wp_bluesky_disablecss', $wp_bluesky_posts_settings ) ? $wp_bluesky_posts_settings['wp_bluesky_disablecss'] : 0 );

		}

	}

	echo '
	<form method="post" action="'.esc_url( admin_url( 'options-general.php' ) ).'?page=wp-bluesky-posts">

	<p><label for="wp_bluesky_author">'.esc_html__( 'Author whose posts to display', 'wp-bluesky-posts' ).'</label><br>
	<input type="text" name="wp_bluesky_author" id="wp_bluesky_author" placeholder="example.bsky.social" value="'.esc_html( $wp_bluesky_author ).'" class="regular-text"></p>

	<p><input type="checkbox" name="wp_bluesky_disablecss" id="wp_bluesky_disablecss" value="1"' . ( $wp_bluesky_disablecss == 1 ? ' checked="checked"' : '' ) . '><label for="wp_bluesky_disablecss">'.esc_html__( 'Disable CSS set by this plugin - I want to use my own CSS.', 'wp-bluesky-posts' ).'</label></p>';

	settings_fields( 'wp-bluesky-posts' );

	submit_button(
		__( 'Save settings', 'wp-bluesky-posts' ),
		'primary',
		'submit'
	);

	echo '</form>';

	echo '<p>'.esc_html__( 'By default the shortcode displays 12 posts. You can specify the amount like the examples below.', 'wp-bluesky-posts' ).'</p>';

	echo '<p>'.esc_html__( 'To display only one post, use this shortcode:', 'wp-bluesky-posts' ).' <code>[bluesky-posts amount=1]</code></p>';
	echo '<p>'.esc_html__( 'To display 12 posts, use this shortcode:', 'wp-bluesky-posts' ).'<code>[bluesky-posts amount=12]</code></p>';

	echo '<p>'.esc_html__( 'See this plugins source code and get the latest version from here:', 'wp-bluesky-posts' ).' <a href="https://github.com/kendafi/wp-bluesky-posts/" target="_blank">github.com/kendafi/wp-bluesky-posts</a></p>';

	echo '</div> <!-- wrap -->';

}

// Add CSS

add_action( 'wp_enqueue_scripts', 'wp_bluesky_assets' );

function wp_bluesky_assets() {

	$wp_bluesky_disablecss = 0;

	// Get any settings we may already have stored.
	$wp_bluesky_posts_settings = get_option( 'wp_bluesky_posts' );

	if ( $wp_bluesky_posts_settings != '' ) {

		$wp_bluesky_posts_settings = json_decode( $wp_bluesky_posts_settings, true );

		if ( is_array( $wp_bluesky_posts_settings ) && !empty( $wp_bluesky_posts_settings ) ) {

			$wp_bluesky_disablecss = ( array_key_exists( 'wp_bluesky_disablecss', $wp_bluesky_posts_settings ) ? $wp_bluesky_posts_settings['wp_bluesky_disablecss'] : 0 );

		}

	}

	if( $wp_bluesky_disablecss != 1 ) {

		// Use our CSS only if user has not disabled it in settings.

		$our_path = plugin_dir_path( __FILE__ );

		if ( file_exists( $our_path.'wp-bluesky-posts.css' ) ) {

			wp_enqueue_style(
				'wp-bluesky-posts',
				plugins_url( basename( $our_path ).'/wp-bluesky-posts.css' ),
				false,
				filemtime( $our_path.'wp-bluesky-posts.css' ),
				false
			);

		}

	}

}

// Shortcode.

function wp_bluesky_posts_shortcode_output( $atts = [], $content = null, $tag = '' ) {

  $return_html = '';

	$bsky_amount = 12;

	if( isset( $atts ) && is_array( $atts ) && array_key_exists( 'amount', $atts ) ) {

		$bsky_amount = $atts['amount'];

	}

	$wp_bluesky_posts_settings = get_option( 'wp_bluesky_posts' );

	if ( $wp_bluesky_posts_settings != '' ) {

		$wp_bluesky_posts_settings = json_decode( $wp_bluesky_posts_settings, true );

		if ( is_array( $wp_bluesky_posts_settings ) && !empty( $wp_bluesky_posts_settings ) ) {

			$wp_bluesky_author = $wp_bluesky_posts_settings['wp_bluesky_author'];

			if ( $wp_bluesky_author != '' ) {

				$curl = curl_init();

				curl_setopt_array(
					$curl,
					array(
						CURLOPT_URL => 'https://public.api.bsky.app/xrpc/app.bsky.feed.getAuthorFeed?actor=' . $wp_bluesky_author . '&limit=' . $bsky_amount . '&filter=posts_no_replies',
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'GET',
					)
				);

				if( $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'wordpress.xx' ) {

					// skip SSL in localhost in case it doesn't support that
					curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );

				}

				$response = curl_exec( $curl );

				$data = json_decode( $response, TRUE );

				curl_close( $curl );

				$date_time_format = 'j.n.Y @ H:i';

				if( is_array( $data ) && !empty( $data ) && array_key_exists( 'feed', $data ) ) {

					$return_html .= '<div class="bsky-wrapper">';

					foreach( $data['feed'] as $bsky_post ) {

						// Original post does not have $bsky_post['reply'].
						// We want to display only original posts, so we skip if it's a reply.

						// If you ever want to include replies, note that in a reply
						// the original post is in 'reply', and the reply is in 'post'.

						if( !array_key_exists( 'reply', $bsky_post ) ) {

							// By comparing username whose feed we fetched and the post username,
							// we can exclude all re-posts of someone elses post.

							if( $wp_bluesky_author == $bsky_post['post']['author']['handle'] ) {

								$return_html .= '<div class="bsky-item">';

									$return_html .= '<div class="bsky-user-and-created"><p>';

										// Avatar
										$return_html .= '<img src="' . $bsky_post['post']['author']['avatar'] . '" alt="" width="50" hspace="10" align="left">';

										// Username
										$return_html .= '<a href="https://bsky.app/profile/'.$bsky_post['post']['author']['handle'].'" target="_blank">' . htmlentities( $bsky_post['post']['author']['displayName'] ) . '</a><br>';

										// We need post ID for the link... This is very ugly.
										$link_parts = explode( 'app.bsky.feed.post/', $bsky_post['post']['uri'] );

										// Timestamp incl. link to post.
										$return_html .= '<small><a href="https://bsky.app/profile/' . $bsky_post['post']['author']['handle'] . '/post/' . $link_parts[ 1 ] . '" target="_blank">' . date( $date_time_format, strtotime( $bsky_post['post']['record']['createdAt'] ) ) . '</a></small>';

										$return_html .= '</p></div> <!--bsky-user-and-created -->';

										$return_html .= '<div class="bsky-item-text">';

										$return_html .= '<p>';

										// The content

										if( array_key_exists( 'record', $bsky_post['post'] ) && array_key_exists( 'facets', $bsky_post['post']['record'] ) ) {

											// We seem to have links. Let's make them clickable in the content.

											$replace = array();

											foreach( $bsky_post['post']['record']['facets'] as $link ) {

												if( array_key_exists( 'features', $link ) && is_array( $link['features'] ) && !empty( $link['features'] ) ) {

													if( $link['features'][0]['$type'] == 'app.bsky.richtext.facet#tag' ) {

														// Hashtag - TODO when this is officially supported

													}
													elseif( $link['features'][0]['$type'] == 'app.bsky.richtext.facet#link' ) {

														// Link

														$uri = $link['features'][0]['uri'];

														$length = $link['index']['byteEnd'] - $link['index']['byteStart'];

														$replace_this = substr( $bsky_post['post']['record']['text'], $link['index']['byteStart'], $length );

														$replace[ $replace_this ] = '<a href="' . $uri . '" target="_blank">' . $replace_this . '</a>';

													}

												}

											}

											$return_html .= nl2br( str_replace( array_keys( $replace ), $replace, $bsky_post['post']['record']['text'] ), false );

										}
										else {

											// We have no rich content. Output as plain text.
											$return_html .= nl2br( $bsky_post['post']['record']['text'], false );

										}

										$return_html .= '</p>';

									// Embeds

									if( array_key_exists( 'embed', $bsky_post['post'] ) ) {

										// Quoted post

										if( array_key_exists( '$type', $bsky_post['post']['embed'] ) && $bsky_post['post']['embed']['$type'] == 'app.bsky.embed.recordWithMedia#view' ) {

											// We have both images and quoted post

										}
										elseif( array_key_exists( 'record', $bsky_post['post']['embed'] ) ) {

											// Quoted post only

											if( $bsky_post['post']['embed']['record']['$type'] == 'app.bsky.embed.record#viewRecord' ) {

												$return_html .= '<div class="bsky-embeds-record"><blockquote>';

												// Avatar
												$return_html .= '<img src="' . $bsky_post['post']['embed']['record']['author']['avatar'] . '" alt="" width="60" hspace="10" align="left">';

												// Username
												$return_html .= '<a href="https://bsky.app/profile/'.$bsky_post['post']['embed']['record']['author']['handle'].'" target="_blank">' . htmlentities( $bsky_post['post']['embed']['record']['author']['displayName'] ) . '</a><br>';

												// We need post ID for the link... This is very ugly.
												$link_parts = explode( 'app.bsky.feed.post/', $bsky_post['post']['embed']['record']['uri'] );

												// Timestamp incl. link to post.
												$return_html .= '<small><a href="https://bsky.app/profile/' . $bsky_post['post']['embed']['record']['author']['handle'] . '/post/' . $link_parts[ 1 ] . '" target="_blank">' . date( $date_time_format, strtotime( $bsky_post['post']['embed']['record']['value']['createdAt'] ) ) . '</a></small>';

												$return_html .= '<div class="bsky-item-embed-record-text"><p>';

													// The content
													$return_html .= nl2br( $bsky_post['post']['embed']['record']['value']['text'], false );

												$return_html .= '</div> <!--bsky-item-embed-record-text -->';

												$return_html .= '</blockquote></div> <!-- bsky-embeds-record -->';

											}

										}

										// External link

										if( array_key_exists( 'external', $bsky_post['post']['embed'] ) ) {

											if( $bsky_post['post']['embed']['$type'] == 'app.bsky.embed.external#view' && array_key_exists( 'thumb', $bsky_post['post']['embed']['external'] ) ) {

												$return_html .= '<div class="bsky-embeds-external"><blockquote>';

												// Thumbnail
												$return_html .= '<img src="' . $bsky_post['post']['embed']['external']['thumb'] . '" alt="" width="40" hspace="10" align="left">';

												$return_html .= '<a href="'.$bsky_post['post']['embed']['external']['uri'].'" target="_blank">';
												$return_html .= '<strong>' . htmlentities( $bsky_post['post']['embed']['external']['title'] ) . '</strong><br>';
												$return_html .= htmlentities( $bsky_post['post']['embed']['external']['description'] );
												$return_html .= '</a><br>';

												$return_html .= '</blockquote></div> <!-- bsky-embeds-external -->';

											}

										}

										// Images

										if( array_key_exists( 'images', $bsky_post['post']['embed'] ) ) {

											$return_html .= '<div class="bsky-embeds-images">';

											foreach( $bsky_post['post']['embed']['images'] as $bsky_image ) {

												$return_html .= '<p><a href="' . $bsky_image['fullsize'] . '" target="_blank"><img src="' . $bsky_image['thumb'] . '" alt="' . $bsky_image['alt'] . '" width="100%"></a></p>';

											}

											$return_html .= '</div> <!-- bsky-embeds-images -->';

										}

										// At the moment Bluesky does not support videos.
										// But some day it may do that and we can do something like this...
										// elseif( array_key_exists( 'videos', $bsky_post['post']['embed'] ) ) {
										// }

									}

									$return_html .= '</div> <!--bsky-item-text -->';

									$return_html .= '<div class="bsky-item-stats"><p><small>';

										// Stats
										$return_html .= '<span class="bsky-stats-likes">Likes <span class="bsky-stats-value">' . $bsky_post['post']['likeCount'] . '</span></span> ';
										$return_html .= '<span class="bsky-stats-reposts">Reposts <span class="bsky-stats-value">' . $bsky_post['post']['repostCount'] . '</span></span> ';
										$return_html .= '<span class="bsky-stats-replies">Replies <span class="bsky-stats-value">' . $bsky_post['post']['replyCount'] . '</span></span>';

									$return_html .= '</small></p></div> <!-- bsky-item-stats -->';

								$return_html .= '</div> <!-- bsky-item -->';

							}

						}

					}

					$return_html .= '</div> <!-- bsky-wrapper -->';

				}

			}

		}

	}

	return $return_html;

}

function wp_bluesky_posts_shortcode_init() {

	add_shortcode( 'bluesky-posts', 'wp_bluesky_posts_shortcode_output' );

}

add_action( 'init', 'wp_bluesky_posts_shortcode_init' );

?>