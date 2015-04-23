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
 * @ORM\Entity(repositoryClass="Calor\ApiBundle\Entity\MealRepository")
 * @ExclusionPolicy("all")
 */
class Meal
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     * @Expose
     * @Type("DateTime<'Y-m-d'>")
     */
    private $date;

    /**
     * @var \DateTime
     * @ORM\Column(name="time", type="time")
     * @Expose
     * @Type("DateTime<'H:i:s'>")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=140)
     * @Assert\NotBlank()
     * @Expose
     * @Type("string")
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="calories", type="float")
     * @Assert\NotBlank()
     * @Expose
     * @Type("float")
     */
    private $calories;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tweets")
     */
    private $user;


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
     * Set date
     *
     * @param \DateTime $date
     * @return Meal
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Meal
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Meal
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set calories
     *
     * @param float $calories
     * @return Meal
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Get calories
     *
     * @return float 
     */
    public function getCalories()
    {
        return $this->calories;
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
        $this->user->addMeal($this);
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

    public function getUsername()
    {
        return $this->user->getUsername();
    }
}
