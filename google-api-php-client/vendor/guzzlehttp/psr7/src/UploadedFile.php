<?php // @codingStandardsIgnoreLine
/**
 * This file for uploaded files.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

namespace GuzzleHttp\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
	/**
	 * This class is uploadedFiles.
	 */
class UploadedFile implements UploadedFileInterface {

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $streams.
	 */
	private static $errors = [
		UPLOAD_ERR_OK,
		UPLOAD_ERR_INI_SIZE,
		UPLOAD_ERR_FORM_SIZE,
		UPLOAD_ERR_PARTIAL,
		UPLOAD_ERR_NO_FILE,
		UPLOAD_ERR_NO_TMP_DIR,
		UPLOAD_ERR_CANT_WRITE,
		UPLOAD_ERR_EXTENSION,
	];

	private $clientFilename;// @codingStandardsIgnoreLine

	private $clientMediaType;// @codingStandardsIgnoreLine
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $error.
	 */
	private $error;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $file.
	 */
	private $file;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      boolean    $moved.
	 */
	private $moved = false;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    $size.
	 */
	private $size;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $stream.
	 */
	private $stream;

	/**
	 * This function is __construct.
	 *
	 * @param string $streamOrFile passes parameter as streamOrFile.
	 * @param string $size passes parameter as size.
	 * @param string $errorStatus passes parameter as errorStatus.
	 * @param string $clientFilename passes parameter as clientFilename.
	 * @param string $clientMediaType passes parameter as clientMediaType.
	 */
	public function __construct(
		$streamOrFile,// @codingStandardsIgnoreLine
		$size,
		$errorStatus,// @codingStandardsIgnoreLine
		$clientFilename = null,// @codingStandardsIgnoreLine
		$clientMediaType = null// @codingStandardsIgnoreLine
	) {
		$this->setError( $errorStatus );// @codingStandardsIgnoreLine
		$this->setSize( $size );
		$this->setClientFilename( $clientFilename );// @codingStandardsIgnoreLine
		$this->setClientMediaType( $clientMediaType );// @codingStandardsIgnoreLine

		if ( $this->isOk() ) {
			$this->setStreamOrFile( $streamOrFile );// @codingStandardsIgnoreLine
		}
	}

	/**
	 * Depending on the value set file or stream variable
	 *
	 * @param mixed $streamOrFile passes parameter as streamOrFile.
	 * @throws InvalidArgumentException On error.
	 */
	private function setStreamOrFile( $streamOrFile ) {// @codingStandardsIgnoreLine
		if ( is_string( $streamOrFile ) ) {// @codingStandardsIgnoreLine
			$this->file = $streamOrFile;// @codingStandardsIgnoreLine
		} elseif ( is_resource( $streamOrFile ) ) {// @codingStandardsIgnoreLine
			$this->stream = new Stream( $streamOrFile );// @codingStandardsIgnoreLine
		} elseif ( $streamOrFile instanceof StreamInterface ) {// @codingStandardsIgnoreLine
			$this->stream = $streamOrFile;// @codingStandardsIgnoreLine
		} else {
			throw new InvalidArgumentException(
				'Invalid stream or file provided for UploadedFile'
			);
		}
	}

	/**
	 * This function is setError.
	 *
	 * @param int $error passes parameter as error.
	 * @throws InvalidArgumentException On error.
	 */
	private function setError( $error ) {
		if ( false === is_int( $error ) ) {
			throw new InvalidArgumentException(
				'Upload file error status must be an integer'
			);
		}

		if ( false === in_array( $error, UploadedFile::$errors ) ) {// @codingStandardsIgnoreLine
			throw new InvalidArgumentException(
				'Invalid error status for UploadedFile'
			);
		}

		$this->error = $error;
	}

	/**
	 * This function is used to set size.
	 *
	 * @param int $size passes parameter as size.
	 * @throws InvalidArgumentException On error.
	 */
	private function setSize( $size ) {
		if ( false === is_int( $size ) ) {
			throw new InvalidArgumentException(
				'Upload file size must be an integer'
			);
		}

		$this->size = $size;
	}

	/**
	 * This function is isStringOrNull.
	 *
	 * @param mixed $param parameter as param.
	 * @return boolean
	 */
	private function isStringOrNull( $param ) {
		return in_array( gettype( $param ), [ 'string', 'NULL' ] );// @codingStandardsIgnoreLine
	}

