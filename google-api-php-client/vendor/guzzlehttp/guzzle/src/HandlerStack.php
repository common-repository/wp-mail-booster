<?php // @codingStandardsIgnoreLine
/**
 * This file to composed Guzzle handler function by stacking middlewares.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */
namespace GuzzleHttp;

use Psr\Http\Message\RequestInterface;

/**
 * Creates a composed Guzzle handler function by stacking middlewares on top of
 * an HTTP handler function.
 */
class HandlerStack {
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $handler.
	 */
	private $handler;
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      array    stack.
	 */
	private $stack = [];
	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $cached.
	 */
	private $cached;

	/**
	 * Creates a default handler stack that can be used by clients.
	 *
	 * The returned handler will wrap the provided handler or use the most
	 * appropriate default handler for you system. The returned HandlerStack has
	 * support for cookies, redirects, HTTP error exceptions, and preparing a body
	 * before sending.
	 *
	 * The returned handler stack can be passed to a client in the "handler"
	 * option.
	 *
	 * @param callable $handler HTTP handler function to use with the stack. If no
	 *                          handler is provided, the best handler for your
	 *                          system will be utilized.
	 *
	 * @return HandlerStack
	 */
	public static function create( callable $handler = null ) {
		$stack = new self( $handler ?: choose_handler() );
		$stack->push( Middleware::httpErrors(), 'http_errors' );
		$stack->push( Middleware::redirect(), 'allow_redirects' );
		$stack->push( Middleware::cookies(), 'cookies' );
		$stack->push( Middleware::prepareBody(), 'prepare_body' );

		return $stack;
	}

	/**
	 * This function is __construct.
	 *
	 * @param callable $handler Underlying HTTP handler.
	 */
	public function __construct( callable $handler = null ) {
		$this->handler = $handler;
	}

	/**
	 * Invokes the handler stack as a composed handler
	 *
	 * @param RequestInterface $request passes parameter as request.
	 * @param array            $options passes parameter as options.
	 */
	public function __invoke( RequestInterface $request, array $options ) {
		$handler = $this->resolve();

		return $handler( $request, $options );
	}

	/**
	 * Dumps a string representation of the stack.
	 *
	 * @return string
	 */
	public function __toString() {
		$depth = 0;
		$stack = [];
		if ( $this->handler ) {
			$stack[] = '0) Handler: ' . $this->debugCallable( $this->handler );
		}

		$result = '';
		foreach ( array_reverse( $this->stack ) as $tuple ) {
			$depth++;
			$str     = "{$depth}) Name: '{$tuple[1]}', ";
			$str    .= 'Function: ' . $this->debugCallable( $tuple[0] );
			$result  = "> {$str}\n{$result}";
			$stack[] = $str;
		}

		foreach ( array_keys( $stack ) as $k ) {
			$result .= "< {$stack[$k]}\n";
		}

		return $result;
	}

	/**
	 * Set the HTTP handler that actually returns a promise.
	 *
	 * @param callable $handler Accepts a request and array of options and
	 *                          returns a Promise.
	 */
	public function setHandler( callable $handler ) {// @codingStandardsIgnoreLine
		$this->handler = $handler;
		$this->cached  = null;
	}

	/**
	 * Returns true if the builder has a handler.
	 *
	 * @return bool
	 */
	public function hasHandler() {// @codingStandardsIgnoreLine
		return (bool) $this->handler;
	}

	/**
	 * Unshift a middleware to the bottom of the stack.
	 *
	 * @param callable $middleware Middleware function.
	 * @param string   $name       Name to register for this middleware.
	 */
	public function unshift( callable $middleware, $name = null ) {
		array_unshift( $this->stack, [ $middleware, $name ] );
		$this->cached = null;
	}

	/**
	 * Push a middleware to the top of the stack.
	 *
	 * @param callable $middleware Middleware function.
	 * @param string   $name       Name to register for this middleware.
	 */
	public function push( callable $middleware, $name = '' ) {
		$this->stack[] = [ $middleware, $name ];
		$this->cached  = null;
	}

