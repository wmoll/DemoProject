<?php

namespace Calor\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * Meal
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Calor\ApiBundle\Entity\SessionRepository")
 * @ExclusionPolicy("all")
 */
class Session
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tweets")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=140)
     * @Assert\NotBlank()
     * @Expose
     * @Type("string")
     */
    private $token;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Type("DateTime")
     *
     */
    private $createdAt;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiresAt", type="datetime")
     * @Type("DateTime")
     *
     */
    private $expiresAt;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Meal
     */

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set token
     *
     * @param string $token
     * @return Session
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }
    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Session
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     * @return Session
     */
    public function setExpiresAt()
    {
        $this->expiresAt = new \DateTime('+1 hour');
        return $this;
    }
    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
