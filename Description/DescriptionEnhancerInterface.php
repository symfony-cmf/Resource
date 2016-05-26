<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Component\Resource\Description;

use Puli\Repository\Api\Resource\PuliResource;

interface DescriptionEnhancerInterface
{
    /**
     * Enrich the payload description.
     *
     * @param Description $description
     *
     * @return Description
     */
    public function enhance(Description $description);

    /**
     * Return true if the provider supports the given type.
     *
     * @param CmfResource $resource
     *
     * @return bool
     */
    public function supports(PuliResource $resource);
}