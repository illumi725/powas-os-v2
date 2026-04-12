<?php

namespace App\Http\Traits;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

trait QRCodeGenerationTraits
{
    public function getQRCode($string)
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(64, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(0, 0, 0))),
                new SvgImageBackEnd
            )
        ))->writeString($string);

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }
}
