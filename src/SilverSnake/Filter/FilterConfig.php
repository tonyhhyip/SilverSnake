<?php

namespace SilverSnake\Filter;


interface FilterConfig {

    /**
     * Returns the filter-name of this filter as defined in the deployment descriptor.
     * @return string filter-name
     */
    public function getFilterName();

    /**
     * Returns a reference to the ControllerContext in which the caller is executing.
     * @return \SilverSnake\Controller\ControllerContext
     */
    public function getControllerContext();
}