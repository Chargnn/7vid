@php if(isset($author)) { $channel = $author; } @endphp

<div class="channels-card generate-background">
    <div class="channels-card-image">
        <a href="{{ route('channel.index', ['userId' => $channel->getId()]) }}">
            <img src="{{ getImage(route('cdn.img.avatar'), $channel->getAvatar()) }}" alt="Avatar">
        </a>
        @if((Auth::check() && Auth::user()->getId() !== $channel->getId()) || !Auth::check())
            @include('shared.video.subscribe')
        @endif
    </div>
    <div class="channels-card-body">
        <div class="channels-title">
            <a href="{{ route('channel.index', ['userId' => $channel->getId()]) }}">
                <p aria-label="{{ $channel->getName() }}">{{ $channel->getName() }}</p>
            </a>
        </div>
    </div>
</div>

