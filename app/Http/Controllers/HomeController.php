<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Country;
use App\User;
use App\Video;
use App\VideoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $newVideos = Video::orderBy('created_at', 'DESC')->limit(16)->get();
        $randomChannels = User::inRandomOrder()->limit(4)->get();
        $popularCategories = Category::withCount('videos')->latest('videos_count')->take(3)->get();

        return view('home.home')->with('newVideos', $newVideos)
            ->with('randomChannels', $randomChannels)
            ->with('categories', $popularCategories);
    }

    public function privacy()
    {
        return view('home.privacy');
    }

    public function settings()
    {
        if (request()->isMethod('post')) {

            $this->validate(request(), [
                'name' => 'required|max:255|min:3',
                'email' => 'required|max:255|min:3',
                'country' => 'required|min:1',
            ]);

            /** @var User $user */
            $user = Auth::user();

            $user->name = request('name');
            $user->email = request('email');

            $country = Country::find(request('country'));
            if ($country) {
                $user->country_id = request('country');
            } else {
                return redirect()->back()->withErrors(['There was an error, please try again.']);
            }

            if (request('new-password')) {
                if (!Hash::check(request('password'), $user->getPassword()) || request('new-password') !== request('confirm-password')) {
                    return redirect()->back()->withErrors(['You\'r password does not match.']);
                } else {
                    $user->password = Hash::make(request('password'));
                }
            }

            $user->save();
            return view('home.settings')->with('success', ['Settings saved.']);
        } else {
            return view('home.settings');
        }
    }

    public function scroll(Request $request)
    {
        if (request()->ajax()) {
            $exclude = request()->input('exclude') ?: [];

            if(request()->input('type') === 'video') {
                $users = User::withCount('videos')->latest('videos_count')->take(3)->whereNotIn('id', $exclude)->get();

                foreach ($users as $user) {
                    if (!count($user->videos) > 0) {
                        return 'Done';
                    }
                }

                return view('shared.video.scroll.result')->with('channels', $users);
            } else if(request()->input('type') === 'comment') {
                $videoId = request()->input('video_id');
                $video = Video::find($videoId);

                if($video) {
                    $comments = Comment::where(['video_id' => $videoId])->orderBy('created_at')->take(5)->whereNotIn('id', $exclude)->get();

                    if (!count($comments) > 0) {
                        return 'Done';
                    }

                    return view('comment.show')->with('comments', $comments);
                }
            } else {
                return 'Done';
            }
        }

        App::abort(405);
    }
}
