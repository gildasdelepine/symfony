<?php
/**
 * Created by PhpStorm.
 * User: gidel
 * Date: 04/05/18
 * Time: 18:00
 */

namespace SML\PlatformBundle\Email;

use SML\PlatformBundle\Entity\Application;

class ApplicationMailer
{

    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNewNotification(Application $application)
    {
        $message = new \Swift_Message(
            'Nouvelle candidature',
            'Vous avez reçu une nouvelle candidature.'
        );

        $message
            ->addTo('gildas.delepine@smile.fr')
            ->addFrom('gizou001@gmail.com')
        ;

        $this->mailer->send($message);
    }
}