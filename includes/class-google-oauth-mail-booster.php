<?php
/**
 * This file is used to Check the credentials for the access token .
 *
 * @author  Tech Banker
 * @package wp-mail-booster/includes
 * @version 2.0.0
 */

/**
 * This class is used to Check the credentials for the access token.
 */
class Google_Oauth_Mail_Booster {
	private $oauthUserEmail    = ''; // @codingStandardsIgnoreLine
	private $oauthRefreshToken = ''; // @codingStandardsIgnoreLine
	private $oauthClientId     = ''; // @codingStandardsIgnoreLine
	private $oauthClientSecret = ''; // @codingStandardsIgnoreLine

	/**
	 * Public Constructor
	 *
	 * @param string $UserEmail .
	 * @param string $ClientSecret .
	 * @param string $ClientId .
	 * @param string $RefreshToken .
	 */
	public function __construct(
		$UserEmail, // @codingStandardsIgnoreLine
		$ClientSecret, // @codingStandardsIgnoreLine
		$ClientId, // @codingStandardsIgnoreLine
		$RefreshToken // @codingStandardsIgnoreLine
	) {
			$this->oauthClientId     = $ClientId; // @codingStandardsIgnoreLine
			$this->oauthClientSecret = $ClientSecret; // @codingStandardsIgnoreLine
			$this->oauthRefreshToken = $RefreshToken; // @codingStandardsIgnoreLine
			$this->oauthUserEmail    = $UserEmail; // @codingStandardsIgnoreLine
	}

