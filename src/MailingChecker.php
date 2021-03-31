<?php


namespace mazahaler\ProjectConnectionChecker;

use yii\mail\MailerInterface;

/**
 * Class MailingChecker
 * @package mazahaler\ProjectConnectionChecker
 */
class MailingChecker extends ProjectChecker
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var string
     */
    private string $from;

    /**
     * @var string
     */
    private string $to;

    /**
     * MailingChecker constructor.
     * @param MailerInterface $mailer
     * @param string $from
     * @param string $to
     */
    public function __construct(MailerInterface $mailer, string $from = 'quality@telecontact.ru', string $to = 'test@telecontact.ru')
    {
        $this->_mailer = $mailer;
        $this->_from = $from;
        $this->_to = $to;
        $this->check();
    }

    /**
     * Check whether the message is sent successfully
     */
    public function check(): void
    {
        try {
            $mailer = $this->_mailer
                ->compose()
                ->setTo($this->_to)
                ->setFrom($this->_from)
                ->setSubject('Entrypoint mailing test')
                ->setTextBody('This is test text');

            // Send email
            if (!$mailer->send()) {
                $this->errors[] = "Can't send the mail. Check your SMTP requisites";
            }
        } catch (\Exception $e) {
            $this->errors[] = "Error while sending mail: " . $e->getMessage();
        }

        $this->printErrors();
    }
}