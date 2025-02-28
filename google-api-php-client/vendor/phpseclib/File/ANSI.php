<?php // @codingStandardsIgnoreLine
/**
 * This file Pure-PHP ANSI Decoder.
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/vendor
 * @version 2.0.0
 */

/**
 * Pure-PHP ANSI Decoder
 *
 * PHP version 5
 *
 * If you call read() in \phpseclib\Net\SSH2 you may get {@link http://en.wikipedia.org/wiki/ANSI_escape_code ANSI escape codes} back.
 * They'd look like chr(0x1B) . '[00m' or whatever (0x1B = ESC).  They tell a
 * {@link http://en.wikipedia.org/wiki/Terminal_emulator terminal emulator} how to format the characters, what
 * color to display them in, etc. \phpseclib\File\ANSI is a {@link http://en.wikipedia.org/wiki/VT100 VT100} terminal emulator.
 */

namespace phpseclib\File;

/**
 * Pure-PHP ANSI Decoder
 *
 * @access  public
 */
class ANSI {

	/**
	 * Max Width
	 *
	 * @var int
	 * @access private
	 */
	var $max_x; // @codingStandardsIgnoreLine

	/**
	 * Max Height
	 *
	 * @var int
	 * @access private
	 */
	var $max_y; // @codingStandardsIgnoreLine

	/**
	 * Max History
	 *
	 * @var int
	 * @access private
	 */
	var $max_history; // @codingStandardsIgnoreLine

	/**
	 * History
	 *
	 * @var array
	 * @access private
	 */
	var $history; // @codingStandardsIgnoreLine

	/**
	 * History Attributes
	 *
	 * @var array
	 * @access private
	 */
	var $history_attrs; // @codingStandardsIgnoreLine

	/**
	 * Current Column
	 *
	 * @var int
	 * @access private
	 */
	var $x; // @codingStandardsIgnoreLine

	/**
	 * Current Row
	 *
	 * @var int
	 * @access private
	 */
	var $y; // @codingStandardsIgnoreLine

	/**
	 * Old Column
	 *
	 * @var int
	 * @access private
	 */
	var $old_x; // @codingStandardsIgnoreLine

	/**
	 * Old Row
	 *
	 * @var int
	 * @access private
	 */
	var $old_y; // @codingStandardsIgnoreLine

	/**
	 * An empty attribute cell
	 *
	 * @var object
	 * @access private
	 */
	var $base_attr_cell; // @codingStandardsIgnoreLine

	/**
	 * The current attribute cell
	 *
	 * @var object
	 * @access private
	 */
	var $attr_cell; // @codingStandardsIgnoreLine

	/**
	 * An empty attribute row
	 *
	 * @var array
	 * @access private
	 */
	var $attr_row; // @codingStandardsIgnoreLine

	/**
	 * The current screen text
	 *
	 * @var array
	 * @access private
	 */
	var $screen; // @codingStandardsIgnoreLine

	/**
	 * The current screen attributes
	 *
	 * @var array
	 * @access private
	 */
	var $attrs; // @codingStandardsIgnoreLine

	/**
	 * Current ANSI code
	 *
	 * @var string
	 * @access private
	 */
	var $ansi; // @codingStandardsIgnoreLine

	/**
	 * Tokenization
	 *
	 * @var array
	 * @access private
	 */
	var $tokenization; // @codingStandardsIgnoreLine

	/**
	 * Default Constructor.
	 *
	 * @return \phpseclib\File\ANSI
	 * @access public
	 */
	public function __construct() {
		$attr_cell             = new \stdClass();
		$attr_cell->bold       = false;
		$attr_cell->underline  = false;
		$attr_cell->blink      = false;
		$attr_cell->background = 'black';
		$attr_cell->foreground = 'white';
		$attr_cell->reverse    = false;
		$this->base_attr_cell  = clone $attr_cell;
		$this->attr_cell       = clone $attr_cell;

		$this->setHistory( 200 );
		$this->setDimensions( 80, 24 );
	}

	/**
	 * Set terminal width and height
	 *
	 * Resets the screen as well
	 *
	 * @param int $x .
	 * @param int $y .
	 * @access public
	 */
	public function setDimensions( $x, $y ) { // @codingStandardsIgnoreLine
		$this->max_x    = $x - 1;
		$this->max_y    = $y - 1;
		$this->x        = $this->y = 0; // @codingStandardsIgnoreLine
		$this->history  = $this->history_attrs = array(); // @codingStandardsIgnoreLine
		$this->attr_row = array_fill( 0, $this->max_x + 2, $this->base_attr_cell );
		$this->screen   = array_fill( 0, $this->max_y + 1, '' );
		$this->attrs    = array_fill( 0, $this->max_y + 1, $this->attr_row );
		$this->ansi     = '';
	}

