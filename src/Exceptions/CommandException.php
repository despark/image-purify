<?php


namespace Despark\ImagePurify\Exceptions;


use Throwable;

/**
 * Class CommandException.
 */
class CommandException extends PurifyException
{
    /**
     * @var int
     */
    protected $exitCode;

    /**
     * CommandException constructor.
     * @param string         $message
     * @param int            $exitCode
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $exitCode = 0, $code = 0, Throwable $previous = null)
    {
        $this->exitCode = $exitCode;
        parent::__construct($message, $code, $previous);
    }


    /**
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

}