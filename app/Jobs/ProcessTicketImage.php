<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessTicketImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $path, public string $disk = 'public')
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $path = Storage::disk($this->disk)->path($this->path);

        Image::make($path)->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
        })->limitColors(255, '#ff9900')->save();
    }
}
