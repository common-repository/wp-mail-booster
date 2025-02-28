<?php // @codingStandardsIgnoreLine
/**
 * This file is for task runner
 *
 * @author  Tech Banker
 * @package wp-mail-booster/google-api-php-client/src/google/
 * @version 2.0.0
 */

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * A task runner with exponential backoff support.
 *
 * @see https://developers.google.com/drive/web/handle-errors#implementing_exponential_backoff
 */
class Google_Task_Runner {

	const TASK_RETRY_NEVER  = 0;
	const TASK_RETRY_ONCE   = 1;
	const TASK_RETRY_ALWAYS = -1;

	/**
	 * Variable for max delay
	 *
	 * @var integer $maxDelay The max time (in seconds) to wait before a retry.
	 */
	private $maxDelay = 60; // @codingStandardsIgnoreLine
	/**
	 * The previous delay from which the next is calculated
	 *
	 * @var integer $delay .
	 */
	private $delay = 1;

	/**
	 * The base number for the exponential back off
	 *
	 * @var integer $factor .
	 */
	private $factor = 2;
	/**
	 * A random number between -$jitter and $jitter will be
	 * added to $factor on each iteration to allow for a better distribution of
	 * retries
	 *
	 * @var float $jitter .
	 */
	private $jitter = 0.5;

	/**
	 * The number of attempts that have been tried so far
	 *
	 * @var integer $attempts .
	 */
	private $attempts = 0;
	/**
	 * The max number of attempts allowed
	 *
	 * @var integer $maxAttempts .
	 */
	private $maxAttempts = 1; // @codingStandardsIgnoreLine

	/**
	 * The task to run and possibly retry
	 *
	 * @var callable $action .
	 */
	private $action;
	/**
	 * The task arguments
	 *
	 * @var array $arguments .
	 */
	private $arguments;

	/**
	 * Map of errors with retry counts
	 *
	 * @var array $retryMap .
	 */
	protected $retryMap = [ // @codingStandardsIgnoreLine
		'500'                   => self::TASK_RETRY_ALWAYS,
		'503'                   => self::TASK_RETRY_ALWAYS,
		'rateLimitExceeded'     => self::TASK_RETRY_ALWAYS,
		'userRateLimitExceeded' => self::TASK_RETRY_ALWAYS,
		6                       => self::TASK_RETRY_ALWAYS,  // CURLE_COULDNT_RESOLVE_HOST .
		7                       => self::TASK_RETRY_ALWAYS,  // CURLE_COULDNT_CONNECT .
		28                      => self::TASK_RETRY_ALWAYS,  // CURLE_OPERATION_TIMEOUTED .
		35                      => self::TASK_RETRY_ALWAYS,  // CURLE_SSL_CONNECT_ERROR .
		52                      => self::TASK_RETRY_ALWAYS,   // CURLE_GOT_NOTHING .
	];

	/**
	 * Creates a new task runner with exponential backoff support.
	 *
	 * @param array    $config The task runner config .
	 * @param string   $name The name of the current task (used for logging) .
	 * @param callable $action The task to run and possibly retry .
	 * @param array    $arguments The task arguments .
	 * @throws Google_Task_Exception When misconfigured .
	 */
	public function __construct(
		$config,
		$name,
		$action,
		array $arguments = array()
	) {
		if ( isset( $config['initial_delay'] ) ) {
			if ( $config['initial_delay'] < 0 ) {
				throw new Google_Task_Exception(
					'Task configuration `initial_delay` must not be negative.'
				);
			}

			$this->delay = $config['initial_delay'];
		}

		if ( isset( $config['max_delay'] ) ) {
			if ( $config['max_delay'] <= 0 ) {
				throw new Google_Task_Exception(
					'Task configuration `max_delay` must be greater than 0.'
				);
			}

			$this->maxDelay = $config['max_delay']; // @codingStandardsIgnoreLine
		}

		if ( isset( $config['factor'] ) ) {
			if ( $config['factor'] <= 0 ) {
				throw new Google_Task_Exception(
					'Task configuration `factor` must be greater than 0.'
				);
			}

			$this->factor = $config['factor'];
		}

		if ( isset( $config['jitter'] ) ) {
			if ( $config['jitter'] <= 0 ) {
				throw new Google_Task_Exception(
					'Task configuration `jitter` must be greater than 0.'
				);
			}

			$this->jitter = $config['jitter'];
		}

		if ( isset( $config['retries'] ) ) {
			if ( $config['retries'] < 0 ) {
				throw new Google_Task_Exception(
					'Task configuration `retries` must not be negative.'
				);
			}
			$this->maxAttempts += $config['retries']; // @codingStandardsIgnoreLine
		}

		if ( ! is_callable( $action ) ) {
			throw new Google_Task_Exception(
				'Task argument `$action` must be a valid callable.'
			);
		}

		$this->action    = $action;
		$this->arguments = $arguments;
	}

