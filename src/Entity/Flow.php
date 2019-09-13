<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Flow
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $flow = '{"class": "go.TreeModel","nodeDataArray": [{"key":0, "text":"How can I help you?", "loc":"0 0"}]}';

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Flow
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     * @param mixed $flow
     * @return Flow
     */
    public function setFlow($flow)
    {
        $this->flow = $flow;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Flow
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}