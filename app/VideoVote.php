<?php

namespace App;


use Cassandra\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class VideoVote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'video_id',
        'author_id',
        'value'
    ];

    /**
     * Get author relation
     *
     * @return HasOne
     */
    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

    /**
     * Get video relation
     *
     * @return HasOne
     */
    public function video(): HasOne
    {
        return $this->hasOne(Video::class, 'id', 'video_id');
    }

    /**
     * Get value
     *
     * @return bool
     */
    public function getValue(): bool
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param bool $value
     */
    public function setValue(bool $value): void
    {
        $this->value = $value;
    }

    /**
     * Set value
     *
     * @param string $videoId
     */
    public function setVideoId(string $videoId): void
    {
        $this->video_id = $videoId;
    }

    /**
     * Set value
     *
     * @param int $authorId
     */
    public function setAuthorId(int $authorId): void
    {
        $this->author_id = $authorId;
    }
}
