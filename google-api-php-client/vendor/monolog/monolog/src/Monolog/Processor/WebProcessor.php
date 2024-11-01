<?php //@codingStandardsIgnoreLine
/**
 * This file is WebProcessor.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

namespace Monolog\Processor;

/**
 * Injects url/method and remote IP of the current web request in all records
 */
class WebProcessor {

	/**
	 * Version of this plugin
	 *
	 * @access protected
	 * @var array $serverData
	 */
	protected $serverData;// @codingStandardsIgnoreLine

	/**
	 * Default fields
	 *
	 * Array is structured as [key in record.extra => key in $serverData]
	 *
	 * @var array
	 */
	protected $extraFields = array(// @codingStandardsIgnoreLine
		'url'         => 'REQUEST_URI',
		'ip'          => 'REMOTE_ADDR',
		'http_method' => 'REQUEST_METHOD',
		'server'      => 'SERVER_NAME',
		'referrer'    => 'HTTP_REFERER',
	);

	/**
	 * This function is __construct.
	 *
	 * @param array|\ArrayAccess $serverData  Array or object w/ ArrayAccess that provides access to the $_SERVER data.
	 * @param array|null         $extraFields Field names and the related key inside $serverData to be added. If not provided it defaults to: url, ip, http_method, server, referrer.
	 * @throws \UnexpectedValueException .
	 */
	public function __construct( $serverData = null, array $extraFields = null ) {// @codingStandardsIgnoreLine
		if ( null === $serverData ) {// @codingStandardsIgnoreLine
			$this->serverData = &$_SERVER;// @codingStandardsIgnoreLine
		} elseif ( is_array( $serverData ) || $serverData instanceof \ArrayAccess ) {// @codingStandardsIgnoreLine
			$this->serverData = $serverData;// @codingStandardsIgnoreLine
		} else {
			throw new \UnexpectedValueException( '$serverData must be an array or object implementing ArrayAccess.' );
		}

		if ( null !== $extraFields ) {// @codingStandardsIgnoreLine
			if ( isset( $extraFields[0] ) ) {// @codingStandardsIgnoreLine
				foreach ( array_keys( $this->extraFields ) as $fieldName ) {// @codingStandardsIgnoreLine
					if ( ! in_array( $fieldName, $extraFields ) ) {// @codingStandardsIgnoreLine
						unset( $this->extraFields[ $fieldName ] );// @codingStandardsIgnoreLine
					}
				}
			} else {
				$this->extraFields = $extraFields;// @codingStandardsIgnoreLine
			}
		}
	}

	/**
	 * This function is __invoke.
	 *
	 * @param  array $record passes parameter as record.
	 * @return array
	 */
	public function __invoke( array $record ) {
		// skip processing if for some reason request data
		// is not present (CLI or wonky SAPIs).
		if ( ! isset( $this->serverData['REQUEST_URI'] ) ) {// @codingStandardsIgnoreLine
			return $record;
		}

		$record['extra'] = $this->appendExtraFields( $record['extra'] );

		return $record;
	}

	/**
	 * This function is addExtraField.
	 *
	 * @param  string $extraName passes parameter as extraName.
	 * @param  string $serverName passes parameter as serverName.
	 * @return $this
	 */
	public function addExtraField( $extraName, $serverName ) {// @codingStandardsIgnoreLine
		$this->extraFields[ $extraName ] = $serverName;// @codingStandardsIgnoreLine

		return $this;
	}

	/**
	 * This function is appendExtraFields.
	 *
	 * @param  array $extra passes parameter as extra.
	 * @return array
	 */
	private function appendExtraFields( array $extra ) {// @codingStandardsIgnoreLine
		foreach ( $this->extraFields as $extraName => $serverName ) {// @codingStandardsIgnoreLine
			$extra[ $extraName ] = isset( $this->serverData[ $serverName ] ) ? $this->serverData[ $serverName ] : null;// @codingStandardsIgnoreLine
		}

		if ( isset( $this->serverData['UNIQUE_ID'] ) ) {// @codingStandardsIgnoreLine
			$extra['unique_id'] = $this->serverData['UNIQUE_ID'];// @codingStandardsIgnoreLine
		}

		return $extra;
	}
}
