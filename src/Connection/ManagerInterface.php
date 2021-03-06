<?php
namespace Disque\Connection;

use Disque\Command\CommandInterface;

interface ManagerInterface
{
    /**
     * Get the connection implementation class
     *
     * @return string A fully classified class name that implements `Disque\Connection\ConnectionInterface`
     */
    public function getConnectionClass();

    /**
     * Set the connection implementation class
     *
     * @param string $class A fully classified class name that must implement `Disque\Connection\ConnectionInterface`
     * @return void
     * @throws InvalidArgumentException
     */
    public function setConnectionClass($class);

    /**
     * Get available servers
     *
     * @return array Each server is an indexed array with `host` and `port`
     */
    public function getServers();

    /**
     * Add a new server
     *
     * @param string $host Host
     * @param int $port Port
     * @param string $password Password to use when connecting to this server
     * @param array $options Connection options
     * @return void
     * @throws InvalidArgumentException
     */
    public function addServer($host, $port = 7711, $password = null, array $options = []);

    /**
     * If a node has produced at least these number of jobs, switch there
     *
     * @param int $minimumJobsToChangeNode Set to 0 to never change
     * @return void
     */
    public function setMinimumJobsToChangeNode($minimumJobsToChangeNode);

    /**
     * Tells if connection is established
     *
     * @return bool Success
     */
    public function isConnected();

    /**
     * Connect to Disque
     *
     * @return array Connected node information
     * @throws AuthenticationException
     * @throws ConnectionException
     */
    public function connect();

    /**
     * Execute the given command on the given connection
     *
     * @param CommandInterface $command Command
     * @return mixed Command response
     */
    public function execute(CommandInterface $command);
}