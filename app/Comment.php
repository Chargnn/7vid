<?php
/**
 * Created by PhpStorm.
 * User: Alexi
 * Date: 2018-12-18
 * Time: 14:23
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function video()
    {
        return $this->hasOne(Video::class, 'id', 'video_id');
    }

    public function votes()
    {
        return $this->hasMany(CommentVote::class, 'comment_id');
    }

    public function getBody()
    {
        return $this->body;
    }
}
