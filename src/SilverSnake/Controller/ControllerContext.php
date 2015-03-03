<?php

namespace SilverSnake\Controller;

use SilverSnake\Filter\Filter;

class ControllerContext {

	/**
	 * @var array<string,Filter>
	 */
	private $filters = array();

	/**
	 * Registers the given filter instance with this ControllerContext under the given filterName.
	 * If this ControllerContext already contains a preliminary Filter for a filter with the given filterName,
	 * it will be skip.
	 *
	 * @param string $filterName
	 * 		the name of the filter
	 * @param Filter $filter
	 * 		the filter instance to register
	 */
	public function addFilter($filterName, Filter $filter) {
		if (!in_array($filter, $filterName)) {
			$this->filters[$filterName] = $filter;
		}
	}

	/**
	 * Get the array of filters.
	 *
	 * @return array<string, Filter>
	 */
	public function getFilters() {
		return $this->filters;
	}

}