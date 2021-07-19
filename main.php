<?php

declare(strict_types=1);

class Convert
{

    protected string $path;
    protected string $destination;
    protected int $numberOfPages;
    protected int $page;

    public function file(string $path): Convert
    {
        $this->path = $path;
        return $this;
    }

    public function dest(string $dest): Convert
    {
        $this->destination = $dest;
        return $this;
    }

    public function convert(): array
    {
        if(!extension_loaded('imagick')) {
            throw new Exception('Image Magic is required to convert PDF');
        }

        echo "Beginning conversion...\n";
        echo "Converting File: " . $this->path . "\n";
        
        $this->getNumberOfPages();

        echo $this->numberOfPages . " - pages to convert\n";

        if($this->numberOfPages === 0)
        {
            return [];
        }

        return array_map(function($pageNumber) {
            $this->setPage($pageNumber);

            $destination = "{$this->destination}/{$pageNumber}.jpg";

            $this->saveImage($destination);

            return $destination;

        }, range(1, $this->numberOfPages));
    }

    protected function saveImage(string $pathToImage): bool
    {
        $imagick = new \Imagick();
        $imagick->setResolution(300, 300);
        $imagick->setCompressionQuality(100);
        $imagick->readImage(sprintf('%s[%s]', $this->path, $this->page - 1)); // Read the first page
        $imagick->setBackgroundColor('white');
        $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $imagick->setFormat('jpg');

        if (!is_dir($this->destination)) {
            // dir doesn't exist, make it
            mkdir($this->destination);
        }

        return file_put_contents($pathToImage, $imagick) !== false;
    }

    protected function setPage(int $page): Convert
    {
        if($page > $this->numberOfPages || $page < 1)
        {
            throw new Exception("Page {$page} does not exist.");
        }

        $this->page = $page;

        return $this;
    }

    protected function getNumberOfPages(): void
    {
        $imagick = new \Imagick();
        $imagick->pingImage($this->path);
        $this->numberOfPages = $imagick->getNumberImages();
    }
}

$pdfConvert = new Convert();
$pdfConvert->file( __DIR__ . '/documents/sample.pdf')->dest(__DIR__ . '/images/sample')->convert();