<?php


namespace Despark\ImagePurify\Interfaces;


use Psr\Log\LoggerInterface;

interface ChainInterface
{
    /**
     * ChainAbstract constructor.
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null);

    /**
     * @param $filePath
     * @return mixed
     */
    public function canHandle($filePath);

    /**
     * @param CommandInterface $command
     * @return mixed
     */
    public function addCommand(CommandInterface $command);


    /**
     * @param CommandInterface[] $commands
     * @return mixed
     */
    public function setCommands(array $commands);

    /**
     * @return CommandInterface[]
     */
    public function getCommands(): array;

    /**
     * @param bool $flag
     * @return mixed
     */
    public function executeFirstOnly(bool $flag);

    /**
     * @param string $filePath
     * @return void
     */
    public function purify($filePath);

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

}