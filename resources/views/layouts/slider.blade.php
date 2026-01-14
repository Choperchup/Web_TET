<div id="hotSlider" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        @foreach($hotProducts as $key => $item)
            <button type="button" data-bs-target="#hotSlider" data-bs-slide-to="{{ $key }}"
                class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}">
            </button>
        @endforeach
    </div>

    <div class="carousel-inner rounded-4 shadow-lg">
        @foreach($hotProducts as $key => $item)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                <a href="{{ route('products.show', $item->slug) }}">
                    <img src="{{ $item->thumbnail ? asset('storage/' . $item->thumbnail) : 'https://picsum.photos/1200/500' }}"
                        class="d-block w-100 slider-img object-fit-cover" style="height: 500px;" alt="{{ $item->name }}">
                </a>
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-25 rounded shadow-sm p-3">
                    <h5 class="fw-bold">{{ $item->name }}</h5>
                    <p>
                        @if($item->is_on_sale)
                            <span class="badge bg-danger me-2">Giảm giá</span>
                            <span class="text-warning fw-bold">{{ number_format($item->sale_price, 0, ',', '.') }} VNĐ</span>
                        @else
                            <span class="badge bg-warning text-dark me-2">Nổi bật</span>
                            <span>{{ number_format($item->price, 0, ',', '.') }} VNĐ</span>
                        @endif
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#hotSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#hotSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

@push('scripts')
    <script>
        // Đảm bảo bootstrap đã được nạp trước đoạn này
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.querySelector('#hotSlider');
            if (slider) {
                const carousel = new bootstrap.Carousel(slider, {
                    interval: 3000,
                    ride: 'carousel'
                });
            }
        });
    </script>
@endpush