	/**
	 * Set the number of lines that should be logged past the terminal height
	 *
	 * @param string $history .
	 * @access public
	 */
	public function setHistory( $history ) { // @codingStandardsIgnoreLine
		$this->max_history = $history;
	}

	/**
	 * Load a string
	 *
	 * @param string $source .
	 * @access public
	 */
	public function loadString( $source ) { // @codingStandardsIgnoreLine
		$this->setDimensions( $this->max_x + 1, $this->max_y + 1 );
		$this->appendString( $source );
	}

	/**
	 * Appdend a string
	 *
	 * @param string $source .
	 * @access public
	 */
	public function appendString( $source ) { // @codingStandardsIgnoreLine
		$this->tokenization = array( '' );
		for ( $i = 0; $i < strlen( $source ); $i++ ) { // @codingStandardsIgnoreLine
			if ( strlen( $this->ansi ) ) {
				$this->ansi .= $source[ $i ];
				$chr         = ord( $source[ $i ] );
				// http://en.wikipedia.org/wiki/ANSI_escape_code#Sequence_elements
				// single character CSI's not currently supported .
				switch ( true ) {
					case $this->ansi == "\x1B=": // @codingStandardsIgnoreLine
						$this->ansi = '';
						continue 2;
					case strlen( $this->ansi ) == 2 && $chr >= 64 && $chr <= 95 && ord( '[' ) != $chr: // WPCS:Loose comparison ok .
					case strlen( $this->ansi ) > 2 && $chr >= 64 && $chr <= 126:
						break;
					default:
						continue 2;
				}
				$this->tokenization[] = $this->ansi;
				$this->tokenization[] = '';
				// http://ascii-table.com/ansi-escape-sequences-vt-100.php .
				switch ( $this->ansi ) {
					case "\x1B[H": // Move cursor to upper left corner .
						$this->old_x = $this->x;
						$this->old_y = $this->y;
						$this->x     = $this->y = 0; // @codingStandardsIgnoreLine
						break;
					case "\x1B[J": // @codingStandardsIgnoreLine.
						$this->history = array_merge( $this->history, array_slice( array_splice( $this->screen, $this->y + 1 ), 0, $this->old_y ) );
						$this->screen  = array_merge( $this->screen, array_fill( $this->y, $this->max_y, '' ) );

						$this->history_attrs = array_merge( $this->history_attrs, array_slice( array_splice( $this->attrs, $this->y + 1 ), 0, $this->old_y ) );
						$this->attrs         = array_merge( $this->attrs, array_fill( $this->y, $this->max_y, $this->attr_row ) );

						if ( count( $this->history ) == $this->max_history ) { // WPCS:Loose comparison ok .
							array_shift( $this->history );
							array_shift( $this->history_attrs );
						}
					case "\x1B[K": // Clear screen from cursor right .
						$this->screen[ $this->y ] = substr( $this->screen[ $this->y ], 0, $this->x );

						array_splice( $this->attrs[ $this->y ], $this->x + 1, $this->max_x - $this->x, array_fill( $this->x, $this->max_x - $this->x - 1, $this->base_attr_cell ) );
						break;
					case "\x1B[2K": // Clear entire line .
						$this->screen[ $this->y ] = str_repeat( ' ', $this->x );
						$this->attrs[ $this->y ]  = $this->attr_row;
						break;
					case "\x1B[?1h": // set cursor key to application .
					case "\x1B[?25h": // show the cursor .
					case "\x1B(B": // set united states g0 character set .
						break;
					case "\x1BE": // Move to next line .
						$this->_newLine();
						$this->x = 0;
						break;
					default:
						switch ( true ) {
							case preg_match( '#\x1B\[(\d+)B#', $this->ansi, $match ): // Move cursor down n lines .
								$this->old_y = $this->y;
								$this->y    += $match[1];
								break;
							case preg_match( '#\x1B\[(\d+);(\d+)H#', $this->ansi, $match ): // Move cursor to screen location v,h .
								$this->old_x = $this->x;
								$this->old_y = $this->y;
								$this->x     = $match[2] - 1;
								$this->y     = $match[1] - 1;
								break;
							case preg_match( '#\x1B\[(\d+)C#', $this->ansi, $match ): // Move cursor right n lines .
								$this->old_x = $this->x;
								$this->x    += $match[1];
								break;
							case preg_match( '#\x1B\[(\d+)D#', $this->ansi, $match ): // Move cursor left n lines .
								$this->old_x = $this->x;
								$this->x    -= $match[1];
								break;
							case preg_match( '#\x1B\[(\d+);(\d+)r#', $this->ansi, $match ): // Set top and bottom lines of a window .
								break;
							case preg_match( '#\x1B\[(\d*(?:;\d*)*)m#', $this->ansi, $match ): // character attributes .
								$attr_cell = &$this->attr_cell;
								$mods      = explode( ';', $match[1] );
								foreach ( $mods as $mod ) {
									switch ( $mod ) {
										case 0: // Turn off character attributes .
											$attr_cell = clone $this->base_attr_cell;
											break;
										case 1: // Turn bold mode on .
											$attr_cell->bold = true;
											break;
										case 4: // Turn underline mode on .
											$attr_cell->underline = true;
											break;
										case 5: // Turn blinking mode on .
											$attr_cell->blink = true;
											break;
										case 7: // Turn reverse video on .
											$attr_cell->reverse    = ! $attr_cell->reverse;
											$temp                  = $attr_cell->background;
											$attr_cell->background = $attr_cell->foreground;
											$attr_cell->foreground = $temp;
											break;
										default: // set colors .
											$front = &$attr_cell->{ $attr_cell->reverse ? 'background' : 'foreground' };
											$back  = &$attr_cell->{ $attr_cell->reverse ? 'foreground' : 'background' };
											switch ( $mod ) {
												case 30: $front = 'black'; break; // @codingStandardsIgnoreLine
												case 31: $front = 'red'; break; // @codingStandardsIgnoreLine
												case 32: $front = 'green'; break; // @codingStandardsIgnoreLine
												case 33: $front = 'yellow'; break; // @codingStandardsIgnoreLine
												case 34: $front = 'blue'; break; // @codingStandardsIgnoreLine
												case 35: $front = 'magenta'; break; // @codingStandardsIgnoreLine
												case 36: $front = 'cyan'; break; // @codingStandardsIgnoreLine
												case 37: $front = 'white'; break; // @codingStandardsIgnoreLine

												case 40: $back = 'black'; break; // @codingStandardsIgnoreLine
												case 41: $back = 'red'; break; // @codingStandardsIgnoreLine
												case 42: $back = 'green'; break; // @codingStandardsIgnoreLine
												case 43: $back = 'yellow'; break; // @codingStandardsIgnoreLine
												case 44: $back = 'blue'; break; // @codingStandardsIgnoreLine
												case 45: $back = 'magenta'; break; // @codingStandardsIgnoreLine
												case 46: $back = 'cyan'; break; // @codingStandardsIgnoreLine
												case 47: $back = 'white'; break; // @codingStandardsIgnoreLine

												default:
													$this->ansi = '';
													break 2;
											}
									}
								}
								break;
							default:
						}
				}
				$this->ansi = '';
				continue;
			}

			$this->tokenization[ count( $this->tokenization ) - 1 ] .= $source[ $i ];
			switch ( $source[ $i ] ) {
				case "\r":
					$this->x = 0;
					break;
				case "\n":
					$this->_newLine();
					break;
				case "\x08": // backspace .
					if ( $this->x ) {
						$this->x--;
						$this->attrs[ $this->y ][ $this->x ] = clone $this->base_attr_cell;
						$this->screen[ $this->y ]            = substr_replace(
							$this->screen[ $this->y ],
							$source[ $i ],
							$this->x,
							1
						);
					}
					break;
				case "\x0F": // shift .
					break;
				case "\x1B": // start ANSI escape code .
					$this->tokenization[ count( $this->tokenization ) - 1 ] = substr( $this->tokenization[ count( $this->tokenization ) - 1 ], 0, -1 );

					$this->ansi .= "\x1B";
					break;
				default:
					$this->attrs[ $this->y ][ $this->x ] = clone $this->attr_cell;
					if ( $this->x > strlen( $this->screen[ $this->y ] ) ) {
						$this->screen[ $this->y ] = str_repeat( ' ', $this->x );
					}
					$this->screen[ $this->y ] = substr_replace(
						$this->screen[ $this->y ],
						$source[ $i ],
						$this->x,
						1
					);

					if ( $this->x > $this->max_x ) {
						$this->x = 0;
						$this->y++;
					} else {
						$this->x++;
					}
			}
		}
	}