	/**
	 * This function is isStringNotEmpty.
	 *
	 * @param mixed $param passes parameter as param.
	 * @return boolean
	 */
	private function isStringNotEmpty( $param ) {
		return is_string( $param ) && false === empty( $param );
	}

	/**
	 * This function is setClientFilename.
	 *
	 * @param string|null $clientFilename passes parameter as clientFilename.
	 * @throws InvalidArgumentException On error.
	 */
	private function setClientFilename( $clientFilename ) {// @codingStandardsIgnoreLine
		if ( false === $this->isStringOrNull( $clientFilename ) ) {// @codingStandardsIgnoreLine
			throw new InvalidArgumentException(
				'Upload file client filename must be a string or null'
			);
		}

		$this->clientFilename = $clientFilename;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is setClientMediaType.
	 *
	 * @param string|null $clientMediaType passes parameter as clientMediaType.
	 * @throws InvalidArgumentException On error.
	 */
	private function setClientMediaType( $clientMediaType ) {// @codingStandardsIgnoreLine
		if ( false === $this->isStringOrNull( $clientMediaType ) ) {// @codingStandardsIgnoreLine
			throw new InvalidArgumentException(
				'Upload file client media type must be a string or null'
			);
		}

		$this->clientMediaType = $clientMediaType;// @codingStandardsIgnoreLine
	}

	/**
	 * Return true if there is no upload error
	 *
	 * @return boolean
	 */
	private function isOk() {
		return $this->error === UPLOAD_ERR_OK;// @codingStandardsIgnoreLine
	}

	/**
	 * This function is isMoved.
	 *
	 * @return boolean
	 */
	public function isMoved() {
		return $this->moved;
	}

	/**
	 * This function is validateActive.
	 *
	 * @throws RuntimeException If is moved or not ok.
	 */
	private function validateActive() {
		if ( false === $this->isOk() ) {
			throw new RuntimeException( 'Cannot retrieve stream due to upload error' );
		}

		if ( $this->isMoved() ) {
			throw new RuntimeException( 'Cannot retrieve stream after it has already been moved' );
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws RuntimeException If the upload was not successful.
	 */
	public function getStream() {
		$this->validateActive();

		if ( $this->stream instanceof StreamInterface ) {
			return $this->stream;
		}

		return new LazyOpenStream( $this->file, 'r+' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @see http://php.net/is_uploaded_file
	 * @see http://php.net/move_uploaded_file
	 * @param string $targetPath Path to which to move the uploaded file.
	 * @throws RuntimeException If the upload was not successful.
	 * @throws InvalidArgumentException If the $path specified is invalid.
	 * @throws RuntimeException On any error during the move operation, or on
	 *     the second or subsequent call to the method.
	 */
	public function moveTo( $targetPath ) {// @codingStandardsIgnoreLine
		$this->validateActive();

		if ( false === $this->isStringNotEmpty( $targetPath ) ) {// @codingStandardsIgnoreLine
			throw new InvalidArgumentException(
				'Invalid path provided for move operation; must be a non-empty string'
			);
		}

		if ( $this->file ) {
			$this->moved = php_sapi_name() == 'cli'// WPCS: Loose comparison ok.
				? rename( $this->file, $targetPath )// @codingStandardsIgnoreLine
				: move_uploaded_file( $this->file, $targetPath );// @codingStandardsIgnoreLine
		} else {
			copy_to_stream(
				$this->getStream(),
				new LazyOpenStream( $targetPath, 'w' )// @codingStandardsIgnoreLine
			);

			$this->moved = true;
		}

		if ( false === $this->moved ) {
			throw new RuntimeException(
				sprintf( 'Uploaded file could not be moved to %s', $targetPath )// @codingStandardsIgnoreLine
			);
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return int|null The file size in bytes or null if unknown.
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @see http://php.net/manual/en/features.file-upload.errors.php
	 * @return int One of PHP's UPLOAD_ERR_XXX constants.
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string|null The filename sent by the client or null if none
	 *     was provided.
	 */
	public function getClientFilename() {
		return $this->clientFilename;// @codingStandardsIgnoreLine
	}

	/**
	 * {@inheritdoc}
	 */
	public function getClientMediaType() {
		return $this->clientMediaType;// @codingStandardsIgnoreLine
	}
}
