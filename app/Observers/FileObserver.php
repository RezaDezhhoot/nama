<?php

namespace App\Observers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileObserver
{
    /**
     * Handle the File "deleted" event.
     */
    public function deleted(File $file): void
    {
        if  (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }
        if (! is_null($file->thumbnail) && Storage::disk($file->disk)->exists($file->thumbnail))
            Storage::disk($file->disk)->delete($file->thumbnail);
    }
}
