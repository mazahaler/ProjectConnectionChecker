<?php

namespace app\commands;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

/**
 * Class ProjectConnectionChecker
 */
class ProjectConnectionChecker
{
    const excludeDirs = [
        '/web/assets',
        '/runtime',
        '/vendor',
        '/log',
        '/.idea',
        '/.git',
        'ProjectConnectionChecker'
    ];

    private array $_errors = [];

    const SCAN_FILES_EXTENSION = '.php';

    public function gitThroughDir(string $dir)
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

        $allSecrets = [];

        foreach ($rii as $file) {

            if ($file->isDir()) {
                continue;
            }

            if (!is_null($file->getPathname()) && !$this->_checkIsExclude($file->getPathname()) && $this->_str_contains(self::SCAN_FILES_EXTENSION, $file->getPathname())) {
                $secrets = $this->_getSecretKeysByFile($file->getPathname());
                $allSecrets = array_merge($allSecrets, $secrets);
            }

        }
        $this->_checkSecrets($allSecrets);
        $this->_printErrors();
    }

    private function _printErrors(){
        if(count($errors = $this->getErrors())){
            foreach ($this->getErrors() as $error){
                echo $error."\n";
            }
            die;
        }
    }

    public function getErrors(): array
    {
        return $this->_errors;
    }

    private static function _getSecrets()
    {
        return json_decode(file_get_contents("secrets/secrets.json"), true)['data'];
    }

    private function _checkSecrets(array $pathAndSecrets)
    {
        $secretsJson = self::_getSecrets();
        $allProjectSecrets = [];
        foreach ($pathAndSecrets as $path => $secrets) {
            if (empty($secrets)) {
                continue;
            }
            foreach ($secrets as $secret) {
                $allProjectSecrets[] = $secret;
                if (!$secretsJson[$secret]) {
                    $this->_errors[] = "Secret [$secret] used in a [$path], but not defined in secrets.json";
                }
            }
        }
        foreach ($secretsJson as $keySecret => $secret) {
            if (!in_array($keySecret, $allProjectSecrets)) {
                $this->_errors[] = "Secret [$keySecret] from secrets.json is not used anywhere.";
            }
        }
    }

    private function _getSecretKeysByFile(string $path): array
    {
        $file = new SplFileObject($path);
        $secretKeys = [];
        $line = 1;
        while (!$file->eof()) {
            if (!empty($secretKey = $this->_getBetween(trim($file->fgets()), "VaultSecret::getSecret(", ")"))) {
                if ($this->_str_contains('\$', $secretKey)) {
                    $this->_errors[] = "Using a variable for secrets is not allowed in [$path]:[$line]";
                } else {
                    $secretKey = str_replace("'", "", $secretKey);
                    $secretKeys[] = strstr($secretKey, ",", TRUE) ? strstr($secretKey, ",", TRUE) : $secretKey;
                }
            }
            $line++;
        }
        $file = null;
        return [$path => $secretKeys];
    }

    private function _checkIsExclude(string $path): bool
    {
        $isExclude = false;
        foreach (self::excludeDirs as $excDir) {
            if ($this->_str_contains($excDir, $path)) {
                $isExclude = true;
            }
        }
        return $isExclude;
    }

    private function _str_contains($needle, $haystack, $i = 'i'): bool
    {
        if (preg_match("#{$needle}#{$i}", $haystack)) {
            return true;
        }
        return false;
    }

    private function _getBetween($string, $start = "", $end = "")
    {
        if (strpos($string, $start)) {
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }
}