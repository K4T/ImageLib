<?php

class Image {

    private $image;
    private $width;
    private $height;

    //copy of source image, so restore option is possible.
    private $originalImage;
    private $originalWidth;
    private $originalHeight;

    public function __construct()
    {
        if (!function_exists("gd_info"))
        {
            throw new Exception('GD library is not initialized!');
        }
    }

    public function create($width, $height)
    {
        $this->destroy();
        
        $this->image = imagecreatetruecolor($width, $height);
        if (!$this->image)
        {
            throw new Exception('Cannot initialize new GD image stream.');
        }

        $this->originalImage = $this->image;
        $this->width = $this->originalWidth = $width;
        $this->height = $this->originalHeight = $height;
    }
    
    public function load($filename)
    {
        if (!file_exists($filename) || !is_readable($filename))
        {
            throw new Exception('File "'.$filename.'" not found or is not readable!');
        }

        $imageInfo = getimagesize($filename);
        if (!$imageInfo)
        {
            throw new Exception('File "'.$filename.'" is not supported!');
        }

        $this->destroy();

        list($this->width, $this->height) = $imageInfo;
        $this->originalWidth = $this->width;
        $this->originalHeight = $this->height;

        switch ($imageInfo[2])
        {
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;

            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $this->image = imagecreatefromjpeg($filename);
                break;

            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;

            default:
                throw new Exception('Image type not supported!');
        }

        $this->originalImage = $this->image;
    }

    public function saveAsJPG($filename, $jpgQuality = 100, $destroy = false)
    {
        $this->isImageInitialized();

        imagejpeg($this->image, $filename, $jpgQuality);

        if ($destroy)
        {
            $this->destroy();
        }
    }

    public function saveAsGIF($filename, $destroy = false)
    {
        $this->isImageInitialized();

        imagegif($this->image, $filename);

        if ($destroy)
        {
            $this->destroy();
        }
    }

    public function saveAsPNG($filename, $destroy = false)
    {
        $this->isImageInitialized();
        
        imagepng($this->image, $filename);

        if ($destroy)
        {
            $this->destroy();
        }
    }

    public function destroy()
    {
        if ($this->image)
        {
            imagedestroy($this->image);

            $this->image = null;
            $this->width = null;
            $this->height = null;
        }
    }

    public function restore()
    {
        $this->isImageInitialized();

        $this->image = $this->originalImage;
        $this->width = $this->originalWidth;
        $this->height = $this->originalHeight;
    }

    public function scaleProportional($newWidth, $newHeight, $scaleSmaller = true)
    {
        $this->isImageInitialized();

        if(!$scaleSmaller && $this->width <= $newWidth && $this->height <= $newHeight)
        {
            return;
        }

        $factor = min(($newWidth / $this->width), ($newHeight / $this->height));
        $finalWidth = ceil($this->width * $factor);
        $finalHeight = ceil($this->height * $factor);

        $scaledImage = imagecreatetruecolor($finalWidth, $finalHeight);
        imagecopyresampled($scaledImage, $this->image, 0, 0, 0, 0, $finalWidth, $finalHeight, $this->width, $this->height);

        $this->setNewImage($scaledImage, $finalWidth, $finalHeight);
    }

    public function scaleCrop($newWidth, $newHeight, $scaleSmaller = true)
    {
        $this->isImageInitialized();

        if(!$scaleSmaller && $this->width <= $newWidth && $this->height <= $newHeight)
        {
            return;
        }

        $factor = min(($this->width / $newWidth), ($this->height / $newHeight));
        $finalWidth = ceil($newWidth * $factor);
        $finalHeight = ceil($newHeight * $factor);

        $x = round(($this->width - $finalWidth) / 2);
        $y = round(($this->height - $finalHeight) / 2);

        $scaledImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($scaledImage, $this->image, 0, 0, $x, $y, $newWidth, $newHeight, $finalWidth, $finalHeight);

        $this->setNewImage($scaledImage, $finalWidth, $finalHeight);
    }

    public function scale($newWidth, $newHeight, $scaleSmaller = true)
    {
        $this->isImageInitialized();

        if(!$scaleSmaller && $this->width <= $newWidth && $this->height <= $newHeight)
        {
            return;
        }

        $scaledImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($scaledImage, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);

        $this->setNewImage($scaledImage, $newWidth, $newHeight);
    }

    public function isImageInitialized()
    {
        if (!$this->image)
        {
            throw new Exception('Image not initialized!');
        }
    }

    public function image()
    {
        return $this->image;
    }

    public function width()
    {
        return $this->width;
    }

    public function height()
    {
        return $this->height;
    }

    private function setNewImage($image, $width, $height)
    {
        $this->image = $image;
        $this->width = $width;
        $this->height = $height;
    }
}

?>
