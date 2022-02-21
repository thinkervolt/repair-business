<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.js') }}"></script>
<script> $(document).ready(function() { 
    $(".toast").toast({ delay: 5000 });
    $(".toast").toast('show'); 
    
}); 
    </script>

@if (Route::current()->getName() == 'view-invoice')
    @include('components.invoice-barcode')
@elseif(Route::current()->getName() == 'view-repair')
    @include('components.repair-barcode')
@else
    @include('components.barcode')
@endif