	/**
	 * Add a new line
	 *
	 * Also update the $this->screen and $this->history buffers
	 *
	 * @access private
	 */
	private function _newLine() { // @codingStandardsIgnoreLine
		while ( $this->y >= $this->max_y ) {
			$this->history  = array_merge( $this->history, array( array_shift( $this->screen ) ) );
			$this->screen[] = '';

			$this->history_attrs = array_merge( $this->history_attrs, array( array_shift( $this->attrs ) ) );
			$this->attrs[]       = $this->attr_row;

			if ( count( $this->history ) >= $this->max_history ) {
				array_shift( $this->history );
				array_shift( $this->history_attrs );
			}

			$this->y--;
		}
		$this->y++;
	}

	/**
	 * Returns the current coordinate without preformating
	 *
	 * @param string $last_attr .
	 * @param string $cur_attr .
	 * @param string $char .
	 * @access private
	 * @return string
	 */
	private function _processCoordinate( $last_attr, $cur_attr, $char ) { // @codingStandardsIgnoreLine
		$output = '';

		if ( $last_attr != $cur_attr ) { // WPCS:Loose comparison ok .
			$close = $open = ''; // @codingStandardsIgnoreLine
			if ( $last_attr->foreground != $cur_attr->foreground ) { // WPCS:Loose comparison ok .
				if ( 'white' != $cur_attr->foreground ) { // WPCS:Loose comparison ok .
					$open .= '<span style="color: ' . $cur_attr->foreground . '">';
				}
				if ( 'white' != $last_attr->foreground ) { // WPCS:Loose comparison ok .
					$close = '</span>' . $close;
				}
			}
			if ( $last_attr->background != $cur_attr->background ) { // WPCS:Loose comparison ok .
				if ( 'black' != $cur_attr->background ) { // WPCS:Loose comparison ok .
					$open .= '<span style="background: ' . $cur_attr->background . '">';
				}
				if ( 'black' != $last_attr->background ) { // WPCS:Loose comparison ok .
					$close = '</span>' . $close;
				}
			}
			if ( $last_attr->bold != $cur_attr->bold ) { // WPCS:Loose comparison ok .
				if ( $cur_attr->bold ) {
					$open .= '<b>';
				} else {
					$close = '</b>' . $close;
				}
			}
			if ( $last_attr->underline != $cur_attr->underline ) { // WPCS:Loose comparison ok .
				if ( $cur_attr->underline ) {
					$open .= '<u>';
				} else {
					$close = '</u>' . $close;
				}
			}
			if ( $last_attr->blink != $cur_attr->blink ) { // WPCS:Loose comparison ok .
				if ( $cur_attr->blink ) {
					$open .= '<blink>';
				} else {
					$close = '</blink>' . $close;
				}
			}
			$output .= $close . $open;
		}

		$output .= htmlspecialchars( $char );

		return $output;
	}

