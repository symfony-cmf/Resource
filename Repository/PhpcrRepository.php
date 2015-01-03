<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\Resource\Repository;

use Puli\Repository\ResourceNotFoundException;
use PHPCR\SessionInterface;
use DTL\Glob\Finder\PhpcrTraversalFinder;
use DTL\Glob\FinderInterface;
use Symfony\Cmf\Component\Resource\Repository\Resource\PhpcrResource;
use Puli\Repository\Resource\Collection\ArrayResourceCollection;

/**
 * Resource repository for PHPCR
 *
 * @author Daniel Leech <daniel@dantleech.com>
 */
class PhpcrRepository extends AbstractPhpcrRepository
{
    /**
     * @var ManagerRegistry
     */
    private $session;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @param SessionInterface $session
     * @param FinderInterface  $finder
     * @param string           $basePath
     */
    public function __construct(SessionInterface $session, $basePath = null, FinderInterface $finder = null)
    {
        parent::__construct($basePath);
        $this->session = $session;
        $this->finder = $finder ?: new PhpcrTraversalFinder($session);
    }

    /**
     * {@inheritDoc}
     */
    public function get($path)
    {
        try {
            $node = $this->session->getNode($this->resolvePath($path));
        } catch (\PathNotFoundException $e) {
            throw new ResourceNotFoundException(sprintf(
                'No PHPCR node could be found at "%s"',
                $path
            ), null, $e);
        }

        $resource = new PhpcrResource($node->getPath(), $node);

        return $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function find($selector, $language = 'glob')
    {
        if ($language != 'glob') {
            throw new UnsupportedLanguageException($language);
        }

        $nodes = $this->finder->find($selector);

        return $this->buildCollection($nodes);
    }

    public function listChildren($path)
    {
        $node = $this->get($path);

        return $this->buildCollection($node->getNodes());
    }

    /**
     * {@inheritDoc}
     */
    public function contains($selector, $language = 'glob')
    {
        return count($this->find($selector, $language)) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function findByTag($tag)
    {
        throw new \Exception('Get by tag not currently supported');
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        return array();
    }

    /**
     * Build a collection of PHPCR resources
     *
     * @return ArrayResourceCollection
     */
    private function buildCollection(array $nodes)
    {
        $collection = new ArrayResourceCollection();

        if (!$nodes) {
            return $collection;
        }

        foreach ($nodes as $node) {
            $path = $this->unresolvePath($node->getPath());
            $resource = new PhpcrResource($path, $node);
            $resource->attachTo($this);
            $collection->add($resource);
        }

        return $collection;
    }
}
