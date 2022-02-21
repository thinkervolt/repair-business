@if (session()->has('error'))
    <div class="position-fixed bottom-15 end-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="mr-auto"><i class="fas fa-bell"></i></strong>
                <small>Now</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body ">
                <p >{{ session()->get('error') }}</p>
            </div>
        </div>

    </div>
@endif
