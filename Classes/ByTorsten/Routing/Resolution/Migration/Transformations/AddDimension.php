<?php
namespace ByTorsten\Routing\Resolution\Migration\Transformations;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3CR".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeDimension;
use TYPO3\TYPO3CR\Domain\Repository\ContentDimensionRepository;
use TYPO3\TYPO3CR\Domain\Model\ContentDimension;

/**
 * Change the value of a given property.
 */
class AddDimension extends \TYPO3\TYPO3CR\Migration\Transformations\AbstractTransformation {

	/**
	 * @Flow\Inject
	 * @var ContentDimensionRepository
	 */
	protected $contentDimensionRepository;

    /**
     * @Flow\Inject
     * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;

	/**
	 * @var string
	 */
	protected $dimensionName = NULL;

    /**
     * @var string
     */
    protected $dimensionValue = NULL;

    /**
     * @param string $dimensionName
     * @throws \TYPO3\TYPO3CR\Migration\Exception\MigrationException
     */
	public function setDimensionName($dimensionName) {
		$this->dimensionName = $dimensionName;

        $configuredDimensions = $this->contentDimensionRepository->findAll();

        /** @var ContentDimension $configuredDimension */
        foreach($configuredDimensions as $configuredDimension) {
            if ($configuredDimension->getIdentifier() === $dimensionName) {
                $this->dimensionValue = $configuredDimension->getDefault();
            }
        }

        if ($this->dimensionValue === NULL) {
            throw new \TYPO3\TYPO3CR\Migration\Exception\MigrationException(sprintf('No such dimension %s', $dimensionName), 1345821743);
        }

	}

	/**
	 * Change the property on the given node.
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeData $node
	 * @return void
	 */
	public function execute(\TYPO3\TYPO3CR\Domain\Model\NodeData $node) {

        if (in_array($this->dimensionName, array_keys($node->getDimensionValues()))) {
            // Nothing to do
            return;
        }

        $dimensions = $node->getDimensions();
        $dimensions[] = new NodeDimension($node, $this->dimensionName, $this->dimensionValue);

        $node->setDimensions(array());
        $this->persistenceManager->persistAll();
		$node->setDimensions($dimensions);
	}
}
