@extends('shared.template')

@section('title')
    Search for {{ $search }}
@endsection

@section('description')
    Results for {{ $search }}
@endsection

@section('content')
    <div class="single-channel-page">
        @include('shared.channel.image')

        <div class="single-channel-nav">
            @include('shared.channel.navbar')
        </div>
        <div class="container-fluid">
            <div class="video-block section-padding">
                <div class="row">
                    @if(count($videos) > 0)
                        <div class="col-md-10 mx-auto">
                            <div class="main-title">
                                <div class="btn-group float-right right-action">
                                    <a href="#" class="right-action-link text-gray" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Sort by <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#"><i class="fas fa-fw fa-star"></i> &nbsp; Top Rated</a>
                                        <a class="dropdown-item" href="#"><i class="fas fa-fw fa-signal"></i> &nbsp; Viewed</a>
                                        <a class="dropdown-item" href="#"><i class="fas fa-fw fa-times-circle"></i> &nbsp; Close</a>
                                    </div>
                                </div>
                                <h6>Results for <b><u>{{ request('search') }}</u></b></h6>
                            </div>
                        </div>
                        <div class="col-lg-10 mx-auto">
                            <div class="row">
                                @foreach($videos as $video)
                                    <div class="col-xl-3 col-sm-6 mb-3">
                                        @include('shared.video.card')
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="col-sm-12 col-md-12 col-lg-12 text-center mb-5 mt-5">
                            <h2>Empty !</h2>
                            <p>We did not find anything for <b><u>{{ request('search') }}</u></b></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
