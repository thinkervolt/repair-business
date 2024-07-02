<html>

<head>
    <style>
        @font-face {
            font-family: 'Nunito';
            font-style: normal;
            font-weight: 400;
            src: url({{ asset('/vendor/google-fonts/nunito/Nunito-Regular.ttf') }}) format('truetype');
        }
    </style>
</head>
<style>
    @media print {
        @page {
            margin: 0;
        }
    }

    body,
    html {

        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        font-family: 'Nunito', sans-serif;
    }

    .print-area-container {
        display: block;
        width: 100%;
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 0;
        margin-top: 0;
        padding-top: 25px;
    }

    .data-block {
        padding-left: 30px;
        padding-right: 30px;
        padding-bottom: 15px;
    }

    .small-data-line {
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 8px;
    }

    .data-line {
        text-align: left;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 12px;
    }

    .big-data-line {

        text-align: left;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        padding: 0;
        font-size: 18px;
    }

    .center {
        text-align: center;
    }

    .right {
        text-align: right;
    }

    .justify {
        text-align: justify;
    }

    .barcode {
        text-align: center;
        padding-top: 5px;
    }
</style>

<body>
    <div class="print-area-container">
        <div class="data-block">
            <p class="big-data-line center">{{ __('repair-business.drop-off-receipt') }}</p>
        </div>
        <div class="data-block">
            <p class="data-line left">{{ $company_profile->name ?? '' }}</p>
            <p class="data-line left">
                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $company_profile->phone) }}</p>
            <p class="data-line left">{{ $company_profile->email }}</p>
            <p class="data-line left">{{ $company_profile->address }}</p>
        </div>
    </div>
    <div class="data-block">
        <p class="big-data-line center">{{ __('repair-business.repair') }} #{{ $repair->id }}</p>



        <p class="data-line center">{{ date_format($repair->created_at, 'M d, Y') }}</p>
    </div>

    <div class="data-block">
        @if (isset($repair->customer_data))
            <p class="big-data-line">{{ __('repair-business.customer') }}</p>
            <p class="data-line">{{ $repair->customer_data->company }}</p>
            <p class="data-line">{{ $repair->customer_data->first_name }} {{ $repair->customer_data->last_name }}</p>
            <p class="data-line">
                {{ preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $repair->customer_data->phone) }}</p>
            <p class="data-line">{{ $repair->customer_data->email }}</p>
            <p class="data-line">{{ $repair->customer_data->address }} {{ $repair->customer_data->city }}
                {{ $repair->customer_data->state }} {{ $repair->customer_data->zip }}</p>
        @endif
    </div>

    <div class="data-block">
        <p class="big-data-line">{{ __('repair-business.repair') }}</p>
        <p class="data-line">{{ $repair->target }}</p>
        <p class="data-line">{{ $repair->request }}</p>
    </div>

    <div class="data-block">
        @if (isset($repair->estimate))
            <p class="big-data-line right">{{ __('repair-business.estimate') }}
                ${{ number_format((float) $repair->estimate, 2, '.', '') }}</p>
        @endif
    </div>
    @if (!$jobs->isEmpty())
        <div class="data-block">
            <p class="big-data-line">{{ __('repair-business.jobs') }}</p>
            @foreach ($jobs as $job)
                <p class="data-line">{{ $job->data }}</p>
                <p class="data-line">{{ date_format($job->created_at, 'M d, Y') }}</p>
            @endforeach
        </div>
    @endif
    </div>
    <div class="data-block">
        <div class="barcode">
            @if (Route::is('print-repair'))
                @php echo DNS2D::getBarcodeSVG(route('view-repair',$repair->id), 'QRCODE',4,4) @endphp
            @else
                <img src="data:image/png;base64, {!! base64_encode(DNS2D::getBarcodeSVG(route('view-repair', $repair->id), 'QRCODE', 4, 4)) !!} ">
            @endif
        </div>
    </div>
    <div class="data-block">
        <p class="small-data-line justify">{{ $company_profile->terms }}</p>
    </div>

    @if (Route::is('print-repair'))
        <script>
            window.print();
        </script>
    @endif
</body>

</html>
