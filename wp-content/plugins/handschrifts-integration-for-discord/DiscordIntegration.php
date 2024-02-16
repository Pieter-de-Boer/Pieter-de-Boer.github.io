<?php
/*
Plugin Name:  Handschrift's integration for Discord
Plugin URI:   https://gitlab.com/Schreibschrift/Wordpress-Discord-Integration
Description:  Adds multiple integrations for discord.
Version:      0.1
Author:       Handschrift
Author URI:   https://github.com/Handschrift/
License:      GPL-3.0
License URI:  https://www.gnu.org/licenses/gpl-3.0.html
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require 'DiscordSettings.php';
require 'DiscordApi.php';
session_start();

class HDIWPP_DiscordIntegration {

	public function __construct() {

		add_action( 'set_user_role', array( $this, 'mapToDiscordRole' ), 10, 3 );
		add_action( 'add_user_role', array( $this, 'mapToDiscordRoleAdd' ), 10, 2 );
		add_action( 'remove_user_role', array( $this, 'mapToDiscordRoleRemove' ), 10, 2 );

		add_action( 'show_user_profile', array( $this, 'discordIdMetadata' ) );
		add_action( 'edit_user_profile', array( $this, 'discordIdMetadata' ) );
		add_action( 'personal_options_update', array( $this, 'discordIdMetadataSave' ) );
		add_action( 'edit_user_profile_update', array( $this, 'discordIdMetadataSave' ) );


		add_action( 'init', function () {
			if ( $this->get( 'code' ) ) {
				$token = $this->apiRequest( 'https://discordapp.com/api/oauth2/token', array(
					'body' => array(
						'grant_type'    => 'authorization_code',
						'client_id'     => get_option( "di_client_id" ),
						'client_secret' => get_option( "di_client_secret" ),
						'redirect_uri'  => get_option( 'di_redirect_url' ),
						'code'          => $this->get( 'code' )
					)
				), true );
				if ( array_key_exists( 'access_token', $token ) ) {
					$logout_token             = $token['access_token'];
					$_SESSION['access_token'] = $token['access_token'];
				}
			}

			if ( $this->session( 'access_token' ) ) {
				$user = $this->apiRequest( 'https://discordapp.com/api/users/@me' );
				//add the user to the guild if setting is activated
				$api = new HDIWPP_DiscordApi();


				$name = $user['username'];
				$mail = $user['email'];
				wp_insert_user(
					array(
						'user_login' => $name,
						'user_email' => $mail,
						'user_pass'  => wp_generate_password( 20 ),
						'role'       => 'subscriber',
					)
				);

				//Meaning the user already exists
				$db_user = get_user_by( 'email', $mail );
				wp_clear_auth_cookie();
				if ( ! $db_user ) {
					$userId = wp_insert_user(
						array(
							'user_login' => $name . '.' . substr( uniqid(), - 4 ),
							'user_email' => $mail,
							'user_pass'  => wp_generate_password( 20 ),
							'role'       => 'subscriber',
						)
					);
				} else {
					$userId = $db_user->ID;
				}
				if ( ! $api->isMemberOfGuild( $user['id'] ) ) {
					if ( get_option( 'di_auto_add' ) && get_option( 'di_guild_id' ) ) {
						$api->addMemberToServer( $this->session( 'access_token' ), $user['id'], array( get_option( "di_wp2d_roles_subscriber" ) ) );
					}
				} else {
					if ( ! get_the_author_meta( 'di_discord_id', $userId ) ) {
						$api->addRoleToMember( $user['id'], get_option( "di_wp2d_roles_subscriber" ) );
						update_user_meta( $userId, 'di_discord_id', $user['id'] );
					}
				}

				wp_set_current_user( $userId );
				wp_set_auth_cookie( $userId );
				unset( $_SESSION['access_token'] );
			}

		} );

		add_shortcode( 'discord_login_button', array( $this, 'discord_login_button_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_css' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'load_css' ) );
		add_action( 'login_form', array( $this, 'discord_login_button_login_form' ) );
	}

	public function discord_login_button_shortcode( $attrs ): string {
		if ( ! get_option( 'di_client_id' ) || ! get_option( 'di_client_secret' ) ) {
			return "";
		}

		$attrs = shortcode_atts( array(
			'text' => 'Login with Discord'
		), $attrs );

		ob_start();
		?>
		<div>
			<a id="discord_login_button" style="background-color: #5865F2"
			   href="<?php echo esc_attr( 'https://discord.com/api/oauth2/authorize?client_id=' . get_option( 'di_client_id' ) . '&redirect_uri=' . urlencode( get_option( 'di_redirect_url' ) ) . '&response_type=code&scope=identify%20email%20guilds%20guilds.join%20guilds.members.read' ); ?>">
				<svg width="40px" height="40px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
					<circle cx="512" cy="512" r="512" style="fill:#5865f2"/>
					<path
						d="M689.43 349a422.21 422.21 0 0 0-104.22-32.32 1.58 1.58 0 0 0-1.68.79 294.11 294.11 0 0 0-13 26.66 389.78 389.78 0 0 0-117.05 0 269.75 269.75 0 0 0-13.18-26.66 1.64 1.64 0 0 0-1.68-.79A421 421 0 0 0 334.44 349a1.49 1.49 0 0 0-.69.59c-66.37 99.17-84.55 195.9-75.63 291.41a1.76 1.76 0 0 0 .67 1.2 424.58 424.58 0 0 0 127.85 64.63 1.66 1.66 0 0 0 1.8-.59 303.45 303.45 0 0 0 26.15-42.54 1.62 1.62 0 0 0-.89-2.25 279.6 279.6 0 0 1-39.94-19 1.64 1.64 0 0 1-.16-2.72c2.68-2 5.37-4.1 7.93-6.22a1.58 1.58 0 0 1 1.65-.22c83.79 38.26 174.51 38.26 257.31 0a1.58 1.58 0 0 1 1.68.2c2.56 2.11 5.25 4.23 8 6.24a1.64 1.64 0 0 1-.14 2.72 262.37 262.37 0 0 1-40 19 1.63 1.63 0 0 0-.87 2.28 340.72 340.72 0 0 0 26.13 42.52 1.62 1.62 0 0 0 1.8.61 423.17 423.17 0 0 0 128-64.63 1.64 1.64 0 0 0 .67-1.18c10.68-110.44-17.88-206.38-75.7-291.42a1.3 1.3 0 0 0-.63-.63zM427.09 582.85c-25.23 0-46-23.16-46-51.6s20.38-51.6 46-51.6c25.83 0 46.42 23.36 46 51.6.02 28.44-20.37 51.6-46 51.6zm170.13 0c-25.23 0-46-23.16-46-51.6s20.38-51.6 46-51.6c25.83 0 46.42 23.36 46 51.6.01 28.44-20.17 51.6-46 51.6z"
						style="fill:#fff"/>
				</svg>
				<?php echo esc_html( $attrs['text'] ) ?>
			</a>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function discord_login_button_login_form(): void {
		if ( get_option( 'di_enable_login' ) && get_option( 'di_client_id' ) && get_option( 'di_client_secret' ) ) {
			?>
			<div>
				<a id="discord_login_button" style="background-color: #5865F2"
				   href="<?php echo esc_attr( 'https://discord.com/api/oauth2/authorize?client_id=' . get_option( 'di_client_id' ) . '&redirect_uri=' . urlencode( get_option( 'di_redirect_url' ) ) . '&response_type=code&scope=identify%20email%20guilds%20guilds.join%20guilds.members.read' ); ?>">
					<svg width="40px" height="40px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
						<circle cx="512" cy="512" r="512" style="fill:#5865f2"/>
						<path
							d="M689.43 349a422.21 422.21 0 0 0-104.22-32.32 1.58 1.58 0 0 0-1.68.79 294.11 294.11 0 0 0-13 26.66 389.78 389.78 0 0 0-117.05 0 269.75 269.75 0 0 0-13.18-26.66 1.64 1.64 0 0 0-1.68-.79A421 421 0 0 0 334.44 349a1.49 1.49 0 0 0-.69.59c-66.37 99.17-84.55 195.9-75.63 291.41a1.76 1.76 0 0 0 .67 1.2 424.58 424.58 0 0 0 127.85 64.63 1.66 1.66 0 0 0 1.8-.59 303.45 303.45 0 0 0 26.15-42.54 1.62 1.62 0 0 0-.89-2.25 279.6 279.6 0 0 1-39.94-19 1.64 1.64 0 0 1-.16-2.72c2.68-2 5.37-4.1 7.93-6.22a1.58 1.58 0 0 1 1.65-.22c83.79 38.26 174.51 38.26 257.31 0a1.58 1.58 0 0 1 1.68.2c2.56 2.11 5.25 4.23 8 6.24a1.64 1.64 0 0 1-.14 2.72 262.37 262.37 0 0 1-40 19 1.63 1.63 0 0 0-.87 2.28 340.72 340.72 0 0 0 26.13 42.52 1.62 1.62 0 0 0 1.8.61 423.17 423.17 0 0 0 128-64.63 1.64 1.64 0 0 0 .67-1.18c10.68-110.44-17.88-206.38-75.7-291.42a1.3 1.3 0 0 0-.63-.63zM427.09 582.85c-25.23 0-46-23.16-46-51.6s20.38-51.6 46-51.6c25.83 0 46.42 23.36 46 51.6.02 28.44-20.37 51.6-46 51.6zm170.13 0c-25.23 0-46-23.16-46-51.6s20.38-51.6 46-51.6c25.83 0 46.42 23.36 46 51.6.01 28.44-20.17 51.6-46 51.6z"
							style="fill:#fff"/>
					</svg>
					Login with Discord
				</a>
			</div>
			<?php
		}
	}

	private function apiRequest( $url, $headers = array(), $post = false ) {
		$headers['headers']['Content-Type'] = 'application/json';

		if ( $this->session( 'access_token' ) ) {
			$headers['headers']['Authorization'] = "Bearer " . $this->session( 'access_token' );
		}

		if ( $post ) {
			$headers['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
			$response                           = wp_remote_post( $url, $headers );
		} else {
			$response = wp_remote_get( $url, $headers );
		}

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	private function get( $key, $default = null ): string {
		return array_key_exists( $key, $_GET ) ? sanitize_text_field( $_GET[ $key ] ) : sanitize_text_field( $default );
	}

	private function session( $key, $default = null ): string {
		return array_key_exists( $key, $_SESSION ) ? sanitize_text_field( $_SESSION[ $key ] ) : sanitize_text_field( $default );
	}

	function load_css(): void {
		wp_enqueue_style( 'discord-integration-css', plugin_dir_url( __FILE__ ) . 'styles/di_button.css', array(), 1, 'all' );
	}

	public function mapToDiscordRole( int $user_id, string $role, array $old_roles ): void {

		$api       = new HDIWPP_DiscordApi();
		$discordId = get_the_author_meta( 'di_discord_id', $user_id );

		foreach ( $old_roles as $old_role ) {
			$api->removeRoleFromMember( $user_id, $old_role );
		}

		if ( $discordId != null ) {
			if ( $api->isMemberOfGuild( $discordId ) ) {
				$lowercaseRole = strtolower( $role );

				$roleId = get_option( "di_wp2d_roles_$lowercaseRole" );


				$api->addRoleToMember( $discordId, $roleId );
			}
		}
	}

	public function mapToDiscordRoleRemove( int $user_id, string $role ): void {
		$api       = new HDIWPP_DiscordApi();
		$discordId = get_the_author_meta( 'di_discord_id', $user_id );

		if ( $discordId != null ) {
			if ( $api->isMemberOfGuild( $discordId ) ) {
				$lowercaseRole = strtolower( $role );

				$roleId = get_option( "di_wp2d_roles_$lowercaseRole" );


				$api->removeRoleFromMember( $discordId, $roleId );
			}
		}
	}

	public function mapToDiscordRoleAdd( int $user_id, string $role ): void {
		$this->mapToDiscordRole( $user_id, $role, array() );
	}

	function discordIdMetadata( WP_User $user ): void {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		?>
		<h3>Discord Integration</h3>
		<table class="form-table">
			<tr>
				<th><label for="di_discord_id">Discord ID</label></th>
				<td>
					<input type="text" maxlength="19" name="di_discord_id" id="di_discord_id"
					       value="<?php echo esc_attr( get_the_author_meta( 'di_discord_id', $user->ID ) ); ?>"
					       class="regular-text"/><br/>
					<span class="description">The discord id of the user.</span>
				</td>
			</tr>
		</table>
		<?php
	}

	function discordIdMetadataSave( int $user_id ): bool {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'di_discord_id', sanitize_text_field( $_POST['di_discord_id'] ) );

		return true;
	}

}

new HDIWPP_DiscordIntegration();