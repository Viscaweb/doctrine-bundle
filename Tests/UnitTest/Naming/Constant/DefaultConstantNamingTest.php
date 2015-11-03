<?php

namespace Visca\Bundle\DoctrineBundle\Tests\UnitTest\Naming\Constant;

use PHPUnit_Framework_TestCase;
use Visca\Bundle\DoctrineBundle\Naming\Constant\DefaultConstantNaming;

/**
 * Class DefaultConstantNamingTest.
 */
class DefaultConstantNamingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultConstantNaming
     */
    private $namingStrategy;

    /**
     * Provider for the test.
     */
    public function namesDataProvider()
    {
        return [
            [
                '\Visca\Bundle\LicomBundle\Entity\EventType',
                'code',
                'MatchIncidentGoalSoccer',
                'MATCH_INCIDENT_GOAL_SOCCER_CODE',
            ],
            [
                '\Visca\Bundle\LicomBundle\Entity\LocalizationTranslationGraphLabel',
                'code',
                'default',
                'DEFAULT_CODE',
            ],
            [
                '\Visca\Bundle\LicomBundle\Entity\MatchStatusDescription',
                'code',
                'ExtraTime1stHalf2ndTime',
                'EXTRA_TIME_1ST_HALF_2ND_TIME_CODE',
            ],
            [
                '\Visca\Bundle\LicomBundle\Entity\MatchResultType',
                'code',
                'strokes_r3',
                'STROKES_R_3_CODE',
            ],
            [
                '\Visca\Bundle\LicomBundle\Entity\MatchStatusDescription',
                'code',
                'TopEI',
                'TOP_EI_CODE',
            ],
            [
                '\Visca\Bundle\LicomBundle\Entity\StandingColumn',
                'code',
                'goalsForTotal1To15Minute',
                'GOALS_FOR_TOTAL_1_TO_15_MINUTE_CODE',
            ],
        ];
    }

    /**
     * Test that the naming strategy return valid name.
     *
     * @param string $className
     * @param string $propertyName
     * @param string $propertyValue
     * @param string $expectedName
     *
     * @dataProvider namesDataProvider
     */
    public function testNamingStrategy(
        $className,
        $propertyName,
        $propertyValue,
        $expectedName
    ) {
        $actualName = $this->namingStrategy->getName(
            $className,
            $propertyName,
            $propertyValue
        );
        $this->assertEquals($expectedName, $actualName);
    }

    protected function setUp()
    {
        $this->namingStrategy = new DefaultConstantNaming();
    }
}
