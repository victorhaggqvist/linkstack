<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 7/20/15
 * Time: 1:48 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Item
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="items")
 */

class Item {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(type="string")   */
    protected $url;

    /** @ORM\Column(type="string")   */
    protected $title;

    /** @ORM\Column(type="string")   */
    protected $tags;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="items")
     * @var User
     */
    protected $user;

    /** @ORM\Column(type="datetime") */
    protected $created;

    /** @ORM\Column(type="datetime") */
    protected $modified;

    /**
     * Item constructor.
     */
    public function __construct() {
        $this->created = new \DateTime();
        if (!$this->modified)
            $this->modified = new \DateTime();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    public function toJson() {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'title' => $this->title,
            'user' => $this->user->getId(),
            'created' => $this->created->format('Y-m-d H:i:s'),
            'modified' => $this->modified->format('Y-m-d H:i:s')
        ];
    }


}