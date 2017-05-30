<?php


namespace Despark\ImagePurify\Interfaces;


interface ImagePurifierInterface
{
    /**
     * @param $filePath
     * @return void
     */
    public function purify($filePath);

}