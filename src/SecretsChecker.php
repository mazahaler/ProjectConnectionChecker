<?php

namespace mazahaler\ProjectConnectionChecker;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

class SecretsChecker extends ProjectChecker
{
    /**
     * Do not read files in these dirs
     */
    const excludeDirs = [
        '/web/assets',
        '/runtime',
        '/vendor',
        '/log',
        '/.idea',
        '/.git',
    ];


    /**
     * File extenstion to find secrets
     */
    const SCAN_FILES_EXTENSION = '.php';

    /**
     * The root directory where the scan starts
     * @var string
     */
    private string $_rootPath = '';

    /**
     * Full path to secrets.json
     * @var string
     */
    private string $_secretPath = '';

    /**
     * SecretsChecker constructor.
     * @param string $rootPath
     * @param string $secretPath
     */
    public function __construct(string $rootPath, string $secretPath)
    {
        $this->_rootPath = $rootPath;
        $this->_secretPath = $secretPath;
        $this->check();
    }

    /**
     * Checks secrets and output errors if exists
     * @param string $dir
     */
    public function check(): void
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_rootPath));

        $allSecrets = [];

        foreach ($rii as $file) {

            if ($file->isDir()) {
                continue;
            }

            if (!is_null($file->getPathname()) && !$this->_checkIsExclude($file->getPathname()) && Utils::strContains(self::SCAN_FILES_EXTENSION, $file->getPathname())) {
                $secrets = $this->_getSecretKeysByFile($file->getPathname());
                $allSecrets = array_merge($allSecrets, $secrets);
            }

        }
        $this->_checkSecrets($allSecrets);
        $this->printErrors();
    }

    /**
     * get secrets from secrets.json
     * @return mixed
     */
    private function _getSecrets()
    {
        return json_decode(file_get_contents($this->_secretPath), true)['data'];
    }

    /**
     * Check secrets for compliance
     * @param array $pathAndSecrets
     */
    private function _checkSecrets(array $pathAndSecrets)
    {
        $secretsJson = $this->_getSecrets();
        $allProjectSecrets = [];
        foreach ($pathAndSecrets as $path => $secrets) {
            if (empty($secrets)) {
                continue;
            }
            foreach ($secrets as $secret) {
                $allProjectSecrets[] = $secret;
                if (!$secretsJson[$secret]) {
                    $this->errors[] = "Secret [$secret] used in a [$path], but not defined or empty in secrets.json";
                }
            }
        }
        foreach ($secretsJson as $keySecret => $secret) {
            if (!in_array($keySecret, $allProjectSecrets)) {
                $this->errors[] = "Secret [$keySecret] from secrets.json is not used anywhere.";
            }
        }
    }

    /**
     * Scan file for secrets and return ['filepath' => ['secretKeys']]
     * @param string $path
     * @return array[]
     */
    private function _getSecretKeysByFile(string $path): array
    {
        $file = new SplFileObject($path);
        $secretKeys = [];
        $line = 1;
        while (!$file->eof()) {
            if (!empty($secretKey = Utils::getBetween(trim($file->fgets()), "VaultSecret::getSecret(", ")"))) {
                if (Utils::strContains('\$', $secretKey)) {
                    $this->errors[] = "Using a variable for secrets is not allowed in [$path]:[$line]";
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

    /**
     * Check is filepath in excludeDirs
     * @param string $path
     * @return bool
     */
    private function _checkIsExclude(string $path): bool
    {
        $isExclude = false;
        foreach (self::excludeDirs as $excDir) {
            if (Utils::strContains($excDir, $path)) {
                $isExclude = true;
            }
        }
        return $isExclude;
    }
}