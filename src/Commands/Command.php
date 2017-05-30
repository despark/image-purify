<?php


namespace Despark\ImagePurify\Commands;


use Despark\ImagePurify\Exceptions\CommandException;
use Despark\ImagePurify\Interfaces\CommandInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class Command.
 */
class Command implements CommandInterface
{
    /**
     * @var array
     */
    protected $rawArguments = [];

    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @var string
     */
    protected $bin;

    /**
     * @var string
     */
    protected $actualBin;

    /**
     * @var string|null
     */
    protected $outFile;

    /**
     * @var
     */
    protected $sourceFile;


    /**
     * Command constructor.
     * @param string $bin
     * @param null   $sourceFile
     */
    public function __construct(string $bin, $sourceFile = null)
    {
        $this->bin = $bin;
        $this->sourceFile = $sourceFile;
    }

    /**
     * @return void
     * @throws CommandException
     */
    public function execute()
    {
        $process = $this->getProcess($this->buildCommand());

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new CommandException($e->getMessage(), $e->getProcess()->getExitCode());
        } catch (\Exception $e) {
            throw new CommandException($e->getMessage());
        }
    }

    /**
     * @return string
     */
    public function buildCommand(): string
    {
        return $this->getBin().' '.$this->buildArguments().' '.escapeshellarg($this->getSourceFile());
    }

    /**
     * Set arguments for the command
     *
     * @param array $arguments
     * @return mixed
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Sets raw arguments without processing them
     *
     * @param array $arguments
     * @return mixed
     */
    public function setRawArguments(array $arguments)
    {
        $this->rawArguments = $arguments;
    }

    /**
     * Gets all the arguments
     *
     * @return array
     */
    public function getArguments(): array
    {
        // We will need to process the arguments and merge them.
        $arguments = array_map([$this, 'processArgument'], $this->arguments);

        return array_merge($arguments, $this->rawArguments);
    }

    /**
     * @param $argument
     * @return string
     */
    public function processArgument(string $argument): string
    {
        return escapeshellarg($argument);
    }

    /**
     * Gets the argument of the command at the specified index.
     *
     * @param int $index Index of the desired argument.
     *
     * @return mixed|null
     */
    public function getArgument($index)
    {
        return $this->arguments[$index] ?? null;
    }


    /**
     * @return string
     */
    public function buildArguments()
    {
        return implode(' ', $this->getArguments());
    }

    /**
     * @throws CommandException
     */
    public function getBin()
    {
        if (! isset($this->actualBin)) {
            $this->bin = escapeshellcmd($this->bin);
            $this->actualBin = (new ExecutableFinder())->find($this->bin, $this->bin, ['/usr/local/bin']);
            if (! is_executable($this->actualBin)) {
                throw new CommandException('Binary '.$this->actualBin.' is not executable');
            }
        }

        return $this->actualBin;
    }

    /**
     * @param $command
     * @return Process
     */
    public function getProcess($command): Process
    {
        return new Process($command);
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setSourceFile(string $filePath)
    {
        $this->sourceFile = $filePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getSourceFile(): string
    {
        return $this->sourceFile;
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setOutFile(string $filePath)
    {
        $this->outFile = $filePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutFile(): string
    {
        if (! $this->outFile) {
            return $this->sourceFile;
        }

        return $this->outFile;
    }

    /**
     * @return bool
     */
    protected function isOutSourceEqual(): bool
    {
        return $this->getSourceFile() === $this->getOutFile();
    }
}