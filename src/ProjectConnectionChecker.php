<?php

namespace mazahaler\ProjectConnectionChecker;

/**
 * Class ProjectConnectionChecker
 * @package mazahaler\ProjectConnectionChecker
 */
class ProjectConnectionChecker
{
    public static function checkAll(string $rootPath, array $dbConnections = [])
    {
        new SecretsChecker($rootPath);
        new ConnectionsChecker($dbConnections);
    }
}