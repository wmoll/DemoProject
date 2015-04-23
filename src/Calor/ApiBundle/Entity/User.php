<?php

namespace Calor\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use FOS\UserBundle\Model\UserInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Calor\ApiBundle\Entity\UserRepository")
 * @ExclusionPolicy("all")
 */


class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     * @ORM\Column(name="display_name", type="string", length=140)
     */
    protected $displayName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Meal", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $meals;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $sessions;

    /**
     * @var integer
     *
     * @ORM\Column(name="target", type="integer", nullable=true)
     *
     * @Expose
     */
    protected $target;


    public function __construct()
    {
        parent::__construct();

    }

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
     * Add meal item
     *
     */

    public function addMeal(Meal $meal)
    {
        $this->meals->add($meal);
    }

    /**
     * Remove meal item
     *
     */

    public function removeMeal(Meal $meal )
    {
        $this->meals->removeElement($meal);
    }

    /**
     * Get meals list
     *
     * @return ArrayCollection
     */
    public function getMeal()
    {
        return $this->meals;
    }

    /**
     * Add session item
     *
     */

    public function addSession(Session $session)
    {
        $this->sessions->add($session);
    }

    /**
     * Remove session item
     *
     */

    public function removeSession(Session $session )
    {
        $this->sessions->removeElement($session);
    }

    /**
     * Get session list
     *
     * @return ArrayCollection
     */
    public function getSession()
    {
        return $this->sessions;
    }

    /**
     * Set Display name
     *
     * @param string $displayName
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }
    /**
     * Get Display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return User
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }
    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
}