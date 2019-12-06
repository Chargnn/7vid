<div class="video-card">
    <div class="video-card-image">
        <a class="play-icon" aria-label="Play video" href="{{ route('video.show', ['video' => $video->id]) }}">
            <i class="fas fa-play-circle"></i>
        </a>
        <a href="{{ route('video.show', ['video' => $video->id]) }}" aria-label="View video">
            <img class="img-fluid" loading="lazy" src="{{ getImage(route('cdn.img'), $video->thumbnail) }}" alt="Video thumbnail">
        </a>
        <div class="time">{{ parseVideoDuration($video->duration) }}</div>
    </div>
    <div class="video-card-body">
        <div class="video-title">
            <a href="{{ route('video.show', ['video' => $video->id]) }}" aria-label="{{strlen($video->title) > 50 ? substr($video->title,0,50)."..." : $video->title}}">
                <h3 class="h4">{{strlen($video->title) > 50 ? substr($video->title,0,50)."..." : $video->title}}</h3>
            </a>
        </div>
        <div class="video-page">
            By <a href="{{ route('channel.index', ['userId' => $video->author->id]) }}" aria-label="{{ $video->author->name }}">{{ $video->author->name }}</a>
        </div>
        <div class="video-view">
            <i class="fas fa-eye"></i> {{ $video->getFormatedViewsCount() }} - <i class="far fa-clock"></i> {{ time_elapsed_string($video->created_at) }}
        </div>
    </div>
</div>
