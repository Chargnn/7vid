<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Video extends Model
{
    // Using UUID instead
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        Video::creating(function ($model) {
            echo "Creating Uuid Model...\n";
            $model->{$model->getKeyName()} = (string)$model->generateNewId();
        });
    }

    public function generateNewId()
    {
        try {
            /** @var \Ramsey\Uuid\Uuid $uuid */
            $uuid = Uuid::generate();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        return $uuid;
    }

    public function category()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }
}
