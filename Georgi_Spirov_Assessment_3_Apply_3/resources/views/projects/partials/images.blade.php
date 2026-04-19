@if($images->count())
    <div id="projectImagesCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($images as $image)
                <button type="button"
                        data-bs-target="#projectImagesCarousel"
                        data-bs-slide-to="{{ $loop->index }}"
                        class="{{ $loop->first ? 'active' : '' }}"
                ></button>
            @endforeach
        </div>

        <div class="carousel-inner rounded-3 border shadow-sm overflow-hidden">
            @foreach($images as $image)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $image->path) }}"
                         class="d-block w-100"
                         style="height: 420px; object-fit: cover; cursor: zoom-in;"
                         data-bs-toggle="modal"
                         data-bs-target="#projectImageLightbox"
                         data-index="{{ $loop->index }}"
                    >
                </div>
            @endforeach
        </div>

        @if($images->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#projectImagesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#projectImagesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        @endif
    </div>

    <div class="modal fade" id="projectImageLightbox" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark border-0">
                <div class="modal-body p-0">
                    <div id="lightboxCarousel" class="carousel slide">
                        <div class="carousel-inner">
                            @foreach($images as $image)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image->path) }}"
                                         class="d-block w-100"
                                         style="max-height: 85vh; object-fit: contain;"
                                    >
                                </div>
                            @endforeach
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#lightboxCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#lightboxCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
