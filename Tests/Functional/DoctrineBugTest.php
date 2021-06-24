<?php
namespace Bug\Doctrine\Tests\Functional;

use Bug\Doctrine\Domain\Model\MainObject;
use Bug\Doctrine\Domain\Model\SecondaryObject;

class DoctrineBugTest extends \Neos\Flow\Tests\FunctionalTestCase {

    /**
     * @var bool
     */
    protected static $testablePersistenceEnabled = true;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function setUp(): void {
        parent::setUp();

        $this->entityManager = $this->objectManager->get(\Doctrine\ORM\EntityManagerInterface::class);
    }

    /**
     * When changing an object's properties, the changes should only be persisted if the object is scheduled for update.
     * This works correctly for a string property and when adding new objects to a collection,
     *
     * but does not work when removing elements from a collection. Instead, the removed elements are collected as orphans
     * even when the annotation specifically states orphanRemoval=false.
     *
     * @test
     */
    public function doctrineBugTest(): void {
        $mainObject = new MainObject();

        $secondaryObjectOne = new SecondaryObject($mainObject);
        $secondaryObjectTwo = new SecondaryObject($mainObject);

        $mainObject->setName('Before changes');
        $mainObject->setSecondaryObjects([$secondaryObjectOne, $secondaryObjectTwo]);

        $this->persistenceManager->add($mainObject);
        $this->persistenceManager->add($secondaryObjectOne);
        $this->persistenceManager->add($secondaryObjectTwo);

        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $mainObject = $this->persistenceManager->getObjectByIdentifier($mainObject->getId(), MainObject::class);

        self::assertEquals('Before changes', $mainObject->getName());
        self::assertCount(2, $mainObject->getSecondaryObjects());

        // This line is not persisted
        $mainObject->setName('After changes');
        // This line is persisted. The change that happens here causes the collection to be emptied because of orphanRemoval
        $mainObject->setSecondaryObjects([$secondaryObjectOne, $secondaryObjectTwo]);

        // No update called.
        $this->persistenceManager->persistAll();
        $this->persistenceManager->clearState();

        $mainObject = $this->persistenceManager->getObjectByIdentifier($mainObject->getId(), MainObject::class);

        // name doesn't get updated because we didn't call persistenceManager->update
        self::assertEquals('Before changes', $mainObject->getName());

        // secondaryObjects gets updated even though we didn't call persistenceManager->update
        self::assertCount(2, $mainObject->getSecondaryObjects());
    }

}
