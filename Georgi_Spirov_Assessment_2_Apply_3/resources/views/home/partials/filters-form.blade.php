<form method="GET" action="{{ route('home') }}">
    <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text"
               id="title"
               name="title"
               class="form-control"
               placeholder="Search by title"
               value="{{ request('title') }}"
        >
    </div>

    <div class="mb-3">
        <label for="short_description" class="form-label">Short Description</label>
        <input type="text"
               id="short_description"
               name="short_description"
               class="form-control"
               placeholder="Search by short description"
               value="{{ request('short_description') }}"
        >
    </div>

    <div class="mb-3">
        <label for="phase" class="form-label">Phase</label>
        <select id="phase" name="phase" class="form-select">
            <option value="">All</option>
            @foreach($phases as $phase)
                <option value="{{ $phase->value }}"
                    @selected(request('phase') === $phase->value)>
                    {{ $phase->value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="start_date" class="form-label">From date</label>
        <input type="date"
               id="start_date"
               name="start_date"
               class="form-control"
               value="{{ request('start_date') }}"
        >
    </div>

    <div class="mb-3">
        <label for="end_date" class="form-label">To date</label>
        <input type="date"
               id="end_date"
               name="end_date"
               class="form-control"
               value="{{ request('end_date') }}"
        >
    </div>

    <div class="mb-4">
        <label for="sort" class="form-label">Sort by</label>
        <select id="sort" name="sort" class="form-select">
            <option value="">Most recent</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
            <option value="highest_rated" @selected(request('sort') === 'highest_rated')>Highest rated</option>
            <option value="most_liked" @selected(request('sort') === 'most_liked')>Most liked</option>
            <option value="title_asc" @selected(request('sort') === 'title_asc')>Title A-Z</option>
            <option value="title_desc" @selected(request('sort') === 'title_desc')>Title Z-A</option>
        </select>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-dark">Apply Filters</button>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
