<div class="row">
    @foreach($services as $service)
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    <p class="card-text">
                        <strong>Type:</strong> {{ $service->type }}<br>
                        <strong>Category:</strong> {{ $service->category }}<br>
                        <strong>Rate:</strong> ${{ $service->rate }} per 1000<br>
                        <strong>Min:</strong> {{ $service->min }}<br>
                        <strong>Max:</strong> {{ $service->max }}<br>
                        <strong>Refill:</strong> {{ $service->refill ? 'Yes' : 'No' }}<br>
                        <strong>Cancel:</strong> {{ $service->cancel ? 'Yes' : 'No' }}<br>
                    </p>
                    <a href="#" class="btn btn-primary">Order Now</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $services->links() }}
</div>
