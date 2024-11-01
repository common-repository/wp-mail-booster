<?php // @codingStandardsIgnoreLine
/**
 * This file is part of Composer.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor/composer
 * @version 2.0.0
 */

/**
 * This file is part of Composer.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Autoload;

/**
 * ClassLoader implements a PSR-0, PSR-4 and classmap class loader.
 *
 * In this example, if you try to use a class in the Symfony\Component
 * namespace or one of its children (Symfony\Component\Console for instance),
 * the autoloader will first look for the class under the component/
 * directory, and it will then fallback to the framework/ directory if not
 * found before giving up.
 *
 * This class is loosely based on the Symfony UniversalClassLoader.
 *
 * @see    http://www.php-fig.org/psr/psr-0/
 * @see    http://www.php-fig.org/psr/psr-4/
 */
class ClassLoader {

	// PSR-4 .
	/**
	 * Variable for prefix length of psr4
	 *
	 * @var $prefixLengthsPsr4
	 */
	private $prefixLengthsPsr4 = array();// @codingStandardsIgnoreLine
	/**
	 * Variable for prefix dir psr4
	 *
	 * @var $prefixDirsPsr4
	 */
	private $prefixDirsPsr4 = array();// @codingStandardsIgnoreLine
	/**
	 * Variable for fallback dir psr4
	 *
	 * @var $fallbackDirsPsr4
	 */
	private $fallbackDirsPsr4 = array();// @codingStandardsIgnoreLine

	// PSR-0 .
	/**
	 * Variable for prefix psr0
	 *
	 * @var $prefixesPsr0
	 */
	private $prefixesPsr0 = array();// @codingStandardsIgnoreLine
	/**
	 * Variable for fall back dir psr0
	 *
	 * @var $fallbackDirsPsr0
	 */
	private $fallbackDirsPsr0 = array();// @codingStandardsIgnoreLine
	/**
	 * Variable for use include path
	 *
	 * @var $useIncludePath
	 */
	private $useIncludePath = false;// @codingStandardsIgnoreLine
	/**
	 * Variable for class map
	 *
	 * @var $classMap
	 */
	private $classMap = array();// @codingStandardsIgnoreLine
	/**
	 * Variable for class map authoritative
	 *
	 * @var $classMapAuthoritative
	 */
	private $classMapAuthoritative = false;// @codingStandardsIgnoreLine
	/**
	 * Variable for missing clases
	 *
	 * @var $missingClasses
	 */
	private $missingClasses = array();// @codingStandardsIgnoreLine
	/**
	 * Variable for ap cu prefix
	 *
	 * @var $apcuPrefix
	 */
	private $apcuPrefix;// @codingStandardsIgnoreLine
	/**
	 * This function is used to get prefix
	 */
	public function getPrefixes() { // @codingStandardsIgnoreLine
		if ( ! empty( $this->prefixesPsr0 ) ) { // @codingStandardsIgnoreLine
			return call_user_func_array( 'array_merge', $this->prefixesPsr0 ); // @codingStandardsIgnoreLine
		}

		return array();
	}
	/**
	 * This function is used to get prefix psr4
	 */
	public function getPrefixesPsr4() { // @codingStandardsIgnoreLine
		return $this->prefixDirsPsr4; // @codingStandardsIgnoreLine
	}
	/**
	 * This function is used to get fallbackdir
	 */
	public function getFallbackDirs() { // @codingStandardsIgnoreLine
		return $this->fallbackDirsPsr0; // @codingStandardsIgnoreLine
	}
	/**
	 * This function is used to get fall backdirpsr
	 */
	public function getFallbackDirsPsr4() { // @codingStandardsIgnoreLine
		return $this->fallbackDirsPsr4; // @codingStandardsIgnoreLine
	}
	/**
	 * This function is used to get class map
	 */
	public function getClassMap() { // @codingStandardsIgnoreLine
		return $this->classMap; // @codingStandardsIgnoreLine
	}

