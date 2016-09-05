<?php

namespace Ripples\Memcached;

use Memcached;
use RuntimeException;

// use Log;

// class MemcachedSaslConnector extends \Illuminate\Cache\MemcachedConnector
class MemcachedSaslConnector
{

    /**
     * Create a new Memcached connection.
     *
     * @param  array  $servers
     * @param string|null $connectionId
     * @param array $options
     * @param array $auth
     *
     * @throws \RuntimeException
     *
     * @return \Memcached
     */
    public function connect(
        array $servers,
        $connectionId = null,
        array $options = [],
        array $auth = []
    ) {
        $memcached = $this->getMemcached($connectionId);

        if (count($memcached->getServerList()) == 0) {
            // For each server in the array, we'll just extract the configuration and add
            // the server to the Memcached connection. Once we have added all of these
            // servers we'll verify the connection is successful and return it back.

            // Log::info('new connection');

            if (count($options)) {
                $memcached->setOptions($options);
            }

            if (count($auth) == 2) {
                $this->setAuth($memcached, $auth);
            }

            foreach ($servers as $server) {
                $memcached->addServer(
                    $server['host'], $server['port'], $server['weight']
                );
            }
        }

        // Log::info('now connections is:'.count($memcached->getServerList()));
        return $this->validateConnection($memcached);
    }

    /**
     * Get a new Memcached instance.
     *
     * @param  string|null  $connectionId
     * @param  array  $auth
     * @param  array  $options
     * @return \Memcached
     */
    protected function getMemcached($connectionId)
    {
        $memcached = $this->createMemcachedInstance($connectionId);

        return $memcached;
    }

    /**
     * Create the Memcached instance.
     *
     * @param  string|null  $connectionId
     * @return \Memcached
     */
    protected function createMemcachedInstance($connectionId)
    {
        return empty($connectionId) ? new Memcached : new Memcached($connectionId);
    }

    /**
     * Set the SASL auth on the Memcached connection.
     *
     * @param  \Memcached  $memcached
     * @param  array  $auth
     * @return void
     */
    protected function setAuth($memcached, $auth)
    {
        // list($username, $password) = $auth;

        if (!ini_get('memcached.use_sasl')) {
            throw new RuntimeException('Memcached SASL should be supported.');
        }

        $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
        $memcached->setSaslAuthData($auth['username'], $auth['password']);
    }

    /**
     * Validate the given Memcached connection.
     *
     * @param  \Memcached  $memcached
     * @return \Memcached
     */
    protected function validateConnection($memcached)
    {
        $status = $memcached->getVersion();

        if (! is_array($status)) {
            throw new RuntimeException('No Memcached servers added.');
        }

        if (in_array('255.255.255', $status) && count(array_unique($status)) === 1) {
            throw new RuntimeException('Could not establish Memcached connection.');
        }

        return $memcached;
    }

}