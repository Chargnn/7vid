<div class="top-mobile-search">
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('video.search') }}" class="mobile-search">
                <div class="input-group">
                    {{ csrf_field() }}

                    <label for="search-mobile" class="d-none">Search
                        <input type="text" name="search" id="search-mobile" class="form-control" placeholder="Video id, Author, Category ..." required>
                    </label>
                    <div class="input-group-append">
                        <button type="button" aria-label="Search" class="btn btn-dark"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
