<?php
declare(strict_types=1);

namespace AmModa\Lib;

use GdImage;
use Imagick;
use ImagickException;
use RuntimeException;
use Throwable;

/**
 * Converts uploaded images to optimized WebP and downscales oversized images.
 * Prefers Imagick (better resampling and format support), falls back to GD.
 */
final class ImageProcessor
{
    /** Longest edge limit in pixels. */
    public const MAX_LONG_EDGE = 2500;

    /** Shortest edge limit in pixels. */
    public const MAX_SHORT_EDGE = 1500;

    /** WebP encoding quality (0-100). */
    public const WEBP_QUALITY = 85;

    /**
     * Active image engine: 'imagick', 'gd' or 'none'.
     */
    public static function engine(): string
    {
        if (extension_loaded('imagick') && class_exists(Imagick::class)) {
            try {
                $formats = Imagick::queryFormats('WEBP');
            } catch (Throwable) {
                $formats = [];
            }
            if (is_array($formats) && $formats !== []) {
                return 'imagick';
            }
        }

        if (function_exists('imagewebp')
            && function_exists('imagecreatetruecolor')
            && function_exists('imagecopyresampled')
        ) {
            return 'gd';
        }

        return 'none';
    }

    /**
     * True when the server can produce WebP output.
     */
    public static function isSupported(): bool
    {
        return self::engine() !== 'none';
    }

    /**
     * Reads $srcPath, normalizes it and writes optimized WebP to $destPath.
     *
     * @throws RuntimeException When no engine is available or processing fails.
     */
    public function process(string $srcPath, string $destPath): void
    {
        // Shortcut: already a WebP within size limits -> copy as-is (no quality loss).
        $info = @getimagesize($srcPath);
        if ($info !== false && (int) ($info[2] ?? 0) === IMAGETYPE_WEBP) {
            [$targetW, $targetH] = self::targetDimensions((int) $info[0], (int) $info[1]);
            if ($targetW === (int) $info[0] && $targetH === (int) $info[1]) {
                if (!@copy($srcPath, $destPath)) {
                    throw new RuntimeException('Failed to copy WebP file.');
                }
                return;
            }
        }

        switch (self::engine()) {
            case 'imagick':
                $this->processWithImagick($srcPath, $destPath);
                return;
            case 'gd':
                $this->processWithGd($srcPath, $destPath);
                return;
            default:
                throw new RuntimeException('No image engine with WebP support is available.');
        }
    }

    /**
     * Target dimensions honoring long/short edge limits (downscale only).
     *
     * @return array{0:int,1:int} [width, height]
     */
    public static function targetDimensions(int $width, int $height): array
    {
        $longEdge = max($width, $height);
        $shortEdge = min($width, $height);
        if ($longEdge <= 0 || $shortEdge <= 0) {
            return [$width, $height];
        }

        $scale = min(
            1.0,
            self::MAX_LONG_EDGE / $longEdge,
            self::MAX_SHORT_EDGE / $shortEdge
        );

        if ($scale >= 1.0) {
            return [$width, $height];
        }

        return [
            max(1, (int) round($width * $scale)),
            max(1, (int) round($height * $scale)),
        ];
    }

    private function processWithImagick(string $srcPath, string $destPath): void
    {
        try {
            $image = new Imagick();
            $image->readImage($srcPath);

            // Keep only the first frame for animated GIF/WebP.
            if ($image->getNumberImages() > 1) {
                $image = $image->coalesceImages();
                $image->setIteratorIndex(0);
                $first = $image->getImage();
                $image->clear();
                $image->destroy();
                $image = $first;
            }

            if (method_exists($image, 'autoOrientImage')) {
                $image->autoOrientImage();
            }

            $width = $image->getImageWidth();
            $height = $image->getImageHeight();
            [$targetW, $targetH] = self::targetDimensions($width, $height);
            if ($targetW < $width || $targetH < $height) {
                $image->resizeImage($targetW, $targetH, Imagick::FILTER_LANCZOS, 1.0);
            }

            $image->setImageFormat('webp');
            $image->setImageCompressionQuality(self::WEBP_QUALITY);
            $image->stripImage();

            if ($image->writeImage($destPath) !== true) {
                throw new RuntimeException('Imagick failed to write WebP output.');
            }

            $image->clear();
            $image->destroy();
        } catch (ImagickException $e) {
            throw new RuntimeException('Imagick processing failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function processWithGd(string $srcPath, string $destPath): void
    {
        $info = @getimagesize($srcPath);
        if ($info === false) {
            throw new RuntimeException('Unsupported or corrupted image file.');
        }

        $type = (int) ($info[2] ?? 0);
        $source = $this->createGdImageFromType($srcPath, $type);
        if ($source === null) {
            throw new RuntimeException('Unsupported image type for GD.');
        }

        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $source = $this->applyExifOrientationGd($source, $srcPath);
        }

        $width = imagesx($source);
        $height = imagesy($source);
        [$targetW, $targetH] = self::targetDimensions($width, $height);

        if ($targetW !== $width || $targetH !== $height) {
            $resized = imagecreatetruecolor($targetW, $targetH);
            $this->preserveAlphaGd($resized);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetW, $targetH, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        $ok = imagewebp($source, $destPath, self::WEBP_QUALITY);
        imagedestroy($source);

        if ($ok !== true) {
            throw new RuntimeException('GD failed to write WebP output.');
        }
    }

    private function createGdImageFromType(string $srcPath, int $type): ?GdImage
    {
        $image = match ($type) {
            IMAGETYPE_JPEG => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($srcPath) : false,
            IMAGETYPE_PNG  => function_exists('imagecreatefrompng') ? @imagecreatefrompng($srcPath) : false,
            IMAGETYPE_GIF  => function_exists('imagecreatefromgif') ? @imagecreatefromgif($srcPath) : false,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false,
            IMAGETYPE_BMP  => function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($srcPath) : false,
            default        => false,
        };

        return $image instanceof GdImage ? $image : null;
    }

    private function preserveAlphaGd(GdImage $image): void
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        if ($transparent !== false) {
            imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
        }
    }

    private function applyExifOrientationGd(GdImage $image, string $srcPath): GdImage
    {
        $exif = @exif_read_data($srcPath);
        $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 0) : 0;

        switch ($orientation) {
            case 2:
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 3:
                $image = $this->rotateGd($image, 180);
                break;
            case 4:
                imageflip($image, IMG_FLIP_VERTICAL);
                break;
            case 5:
                $image = $this->rotateGd($image, -90);
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 6:
                $image = $this->rotateGd($image, -90);
                break;
            case 7:
                $image = $this->rotateGd($image, 90);
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 8:
                $image = $this->rotateGd($image, 90);
                break;
        }

        return $image;
    }

    private function rotateGd(GdImage $image, int $angle): GdImage
    {
        $rotated = imagerotate($image, $angle, 0);
        if ($rotated instanceof GdImage) {
            imagedestroy($image);
            return $rotated;
        }
        return $image;
    }
}
