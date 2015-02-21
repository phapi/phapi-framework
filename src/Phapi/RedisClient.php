<?php

namespace Phapi;

/**
 * Redis client for connecting to a Redis server
 *
 * @category Resource
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class RedisClient
{
    /**
     * Store the socket
     *
     * @var resource
     */
    private $socket;

    /**
     * Create a socket
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host = 'localhost', $port = 6379)
    {
        $this->socket = stream_socket_client($host .':'. $port);
    }

    /**
     * Handle all the different functions
     *
     * @param $method
     * @param array $args
     * @return array|null|string
     * @throws \Exception
     */
    public function __call($method, array $args)
    {
        array_unshift($args, $method);

        // Count arguments and start building command
        $cmd = '*' . count($args) . "\r\n";

        // Loop through all arguments
        foreach ($args as $item) {
            // Check and add length and the argument to the command
            $cmd .= '$' . strlen($item) . "\r\n" . $item . "\r\n";
        }

        // Send command
        fwrite($this->socket, $cmd);

        // Parse the response
        return $this->parseResponse();
    }

    /**
     * Parse the response from the Redis server
     *
     * @return array|null|string
     * @throws \Exception
     */
    protected function parseResponse()
    {
        $line = fgets($this->socket);
        list($type, $result) = array($line[0], substr($line, 1, strlen($line) - 3));

        // Check response type
        if ($type == '$') {
            // This is a bulk reply
            if ($result == -1) {
                $result = null;
            } else {
                $line = fread($this->socket, $result + 2);
                $result = substr($line, 0, strlen($line) - 2);
            }
        } elseif ($type == '*') {
            // Multi bulk reply
            $count = (int) $result;
            for ($i = 0, $result = array(); $i < $count; $i++) {
                $result[] = $this->parseResponse();
            }
        } elseif ($type == '-') {
            // An error occurred
            throw new \Exception($result);
        }

        return $result;
    }
}