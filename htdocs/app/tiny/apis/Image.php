<?php namespace tiny;

use GdImage;


interface ImageStructure
{

    public function getImage(): GdImage | null;
}


class Image implements ImageStructure
{

    protected GdImage | null $gdImage;
    protected string $filename;
    protected string | null $mimetype;
    protected string | null $ext;

    public function __construct(string $filename)
    {

        $this->gdImage = null;
        $this->filename = $filename;
        $this->mimetype = null;
        $this->ext = null;

        if (extension_loaded("gd")) {

            // not bad
            $this->__Mime__();

            if (file_exists($filename))
                switch ($this->ext) {
                    case "png":

                        $this->gdImage = @imagecreatefrompng($filename);
                        break;

                    case "jpg":
                    case "jpeg":

                        $this->gdImage = @imagecreatefromjpeg($filename);
                        break;

                    case "gif":

                        $this->gdImage = @imagecreatefromgif($filename);
                        break;
                }
        }
    }

    protected function __Mime__(): string
    {
        if (file_exists($this->filename)) {

            $info = @getimagesize($this->filename);

            if (!empty($info)) {

                $mime = c($info, "mime");

                if (!empty($mime)) {

                    $this->ext = getExtFromMime($mime);
                    $this->mimetype = $mime;
                    return $mime;
                }
            }
        }

        $this->mimetype = "unknown";
        return "unknown";
    }

    public function getImage(): GdImage | null
    {

        return $this->gdImage;
    }

    public function getMime(): string | null
    {

        return $this->mimetype;
    }

    public function scaleImage(int $width, int $height): void
    {

        if ($this->gdImage)
            if (!empty($width) && !empty($height)) {

                $image = @imagescale($this->gdImage, $width, $height);
                if ($image) $this->gdImage = $image;
            }
    }

    public function showImage(): bool
    {

        if (!empty($this->ext))
            switch ($this->ext) {

                case "png":

                    @imagepng($this->gdImage);
                    return true;

                case "jpg":
                case "jpeg":

                    @imagejpeg($this->gdImage);
                    return true;

                case "gif":

                    @imagegif($this->gdImage);
                    return true;
            }

        return false;
    }
}