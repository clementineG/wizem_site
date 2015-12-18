<?php

namespace wizem\UserBundle\Entity;

/**
 * FriendshipRepository
 */
class FriendshipRepository extends \Doctrine\ORM\EntityRepository
{

    /*
     * 	Recherche de tous les amis d'un user
     */
    public function getFriends($userId)
    {
        $q = $this->_em->createQueryBuilder()
        ->select('f')
        ->from('wizemUserBundle:Friendship','f')
        ->where('f.user = :userId')
        ->orWhere('f.friend = :userId')
        ->setParameter('userId', $userId);

        return $q->getQuery()->getResult();
    }

}
