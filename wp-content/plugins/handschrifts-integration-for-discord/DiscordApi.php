<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HDIWPP_DiscordApi {
	public function __construct() {

	}

	public function addMemberToServer( string $token, string $member, array $roles ): void {
		$url      = 'https://discordapp.com/api/guilds/' . get_option( "di_guild_id" ) . '/members/' . $member;
		$botToken = get_option( "di_bot_token" );

		$args = array(
			'method'  => 'PUT',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => ' Bot ' . $botToken
			),
			'body'    => wp_json_encode( array(
				"access_token" => $token,
				"roles"        => $roles
			) )
		);

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		}

	}

	public function isMemberOfGuild( string $member ): bool {
		$url      = 'https://discordapp.com/api/guilds/' . get_option( "di_guild_id" ) . '/members/' . $member;
		$botToken = get_option( "di_bot_token" );
		$args     = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => ' Bot ' . $botToken
			),
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		}

		return ! array_key_exists( 'code', json_decode( wp_remote_retrieve_body( $response ), true ) );
	}

	public function addRoleToMember( string $member, string $role ): void {
		$url      = 'https://discordapp.com/api/guilds/' . get_option( "di_guild_id" ) . '/members/' . $member . "/roles/" . $role;
		$botToken = get_option( "di_bot_token" );

		$args = array(
			'method'  => 'PUT',
			'headers' => array(
				'Content-Type'   => 'application/json',
				'Authorization'  => 'Bot ' . $botToken,
				'Content-Length' => '0'
			)
		);

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		}
	}

	public function removeRoleFromMember( string $member, string $role ): void {
		$url      = 'https://discordapp.com/api/guilds/' . get_option( "di_guild_id" ) . '/members/' . $member . "/roles/" . $role;
		$botToken = get_option( "di_bot_token" );

		$args = array(
			'method'  => 'DELETE',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $botToken
			)
		);

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );
		}

	}

	public function getGuildRoles(): array {
		$url      = 'https://discordapp.com/api/guilds/' . get_option( "di_guild_id" ) . '/roles';
		$botToken = get_option( "di_bot_token" );

		$args = array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bot ' . $botToken
			)
		);

		$response = wp_remote_get( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( $response->get_error_message() );

			return array();
		} else {
			return json_decode( wp_remote_retrieve_body( $response ), true );
		}
	}
}