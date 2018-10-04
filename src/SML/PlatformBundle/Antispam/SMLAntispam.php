<?php
/**
 * User: Gildas DELEPINE
 * Date: 02/05/18
 */

namespace SML\PlatformBundle\Antispam;

class SMLAntispam
{
  private $mailer;
  private $locale;
  private $minLength;

  /**
   * SMLAntispam constructor.
   *
   * @param \Swift_Mailer $mailer
   * @param $minLength
   */
  public function __construct(\Swift_Mailer $mailer, $minLength)
  {
    $this->mailer = $mailer;
    $this->minLength = (int)$minLength;
  }

  /**
   * @param mixed $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
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