	/**
	 * Checks if a retry can be attempted.
	 *
	 * @return boolean
	 */
	public function canAttempt() { // @codingStandardsIgnoreLine
		return $this->attempts < $this->maxAttempts; // @codingStandardsIgnoreLine
	}

	/**
	 * Runs the task and (if applicable) automatically retries when errors occur.
	 *
	 * @return mixed
	 * @throws Google_Service_Exception .
	 */
	public function run() {
		while ( $this->attempt() ) {
			try {
				return call_user_func_array( $this->action, $this->arguments );
			} catch ( Google_Service_Exception $exception ) {
				$allowedRetries = $this->allowedRetries( // @codingStandardsIgnoreLine
					$exception->getCode(),
					$exception->getErrors()
				);

				if ( ! $this->canAttempt() || ! $allowedRetries ) { // @codingStandardsIgnoreLine
					throw $exception;
				}

				if ( $allowedRetries > 0 ) { // @codingStandardsIgnoreLine
					$this->maxAttempts = min( // @codingStandardsIgnoreLine
						$this->maxAttempts, // @codingStandardsIgnoreLine
						$this->attempts + $allowedRetries // @codingStandardsIgnoreLine
					);
				}
			}
		}
	}

	/**
	 * Runs a task once, if possible. This is useful for bypassing the `run()`
	 * loop.
	 *
	 * NOTE: If this is not the first attempt, this function will sleep in
	 * accordance to the backoff configurations before running the task.
	 *
	 * @return boolean
	 */
	public function attempt() {
		if ( ! $this->canAttempt() ) {
			return false;
		}

		if ( $this->attempts > 0 ) {
			$this->backOff();
		}

		$this->attempts++;
		return true;
	}

	/**
	 * Sleeps in accordance to the backoff configurations.
	 */
	private function backOff() { // @codingStandardsIgnoreLine
		$delay = $this->getDelay();

		usleep( $delay * 1000000 );
	}

	/**
	 * Gets the delay (in seconds) for the current backoff period.
	 *
	 * @return float
	 */
	private function getDelay() { // @codingStandardsIgnoreLine
		$jitter = $this->getJitter();
		$factor = $this->attempts > 1 ? $this->factor + $jitter : 1 + abs( $jitter );

		return $this->delay = min( $this->maxDelay, $this->delay * $factor ); // @codingStandardsIgnoreLine
	}

	/**
	 * Gets the current jitter (random number between -$this->jitter and
	 * $this->jitter).
	 *
	 * @return float
	 */
	private function getJitter() { // @codingStandardsIgnoreLine
		return $this->jitter * 2 * mt_rand() / mt_getrandmax() - $this->jitter;
	}

	/**
	 * Gets the number of times the associated task can be retried.
	 *
	 * NOTE: -1 is returned if the task can be retried indefinitely
	 *
	 * @param string $code .
	 * @param array  $errors .
	 * @return integer
	 */
	public function allowedRetries( $code, $errors = array() ) { // @codingStandardsIgnoreLine
		if ( isset( $this->retryMap[ $code ] ) ) { // @codingStandardsIgnoreLine
			return $this->retryMap[ $code ]; // @codingStandardsIgnoreLine
		}

		if (
		! empty( $errors ) &&
		isset( $errors[0]['reason'], $this->retryMap[ $errors[0]['reason'] ] ) // @codingStandardsIgnoreLine
		) {
			return $this->retryMap[ $errors[0]['reason'] ]; // @codingStandardsIgnoreLine
		}

		return 0;
	}
	/**
	 * This functiion is to set retry map .
	 *
	 * @param string $retryMap .
	 */
	public function setRetryMap( $retryMap ) { // @codingStandardsIgnoreLine
		$this->retryMap = $retryMap; // @codingStandardsIgnoreLine
	}
}
