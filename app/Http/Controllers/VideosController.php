<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Subscription;
use App\User;
use App\Video;
use App\Vote;
use Faker\Provider\File;
use getid3_exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\ValidationException;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return redirect(route('home'));
    }

    /**
     * Search for videos that fits filters
     *
     * @param Request $request
     * @return HomeController
     */
    public function search(Request $request)
    {
        $search = Input::get('search');

        $videos = Video::where('title', 'like', '%' . $search . '%')->orWhere('description', 'like', '% ' . $search . ' %')->paginate(20, ['*'], 'video_page');
        $authors = User::where('name', 'like', '%' . $search . '%')->paginate(12, ['*'], 'author_page');


        return view('video.search')->with('videos', $videos)
            ->with('authors', $authors);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('video.create')->with('categories', $categories);
    }

    public static function vote()
    {
        $value = Input::post('value');
        $videoId = Input::post('video_id');

        if (Vote::hasVoted($videoId)) {
            /** @var Vote $vq */
            $vq = Vote::where([['author_id', '=', Auth::id()], ['video_id', '=', $videoId]])->first();

            if ($vq->value != $value) {
                $vq->update(['value' => $value]);
            } else {
                $vq->delete();
            }
        } else {
            $vote = new Vote();
            $vote->video_id = $videoId;
            $vote->author_id = Auth::id();
            $vote->value = $value;
            $vote->save();
        }

        return response(200);
    }

    /**
     * Subscribe to another user
     *
     * @return RedirectResponse
     */
    public function subscribe()
    {
        $channelId = Input::post('channel_id');

        if (!isset($channelId) || $channelId <= 0 || !is_numeric($channelId)) {
            return back()->with('error', 'There was an error! Please try again later.');;
        }

        /** @var User $channel */
        $channel = User::find($channelId);

        if (!Auth::user()->isSubscribed($channelId)) {
            Auth::user()->subscribe($channelId);
            $text = 'You have successfuly subscribed to <b>' . $channel->name . '</b>';
        } else {
            Auth::user()->unsubscribe($channelId);
            $text = 'You have successfuly unsubscribed from <b>' . $channel->name . '</b>';
        }

        return back()->with('success', $text);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     * @throws getid3_exception
     */
    public function store(Request $request)
    {
        $this->validateVideoInputs($request);

        $video = new Video;
        $video->author_id = Auth::user()->id;

        if (strlen($request->input('description')) > 0 && strlen(trim($request->input('description'))) === 0) {
            $request->merge(['description' => 'No description provided']);
        }

        if ($request->hasFile('upload')) {
            $this->saveVideo($request->file('upload'), $request, $video);
        }

        if ($request->hasFile('upload')) {
            $this->saveThumbnail($request->file('image'), $video);
        }

        $video->save();

        return redirect(route('video.show', ['video' => $video->ìd]))->with('success', 'Your video as been shared.');
    }

    /**
     * @param \Illuminate\Http\File $file
     * @param Request $request
     * @param Video $video
     * @return RedirectResponse
     * @throws getid3_exception
     */
    private function saveVideo($file, $request, &$video)
    {
        $destinationPath = 'videos';
        $extension = $file->getClientOriginalExtension();
        $allowedExtensions = ['avi', 'flv', 'mov', 'mp4'];

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return redirect('/video')->with('error', 'Wrong format. Must be: avi, flv, wmv, mov, mp4');
        }

        if ($file == null) {
            return redirect('/video')->with('error', 'There was an error when uploading your video.');
        }

        $filename = time() . '_' . uniqid() . '.' . $extension;
        $file->move($destinationPath, $filename);

        $video->title = $request->input('title');
        $video->description = $request->input('description');
        $video->category_id = $request->input('category');
        $video->extension = $extension;
        $video->location = $destinationPath . '/' . $filename;

        $getID3 = new \getID3();
        $file = $getID3->analyze(public_path() . '/' . $video->location);
        $video->duration = $file['playtime_seconds'];
        $video->frame_rate = $file['frame_rate'];
        $video->mime_type = $file['mime_type'];
    }

    /**
     * @param \Illuminate\Http\File $file
     * @param Video $video
     */
    private function saveThumbnail($file, &$video)
    {
        $destinationPath = 'images';
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $allowedExtensions = ['jpeg', 'jpg', 'png'];

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            redirect(route('video.create'))->with('error', 'Wrong format. Must be: jpeg, jpg, png');
        }

        if ($file == null) {
            redirect(route('video.create'))->with('error', 'There was an error when uploading your image.');
        }

        $file->move($destinationPath, $filename);

        $video->thumbnail = $destinationPath . '\\' . $filename;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $video = Video::find($id);
        if ($video === null) {
            abort(404);
        }

        $comments = Comment::where('video_id', '=', $video->id)->orderBy('created_at', 'DESC')->get();
        $subscriptionCount = Subscription::where('author_id', '=', $video->author->id)->count();

        $relatedVideos = Video::where('title', 'like', '%' . $video->title . '%')
            ->orWhere('category_id', '=', $video->category_id)->limit(10)->get();

        return view('video.show')
            ->with('video', $video)
            ->with('comments', $comments)
            ->with('subscriptionCount', $subscriptionCount)
            ->with('relatedVideos', $relatedVideos);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $video = Video::find($id);

        if ($video === null) {
            abort(404);
        }

        return view('video.settings')->with('video', $video);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validateVideoInputs($request, '', '');

        $video = Video::find($id);

        if ($video === null) {
            abort(404);
        }

        $video->update($request->except(['_token', '_method']));

        return back()->with('video', $video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        $video = Video::find($id);

        if ($video === null) {
            return back()->with('error', 'There was an error while trying to delete your video!');
        }

        $video->delete();

        return back();
    }

    /**
     * Check if form inputs on creating / updating a video are all valid
     *
     * @param $request
     * @param string $imageRequired
     * @param string $uploadRequired
     * @throws ValidationException
     */
    public function validateVideoInputs($request, $imageRequired = 'required', $uploadRequired = 'required')
    {
        $this->validate($request, [
            'title' => 'required|max:64',
            'upload' => 'file|' . $uploadRequired,
            'image' => 'file|' . $imageRequired,
            'description' => 'max:255',
            'recaptcha' => 'required|recaptcha'
        ], [
            'title.required' => 'A title is required for your video.',
            'upload.required' => 'You must choose your video to upload.',
            'upload.file' => 'Your uploaded file must be a video.'
        ]);
    }
}
