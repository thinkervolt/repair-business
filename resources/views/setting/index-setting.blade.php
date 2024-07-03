@extends('layouts.admin')
@section('page'){{ __('repair-business.settings') }}@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.settings') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                @if (!$settings->isEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm mt-3 ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_name') }}</th>
                                    <th scope="col">{{ __('repair-business.table_group') }}</th>
                                    <th scope="col">{{ __('repair-business.table_value') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($settings as $setting)
                                    <form method="POST" action="{{ route('update-setting', $setting) }}">
                                        @csrf
                                        @method('PUT')
                                        <tr>
                                            <td>{{ $setting->name }}</td>
                                            <td>{{ $setting->group }}</td>
                                            <td>
                                                <textarea name="data" class="form-control form-control-sm m-0 overflow-auto">{{ $setting->data }}</textarea>
                                            </td>
                                            <td><button class="btn btn-warning "><i class="fas fa-edit"></i> {{ __('repair-business.button_update') }}</button></td>
                                        </tr>
                                    </form>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-secondary" role="alert">
                        {{ __('repair-business.no-information-to-show') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
