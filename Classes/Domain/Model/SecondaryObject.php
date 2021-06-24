<?php
namespace Bug\Doctrine\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 * @ORM\Table(name="bug_doctrine_secondaryobject")
 */
class SecondaryObject {

    /**
     * @var MainObject
     * @ORM\ManyToOne(inversedBy="secondaryObjects", cascade={})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $mainObject;

    /**
     * @param MainObject $mainObject
     */
    public function __construct(MainObject $mainObject) {
        $this->mainObject = $mainObject;
    }
}
