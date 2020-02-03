<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Notifications\_Notification;
use App\Subscription;
use App\User;
use App\Video;
use App\VideoSetting;
use Exception;
use getid3_exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VideosController extends Controller
{
    public function __construct()
    {
        $this->middleware('viewsCounter');
        $this->middleware('checkAuthorisation');
    }

    /**
     * Display a listing of the resource.
     *
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return redirect(route('home'));
    }

    /**
     * Search for videos that fits filters
     *
     * @return View
     */
    public function search(): View
    {
        $search = request('search');

        $videos = Video::whereHas('setting', static function ($query) {
                $query->where(['private' => 0]);
        })->where('title', 'like', '%' . $search . '%')
          ->orWhere('description', 'like', '% ' . $search . ' %')
          ->paginate(20, ['*'], 'video_page');

        $authors = User::where('name', 'like', '%' . $search . '%')->paginate(12, ['*'], 'author_page');

        return view('video.search')->with('videos', $videos)
            ->with('authors', $authors);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('video.create')->with('categories', $categories);
    }

    /**
     * Add vote to video
     *
     * @return Response
     * @throws Exception
     */
    public static function vote(): Response
    {
        if (Auth::check() && request()->ajax()) {
            $value = request('value');
            $videoId = request('id');

            if (!Auth::user()->voteVideo((bool)$value, $videoId)) {
                return response(403);
            }
        }

        return response(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     * @throws getid3_exception
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            abort(403);
        }

        $this->validateVideoInputs($request);

        /** @var Video $video */
        $video = new Video();
        $video->setAuthorId(Auth::user()->getId());

        if (empty(request('description'))) {
            $request->merge(['description' => 'No description provided']);
        }

        if ($request->hasFile('upload')) {
            $destinationPath = 'videos';
            $extension = request('upload')->getClientOriginalExtension();
            $allowedExtensions = ['avi', 'flv', 'mov', 'mp4'];

            if (!in_array(strtolower($extension), $allowedExtensions, true)) {
                return redirect('/video')->with('error', 'Wrong format. Must be: avi, flv, mov, mp4');
            }

            if (request('upload') === null) {
                return redirect('/video')->with('error', 'There was an error when uploading your video.');
            }

            $filename = time() . '_' . uniqid('', true) . '.' . $extension;
            request('upload')->move($destinationPath, $filename);

            $video->setTitle(request('title'));
            $video->setDescription(request('description'));
            $video->setCategoryId(request('category'));
            $video->setExtension($extension);
            $video->setLocation($destinationPath . '/' . $filename);

            $getID3 = new \getID3();
            $file = $getID3->analyze(public_path() . '/' . $video->getLocation());

            if((int) $file['playtime_seconds'] > 900){
                return back()->withErrors(['Your video must be 15 minutes or shorter.']);
            }

            $video->setDuration($file['playtime_seconds']);
            $video->setFrameRate($file['frame_rate'] ?? 0);
            $video->setMimeType($file['mime_type']);
        }

        if (request('image') || request('generated_image')) {
            $extension = request('image') ? request('image')->getClientOriginalExtension() : 'jpg';
            $filename = time() . '_' . uniqid('', true) . '.' . $extension;
            $allowedExtensions = ['jpeg', 'jpg', 'png'];

            if (!in_array(strtolower($extension), $allowedExtensions, true)) {
                return redirect(route('video.create'))->with('error', 'Wrong format. Must be: jpeg, jpg, png');
            }

            if (request('image')) {
                request('image')->move(storage_path('app/img/'), $filename);
            } else {
                $base64 = request('generated_image');
                $base64 = str_replace(['data:image/png;base64,', ' '], ['', '+'], $base64);
                $data = base64_decode($base64);

                file_put_contents(storage_path('app/img/') . $filename, $data);
            }

            $video->setThumbnail($filename);
        } else {
            return back()->withErrors(['No thumbnail selected']);
        }

        $video->save();

        /** @var VideoSetting $setting */
        $setting = new VideoSetting();
        $setting->setVideoId($video->getId());
        $setting->setPrivate(request('private') ? 1 : 0);
        $setting->setAllowComments(request('allow_comments') ? 1 : 0);
        $setting->setAllowVotes(request('allow_votes') ? 1 : 0);
        $setting->save();

        /** @var User $author */
        $author = $video->author()->first();
        $subscribers = $author->subscribers();

        if (count($subscribers) > 0) {
            /** @var User $subscriber */
            foreach ($subscribers as $subscriber) {
                $subscriber->notify(
                    new _Notification(
                        [
                            'desc' => $author->getName() . ' uploaded a video',
                            'link' => route('video.show', ['video' => $video->getId()])
                        ]
                    )
                );
            }
        }

        return redirect(route('video.show', ['video' => $video->getId()]))
            ->with('success', 'Your video as been shared.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show($id): View
    {
        /** @var Video $video */
        $video = Video::find($id);

        if ($video === null) {
            abort(404);
        }

        /** @var Builder $comments */
        $comments = Comment::getByFilter(request('filter_comments') ?: '', $video->getId());

        /** @var array $comments */
        $comments = $comments->limit(5)->get();

        /** @var int $subscriptionCount */
        $subscriptionCount = Subscription::where('author_id', '=', $video->author->id)->count();

        /** @var array $relatedVideos */
        $relatedVideos = Video::where('category_id', '=', $video->getCategoryId())
            ->whereHas('setting', static function ($query) {
                    $query->where(['private' => 0]);
            })->limit(9)->get();

        return view('video.show')
            ->with('video', $video)
            ->with('comments', $comments)
            ->with('subscriptionCount', $subscriptionCount)
            ->with('relatedVideos', $relatedVideos)
            ->with('upVotes', $video->getUpVotes())
            ->with('downVotes', $video->getDownVotes());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return RedirectResponse|Redirector|View
     */
    public function edit($id)
    {
        /** @var Video $video */
        $video = Video::find($id);

        if ($video === null) {
            abort(404);
        }

        if (!Auth::check()) {
            abort(405);
        }

        if (Auth::user()->getId() !== $video->author()->first()->getId()) {
            return redirect(route('video.show', ['video' => $video->getId()]));
        }

        return view('video.settings')->with('video', $video);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validateVideoInputs($request);

        /** @var Video $video */
        $video = Video::find($id);

        if ($video === null) {
            abort(404);
        }

        if ($request->hasFile('thumbnail')) {
            $video->update($request->except(['_token', '_method']));
            $this->saveThumbnail($request->file('thumbnail'), $video);
        } else {
            $video->update($request->except(['_token', '_method', 'thumbnail']));
        }

        /** @var VideoSetting $setting */
        $setting = $video->setting()->first();

        $setting->setAllowComments($request->input('allow_comments') ? 1 : 0);
        $setting->setAllowVotes($request->input('allow_votes') ? 1 : 0);
        $setting->setPrivate($request->input('private') ? 1 : 0);

        $video->save();
        $setting->save();

        return back()->with('video', $video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy($id): RedirectResponse
    {
        /** @var Video $video */
        $video = Video::find($id);

        if ($video === null) {
            abort(404);
        }

        if (!Auth::check() || Auth::user()->getId() !== $video->author()->first()->getId()) {
            abort(405);
        }

        $video->delete();

        return redirect(route('channel.index', ['userId' => Auth::user()->getId()]));
    }

    /**
     * Check if form inputs on creating / updating a video are all valid
     *
     * @param $request
     * @throws ValidationException
     */
    public function validateVideoInputs($request): void
    {
        $this->validate(
            $request,
            [
                'title' => 'required|max:64',
                'upload' => 'file',
                'image' => 'file|max:1000',
                'description' => 'max:255',
                'recaptcha' => 'required|recaptcha'
            ],
            [
                'title.required' => 'A title is required for your video.',
                'upload.required' => 'You must choose your video to upload.',
                'upload.file' => 'Your uploaded file must be a video.',
                'image.max' => 'Your thumbnail size must be lower than 1Mb'
            ]
        );
    }
}