	/**
	 * Returns the current screen without preformating
	 *
	 * @access private
	 * @return string
	 */
	private function _getScreen() { // @codingStandardsIgnoreLine
		$output    = '';
		$last_attr = $this->base_attr_cell;
		for ( $i = 0; $i <= $this->max_y; $i++ ) {
			for ( $j = 0; $j <= $this->max_x; $j++ ) {
				$cur_attr  = $this->attrs[ $i ][ $j ];
				$output   .= $this->_processCoordinate( $last_attr, $cur_attr, isset( $this->screen[ $i ][ $j ] ) ? $this->screen[ $i ][ $j ] : '' );
				$last_attr = $this->attrs[ $i ][ $j ];
			}
			$output .= "\r\n";
		}
		$output = substr( $output, 0, -2 );
		// close any remaining open tags .
		$output .= $this->_processCoordinate( $last_attr, $this->base_attr_cell, '' );
		return rtrim( $output );
	}

	/**
	 * Returns the current screen
	 *
	 * @access public
	 * @return string
	 */
	public function getScreen() { // @codingStandardsIgnoreLine
		return '<pre width="' . ( $this->max_x + 1 ) . '" style="color: white; background: black">' . $this->_getScreen() . '</pre>';
	}

	/**
	 * Returns the current screen and the x previous lines
	 *
	 * @access public
	 * @return string
	 */
	public function getHistory() { // @codingStandardsIgnoreLine
		$scrollback = '';
		$last_attr  = $this->base_attr_cell;
		for ( $i = 0; $i < count( $this->history ); $i++ ) { // @codingStandardsIgnoreLine
			for ( $j = 0; $j <= $this->max_x + 1; $j++ ) {
				$cur_attr    = $this->history_attrs[ $i ][ $j ];
				$scrollback .= $this->_processCoordinate( $last_attr, $cur_attr, isset( $this->history[ $i ][ $j ] ) ? $this->history[ $i ][ $j ] : '' );
				$last_attr   = $this->history_attrs[ $i ][ $j ];
			}
			$scrollback .= "\r\n";
		}
		$base_attr_cell       = $this->base_attr_cell;
		$this->base_attr_cell = $last_attr;
		$scrollback          .= $this->_getScreen();
		$this->base_attr_cell = $base_attr_cell;

		return '<pre width="' . ( $this->max_x + 1 ) . '" style="color: white; background: black">' . $scrollback . '</span></pre>';
	}
}
