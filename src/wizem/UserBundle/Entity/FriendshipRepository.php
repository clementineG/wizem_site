<?php

namespace wizem\UserBundle\Entity;

/**
 * FriendshipRepository
 */
class FriendshipRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * 	Research of all friends of an user
     *
     * @param   integer     $userId     id of user 
     * @param   Boolean     $confirmed  Friends only if state is confirmed
     *
     */
    public function getFriends($userId, $confirmed = true)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('f')
            ->from('wizemUserBundle:Friendship','f')
            ->where('f.user = :userId')
            ->orWhere('f.friend = :userId')
            ->andWhere('f.state != :stateFalse');
            
            if($confirmed == true){
                $q->andWhere('f.state = :state')
                ->setParameter('state', $confirmed);
            }
            
            $q->setParameter('userId', $userId)
            ->setParameter('stateFalse', false);

        return $q->getQuery()->getResult();
    }

}
