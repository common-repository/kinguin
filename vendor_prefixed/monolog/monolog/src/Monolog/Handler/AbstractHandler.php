<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ILKinguinVendor\Monolog\Handler;

use ILKinguinVendor\Monolog\Formatter\FormatterInterface;
use ILKinguinVendor\Monolog\Formatter\LineFormatter;
use ILKinguinVendor\Monolog\Logger;
use ILKinguinVendor\Monolog\ResettableInterface;
/**
 * Base Handler class providing the Handler structure
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
abstract class AbstractHandler implements \ILKinguinVendor\Monolog\Handler\HandlerInterface, \ILKinguinVendor\Monolog\ResettableInterface
{
    protected $level = \ILKinguinVendor\Monolog\Logger::DEBUG;
    protected $bubble = \true;
    /**
     * @var FormatterInterface
     */
    protected $formatter;
    protected $processors = array();
    /**
     * @param int|string $level  The minimum logging level at which this handler will be triggered
     * @param bool       $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = \ILKinguinVendor\Monolog\Logger::DEBUG, $bubble = \true)
    {
        $this->setLevel($level);
        $this->bubble = $bubble;
    }
    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return $record['level'] >= $this->level;
    }
    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }
    /**
     * Closes the handler.
     *
     * This will be called automatically when the object is destroyed
     */
    public function close()
    {
    }
    /**
     * {@inheritdoc}
     */
    public function pushProcessor($callback)
    {
        if (!\is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), ' . \var_export($callback, \true) . ' given');
        }
        \array_unshift($this->processors, $callback);
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return \array_shift($this->processors);
    }
    /**
     * {@inheritdoc}
     */
    public function setFormatter(\ILKinguinVendor\Monolog\Formatter\FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }
        return $this->formatter;
    }
    /**
     * Sets minimum logging level at which this handler will be triggered.
     *
     * @param  int|string $level Level or level name
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = \ILKinguinVendor\Monolog\Logger::toMonologLevel($level);
        return $this;
    }
    /**
     * Gets minimum logging level at which this handler will be triggered.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }
    /**
     * Sets the bubbling behavior.
     *
     * @param  bool $bubble true means that this handler allows bubbling.
     *                      false means that bubbling is not permitted.
     * @return self
     */
    public function setBubble($bubble)
    {
        $this->bubble = $bubble;
        return $this;
    }
    /**
     * Gets the bubbling behavior.
     *
     * @return bool true means that this handler allows bubbling.
     *              false means that bubbling is not permitted.
     */
    public function getBubble()
    {
        return $this->bubble;
    }
    public function __destruct()
    {
        try {
            $this->close();
        } catch (\Exception $e) {
            // do nothing
        } catch (\Throwable $e) {
            // do nothing
        }
    }
    public function reset()
    {
        foreach ($this->processors as $processor) {
            if ($processor instanceof \ILKinguinVendor\Monolog\ResettableInterface) {
                $processor->reset();
            }
        }
    }
    /**
     * Gets the default formatter.
     *
     * @return FormatterInterface
     */
    protected function getDefaultFormatter()
    {
        return new \ILKinguinVendor\Monolog\Formatter\LineFormatter();
    }
}
