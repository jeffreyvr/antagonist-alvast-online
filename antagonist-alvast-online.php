<?php
/**
 * Plugin name: Antagonist Alvast Online
 * Plugin URI: https://vanrossum.dev
 * Description: Fixing Alvast online from Antagonist for WordPress.
 * Version: 2.0.0
 * Author: Jeffrey van Rossum
 * Author URI: https://www.vanrossum.dev
 */

class Alvast_Online {
	private static $instance = null;

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Alvast_Online ) ) {
			self::$instance = new Alvast_Online();
		}

		return self::$instance;
	}

	private function __construct() {
		if ( ! $this->is_alvast_online_url_set() ) {
			return;
		}

		add_filter( 'post_link', array( $this, 'replace_domain' ) );
		add_filter( 'page_link', array( $this, 'replace_domain' ) );
		add_filter( 'attachment_link', array( $this, 'replace_domain' ) );
		add_filter( 'post_type_link', array( $this, 'replace_domain' ) );
		add_filter( 'wp_get_attachment_url', array( $this, 'replace_domain' ) );

		if ( $this->should_buffer_be_applied() ) {
			add_action( 'init', array( $this, 'start' ) );
			add_action( 'shutdown', array( $this, 'end' ) );
		}
	}

	public function is_alvast_online_url_set() {
		return strpos( $_SERVER['HTTP_REFERER'], 'alvast-online.nl' );
	}

	public function get_alvast_online_domain() {
		return parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_HOST );
	}

	public function get_domain() {
		return $_SERVER['SERVER_NAME'];
	}

	public function get_replaceables() {
		$alvast_online_domain = $this->get_alvast_online_domain();
		$domain               = $this->get_domain();

		return array(
			'http://' . $domain    => 'http://' . $alvast_online_domain,
			'https://' . $domain   => 'https://' . $alvast_online_domain,
			'http:\/\/' . $domain  => 'http:\/\/' . $alvast_online_domain,
			'https:\/\/' . $domain => 'https:\/\/' . $alvast_online_domain
		);
	}

	public function should_buffer_be_applied() {
		$endpoints = array( 'wp-admin/themes.php', 'wp-admin/post-new.php', 'wp-admin/post.php' );

		foreach ( $endpoints as $endpoint ) {
			if ( strstr( $_SERVER['REQUEST_URI'], $endpoint ) ) {
				return true;
			}
		}

		return false;
	}

	public function replace_domain( $data ) {
		$replaceables = $this->get_replaceables();

		return str_replace( array_keys( $replaceables ), array_values( $replaceables ), $data );
	}

	public function start() {
		ob_start( array( $this, 'replace_domain' ) );
	}

	public function end() {
		@ob_end_flush();
	}
}

function alvast_online() {
	return Alvast_Online::instance();
}

alvast_online();
