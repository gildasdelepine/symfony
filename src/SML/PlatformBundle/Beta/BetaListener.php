<?php

namespace SML\PlatformBundle\Beta;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
/**
 * Class BetaListener
 * @package SML\PlatformBundle\Beta
 */
class BetaListener
{
  // Notre processeur
  protected $betaHTML;

  // La date de fin de la version bêta :
  // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
  // - Après cette date, on n'affichera plus le « bêta »
  protected $endDate;

  public function __construct(BetaHTMLAdder $betaHTML, $endDate)
  {
    $this->betaHTML = $betaHTML;
    $this->endDate  = new \Datetime($endDate);
  }

  // L'argument de la méthode est un FilterResponseEvent
  public function processBeta(FilterResponseEvent $event)
  {
    // On teste si la requête est bien la requête principale (et non une sous-requête)
    if (!$event->isMasterRequest()) {
      return;
    }

    // Ici on modifie comme on veut la réponse…
    $remainingDays = $this->endDate->diff(new \Datetime())->days;

    if ($remainingDays <= 0) {
      // Si la date est dépassée, on ne fait rien
      return;
    }

    // On utilise notre BetaHRML
    $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);


    // Puis on insère la réponse modifiée dans l'évènement
    $event->setResponse($response);
  }
}