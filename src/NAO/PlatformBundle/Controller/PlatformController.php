<?php

namespace NAO\PlatformBundle\Controller;

use NAO\PlatformBundle\Entity\EspeceNomVern;
use NAO\PlatformBundle\Entity\Observation;
use NAO\PlatformBundle\Entity\User;
use NAO\PlatformBundle\Form\EspeceNomVernType;
use NAO\PlatformBundle\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use NAO\PlatformBundle\Form\ObservationType;
use NAO\PlatformBundle\Form\NaturalisteType;
use NAO\PlatformBundle\Form\UserParticulierType;
use Symfony\Component\HttpFoundation\Request;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Cookie;


class PlatformController extends Controller
{
    public function indexAction(Request $request)
    {
        $first_visit = $request->cookies->has('charte');

        $user = $this->getUser();
        $typeCompte = ($user == null) ? null : $user->getTypeCompte();
        $espece = new EspeceNomVern();
        $form = $this->createForm(EspeceNomVernType::class, $espece);

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
        $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listDerObs);

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

            // data is an array with "nomVern" keys
            $data = $form->getData();

            $manager = $this->getDoctrine()->getManager();

            //on récupère les espèces correspondant à la recherche
            $listeEspeces = $manager->getRepository('NAOPlatformBundle:EspeceNomVern')
                ->findLikeByName($data["nomConcat"], 100);

            //on récupère les observations valides correspondants aux espèces
            $listObserv = $manager
                ->getRepository('NAOPlatformBundle:Observation')
                ->getListObsByEspeceValides($listeEspeces);

            //liste des espèces observées - pas top
            $idsEspecesObservees = [];
            $listEspecesObservees = [];
            foreach ($listObserv as $obs){
                if(!in_array($obs->getEspeceNomVern()->getId(), $idsEspecesObservees)){
                    array_push($listEspecesObservees, $obs->getEspeceNomVern());
                    array_push($idsEspecesObservees, $obs->getEspeceNomVern()->getId());
                }
            }

            // les observations sont encodées en json pour être affichées sur la carte, via le service dédié
            $observation_JSON = $this->get('service_container')->get('nao_platform.jsonencode')->jsonEncode($listObserv);

            return $this->render('NAOPlatformBundle:Platform:rechercher.html.twig', array(
                'form' => $form->createView(),
                'listEspecesObservees' =>$listEspecesObservees,
                'observation_JSON' => $observation_JSON,
                'typeCompte' => $typeCompte,
                'recherche' => $data["nomConcat"]
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
            $photo = $observation->getPhoto();
            $fichierPhoto = $this->get('nao_platform.fileuploader')->upload($photo);
            $observation->setPhoto($fichierPhoto);

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
        $userManager = $this->get('fos_user.user_manager');
        $naturaliste = $userManager->createUser();

        $form = $this->createForm(NaturalisteType::class, $naturaliste);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $naturaliste->setEnabled(false);
            $naturaliste->setValide(false);
            $naturaliste->setTypeCompte(0); /* a modifier qd l'admin le valide*/
            $naturaliste->setEnAttente(true);
            $userManager->updateUser($naturaliste);

            $request->getSession()->getFlashBag()->add('notice', 'Demande de compte naturaliste bien enregistrée. Vous allez être contacter par nos équipes.');
            return $this->redirectToRoute('nao_platform_home');
        }

        return $this->render('NAOPlatformBundle:Platform:demandeCompteNaturaliste.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
