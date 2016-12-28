<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\Espece;
use NAO\PlatformBundle\Entity\Observation;
use NAO\PlatformBundle\Form\EspeceType;
use NAO\PlatformBundle\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use NAO\PlatformBundle\Form\ObservationType;
use NAO\PlatformBundle\Form\NaturalisteType;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class PlatformController extends Controller
{
    public function indexAction(Request $request)
    {
        $first_visit = $request->cookies->has('charte');

        $user = $this->getUser();
        $typeCompte = ($user == null) ? null : $user->getTypeCompte();
        $espece = new Espece();
        $form = $this->createForm(EspeceType::class, $espece);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'espece' => $espece
            ));
        }

        $manager = $this->getDoctrine()->getManager();
        $listDerObs = $manager
            ->getRepository('NAOPlatformBundle:Observation')
            ->getDerObsValides(30); //Observation des X derniers jours

        // les observations sont encodées en json pour être affichées sur la carte, via le service dédié
        $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listDerObs, $request->getSchemeAndHttpHost());

        return $this->render('NAOPlatformBundle:Platform:index.html.twig', array(
            'form' => $form->createView(),
            'DerObs' => $listDerObs,
            'observation_JSON' => $observation_JSON,
            'typeCompte' => $typeCompte,
            'first_visit' => $first_visit
        ));
    }

    public function rechercherAction(Request $request)
    {
        $user = $this->getUser();
        $typeCompte = ($user == null) ? null : $user->getTypeCompte();

        $form=$this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // on récupère les données de recherche
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            //on récupère les espèces correspondant à la recherche
            $listeEspeces = $manager->getRepository('NAOPlatformBundle:Espece')
                ->findLikeByName($data["nomConcat"], 100);

            //on récupère les observations valides correspondants aux espèces
            $listObserv = $manager
                ->getRepository('NAOPlatformBundle:Observation')
                ->getListObsByEspeceValides($listeEspeces);

            //liste des espèces observées - pas top
            $idsEspecesObservees = [];
            $listEspecesObservees = [];
            foreach ($listObserv as $obs){
                if(!in_array($obs->getEspece()->getId(), $idsEspecesObservees)){
                    array_push($listEspecesObservees, $obs->getEspece());
                    array_push($idsEspecesObservees, $obs->getEspece()->getId());
                }
            }

            // les observations sont encodées en json pour être affichées sur la carte, via le service dédié
            $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listObserv, $request->getSchemeAndHttpHost());

            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'listEspecesObservees' =>$listEspecesObservees,
                'observation_JSON' => $observation_JSON,
                'typeCompte' => $typeCompte,
                'recherche' => $data["nomConcat"],
                'especes' => $listeEspeces
            ));
        }

        return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
            'form' => $form->createView()
        ));

    }

    public function observerAction(Request $request)
    {
        $user = $this->getUser();

        if($user === null){
            $request->getSession()->getFlashBag()->add('notice', 'Merci de vous enregistrer pour réaliser une observation !');
            return $this->redirectToRoute('fos_user_security_login');
        }

        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            if($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            {
                $observation->setValide(true);
                $observation->setEnAttente(false);
                $request->getSession()->getFlashBag()->add('notice', 'Observation bien enregistrée.');
            }
            else
            {
                $observation->setValide(false);
                $observation->setEnAttente(true);
                $request->getSession()->getFlashBag()->add('notice', 'Observation bien enregistrée. En attente de validation.');
            }
            $observation->setUser($user);

            //traitement de la photo , le traitement de l'upload(déplacement, nouveau nom) se fait via le service
            if ($observation->getPhoto() != null) {
                $fichierPhoto = $this->get('nao_platform.fileuploader')->upload($observation->getPhoto(), 'photoDirectory');
                $observation->setPhoto($fichierPhoto);
            }
            else {
                $observation->setPhoto('photo_generique.jpg');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($observation);
            $em->flush();

            return $this->redirectToRoute('nao_platform_home');
        }

        return $this->render('NAOPlatformBundle:Platform:observer.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /* devenir naturaliste */

    public function demandeAction(Request $request)
    {
        // cet espace est interdit aux administrateurs et aux naturalistes
        if ( $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $request->getSession()->getFlashBag()->add('notice', 'Vous ne pouvez pas faire une demande de compte naturaliste, vous l\'êtes déjà !');
            return $this->redirectToRoute('fos_user_profile_show');
        }

        $userManager = $this->get('fos_user.user_manager');
        $naturaliste = $userManager->createUser();

        $form = $this->createForm(NaturalisteType::class, $naturaliste);
        $form->setData($naturaliste);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $naturaliste->setEnabled(false);
                $naturaliste->setValide(false);
                $naturaliste->setTypeCompte(0); /* a modifier qd l'admin le valide*/
                $naturaliste->setEnAttente(true);

                //traitement du pdf , le traitement de l'upload(déplacement, nouveau nom) se fait via le service
                if($naturaliste->getCv()!== null){
                        $pdf = $naturaliste->getCv();
                        $fichierPdf = $this->get('nao_platform.fileuploader')->upload($pdf, 'pdfDirectory');
                        $naturaliste->setCv($fichierPdf);
                }

                $userManager->updateUser($naturaliste);

                $request->getSession()->getFlashBag()->add('notice', 'Demande de compte naturaliste bien enregistrée. Vous allez être contacter par nos équipes.');
                return $this->redirectToRoute('nao_platform_home');
            }

        }
        return $this->render('NAOPlatformBundle:Platform:demandeCompteNaturaliste.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function mentionsAction()
    {
        return $this->render('NAOPlatformBundle:Platform:mentionslegales.html.twig');
    }
}
