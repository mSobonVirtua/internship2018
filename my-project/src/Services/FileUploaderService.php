<?php /**
       * @noinspection PhpCSValidationInspection
       */

/**
 * VI-36
 *
 * @category  Virtua
 * @package   Virtua_Module
 * @copyright Copyright (c) Virtua
 * @author    Mateusz Soboń <m.sobon@wearevirtua.com>
 */
namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploaderService
 */
class FileUploaderService
{
    /**
     * @var string $targetDirectory
     */
    private $targetDirectory;
    /**
     * @var int $imageMaxSize
     */
    private $imageMaxSize;

    /**
     * FileUploaderService constructor.
     * @param $targetDirectory
     */
    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
        $this->imageMaxSize = 250000;
    }

    /**
     * @param  UploadedFile $file
     * @return string
     * @throws FileException
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    /**
     * @param File $file
     * @return string
     */
    public function uploadFile(File $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    /**
     * @return string
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * @param integer $size
     */
    public function setMaxImageSize($size)
    {
        $this->imageMaxSize = $size;
    }

    /**
     * @param  UploadedFile $file
     * @return bool
     */
    public function isJpegOrPng(UploadedFile $file)
    {
        $mimeType = $file->getMimeType();
        if ($mimeType === "image/png") {
            return true;
        }
        if ($mimeType === "image/jpeg") {
            return true;
        }
        return false;
    }

    /**
     * @param  UploadedFile $file
     * @return bool
     */
    public function isAllowedSize(UploadedFile $file)
    {
        $fileSize = $file->getSize();

        if ($this->isJpegOrPng($file)) {
            if ($fileSize <= $this->imageMaxSize) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param  UploadedFile $file
     * @return bool
     */
    public function isAllowedFileType(UploadedFile $file)
    {
        if ($this->isJpegOrPng($file)) {
            return true;
        }
        return false;
    }

    /**
     * @param  UploadedFile $file
     * @return bool
     */
    public function isAllowed(UploadedFile $file)
    {
        return $this->isAllowedFileType($file) * $this->isAllowedSize($file);
    }
}