	/**
	 * This function is used to add class map
	 *
	 * @param array $classMap Class to filename map .
	 */
	public function addClassMap( array $classMap ) { //@codingStandardsIgnoreLine
		if ( $this->classMap ) { // @codingStandardsIgnoreLine
			$this->classMap = array_merge( $this->classMap, $classMap ); // @codingStandardsIgnoreLine
		} else {
			$this->classMap = $classMap; // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Registers a set of PSR-0 directories for a given prefix, either
	 * appending or prepending to the ones previously set for this prefix.
	 *
	 * @param string       $prefix  The prefix .
	 * @param array|string $paths   The PSR-0 root directories .
	 * @param bool         $prepend Whether to prepend the directories .
	 */
	public function add( $prefix, $paths, $prepend = false ) {
		if ( ! $prefix ) {
			if ( $prepend ) {
				$this->fallbackDirsPsr0 = array_merge( // @codingStandardsIgnoreLine
					(array) $paths,
					$this->fallbackDirsPsr0 // @codingStandardsIgnoreLine
				);
			} else {
				$this->fallbackDirsPsr0 = array_merge( // @codingStandardsIgnoreLine
					$this->fallbackDirsPsr0, // @codingStandardsIgnoreLine
					(array) $paths
				);
			}

			return;
		}

		$first = $prefix[0];
		if ( ! isset( $this->prefixesPsr0[ $first ][ $prefix ] ) ) { // @codingStandardsIgnoreLine
			$this->prefixesPsr0[ $first ][ $prefix ] = (array) $paths; // @codingStandardsIgnoreLine

			return;
		}
		if ( $prepend ) {
			$this->prefixesPsr0[ $first ][ $prefix ] = array_merge( // @codingStandardsIgnoreLine
				(array) $paths,
				$this->prefixesPsr0[ $first ][ $prefix ] // @codingStandardsIgnoreLine
			);
		} else {
			$this->prefixesPsr0[ $first ][ $prefix ] = array_merge( // @codingStandardsIgnoreLine
				$this->prefixesPsr0[ $first ][ $prefix ], // @codingStandardsIgnoreLine
				(array) $paths
			);
		}
	}

	/**
	 * Registers a set of PSR-4 directories for a given namespace, either
	 * appending or prepending to the ones previously set for this namespace.
	 *
	 * @param string       $prefix  The prefix/namespace, with trailing '\\' .
	 * @param array|string $paths   The PSR-4 base directories .
	 * @param bool         $prepend Whether to prepend the directories .
	 *
	 * @throws \InvalidArgumentException .
	 */
	public function addPsr4( $prefix, $paths, $prepend = false ) { // @codingStandardsIgnoreLine
		if ( ! $prefix ) {
			// Register directories for the root namespace.
			if ( $prepend ) {
				$this->fallbackDirsPsr4 = array_merge( // @codingStandardsIgnoreLine
					(array) $paths,
					$this->fallbackDirsPsr4 // @codingStandardsIgnoreLine
				);
			} else {
				$this->fallbackDirsPsr4 = array_merge( // @codingStandardsIgnoreLine
					$this->fallbackDirsPsr4, // @codingStandardsIgnoreLine
					(array) $paths
				);
			}
		} elseif ( ! isset( $this->prefixDirsPsr4[ $prefix ] ) ) { // @codingStandardsIgnoreLine
			// Register directories for a new namespace.
			$length = strlen( $prefix );
			if ( '\\' !== $prefix[ $length - 1 ] ) {
				throw new \InvalidArgumentException( 'A non-empty PSR-4 prefix must end with a namespace separator.' );
			}
			$this->prefixLengthsPsr4[ $prefix[0] ][ $prefix ] = $length; // @codingStandardsIgnoreLine
			$this->prefixDirsPsr4[ $prefix ]                  = (array) $paths; // @codingStandardsIgnoreLine
		} elseif ( $prepend ) {
			// Prepend directories for an already registered namespace.
			$this->prefixDirsPsr4[ $prefix ] = array_merge( // @codingStandardsIgnoreLine
				(array) $paths,
				$this->prefixDirsPsr4[ $prefix ] // @codingStandardsIgnoreLine
			);
		} else {
			// Append directories for an already registered namespace.
			$this->prefixDirsPsr4[ $prefix ] = array_merge( // @codingStandardsIgnoreLine
				$this->prefixDirsPsr4[ $prefix ], // @codingStandardsIgnoreLine
				(array) $paths
			);
		}
	}

	/**
	 * Registers a set of PSR-0 directories for a given prefix,
	 * replacing any others previously set for this prefix.
	 *
	 * @param string       $prefix The prefix .
	 * @param array|string $paths  The PSR-0 base directories .
	 */
	public function set( $prefix, $paths ) {
		if ( ! $prefix ) {
			$this->fallbackDirsPsr0 = (array) $paths; // @codingStandardsIgnoreLine
		} else {
			$this->prefixesPsr0[ $prefix[0] ][ $prefix ] = (array) $paths; // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Registers a set of PSR-4 directories for a given namespace,
	 * replacing any others previously set for this namespace.
	 *
	 * @param string       $prefix The prefix/namespace, with trailing '\\' .
	 * @param array|string $paths  The PSR-4 base directories .
	 *
	 * @throws \InvalidArgumentException .
	 */
	public function setPsr4( $prefix, $paths ) { // @codingStandardsIgnoreLine
		if ( ! $prefix ) {
			$this->fallbackDirsPsr4 = (array) $paths; // @codingStandardsIgnoreLine
		} else {
			$length = strlen( $prefix );
			if ( '\\' !== $prefix[ $length - 1 ] ) {
				throw new \InvalidArgumentException( 'A non-empty PSR-4 prefix must end with a namespace separator.' );
			}
			$this->prefixLengthsPsr4[ $prefix[0] ][ $prefix ] = $length; // @codingStandardsIgnoreLine
			$this->prefixDirsPsr4[ $prefix ]                  = (array) $paths; // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Turns on searching the include path for class files.
	 *
	 * @param bool $useIncludePath .
	 */
	public function setUseIncludePath( $useIncludePath ) { // @codingStandardsIgnoreLine
		$this->useIncludePath = $useIncludePath; // @codingStandardsIgnoreLine
	}

	/**
	 * Can be used to check if the autoloader uses the include path to check
	 * for classes.
	 *
	 * @return bool
	 */
	public function getUseIncludePath() { // @codingStandardsIgnoreLine
		return $this->useIncludePath; // @codingStandardsIgnoreLine
	}

	/**
	 * Turns off searching the prefix and fallback directories for classes
	 * that have not been registered with the class map.
	 *
	 * @param bool $classMapAuthoritative .
	 */
	public function setClassMapAuthoritative( $classMapAuthoritative ) { // @codingStandardsIgnoreLine
		$this->classMapAuthoritative = $classMapAuthoritative; // @codingStandardsIgnoreLine
	}

	/**
	 * Should class lookup fail if not found in the current class map?
	 *
	 * @return bool
	 */
	public function isClassMapAuthoritative() { // @codingStandardsIgnoreLine
		return $this->classMapAuthoritative; // @codingStandardsIgnoreLine
	}

	/**
	 * APCu prefix to use to cache found/not-found classes, if the extension is enabled.
	 *
	 * @param string|null $apcuPrefix .
	 */
	public function setApcuPrefix( $apcuPrefix ) { // @codingStandardsIgnoreLine
		$this->apcuPrefix = function_exists( 'apcu_fetch' ) && ini_get( 'apc.enabled' ) ? $apcuPrefix : null; // @codingStandardsIgnoreLine
	}

	/**
	 * The APCu prefix in use, or null if APCu caching is not enabled.
	 *
	 * @return string|null
	 */
	public function getApcuPrefix() { // @codingStandardsIgnoreLine
		return $this->apcuPrefix; // @codingStandardsIgnoreLine
	}

	/**
	 * Registers this instance as an autoloader.
	 *
	 * @param bool $prepend Whether to prepend the autoloader or not .
	 */
	public function register( $prepend = false ) {
		spl_autoload_register( array( $this, 'loadClass' ), true, $prepend );
	}

	/**
	 * Unregisters this instance as an autoloader.
	 */
	public function unregister() {
		spl_autoload_unregister( array( $this, 'loadClass' ) );
	}

	/**
	 * Loads the given class or interface.
	 *
	 * @param  string $class The name of the class .
	 * @return bool|null True if loaded, null otherwise .
	 */
	public function loadClass( $class ) { // @codingStandardsIgnoreLine
		if ( $file = $this->findFile( $class ) ) { // @codingStandardsIgnoreLine
			includeFile( $file );

			return true;
		}
	}

	/**
	 * Finds the path to the file where the class is defined.
	 *
	 * @param string $class The name of the class .
	 *
	 * @return string|false The path if found, false otherwise
	 */
	public function findFile( $class ) { // @codingStandardsIgnoreLine
		// class map lookup .
		if ( isset( $this->classMap[ $class ] ) ) { // @codingStandardsIgnoreLine
			return $this->classMap[ $class ]; // @codingStandardsIgnoreLine
		}
		if ( $this->classMapAuthoritative || isset( $this->missingClasses[ $class ] ) ) { // @codingStandardsIgnoreLine
			return false;
		}
		if ( null !== $this->apcuPrefix ) { // @codingStandardsIgnoreLine
			$file = apcu_fetch( $this->apcuPrefix . $class, $hit ); // @codingStandardsIgnoreLine
			if ( $hit ) {
				return $file;
			}
		}

		$file = $this->findFileWithExtension( $class, '.php' );

		// Search for Hack files if we are running on HHVM .
		if ( false === $file && defined( 'HHVM_VERSION' ) ) {
			$file = $this->findFileWithExtension( $class, '.hh' );
		}

		if ( null !== $this->apcuPrefix ) { // @codingStandardsIgnoreLine
			apcu_add( $this->apcuPrefix . $class, $file ); // @codingStandardsIgnoreLine
		}

		if ( false === $file ) {
			// Remember that this class does not exist.
			$this->missingClasses[ $class ] = true; // @codingStandardsIgnoreLine
		}

		return $file;
	}
	/**
	 * This function is to find file with extension
	 *
	 * @param array  $class .
	 * @param string $ext .
	 */
	private function findFileWithExtension( $class, $ext ) { // @codingStandardsIgnoreLine
		// PSR-4 lookup .
		$logicalPathPsr4 = strtr( $class, '\\', DIRECTORY_SEPARATOR ) . $ext; // @codingStandardsIgnoreLine

		$first = $class[0];
		if ( isset( $this->prefixLengthsPsr4[ $first ] ) ) { // @codingStandardsIgnoreLine
			$subPath = $class; // @codingStandardsIgnoreLine
			while ( false !== $lastPos = strrpos( $subPath, '\\' ) ) { // @codingStandardsIgnoreLine
				$subPath = substr( $subPath, 0, $lastPos ); // @codingStandardsIgnoreLine
				$search  = $subPath . '\\'; // @codingStandardsIgnoreLine
				if ( isset( $this->prefixDirsPsr4[ $search ] ) ) { // @codingStandardsIgnoreLine
					foreach ( $this->prefixDirsPsr4[ $search ] as $dir ) { // @codingStandardsIgnoreLine
						$length = $this->prefixLengthsPsr4[ $first ][ $search ]; // @codingStandardsIgnoreLine
						if ( file_exists( $file = $dir . DIRECTORY_SEPARATOR . substr( $logicalPathPsr4, $length ) ) ) { // @codingStandardsIgnoreLine
							return $file;
						}
					}
				}
			}
		}

		// PSR-4 fallback dirs .
		foreach ( $this->fallbackDirsPsr4 as $dir ) { // @codingStandardsIgnoreLine
			if ( file_exists( $file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4 ) ) { // @codingStandardsIgnoreLine
				return $file;
			}
		}

		// PSR-0 lookup .
		if ( false !== $pos = strrpos( $class, '\\' ) ) { // @codingStandardsIgnoreLine
			// namespaced class name .
			$logicalPathPsr0 = substr( $logicalPathPsr4, 0, $pos + 1 ) // @codingStandardsIgnoreLine
				. strtr( substr( $logicalPathPsr4, $pos + 1 ), '_', DIRECTORY_SEPARATOR ); // @codingStandardsIgnoreLine
		} else {
			// PEAR-like class name .
			$logicalPathPsr0 = strtr( $class, '_', DIRECTORY_SEPARATOR ) . $ext; // @codingStandardsIgnoreLine
		}

		if ( isset( $this->prefixesPsr0[ $first ] ) ) {  // @codingStandardsIgnoreLine
			foreach ( $this->prefixesPsr0[ $first ] as $prefix => $dirs ) { // @codingStandardsIgnoreLine
				if ( 0 === strpos( $class, $prefix ) ) {
					foreach ( $dirs as $dir ) {
						if ( file_exists( $file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0 ) ) { // @codingStandardsIgnoreLine
							return $file;
						}
					}
				}
			}
		}

		// PSR-0 fallback dirs .
		foreach ( $this->fallbackDirsPsr0 as $dir ) { // @codingStandardsIgnoreLine
			if ( file_exists( $file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0 ) ) { // @codingStandardsIgnoreLine
				return $file;
			}
		}

		// PSR-0 include paths.
		if ( $this->useIncludePath && $file = stream_resolve_include_path( $logicalPathPsr0 ) ) {  // @codingStandardsIgnoreLine
			return $file;
		}

		return false;
	}
}

/**
 * Scope isolated include.
 * Prevents access to $this/self from included files.
 *
 * @param string $file .
 */
function includeFile( $file ) { // @codingStandardsIgnoreLine
	include $file;
}
