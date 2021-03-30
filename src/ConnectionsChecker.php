<?php


namespace mazahaler\ProjectConnectionChecker;

/**
 * Class ConnectionsChecker
 * @package mazahaler\ProjectConnectionChecker
 */
class ConnectionsChecker extends ProjectChecker
{
    /**
     * Array of db connections
     * @var array 
     */
    private array $_connections = [];
    
    /**
     * ConnectionsChecker constructor.
     * @param array $connections
     * @throws \Exception
     */
    public function __construct(array $connections)
    {
        $this->_connections = $connections;
        $this->check();
    }

    /**
     * Checks db connections
     * @throws \Exception
     */
    public function check(): void
    {
        foreach ($this->_connections as $connKey =>  $connection) {
            $connection = $connection[0];
            try {
                $connection->open();
                if (!$connection->getIsActive()) {
                    $this->errors[] = "The connection {$connKey} was not established";
                }
            } catch (\Exception $exception) {
                $this->errors[] = "Exception with connection {$connKey}: " . $exception;
            }
        }
        $this->printErrors();
    }
}