<?php

namespace Phapi;

use Phapi\Exception\Error\MethodNotAllowed;
use Phapi\Http\Response;

/**
 * Resource class
 *
 * Parent class for all resources. Implements some basic
 * functionality like OPTIONS responses and adds the Phapi
 * and Response objects on creation for easier access.
 *
 * @category Resource
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Resource
{

    /**
     * The app
     *
     * @var null|Phapi
     */
    protected $app;

    /**
     * The response object
     *
     * @var Http\Response
     */
    protected $response;

    /**
     * The request object
     *
     * @var Http\Request
     */
    protected $request;

    /**
     * Set app and response object for easier access and development
     *
     * @param $app
     */
    public function __construct($app)
    {
        // check if an instance of the app(Phapi) has been passed through
        if ($app instanceof Phapi) {
            $this->app = $app;
            $this->response = $this->app->getResponse();
            $this->request = $this->app->getRequest();
        }
    }

    /**
     * Options
     *
     * Responding to OPTIONS requests and checks for implemented
     * methods that matches HTTP METHODS.
     *
     * @return array
     * @throws MethodNotAllowed
     */
    public function options()
    {
        if (!($this->response instanceof Response)) {
            throw new MethodNotAllowed();
        }

        // get all implemented methods for this resources
        $methods = get_class_methods(get_class($this));

        // get supported verbs
        $reflection = new \ReflectionClass('\Phapi\Http\Request');
        $constants = $reflection->getConstants();

        // loop though class functions/methods
        foreach ($methods as $key => &$method) {
            $method = strtoupper($method);
            // if class function/method isn't a verb, then unset it
            if (!in_array($method, $constants)) {
                unset($methods[$key]);
            }
        }

        // Set accept header
        $this->response->addHeaders(['Access-Control-Allow-Methods' => implode(', ', $methods)]);
        $this->response->addHeaders(['Accept' => implode(', ', $methods)]);

        // Prepare output
        $output = [];
        $output['contentTypes'] = $this->app->getNegotiator()->getContentTypes();
        $output['accept'] = $this->app->getNegotiator()->getAccepts();

        foreach ($methods as $verb) {
            // Reflect the method
            $rm = new \ReflectionMethod($this, $verb);
            // Get method documentation
            $doc = $rm->getDocComment();

            // Prepare output
            $verbOutput = [];

            $longKey = null;

            // Loop through all lines
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $doc) as $line) {

                // Reset value
                $value = '';
                // Reset key
                $key = '';

                // Remove some unwanted chars from the line
                $line = trim(str_replace('*', '', trim($line)));

                // check if line starts with @api
                if (substr($line, 0, 4) == '@api') {
                    // find the annotation and use it as a key/identifier, example: @apiDescription
                    preg_match('/^@api[a-zA-Z]*/i', $line, $matches);
                    $longKey = $matches[0];

                    // remove @api from the key/identifier
                    $key = lcfirst(str_replace('@api', '', $longKey));

                    // remove whitespace from the line
                    $value = trim($line);

                    // check if line doesnt have a annotation
                } elseif (!in_array(substr($line, 0, 1), ['@', '/']) && !empty($line)) {
                    // check if we have the key/identifier from last loop
                    if (!empty($longKey)) {
                        // remove whitespace
                        $value .= trim($line);
                        // create key/identifier by removing @api and making first letter lowercase
                        $key = lcfirst(str_replace('@api', '', $longKey));
                    }
                } else {
                    // don't include this line in the doc
                    $longKey = null;
                    continue;
                }

                // check if we already have a key/identifier in the output
                if (array_key_exists($key, $verbOutput)) {
                    // check if value is an array (has multiple values)
                    if (is_array($verbOutput[$key])) {

                        // remove the key from the value and remove whitespace
                        $newValue = str_replace($longKey.' ', '', trim($value));

                        // check if there was a key to remove
                        if (trim($value) !== $newValue) {
                            // the key was removed and that means we wasn't to add the line as a new row in the array
                            $verbOutput[$key][] = $newValue;
                        } else {
                            // the key wasn't removed so we want to merge this line with the previous one
                            // count rows in array to get the last key
                            $last = count($verbOutput[$key]) -1;
                            // merge this line with the previous one
                            $verbOutput[$key][$last] = $verbOutput[$key][$last]. ' '. $newValue;
                        }
                    } else {
                        // value is not an array

                        // save the current value
                        $oldValue = $verbOutput[$key];

                        // remove the key from the value and remove whitespace
                        $newValue = trim(str_replace($longKey.' ', '', trim($value)));

                        // check if there was a key to remove
                        if (trim($value) !== $newValue) {
                            // the key was removed so we want to create an array with the previous and new value
                            $verbOutput[$key] = [$oldValue, $newValue];
                        } else {
                            // the wasnt a key to remove so we want to merge this line with the previous one
                            $verbOutput[$key] .= ' '. $newValue;
                        }
                    }
                } else {
                    // this is a new key/identifier
                    // check if we have a key/identifier
                    if (isset($longKey)) {
                        // add key and value to output
                        $verbOutput[$key] = str_replace($longKey.' ', '', trim($value));
                    }
                }
            }

            // check if there is any output to show
            if (!empty($verbOutput)) {
                $output['methods'][$verb] = $verbOutput;
            }
        }

        // return output
        return $output;
    }
}