	/**
	 * Add a middleware before another middleware by name.
	 *
	 * @param string   $findName   Middleware to find.
	 * @param callable $middleware Middleware function.
	 * @param string   $withName   Name to register for this middleware.
	 */
	public function before( $findName, callable $middleware, $withName = '' ) {// @codingStandardsIgnoreLine
		$this->splice( $findName, $withName, $middleware, true );// @codingStandardsIgnoreLine
	}

	/**
	 * Add a middleware after another middleware by name.
	 *
	 * @param string   $findName   Middleware to find.
	 * @param callable $middleware Middleware function.
	 * @param string   $withName   Name to register for this middleware.
	 */
	public function after( $findName, callable $middleware, $withName = '' ) {// @codingStandardsIgnoreLine
		$this->splice( $findName, $withName, $middleware, false );// @codingStandardsIgnoreLine
	}

	/**
	 * Remove a middleware by instance or name from the stack.
	 *
	 * @param callable|string $remove Middleware to remove by instance or name.
	 */
	public function remove( $remove ) {
		$this->cached = null;
		$idx          = is_callable( $remove ) ? 0 : 1;
		$this->stack  = array_values(
			array_filter(
				$this->stack,
				function ( $tuple ) use ( $idx, $remove ) {
					return $tuple[ $idx ] !== $remove;
				}
			)
		);
	}

	/**
	 * Compose the middleware and handler into a single callable function.
	 *
	 * @return callable
	 * @throws \LogicException On error.
	 */
	public function resolve() {
		if ( ! $this->cached ) {
			if ( ! ( $prev = $this->handler ) ) {// @codingStandardsIgnoreLine
				throw new \LogicException( 'No handler has been specified' );
			}

			foreach ( array_reverse( $this->stack ) as $fn ) {
				$prev = $fn[0]($prev);
			}

			$this->cached = $prev;
		}

		return $this->cached;
	}

	/**
	 * This function is findByName.
	 *
	 * @param string $name passes parameter as name.
	 * @return int
	 * @throws \InvalidArgumentException On error.
	 */
	private function findByName( $name ) {// @codingStandardsIgnoreLine
		foreach ( $this->stack as $k => $v ) {
			if ( $v[1] === $name ) {
				return $k;
			}
		}

		throw new \InvalidArgumentException( "Middleware not found: $name" );
	}

	/**
	 * Splices a function into the middleware list at a specific position.
	 *
	 * @param string   $findName passes parameter as findName.
	 * @param string   $withName passes parameter as $withName.
	 * @param callable $middleware passes parameter as middleware.
	 * @param string   $before passes parameter as before.
	 */
	private function splice( $findName, $withName, callable $middleware, $before ) {// @codingStandardsIgnoreLine
		$this->cached = null;
		$idx          = $this->findByName( $findName );// @codingStandardsIgnoreLine
		$tuple        = [ $middleware, $withName ];// @codingStandardsIgnoreLine

		if ( $before ) {
			if ( 0 === $idx ) {
				array_unshift( $this->stack, $tuple );
			} else {
				$replacement = [ $tuple, $this->stack[ $idx ] ];
				array_splice( $this->stack, $idx, 1, $replacement );
			}
		} elseif ( $idx === count( $this->stack ) - 1 ) {// @codingStandardsIgnoreLine
			$this->stack[] = $tuple;
		} else {
			$replacement = [ $this->stack[ $idx ], $tuple ];
			array_splice( $this->stack, $idx, 1, $replacement );
		}
	}

	/**
	 * Provides a debug string for a given callable.
	 *
	 * @param array|callable $fn Function to write as a string.
	 *
	 * @return string
	 */
	private function debugCallable( $fn ) {// @codingStandardsIgnoreLine
		if ( is_string( $fn ) ) {
			return "callable({$fn})";
		}

		if ( is_array( $fn ) ) {
			return is_string( $fn[0] )
				? "callable({$fn[0]}::{$fn[1]})"
				: "callable(['" . get_class( $fn[0] ) . "', '{$fn[1]}'])";
		}

		return 'callable(' . spl_object_hash( $fn ) . ')';
	}
}
