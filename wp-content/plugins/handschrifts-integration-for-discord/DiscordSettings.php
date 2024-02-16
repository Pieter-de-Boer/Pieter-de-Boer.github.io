<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HDIWPP_DiscordSettings {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function add_plugin_menu(): void {
		add_menu_page( "Discord Integration", 'Discord Integration', 'administrator', "discord_menu", array(
			$this,
			'displayPluginAdminDashboard'
		), 'dashicons-networking' );
	}

	public function register_settings(): void {
		register_setting( "discord_group", "di_client_id" );
		register_setting( "discord_group", "di_client_secret" );
		register_setting( "discord_group", "di_redirect_url" );
		register_setting( "discord_group", "di_guild_id" );
		register_setting( "discord_group", "di_enable_login" );
		register_setting( "discord_group", "di_bot_token" );
		register_setting( "discord_group", "di_auto_add" );

		add_settings_section( "discord_section", "Settings", null, "discord_menu" );
		add_settings_section( "wp2di_role_mapping", "Wordpress to Discord role Mapping", null, "discord_menu" );


		foreach ( get_editable_roles() as $currentRole ) {
			$roleName      = $currentRole["name"];
			$lowercaseName = strtolower( $roleName );
			register_setting( "discord_group", "di_wp2d_roles_$lowercaseName" );

			add_settings_field( "di_wp2d_roles_$lowercaseName", $roleName, array(
				$this,
				'wp_to_discord_mapping'
			), "discord_menu", "wp2di_role_mapping", $lowercaseName );
		}

		add_settings_field( "di_client_id", "ClientID", array(
			$this,
			'di_client_id'
		), "discord_menu", "discord_section" );

		add_settings_field( "di_client_secret", "ClientSecret", array(
			$this,
			'di_client_secret'
		), "discord_menu", "discord_section" );

		add_settings_field( "di_redirect_url", "Redirect URL", array(
			$this,
			'di_redirect_url'
		), "discord_menu", "discord_section" );


		add_settings_field( "di_guild_id", "Discord server id", array(
			$this,
			'di_guild_id'
		), "discord_menu", "discord_section" );

		add_settings_field( "di_bot_token", "Bot token", array(
			$this,
			'di_bot_token'
		), "discord_menu", "discord_section" );

		add_settings_field( "di_enable_login", "Enable discord login", array(
			$this,
			'di_enable_login'
		), "discord_menu", "discord_section" );


		add_settings_field( "di_auto_add", "Auto add users to the server", array(
			$this,
			'di_auto_add'
		), "discord_menu", "discord_section" );
	}

	public function wp_to_discord_mapping( string $role ): void {

		$api   = new HDIWPP_DiscordApi();
		$roles = $api->getGuildRoles();
		if ( ! get_option( 'di_bot_token' ) ) {
			echo "Please provide a bot token!";

			return;
		}
		?>

		<select name="<?php echo esc_attr( 'di_wp2d_roles_' . $role ) ?>"
		        id="<?php echo esc_attr( 'di_wp2d_roles_' . $role ) ?>">
			<?php
			foreach ( $roles as $guildRole ) {
				$roleId   = esc_attr( $guildRole['id'] );
				$roleName = esc_html( $guildRole['name'] );
				echo "<option value='$roleId' " . esc_html( selected( get_option( 'di_wp2d_roles_' . $role ), $guildRole['id'] ) ) . "> $roleName </option>";
			}
			?>
		</select>

		<?php
	}


	public function di_enable_login(): void {
		?>
		<input <?php echo 'type="checkbox" id="di_enable_login" name="di_enable_login" value="1"' . esc_html( checked( 1, get_option( 'di_enable_login' ), false ) ) ?>>
		<?php
	}

	public function di_auto_add(): void {
		?>
		<input <?php echo 'type="checkbox" id="di_auto_add" name="di_auto_add" value="1"' . esc_html( checked( 1, get_option( 'di_auto_add' ), false ) ) ?>>
		<?php
	}

	public function di_client_id(): void {
		?>
		<input type="text" id="di_client_id" name="di_client_id"
		       value="<?php echo esc_attr( sanitize_option( 'di_redirect_url', get_option( "di_client_id" ) ) ) ?>">
		<?php
	}

	public function di_redirect_url(): void {
		?>
		<input type="url" id="di_redirect_url" name="di_redirect_url"
		       value="<?php echo esc_attr( sanitize_option( 'di_redirect_url', get_option( "di_redirect_url" ) ) ) ?>">
		<?php
	}

	public function di_client_secret(): void {
		?>
		<input type="text" id="di_client_secret" name="di_client_secret"
		       value="<?php echo esc_attr( sanitize_option( 'di_client_secret', get_option( "di_client_secret" ) ) ) ?>">
		<?php
	}

	public function di_guild_id(): void {
		?>
		<input type="text" id="di_guild_id" name="di_guild_id"
		       value="<?php echo esc_attr( sanitize_option( 'di_guild_id', get_option( "di_guild_id" ) ) ) ?>">
		<?php
	}


	public function di_bot_token(): void {
		?>
		<input type="text" id="di_bot_token" name="di_bot_token"
		       value="<?php echo esc_attr( sanitize_option( 'di_bot_token', get_option( "di_bot_token" ) ) ) ?>">
		<?php
	}

	public function displayPluginAdminDashboard(): void {
		?>
		<h1>Discord Integration</h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( "discord_group" );
			do_settings_sections( "discord_menu" );
			submit_button();
			?>
		</form>
		<?php
	}
}

new HDIWPP_DiscordSettings();