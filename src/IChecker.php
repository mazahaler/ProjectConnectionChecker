<?php


namespace mazahaler\ProjectConnectionChecker;

/**
 * Class Checker
 * @package mazahaler\ProjectConnectionChecker
 */
interface IChecker
{
    /**
     * Get errors from checker
     * @return mixed
     */
    function getErrors(): array;

    /**
     * The method init check
     * @return void
     */
    public function check(): void;

}