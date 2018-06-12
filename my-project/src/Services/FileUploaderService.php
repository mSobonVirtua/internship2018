<?php
/**
 * VI-36
 *
 * @category   Virtua
 * @package    Virtua_Module
 * @copyright  Copyright (c) Virtua
 * @author     Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Services;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileUploaderService
{
    private $targetDirectory;
    private $imageMaxSize;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->imageMaxSize = 250000;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws FileException
     */
    public function upload(UploadedFile $file)
    {
//        if(!$this->isAllowed($file))
//        {
//            throw new FileException("File is too big or have wrong extension.");
//        }
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /** @param integer $size*/
    public function setMaxImageSize($size)
    {
        $this->imageMaxSize = $size;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function isJpegOrPng(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        if($mimeType === "image/png")
        {
            return true;
        }
        if($mimeType === "image/jpeg")
        {
            return true;
        }
        return false;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function isAllowedSize(UploadedFile $file)
    {
        $fileSize = $file->getSize();

        if($this->isJpegOrPng($file))
        {
            if($fileSize <= $this->imageMaxSize)
            {
                return true;
            }
        }
        return false;
    }


    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function isAllowedFileType(UploadedFile $file)
    {
        if($this->isJpegOrPng($file))
        {
            return true;
        }
        return false;
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function isAllowed(UploadedFile $file)
    {
        return $this->isAllowedFileType($file) * $this->isAllowedSize($file);
    }


}