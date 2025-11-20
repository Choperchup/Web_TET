<div id="hotSlider" class="carousel slide" data-bs-ride="carousel">
    <!-- Indicators -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#hotSlider" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#hotSlider" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#hotSlider" data-bs-slide-to="2"></button>
    </div>

    <!-- Slides -->
    <div class="carousel-inner rounded-4 shadow-lg">
        <div class="carousel-item active">
            <img src="https://picsum.photos/id/1011/1200/500" class="d-block w-100 slider-img" alt="Slide 1">
            <div class="carousel-caption d-none d-md-block">
                <h5>Top Image 1</h5>
                <p>Ảnh nổi bật nhất tuần</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="https://picsum.photos/id/1015/1200/500" class="d-block w-100 slider-img" alt="Slide 2">
            <div class="carousel-caption d-none d-md-block">
                <h5>Top Image 2</h5>
                <p>Đang được xem nhiều</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="https://picsum.photos/id/1005/1200/500" class="d-block w-100 slider-img" alt="Slide 3">
            <div class="carousel-caption d-none d-md-block">
                <h5>Top Image 3</h5>
                <p>Hot trend hôm nay</p>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#hotSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#hotSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

@push('scripts')
    <script>
        const slider = document.querySelector('#hotSlider');
        const carousel = new bootstrap.Carousel(slider, {
            interval: 3000, // 3 giây đổi slide
            ride: 'carousel'
        });
    </script>
@endpush