<?php


namespace Despark\ImagePurify\Chains;


class GifChain extends ChainAbstract
{

    /**
     * @param $filePath
     * @return mixed
     */
    public function canHandle($filePath)
    {
        $imageType = exif_imagetype($filePath);

        if ($imageType == IMAGETYPE_GIF) {
            return true;
        }

        return false;
    }
}