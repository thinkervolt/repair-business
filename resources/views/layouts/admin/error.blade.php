<div class="position-fixed bottom-15 end-0 p-3" style="z-index: 5; right: 0; top: 0;">
    @if (session()->has('error'))
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="mr-auto"><i class="fas fa-square text-{{ session()->get('error-color') }}"></i></strong>
                <small>Now</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body ">
                <p class="px-md-4 font-weight-bold">{{ session()->get('error') }}</p>
            </div>
        </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="mr-auto"><i class="fas fa-square text-danger"></i></strong>
                <small>Now</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body ">
                <p class="px-md-4 font-weight-bold">{{ $error }}</p>
            </div>
        </div>
        @endforeach
    @endif
</div>