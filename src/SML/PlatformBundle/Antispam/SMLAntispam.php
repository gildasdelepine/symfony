<?php
/**
 * Created by PhpStorm.
 * User: gidel
 * Date: 02/05/18
 * Time: 10:36
 */

namespace SML\PlatformBundle\Antispam;


class SMLAntispam
{

    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer = $mailer;
        $this->locale = $locale;
        $this->minLength = (int)$minLength;

    }


    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
}