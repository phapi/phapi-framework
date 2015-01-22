<?php

namespace Phapi;

/**
 * Route parser class
 *
 * Class for parsing routes and converting them to regex
 *
 * @category Router
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class RouteParser {

    /**
     * Parameter type restrictions.
     *
     * @var array
     */
    private $shortcuts = [
        ':i}'  => ':[0-9]+}',
        ':a}'  => ':[0-9A-Za-z]+}',
        ':h}'  => ':[0-9A-Fa-f]+}',
        ':c}'  => ':[a-zA-Z0-9+_\-.]+}'
    ];

    protected $paramNames = [];

    /**
     * Parse the route (string) and convert it to a regex.
     *
     * @param string $route
     * @return array
     */
    public function parse($route)
    {
        // reset param names
        $this->paramNames = [];

        // replace shortcuts
        $regex = strtr($route, $this->shortcuts);

        // replace param names that doesn't include shortcuts or regex
        $regex = preg_replace_callback('/{([a-zA-Z0-9]+)}/', [$this, 'replaceWithoutRegex'], $regex);

        // replace param names that includes shortcuts or regex
        $regex = preg_replace_callback('/{([a-zA-Z0-9]+):/', [$this, 'replaceWithRegex'], $regex);

        // replace ending grouping
        $regex = str_replace('}', ')', $regex);

        // fix slashes when optional params are present
        $regex = $this->fixOptionalSlashes($regex);

        // fix for handling trailing slashes
        $regex .= '(/)?';

        // return regex and param names
        return ['#^'. $regex .'$#', $this->paramNames];
    }

    /**
     * Replace param names that doesn't include a regex
     *
     * @param array $matches
     * @return string
     */
    protected function replaceWithoutRegex(array $matches)
    {
        $this->paramNames[] = $matches[1];
        return '([^/]+)';
    }

    /**
     * Replace param names that includes a regex
     *
     * @param array $matches
     * @return string
     */
    protected function replaceWithRegex(array $matches)
    {
        $this->paramNames[] = $matches[1];
        return '(';
    }

    /**
     * Takes the input string and splits the params up and fixes
     * the slashes depending on if the param name following the slash
     * is optional or not. If the following param name is optional
     * the slash must also be optional.
     *
     * @param string $string
     * @return string
     */
    protected function fixOptionalSlashes($string)
    {
        // split up the string to an array
        $parts = explode('/(', $string);

        // create emtpy output
        $regex = '';

        // count how many params there is
        $num = count($parts);

        // create counter
        $counter = 1;

        // loop through the params
        foreach ($parts as $part) {

            // add the param back to the regex/output
            $regex .= $part;

            // make sure it's not the last param
            if ($num > $counter) {
                // check ahead on next param and determine if its optional or not

                if (preg_match('/\)\?$/', $parts[$counter])) {
                    // next param is optional so make the slash optional as well
                    $regex .= '(/)?(';
                } else {
                    // next param is required and so should the slash be as well
                    $regex .= '/(';
                }
            }

            // increase counter
            $counter++;
        }

        // return regex
        return $regex;
    }
} 