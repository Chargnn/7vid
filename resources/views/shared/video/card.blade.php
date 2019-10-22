<div class="col-xl-3 col-sm-6 mb-3">
    <div class="video-card">
        <div class="video-card-image">
            <a class="play-icon" href="{{ route('video.show', ['video' => $video->id]) }}"><i class="fas fa-play-circle"></i></a>
            <a href="{{ route('video.show', ['video' => $video->id]) }}"><img class="img-fluid" src="/{{ $video->thumbnail }}" alt=""></a>
            <div class="time">{{ gmdate("H:i:s",$video->duration) }}</div>
        </div>
        <div class="video-card-body">
            <div class="video-title">
                <a href="{{ route('video.show', ['video' => $video->id]) }}">{{strlen($video->title) > 50 ? substr($video->title,0,50)."..." : $video->title}}</a>
            </div>
            <div class="video-page text-success">
                {{ $video->author->name }}  <a title="{{ route('channel.index', ['userId' => $video->author->id]) }}" data-placement="top" data-toggle="tooltip" href="#" data-original-title="Verified"><i class="fas fa-check-circle text-success"></i></a>
            </div>
            <div class="video-view">
                {{ $video->category->title }}
                <br>
                <i class="fas fa-eye"></i> 1.8M views&nbsp;<i class="far fa-clock"></i> {{ time_elapsed_string($video->created_at) }}
            </div>
        </div>
    </div>
</div>