<?php

namespace NAO\PlatformBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ObservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ObservationRepository extends \Doctrine\ORM\EntityRepository
{
    function getListObsByUser($id)    {
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.user', 'user')
            ->where('user.id = :id_user')
            ->setParameter('id_user', $id);
        return $qb->getQuery()->getResult();
    }

    function getListObsValidesByUser($id)    {
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.user', 'user')
            ->where('user.id = :id_user')
            ->andWhere('obs.valide = :valide')
            ->andWhere('obs.enAttente = :enAttente')
            ->setParameters(array('id_user' => $id, 'valide' => true, 'enAttente' => false));
        return $qb->getQuery()->getResult();
    }

    function getListObsEnAttenteByUser($id)    {
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.user', 'user')
            ->where('user.id = :id_user')
            ->andWhere('obs.valide = :valide')
            ->andWhere('obs.enAttente = :enAttente')
            ->setParameters(array('id_user' => $id, 'valide' => false, 'enAttente' => true));
        return $qb->getQuery()->getResult();
    }

    function getListObsNonvalideEnAttente() {
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.user', 'user')
            ->where('obs.valide = :valide')
            ->andWhere('user.typeCompte = :typeCompte')
            ->andWhere('obs.enAttente = :enAttente')
            ->setParameters(array('valide' => false, 'typeCompte' => 0, 'enAttente' => true));
        return $qb->getQuery()->getResult();
    }

    //pour récupérer les observations traitées par un naturaliste
    function getListObsTraiteeParNaturaliste($id) {
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.validateur', 'user')
            ->where('user.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getResult();
    }

    //pour récupérer les observations refusées par un naturaliste
    function getListObsRefuseesParNaturaliste($id) {
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.validateur', 'user')
            ->where('user.id = :id')
            ->andWhere('obs.valide = :valid')
            ->andWhere('obs.enAttente = :enAttente')
            ->setParameters(array(
                'id'=> $id,
                'valid'=>false,
                'enAttente'=>false
            ));
        return $qb->getQuery()->getResult();
    }

    function getAllObserv() { // Pour l'export de toutes les observations
        return $this->createQueryBuilder('obs')->getQuery()->getResult();
    }

    function getDerObsValides($jours) { // Observation des X derniers jours / à mettre sur la page d'accueil ?
        $qb = $this->createQueryBuilder('obs')
            ->where('obs.dateObs > :todayMoinsJours')
            ->andWhere('obs.valide = :valide')
            ->setParameters(array(
                'todayMoinsJours' => (new \Datetime())->sub(new \DateInterval('P'.$jours.'D')),
                'valide' => true
                ));
        return $qb->getQuery()->getResult();
    }

    //récupère les observations valides pour une liste d'id d'espèces
    function getListObsByEspeceValides($listEspece) {
        $listEspeceId = [];
        foreach ($listEspece as $espece){
            array_push($listEspeceId, $espece->getId());
        }
        $qb = $this->_em->createQuery("SELECT obs FROM NAOPlatformBundle:Observation obs JOIN obs.espece esp WHERE esp.id IN(:listeEspeceId) AND obs.valide=:valide")
            ->setParameters(array(
                'valide' => true,
                'listeEspeceId' => $listEspece
            ));
        return $qb->getResult();
    }

    //récupère toutes les observations selon les critères de recherche
    function getListObsByParameters($data) {
        /* faire un traitement pour la date*/
        $qb = $this->createQueryBuilder('obs')
            ->leftJoin('obs.validateur', 'validateur')
            ->leftJoin('obs.user', 'user')
            ->leftJoin('obs.espece', 'espece')
            ->where('obs.id > 0'); // condition bidon pour pouvoir écrire des andWhere après

        // Si la recherche ne porte pas sur toutes les espèces
        if($data['espece'] != '') {
            $qb->andWhere('espece.nomConcat = :espece')
                ->setParameter('espece', $data['espece']);
        }
        // Si la recherche ne porte pas sur toutes les dates
        if($data['dateObs'] != '') {
            $date = new \DateTime(($data['dateObs']->format('Y-m-d H:i:s')));
            $qb->andwhere('obs.dateObs > :dateObs_start')
                ->andwhere('obs.dateObs < :dateObs_end')
                ->setParameter('dateObs_start', $date->format('Y-m-d 00:00:00'))
                ->setParameter('dateObs_end', $date->format('Y-m-d 23:59:59'));
        }

        // Si la recherche ne porte pas sur tous les user
        if($data['user'] != '') {
            $qb->andWhere('user.username = :user')
                ->setParameter('user', $data['user']);
        }
        // Si la recherche ne porte pas sur tous les validateurs
        if($data['validateur'] != '') {
            $qb->andWhere('validateur.username = :validateur')
                ->setParameter('validateur', $data['validateur']);
        }

        $qb->orderBy('obs.dateObs', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
