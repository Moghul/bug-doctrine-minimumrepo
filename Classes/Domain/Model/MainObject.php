<?php
namespace Bug\Doctrine\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Entity
 * @ORM\Table(name="bug_doctrine_mainobject")
 */
class MainObject {

    /**
     * @var string
     * @ORM\Column(length=200)
     */
    protected $name = '';

    /**
     * @var Collection<SecondaryObject>
     * @ORM\OneToMany(mappedBy="mainObject", cascade={}, orphanRemoval=false)
     */
    protected $secondaryObjects;

    public function __construct() {
        $this->secondaryObjects = new ArrayCollection();
    }

    public function getId(): string {
        return $this->Persistence_Object_Identifier;
    }

    public function setSecondaryObjects(array $secondaryObjects): void {
        foreach ($this->secondaryObjects as $secondaryObject) {
            $this->secondaryObjects->removeElement($secondaryObject);
        }

        foreach ($secondaryObjects as $secondaryObject) {
            $this->secondaryObjects->add($secondaryObject);
        }
    }

    /**
     * @return Collection
     */
    public function getSecondaryObjects(): Collection {
        return $this->secondaryObjects;
    }

    public function emptyCollection(): void {
        $this->secondaryObjects->clear();
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
