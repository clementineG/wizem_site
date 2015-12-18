<?php

namespace wizem\UserBundle\Entity;

/**
 * UserEventRepository
 */
class UserEventRepository extends \Doctrine\ORM\EntityRepository
{

    /*
     * 	Récupère l'host de l'évenement passé en parametre (prénom, nom, username)
     */
    public function getHost($eventId)
    {
        $q = $this->_em->createQueryBuilder()
        ->select('user.firstname, user.lastname, user.username')
        ->from('wizemUserBundle:UserEvent','ue')
        ->where('ue.event = :eventId')
        ->andWhere('ue.host = :host')
        ->join('ue.user', 'user')
        ->setParameter('eventId', $eventId)
        ->setParameter('host', true);

        return $q->getQuery()->getSingleResult();
    }

}
