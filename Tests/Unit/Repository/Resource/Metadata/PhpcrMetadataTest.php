<?php

namespace Symfony\Cmf\Component\Resource\Unit\Repository\Metadata\Resource;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symfony\Cmf\Component\Resource\Repository\Resource\Metadata\PhpcrMetadata;

class PhpcrMetadataTest extends ProphecyTestCase
{
    private $node;
    private $property;

    public function setUp()
    {
        $this->node = $this->prophesize('PHPCR\NodeInterface');
        $this->property = $this->prophesize('PHPCR\PropertyInterface');
        $this->metadata = new PhpcrMetadata($this->node->reveal());
    }

    public function provideMethods()
    {
        return array(
            array(
                'getCreationTime',
                'mix:created',
                true,
                'jcr:created',
                new \DateTime('2015-01-01'),
                1420070400,
            ),
            array(
                'getModificationTime',
                'mix:lastModified',
                true,
                'jcr:lastModified',
                new \DateTime('2015-01-01'),
                1420070400,
            ),
            array(
                'getCreationTime',
                'mix:created',
                false,
                null,
                null,
                0,
            ),
            array(
                'getModificationTime',
                'mix:lastModified',
                false,
                null,
                null,
                0,
            ),
            array(
                'getAccessTime',
                null,
                null,
                null,
                null,
                0,
            ),
        );
    }

    /**
     * @dataProvider provideMethods
     */
    public function testMethods($method, $mixinType, $hasMixin, $propertyName, $propertyValue, $expectedValue)
    {
        $this->node->isNodeType($mixinType)->willReturn($hasMixin);
        $this->node->getProperty($propertyName)->willReturn($this->property->reveal());
        $this->property->getDate()->willReturn($propertyValue);

        $res = $this->metadata->{$method}();
        $this->assertEquals($expectedValue, $res);
    }
}
