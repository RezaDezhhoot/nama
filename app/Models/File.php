<?php

namespace App\Models;

use App\Enums\FileStatus;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * @method static find($id)
 */
class File extends Model
{
    use HasFactory , Loggable;

    protected $guarded = ['id'];

    protected $appends = ['url','thumbnail_url'];


    protected $casts = [
        'status' => FileStatus::class
    ];

    protected static function boot(): void
    {
        parent::boot();

        // auto-sets values on creation
        static::creating(function ($query) {
            $query->status = FileStatus::PROCESSED;
        });
    }

    public $timestamps = false;
    public function fileable(): MorphTo
    {
        return $this->morphTo('fileable');
    }

    public function url(): Attribute
    {
        return Attribute::get(function (){
            return filter_var($this->path , FILTER_VALIDATE_URL) ? $this->path : asset(config('filesystems.disks.'.($this->disk ?? 'public').'.url').'/'.ltrim($this->path,'/'));
        });
    }

    public function scopeSubject(Builder $builder , $subject): Builder
    {
        return $builder->where('subject' , $subject);
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(function (){
            return $this->thumbnail ?
                (
                filter_var($this->thumbnail , FILTER_VALIDATE_URL) ? $this->thumbnail : asset(config('filesystems.disks.'.($this->disk ?? 'public').'.url').'/'.ltrim($this->thumbnail,'/'))
                )
                : null;
        });
    }
}
