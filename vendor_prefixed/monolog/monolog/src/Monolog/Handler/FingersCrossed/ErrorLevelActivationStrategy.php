<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ILKinguinVendor\Monolog\Handler\FingersCrossed;

use ILKinguinVendor\Monolog\Logger;
/**
 * Error level based activation strategy.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ErrorLevelActivationStrategy implements \ILKinguinVendor\Monolog\Handler\FingersCrossed\ActivationStrategyInterface
{
    private $actionLevel;
    public function __construct($actionLevel)
    {
        $this->actionLevel = \ILKinguinVendor\Monolog\Logger::toMonologLevel($actionLevel);
    }
    public function isHandlerActivated(array $record)
    {
        return $record['level'] >= $this->actionLevel;
    }
}
