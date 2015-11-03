<?php

namespace Visca\Bundle\DoctrineBundle\Naming\Constant;

use Visca\Bundle\DoctrineBundle\Naming\Constant\Interfaces\ConstantNamingInterface;

/**
 * Class DefaultConstantNaming.
 */
final class DefaultConstantNaming implements ConstantNamingInterface
{
    private $substitutions = [
        '^1st' => 'First',
        '^2nd' => 'Second',
        '^3rd' => 'Third',
        '^4th' => 'Fourth',
        '^5th' => 'Fifth',
        '^6th' => 'Sixth',
        '^7th' => 'Seventh',
        '^1' => 'One',
        '^2' => 'Two',
        '^3' => 'Three',
        '^4' => 'Four',
        '^5' => 'Five',
        '^6' => 'Six',
        '^7' => 'Seven',
        '^8' => 'Eight',
        '^9' => 'Nine',
    ];

    /**
     * {@inheritdoc}
     */
    public function getName($className, $propertyName, $propertyValue)
    {
        $constantName = $propertyValue.'_'.$propertyName;

        // Remove all non ASCII
        $constantName = iconv('utf-8', 'ascii//TRANSLIT', $constantName);

        foreach ($this->substitutions as $search => $replace) {
            $pattern = sprintf('@%s@', $search);
            $constantName = preg_replace($pattern, $replace, $constantName);
        }

        // If add a space before number to avoid hugging for 1st, 2nd, 3rd ,etc
        $constantName = preg_replace('([\d]+[st|nd|th|rd])', ' $0', $constantName);

        $constantName = preg_replace_callback('@([a-zA-Z]+)([\d]+)@', function (array $matches) {
            return implode('_', array_slice($matches, 1));
        }, $constantName);

        // If there are uppercase and lowercase, it's probably camelcase
        $parts = [];
        preg_match_all('/((?:^|[A-Z]{1,})[^A-Z]+)/', $constantName, $parts);
        $constantName = implode('_', $parts[0]);

        // Remove leading and trailing spaces
        $constantName = trim($constantName);

        // Replace white space by underscore
        $constantName = preg_replace('@[^\w]@', '_', $constantName);
        $constantName = preg_replace('@[_]{2,}@', '_', $constantName);

        // Use uppercase
        $constantName = strtoupper($constantName);

        return $constantName;
    }
}