	/**
	 * Set Client id.
	 */
	public static function getClient() { // @codingStandardsIgnoreLine
		global $wpdb;
		$mb_table_prefix = $wpdb->prefix;
		if ( is_multisite() ) {
			$get_other_settings_meta_value    = $wpdb->get_var(
				$wpdb->prepare(
					'SELECT meta_value FROM ' . $wpdb->base_prefix . 'mail_booster_meta WHERE meta_key=%s', 'settings'
				)
			);// WPCS: db call ok; no-cache ok.
			$other_settings_unserialized_data = maybe_unserialize( $get_other_settings_meta_value );
			if ( isset( $other_settings_unserialized_data['fetch_settings'] ) && 'network_site' === $other_settings_unserialized_data['fetch_settings'] ) {
				$mb_table_prefix = $wpdb->base_prefix;
			}
		}
		$email_configuration_data  = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT meta_value FROM ' . $mb_table_prefix . 'mail_booster_meta WHERE meta_key=%s', 'email_configuration'
			)
		);// WPCS: db call ok; no-cache ok, unprepared SQL ok.
		$email_configuration_array = maybe_unserialize( $email_configuration_data );

		$google_client = new \Google_Client();
		$clientId     = $email_configuration_array['client_id']; // @codingStandardsIgnoreLine
		$clientSecret = $email_configuration_array['client_secret']; // @codingStandardsIgnoreLine
		$google_client->setScopes( 'https://mail.google.com/' );
		$google_client->setClientId( $clientId ); // @codingStandardsIgnoreLine
		$google_client->setClientSecret( $clientSecret ); // @codingStandardsIgnoreLine
		$redirect_url = admin_url( 'admin.ajax.php' );
		$google_client->setRedirectUri( $redirect_url );
		$google_client->setAccessType( 'offline' );
		return $google_client;
	}

	/**
	 * Checks the credentials for the access token, if present; it returns that
	 * or refreshes it if expired.
	 * if the credentials file is empty, it will return the authorization url to which you must redirect too
	 * for user user authorization
	 */
	public static function authenticate() {

		$client         = Google_Oauth_Mail_Booster::getClient();
		$get_token_data = get_option( 'mail_booster_auth' );
		if ( ! empty( $get_token_data ) ) {
			$accessToken = $get_token_data; // @codingStandardsIgnoreLine
		} else {
			return array( 'authorization_uri' => $client->createAuthUrl() );
		}
		$client->setAccessToken( $accessToken ); // @codingStandardsIgnoreLine
		if ( $client->isAccessTokenExpired() ) {
			$client->refreshToken( $client->getRefreshToken() );
			$new_accessToken = $client->getAccessToken(); // @codingStandardsIgnoreLine
			$token_data = $new_accessToken; // @codingStandardsIgnoreLine
			update_option( 'mail_booster_auth', $token_data );
			return json_decode( $new_accessToken, true ); // @codingStandardsIgnoreLine
		}
		return $accessToken; // @codingStandardsIgnoreLine
	}
	/**
	 * Call this in your callback (redirect url), code the authorization for and exchanges it for an
	 * access token.
	 * it stores this in the token file for future reference.
	 * if the user denies your app access, it will still return just that error and not write to the token file
	 *
	 * @param string $authCode .
	 */
	public static function resetCredentials( $authCode ) { // @codingStandardsIgnoreLine
		$client      = Google_Oauth_Mail_Booster::getClient();
		$accessToken = $client->authenticate( $authCode ); // @codingStandardsIgnoreLine
		// $options = gmail_smtp_get_option();
		if ( ! empty( $accessToken ) ) { // @codingStandardsIgnoreLine
			if ( isset( $accessToken['error'] ) || isset( $accessToken['error_description'] ) ) { // @codingStandardsIgnoreLine
				echo '<div id="message" class="error"><p><strong>';
				echo __( 'Error: ' . $accessToken['error'] . ', Error Description: ' . $accessToken['error_description'], 'gmail-smtp' ); // @codingStandardsIgnoreLine
				echo '</strong></p></div>';
				return false;
			}
			$token_data = $new_accessToken; // @codingStandardsIgnoreLine
			update_option( 'mail_booster_auth', $token_data );
			return $accessToken; // @codingStandardsIgnoreLine
		}
		return false;
	}

	/**
	 * GetOauth64
	 *
	 * Encode the user email related to this request along with the token in base64
	 * this is used for authentication, in the phpmailer smtp class
	 *
	 * @return string
	 */
	public function getOauth64() { // @codingStandardsIgnoreLine
		$client         = Google_Oauth_Mail_Booster::getClient();
		$get_token_data = get_option( 'mail_booster_auth' );
		if ( ! empty( $get_token_data ) ) {
			$accessToken = $get_token_data; // @codingStandardsIgnoreLine
		} else {
			return false;
		}
		$client->setAccessToken( $accessToken ); // @codingStandardsIgnoreLine
		if ( $client->isAccessTokenExpired() ) {
			$client->refreshToken( $client->getRefreshToken() );
			$accessToken = $client->getAccessToken(); // @codingStandardsIgnoreLine
			$token_data = $accessToken; // @codingStandardsIgnoreLine
			update_option( 'mail_booster_auth', $token_data );
		}
		$offlineToken = Google_Oauth_Mail_Booster::request_offline_token(); // @codingStandardsIgnoreLine
		return base64_encode( 'user=' . $this->oauthUserEmail . "\001auth=Bearer " . $offlineToken . "\001\001" ); // @codingStandardsIgnoreLine
	}

	/**
	 * This makes a request to the Google API, using Curl to get another access token that we can use
	 * for authentication on the Gmail API when sending messages
	 */
	private function request_offline_token() {
		$token_uri  = 'https://accounts.google.com/o/oauth2/token';
		$parameters = array(
			'grant_type'    => 'refresh_token',
			'client_id'     => $this->oauthClientId, // @codingStandardsIgnoreLine
			'client_secret' => $this->oauthClientSecret, // @codingStandardsIgnoreLine
			'refresh_token' => $this->oauthRefreshToken, // @codingStandardsIgnoreLine
		);
		$curl = curl_init( $token_uri ); // @codingStandardsIgnoreLine
		curl_setopt( $curl, CURLOPT_POST, true ); // @codingStandardsIgnoreLine
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $parameters ); // @codingStandardsIgnoreLine
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY ); // @codingStandardsIgnoreLine
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false ); // @codingStandardsIgnoreLine
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 ); // @codingStandardsIgnoreLine
		$response = curl_exec( $curl ); // @codingStandardsIgnoreLine
		curl_close( $curl ); // @codingStandardsIgnoreLine
		$response = json_decode( $response, true );
		return $response['access_token'];
	}
}
