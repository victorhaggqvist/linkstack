<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/21/15
 * Time: 3:08 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class ApiKey
 * @ORM\Entity()
 * @ORM\Table(name="apikey")
 */
class ApiKey {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $akey;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="apikey")
     * @var User
     */
    protected $user;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $lastuse;

    /**
     * ApiKey constructor.
     */
    public function __construct($akey, $user) {
        $this->akey = $akey;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getAkey()
    {
        return $this->akey;
    }

    /**
     * @param mixed $akey
     */
    public function setAkey($akey)
    {
        $this->akey = $akey;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getLastuse()
    {
        return $this->lastuse;
    }

    /**
     * @param mixed $lastuse
     */
    public function setLastuse($lastuse)
    {
        $this->lastuse = $lastuse;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}