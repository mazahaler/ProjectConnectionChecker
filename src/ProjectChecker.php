<?php


namespace mazahaler\ProjectConnectionChecker;

/**
 * Class ProjectChecker
 * @package mazahaler\ProjectConnectionChecker
 */
abstract class ProjectChecker implements IChecker
{
    /**
     * Errors to print before terminate
     * @var array
     */
    protected array $errors = [];

    /**
     * Print errors if exists and terminate process
     * @return void
     */
    public function printErrors(): void
    {
        if (count($errors = $this->getErrors())) {
            foreach ($this->getErrors() as $error) {
                echo $error . "\n";
            }
            exit(1);
        }
    }

    /**
     * Errors getter
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


}