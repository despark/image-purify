<?php


namespace Despark\ImagePurify;


use Despark\ImagePurify\Exceptions\PurifyException;
use Despark\ImagePurify\Interfaces\HasChains;
use Despark\ImagePurify\Traits\PurifierChains;

/**
 * Class ImagePurifier.
 */
class ImagePurifier implements HasChains
{
    use PurifierChains;

    /**
     * @var bool
     */
    protected $suppressErrors = false;


    /**
     * @param $filePath
     * @return void
     * @throws PurifyException
     */
    public function purify($filePath)
    {
        foreach ($this->getChains() as $chain) {
            if ($this->suppressErrors()) {
                try {
                    $chain->purify($filePath);
                } catch (PurifyException $exc) {
                    $chain->getLogger()->warning($exc->getMessage());
                }
            } else {
                $chain->purify($filePath);
            }
        }
    }

    /**
     * @return bool
     */
    public function suppressErrors(): bool
    {
        return $this->suppressErrors;
    }

    /**
     * @param bool $suppressErrors
     * @return ImagePurifier
     */
    public function setSuppressErrors(bool $suppressErrors): ImagePurifier
    {
        $this->suppressErrors = $suppressErrors;

        return $this;
    }
}