<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessTicketImage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessTicketImageTest extends TestCase
{
    private function optimizedImagePath(): string
    {
        return base_path('tests/__fixtures__/optimized.png');
    }

    private function unoptimizedImagePath(): string
    {
        return base_path('tests/__fixtures__/unoptimized.png');
    }

    private function resizedImagePath(): string
    {
        return base_path('tests/__fixtures__/resized.png');
    }

    /** @test */
    public function resizes_image()
    {
        Storage::fake('public');

        Storage::disk('public')->put('image.png', file_get_contents($this->unoptimizedImagePath()));
        $path = Storage::disk('public')->path('image.png');

        [$width, $height] = getimagesize($path);
        $this->assertNotEquals(400, $width);
        $this->assertNotEquals(240, $height);

        ProcessTicketImage::dispatch('image.png');

        [$width, $height] = getimagesize($path);
        $this->assertEquals(400, $width);
        $this->assertEquals(240, $height);

        $this->assertFileEquals(
            $this->optimizedImagePath(),
            $path,
        );
    }

    /** @test */
    public function optimizes_image_size()
    {
        Storage::fake('public');

        Storage::disk('public')->put('image.png', file_get_contents($this->resizedImagePath()));
        $path = Storage::disk('public')->path('image.png');

        [$width, $height] = getimagesize($path);
        $this->assertEquals(400, $width);
        $this->assertEquals(240, $height);

        ProcessTicketImage::dispatch('image.png');

        clearstatcache();
        $this->assertLessThan(
            filesize($this->resizedImagePath()),
            filesize($path),
        );

        $this->assertFileEquals(
            $this->optimizedImagePath(),
            $path,
        );
    }
}
