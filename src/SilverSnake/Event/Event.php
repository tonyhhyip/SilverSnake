<?php

namespace SilverSnake\Event;


class Event {

	/**
	 * @var mixed
	 */
	protected $source;

	/**
	 * constructor.
	 *
	 * @param mixed $source
	 */
	public function __construct($source) {
		$this->source = $source;
	}

	/**
	 * The object on which the Event initially occurred.
	 *
	 * @return mixed The object on which the Event initially occurred.
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * Returns a String representation of this Event.
	 *
	 * @return string A String representation of this Event.
	 */
	public function __toString() {
		return 'Event of $source';
	}
}