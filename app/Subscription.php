<?php
/**
 * Created by PhpStorm.
 * User: Alexi
 * Date: 2018-12-22
 * Time: 22:42
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'author_id',
        'user_id'
    ];

    public function channel()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function subscribers()
    {
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    /**
     * Check if the logged user is subscribed to the other user
     *
     * @param $authorId
     * @return boolean
     */
    public function isSubscribed($authorId)
    {
        return 0;//$this->subscribers()->where([['author_id', '=', $authorId], ['user_id', '=', Auth::id()]])->exists();
    }

    /**
     * Get subscription count for authorId
     *
     * @param $author_id
     * @return integer
     */
    public static function getSubscriptionCount($authorId)
    {
        return Subscription::where('author_id', '=', $authorId)->count();
    }
}
