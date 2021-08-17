<?php
/**
 * Plugin name: Antagonist Alvast Online
 * Plugin URI: https://vanrossum.dev
 * Description: Fixing Alvast online from Antagonist for WordPress.
 * Version: 1.0.0
 * Author: Jeffrey van Rossum
 * Author URI: https://www.vanrossum.dev
 */

class Alvast_Online {
	private static $instance = null;
	public $server_name;

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Alvast_Online ) ) {
			self::$instance = new Alvast_Online();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->server_name = $_SERVER['SERVER_NAME'];

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
		return defined( 'ALVAST_ONLINE_DOMAIN' );
	}

	public function replaceables() {
		return array(
			'http://' . $this->server_name    => 'http://' . ALVAST_ONLINE_DOMAIN,
			'https://' . $this->server_name   => 'https://' . ALVAST_ONLINE_DOMAIN,
			'http:\/\/' . $this->server_name  => 'http:\/\/' . ALVAST_ONLINE_DOMAIN,
			'https:\/\/' . $this->server_name => 'https:\/\/' . ALVAST_ONLINE_DOMAIN
		);
	}

	public function should_buffer_be_applied() {
		if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'],
				'wp-admin/post.php' ) ) {
			return true;
		}

		return false;
	}

	public function replace_domain( $data ) {
		$replaceables = $this->replaceables();

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
