<?php


namespace Despark\ImagePurify\Chains;


use Despark\ImagePurify\Interfaces\ChainInterface;
use Despark\ImagePurify\Interfaces\CommandInterface;
use Despark\ImagePurify\Interfaces\Executable;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class ChainAbstract.
 */
abstract class ChainAbstract implements ChainInterface
{

    /**
     * @var Executable[]
     */
    protected $commands = [];

    /**
     * @var bool
     */
    protected $executeFirstOnly = false;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ChainAbstract constructor.
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $filePath
     * @return void
     */
    public function purify($filePath)
    {
        if ($this->canHandle($filePath)) {
            foreach ($this->commands as $command) {
                if ($command instanceof CommandInterface) {
                    $command->setSourceFile($filePath);
                }
                if (method_exists($command, 'buildCommand')) {
                    $this->getLogger()->info('Purifier executing: '.$command->buildCommand());
                }
                $command->execute();
                if ($this->isExecuteFirstOnly()) {
                    break;
                }
            }
        }
    }

    /**
     * @param CommandInterface $command
     *
     * @return $this
     */
    public function addCommand(CommandInterface $command)
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @param array $commands
     * @return $this
     */
    public function setCommands(array $commands)
    {
        $this->commands = [];
        foreach ($commands as $command) {
            $this->addCommand($command);
        }

        return $this;
    }

    /**
     * @return CommandInterface[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param bool $flag
     * @return $this;
     */
    public function executeFirstOnly(bool $flag)
    {
        $this->executeFirstOnly = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExecuteFirstOnly(): bool
    {
        return $this->executeFirstOnly;
    }


    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        if (is_null($this->logger)) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

}