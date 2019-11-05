@php if(isset($video)) $author = $video; @endphp
@php if(isset($channel)) $author = $channel; @endphp

<form action="{{ route('video.subscribe') }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="author_id" value="{{ $author->id }}" required>
    <button class="btn btn-danger" type="submit">Subscribe | <strong>{{ \App\Subscription::getSubscriptionCount($author->id) }}</strong></button>
</form>
