<?php

/**
 * Pomocne rozhrani, aby mi PhpStorm neukazoval v sablonach, ze mam neexistujici metody.
 */
interface PlatesTemplateInterface {
    /**
     * Assign or get template data.
     * @param  array $data
     * @return mixed
     */
    public function data(array $data = null);

    /**
     * Set the template's layout.
     * @param  string $name
     * @param  array $data
     * @return null
     */
    public function layout($name, array $data = array());

    /**
     * Start a new section block.
     * @param  string $name
     * @return null
     */
    public function start($name);

    /**
     * Start a new append section block.
     * @param  string $name
     * @return null
     */
    public function push($name);

    /**
     * Stop the current section block.
     * @return null
     */
    public function stop();

    /**
     * Alias of stop().
     * @return null
     */
    public function end();

    /**
     * Returns the content for a section block.
     * @param  string $name Section name
     * @param  string $default Default section content
     * @return string|null
     */
    public function section($name, $default = null);

    /**
     * Fetch a rendered template.
     * @param  string $name
     * @param  array $data
     * @return string
     */
    public function fetch($name, array $data = array());

    /**
     * Output a rendered template.
     * @param  string $name
     * @param  array $data
     * @return null
     */
    public function insert($name, array $data = array());

    /**
     * Apply multiple functions to variable.
     * @param  mixed $var
     * @param  string $functions
     * @return mixed
     */
    public function batch($var, $functions);

    /**
     * Escape string.
     * @param  string $string
     * @param  null|string $functions
     * @return string
     */
    public function escape($string, $functions = null);

    /**
     * Alias to escape function.
     * @param  string $string
     * @param  null|string $functions
     * @return string
     */
    public function e($string, $functions = null);

//	public function baseUrl();

//	public function uriFull();

    /**
     * Build the path for a named route including the base path
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function pathFor($name, array $data = [], array $queryParams = []);

//	public function basePath();

//	public function uriScheme();

//	public function uriHost();

//	public function uriPort();

//	public function uriPath();

//	public function uriQuery();

//	public function uriFragment();

}