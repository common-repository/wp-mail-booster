<?php // @codingStandardsIgnoreLine
/**
 * This file Pure-PHP ssh-agent client.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP ssh-agent client.
 *
 * PHP version 5
 */

namespace phpseclib\System\SSH;

use phpseclib\Crypt\RSA;
use phpseclib\System\SSH\Agent\Identity;

/**
 * Pure-PHP ssh-agent client identity factory
 * requestIdentities() method pumps out \phpseclib\System\SSH\Agent\Identity objects
 */
class Agent {

	/**#@+
	 * Message numbers
	 *
	 * @access private
	 */
	// to request SSH1 keys you have to use SSH_AGENTC_REQUEST_RSA_IDENTITIES (1) .
	const SSH_AGENTC_REQUEST_IDENTITIES = 11;
	// this is the SSH2 response; the SSH1 response is SSH_AGENT_RSA_IDENTITIES_ANSWER (2).
	const SSH_AGENT_IDENTITIES_ANSWER = 12;
	// the SSH1 request is SSH_AGENTC_RSA_CHALLENGE (3) .
	const SSH_AGENTC_SIGN_REQUEST = 13;
	// the SSH1 response is SSH_AGENT_RSA_RESPONSE (4) .
	const SSH_AGENT_SIGN_RESPONSE = 14;
	/**#@-*/

	/**
	 * Agent forwarding status
	 *
	 * @access private
	 */
	// no forwarding requested and not active .
	const FORWARD_NONE = 0;
	// request agent forwarding when opportune .
	const FORWARD_REQUEST = 1;
	// forwarding has been request and is active .
	const FORWARD_ACTIVE = 2;
	/**#@-*/

	/**
	 * Unused
	 */
	const SSH_AGENT_FAILURE = 5;

	/**
	 * Socket Resource
	 *
	 * @var resource
	 * @access private
	 */
	var $fsock; // @codingStandardsIgnoreLine

	/**
	 * Agent forwarding status
	 *
	 * @access private
	 * @var $forward_status
	 */
	var $forward_status = self::FORWARD_NONE; // @codingStandardsIgnoreLine

	/**
	 * Buffer for accumulating forwarded authentication
	 * agent data arriving on SSH data channel destined
	 * for agent unix socket
	 *
	 * @var $socket_buffer
	 */
	var $socket_buffer = ''; // @codingStandardsIgnoreLine

	/**
	 * Tracking the number of bytes we are expecting
	 * to arrive for the agent socket on the SSH data
	 * channel .
	 *
	 * @var $expected_bytes
	 */
	var $expected_bytes = 0; // @codingStandardsIgnoreLine

	/**
	 * Default Constructor
	 *
	 * @return \phpseclib\System\SSH\Agent
	 * @access public
	 */
	public function __construct() {
		switch ( true ) {
			case isset( $_SERVER['SSH_AUTH_SOCK'] ) : // @codingStandardsIgnoreLine
				$address = $_SERVER['SSH_AUTH_SOCK']; // @codingStandardsIgnoreLine
				break;
			case isset( $_ENV['SSH_AUTH_SOCK'] ):
				$address = $_ENV['SSH_AUTH_SOCK'];
				break;
			default:
				user_error( 'SSH_AUTH_SOCK not found' );
				return false;
		}

		$this->fsock = fsockopen( 'unix://' . $address, 0, $errno, $errstr ); // @codingStandardsIgnoreLine
		if ( ! $this->fsock ) {
			user_error( "Unable to connect to ssh-agent (Error $errno: $errstr)" );
		}
	}

	/**
	 * Request Identities
	 *
	 * See "2.5.2 Requesting a list of protocol 2 keys"
	 * Returns an array containing zero or more \phpseclib\System\SSH\Agent\Identity objects
	 *
	 * @return array
	 * @access public
	 */
	public function requestIdentities() { // @codingStandardsIgnoreLine
		if ( ! $this->fsock ) {
			return array();
		}

		$packet = pack( 'NC', 1, self::SSH_AGENTC_REQUEST_IDENTITIES );
		if ( strlen( $packet ) != fputs( $this->fsock, $packet ) ) { // @codingStandardsIgnoreLine
			user_error( 'Connection closed while requesting identities' );
		}

		$length = current( unpack( 'N', fread( $this->fsock, 4 ) ) ); // @codingStandardsIgnoreLine
		$type   = ord( fread( $this->fsock, 1 ) ); // @codingStandardsIgnoreLine
		if ( $type != self::SSH_AGENT_IDENTITIES_ANSWER ) { // @codingStandardsIgnoreLine
			user_error( 'Unable to request identities' );
		}

		$identities = array();
		$keyCount   = current( unpack( 'N', fread( $this->fsock, 4 ) ) ); // @codingStandardsIgnoreLine
		for ( $i = 0; $i < $keyCount; $i++ ) { // @codingStandardsIgnoreLine
			$length   = current( unpack( 'N', fread( $this->fsock, 4 ) ) ); // @codingStandardsIgnoreLine
			$key_blob = fread( $this->fsock, $length ); // @codingStandardsIgnoreLine
			$key_str  = 'ssh-rsa ' . base64_encode( $key_blob );
			$length   = current( unpack( 'N', fread( $this->fsock, 4 ) ) ); // @codingStandardsIgnoreLine
			if ( $length ) {
				$key_str .= ' ' . fread( $this->fsock, $length ); // @codingStandardsIgnoreLine
			}
			$length   = current( unpack( 'N', substr( $key_blob, 0, 4 ) ) );
			$key_type = substr( $key_blob, 4, $length );
			switch ( $key_type ) {
				case 'ssh-rsa':
					$key = new RSA();
					$key->loadKey( $key_str );
					break;
				case 'ssh-dss':
					// not currently supported .
					break;
			}
			// resources are passed by reference by default .
			if ( isset( $key ) ) {
				$identity = new Identity( $this->fsock );
				$identity->setPublicKey( $key );
				$identity->setPublicKeyBlob( $key_blob );
				$identities[] = $identity;
				unset( $key );
			}
		}

		return $identities;
	}

