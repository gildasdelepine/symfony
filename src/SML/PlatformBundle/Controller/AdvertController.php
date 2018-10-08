<?php
/**
 * Created by PhpStorm.
 * User: gidel
 * Date: 27/04/18
 * Time: 15:02
 */

namespace SML\PlatformBundle\Controller;

use SML\PlatformBundle\Entity\Advert;
use SML\PlatformBundle\Entity\AdvertSkill;
use SML\PlatformBundle\Entity\Application;
use SML\PlatformBundle\Event\PlatformEvents;
use SML\PlatformBundle\Event\MessagePostEvent;
use SML\PlatformBundle\Form\AdvertEditType;
use SML\PlatformBundle\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class AdvertController extends Controller
{

  public function indexAction($page)
  {
    // On ne sait pas combien de pages il y a
    // Mais on sait qu'une page doit être supérieure ou égale à 1
    if ($page < 1) {
      // On déclenche une exception NotFoundHttpException, cela va afficher
      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
      throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
    }

    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository(Advert::class);

    $listAdvWithCat = $advert->getAdvertWithCategories(array('Développement web', 'Développement mobile', 'Animaux', 'Symfony'));


    $applicationRep = $em->getRepository(Application::class);

    $listApplications = $applicationRep->getApplicationsWithAdvert(3);


    // Mais pour l'instant, on ne fait qu'appeler le template
    return $this->render('@SMLPlatform/Advert/index.html.twig', array(
      'listAdvWithCat' => $listAdvWithCat,
      'listApplications' => $listApplications));
  }

  public function menuAction($limit = null)
  {
    $em = $this->getDoctrine()->getRepository(Advert::class);
    $listAdverts = $em->findBy(array(), array('updatedAt' => 'DESC'), $limit);

    return $this->render('@SMLPlatform/Advert/menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }

  public function viewAction($id)
  {
    // On récupère le repository
    $em = $this->getDoctrine()
      ->getManager();
    //->getRepository(Advert::class);
    // ou ->getRepository('SMLPlatformBundle:Advert');

    // On récupère l'entité correspondante à l'id $id
    $advert = $em->getRepository(Advert::class)->find($id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
    }


    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em->getRepository(Application::class)
      ->findBy(array('advert' => $advert));


    $listAdvertSkills = $em->getRepository(AdvertSkill::class)->findBy(array('advert' => $advert));

    return $this->render('@SMLPlatform/Advert/view.html.twig', array(
      'advert' => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  /**
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function addAction(Request $request)
  {
    $advert = new Advert();
    $form = $this->createForm(AdvertType::class, $advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      // On crée l'évènement avec ses 2 arguments
      $event = new MessagePostEvent($advert->getContent(), $this->getUser());

      // On déclenche l'évènement
      $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

      // On récupère ce qui a été modifié par le ou les listeners, ici le message
      $advert->setContent($event->getMessage());


      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('success', 'Annonce bien enregistrée.');

      // On redirige vers la page de visualisation de l'annonce nouvellement créée
      return $this->redirectToRoute('sml_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('@SMLPlatform/Advert/add.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository('SMLPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
    }

    $form = $this->createForm(AdvertEditType::class, $advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->flush();

      $request->getSession()->getFlashBag()->add('success', 'Annonce bien modifiée.');

      return $this->redirectToRoute('sml_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('@SMLPlatform/Advert/edit.html.twig', array(
      'advert' => $advert,
      'form' => $form->createView()
    ));
  }

  public function deleteAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('SMLPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
    }

    $form = $this->createForm(FormType::class);
    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirectToRoute('sml_platform_home');
    }

    return $this->render('@SMLPlatform/Advert/delete.html.twig', array(
      'advert' => $advert,
      'form' => $form->createView(),
    ));
  }

  public function translationAction($name)
  {
    return $this->render('SMLPlatformBundle:Advert:translation.html.twig', array(
      'name' => $name
    ));
  }
}