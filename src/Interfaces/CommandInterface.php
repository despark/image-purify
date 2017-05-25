<?php


namespace Despark\ImagePurify\Interfaces;

/**
 * Interface CommandInterface
 * @package Despark\ImagePurify\Interfaces
 */
interface CommandInterface extends Executable
{

    /**
     * CommandInterface constructor. Initialize the binary
     *
     * @param string $bin
     * @param null   $sourceFile
     */
    public function __construct(string $bin, $sourceFile = null);

    /**
     * Set arguments for the command
     *
     * @param array $arguments
     * @return $this
     */
    public function setArguments(array $arguments);

    /**
     * Sets raw arguments without processing them
     *
     * @param array $arguments
     * @return $this
     */
    public function setRawArguments(array $arguments);

    /**
     * Gets all the arguments
     *
     * @return array
     */
    public function getArguments(): array;

    /**
     * Gets the argument of the command at the specified index.
     *
     * @param int $index Index of the desired argument.
     *
     * @return mixed|null
     */
    public function getArgument($index);

    /**
     * @return $this
     */
    public function setSourceFile(string $filePath);

    /**
     * @return string
     */
    public function getSourceFile(): string;

    /**
     * @return $this
     */
    public function setOutFile(string $filePath);

    /**
     * Get the file the command needs to write in
     *
     * @return string
     */
    public function getOutFile(): string;


    /**
     * @return mixed
     */
    public function getBin();

}