	/**
	 * Signal that agent forwarding should
	 * be requested when a channel is opened
	 *
	 * @param Net_SSH2 $ssh .
	 * @access public
	 */
	public function startSSHForwarding( $ssh ) { // @codingStandardsIgnoreLine
		if ( $this->forward_status == self::FORWARD_NONE ) { // @codingStandardsIgnoreLine
			$this->forward_status = self::FORWARD_REQUEST;
		}
	}

	/**
	 * Request agent forwarding of remote server
	 *
	 * @param Net_SSH2 $ssh .
	 * @return bool
	 * @access private
	 */
	private function _request_forwarding( $ssh ) { // @codingStandardsIgnoreLine
		$request_channel = $ssh->_get_open_channel();
		if ( false === $request_channel ) {
			return false;
		}

		$packet = pack(
			'CNNa*C',
			NET_SSH2_MSG_CHANNEL_REQUEST,
			$ssh->server_channels[ $request_channel ],
			strlen( 'auth-agent-req@openssh.com' ),
			'auth-agent-req@openssh.com',
			1
		);

		$ssh->channel_status[ $request_channel ] = NET_SSH2_MSG_CHANNEL_REQUEST;

		if ( ! $ssh->_send_binary_packet( $packet ) ) {
			return false;
		}

		$response = $ssh->_get_channel_packet( $request_channel );
		if ( false === $response ) {
			return false;
		}

		$ssh->channel_status[ $request_channel ] = NET_SSH2_MSG_CHANNEL_OPEN;
		$this->forward_status                    = self::FORWARD_ACTIVE;

		return true;
	}

	/**
	 * On successful channel open
	 *
	 * This method is called upon successful channel
	 * open to give the SSH Agent an opportunity
	 * to take further action. i.e. request agent forwarding
	 *
	 * @param Net_SSH2 $ssh .
	 * @access private
	 */
	private function _on_channel_open( $ssh ) { // @codingStandardsIgnoreLine
		if ( $this->forward_status == self::FORWARD_REQUEST ) { // @codingStandardsIgnoreLine
			$this->_request_forwarding( $ssh );
		}
	}

	/**
	 * Forward data to SSH Agent and return data reply
	 *
	 * @param string $data .
	 * @return data from SSH Agent
	 * @access private
	 */
	private function _forward_data( $data ) { // @codingStandardsIgnoreLine
		if ( $this->expected_bytes > 0 ) {
			$this->socket_buffer  .= $data;
			$this->expected_bytes -= strlen( $data );
		} else {
			$agent_data_bytes    = current( unpack( 'N', $data ) );
			$current_data_bytes  = strlen( $data );
			$this->socket_buffer = $data;
			if ( $current_data_bytes != $agent_data_bytes + 4 ) { // WPCS:Loose comparison ok .
				$this->expected_bytes = ( $agent_data_bytes + 4 ) - $current_data_bytes;
				return false;
			}
		}

		if ( strlen( $this->socket_buffer ) != fwrite( $this->fsock, $this->socket_buffer ) ) { // @codingStandardsIgnoreLine
			user_error( 'Connection closed attempting to forward data to SSH agent' );
		}

		$this->socket_buffer  = '';
		$this->expected_bytes = 0;

		$agent_reply_bytes = current( unpack( 'N', fread( $this->fsock, 4 ) ) ); // @codingStandardsIgnoreLine

		$agent_reply_data = fread( $this->fsock, $agent_reply_bytes ); // @codingStandardsIgnoreLine
		$agent_reply_data = current( unpack( 'a*', $agent_reply_data ) );

		return pack( 'Na*', $agent_reply_bytes, $agent_reply_data );
	}
}
