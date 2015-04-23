<?php

namespace Calor\ApiBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SessionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SessionRepository extends EntityRepository
{
    public function isTokenValid($token)
    {

        /** @var Session $session */
        $session = $this->findOneBy(array('token'=>$token));


        if($session===null){
            return false;
        }

        if ($session->getExpiresAt() < new \DateTime()) {
            $this->validateTokens();
            return false;
        }

        $session->setExpiresAt();
        $this->getEntityManager()->flush();
        return $session->getUser();
    }

    public function validateTokens()
    {

        $qb = $this->createQueryBuilder('s');

        $sessions = $qb->where('s.expiresAt < :date')
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->getResult()
        ;

        foreach ($sessions as $session)
        {
            $this->getEntityManager()->remove($session);
        }

        $this->getEntityManager()->flush();


    }
}
