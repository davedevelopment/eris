<?php
namespace Eris\Generator;

class FrequencyTest extends \PHPUnit_Framework_TestCase
{
    public function testMoreFrequentGeneratorIsChosenMoreOften()
    {
        $generator = new Frequency([
            [10, 42],
            [1, 21],
        ]);

        $countOf = [42 => 0, 21 => 0];
        for ($i=0; $i<1000; $i++) {
            $countOf[$generator()] += 1;
        }
        $this->assertTrue($countOf[42] > $countOf[21]);
    }

    public function testZeroFrequencyMeansItWillNotBeChosen()
    {
        $generator = new Frequency([
            [0, 42],
            [1, 21],
        ]);

        $countOf = [42 => 0, 21 => 0];
        for ($i=0; $i<1000; $i++) {
            $countOf[$generator()] += 1;
        }
        $this->assertEquals(0, $countOf[42]);
        $this->assertEquals(1000, $countOf[21]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithNoArguments()
    {
        new Frequency([]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testFrequenciesMustBeNaturals()
    {
        new Frequency([
            [10, 42],
            [false, 21],
        ]);
    }

    public function testShrinkDisjointDomains()
    {
        $generator = new Frequency([
            [10, 42],
            [1, 21],
        ]);

        $this->assertEquals(42, $generator->shrink(42));
        $this->assertEquals(21, $generator->shrink(21));
    }

    public function testShrinkIntersectingDomains()
    {
        $generator = new Frequency([
            [10, new Natural(1, 100)],
            [1, new Natural(10, 100)],
        ]);

        $element = 42;
        for ($i=0; $i<100; $i++) {
            $element = $generator->shrink($element);
        }

        $this->assertEquals(1, $element);
    }

    /**
     * @expectedException DomainException
     */
    public function testShrinkSomethingThatIsNotInDomain()
    {
        $generator = new Frequency([
            [10, 42],
            [1, 21],
        ]);

        $generator->shrink('something');
    }
}