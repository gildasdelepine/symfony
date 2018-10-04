<?php

namespace SML\PlatformBundle\Twig;

use SML\PlatformBundle\Antispam\SMLAntispam;

class AntispamExtension extends \Twig_Extension
{
  /**
   * @var SMLAntispam
   */
  private $smlAntispam;

  public function __construct(SMLAntispam $smlAntispam)
  {
    $this->smlAntispam = $smlAntispam;
  }

  public function checkIfArgumentIsSpam($text)
  {
    return $this->smlAntispam->isSpam($text);
  }

  // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
    );
  }

  // La méthode getName() identifie votre extension Twig, elle est obligatoire
  public function getName()
  {
    return 'SMLAntispam';
  }
}