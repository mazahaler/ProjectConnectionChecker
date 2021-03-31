<?php

namespace mazahaler\ProjectConnectionChecker;

use yii\mail\MailerInterface;

/**
 * Class ProjectConnectionChecker
 * @package mazahaler\ProjectConnectionChecker
 */
class ProjectConnectionChecker
{
    /**
     * Check secrets, db connections and mailing
     * @param string $rootPath
     * @param array $dbConnections
     * @param \Swift_Mailer $mailer
     * @param $from
     * @param $to
     */
    public static function checkAll(string $rootPath, array $dbConnections = [], MailerInterface $mailer, string $from = 'quality@telecontact.ru', string $to = 'v.xarlanchuk@telecontact.ru')
    {
        self::checkSecrets($rootPath);
        self::checkMailing($mailer, $from, $to);
        self::checkConnections($dbConnections);
    }


    /**
     * Check secrets
     * @param string $rootPath
     */
    public static function checkSecrets(string $rootPath)
    {
        new SecretsChecker($rootPath);
    }

    /**
     * Check db connections
     * @param array $dbConnections
     */
    public static function checkConnections(array $dbConnections = [])
    {
        new ConnectionsChecker($dbConnections);
    }

    /**
     * Check whether the message is sent successfully
     * @param MailerInterface $mailer
     * @param string $from
     * @param string $to
     */
    public static function checkMailing(MailerInterface $mailer, string $from = 'quality@telecontact.ru', string $to = 'v.xarlanchuk@telecontact.ru')
    {
        new MailingChecker($mailer, $from, $to);
    }
}