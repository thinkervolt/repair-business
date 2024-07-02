@extends('layouts.admin')
@section('page')
    {{ __('repair-business.repair-settings') }}
@endsection

@section('page-content')

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.repair-settings') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h5 class="h5 mb-0 text-gray-700">{{ __('repair-business.priority_and_status') }}</h5>
                </div>
                <form action="{{ route('create-setting-repair') }}" method="POST">
                    @csrf

                    <div class="form-group row m-0">
                        <label for="name" class="col-sm-4 col-form-label">{{ __('repair-business.input_name') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                id="name" value="{{ old('name') }}" name="name" placeholder="">
                            @error('name')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="color"
                            class="col-sm-4 col-form-label">{{ __('repair-business.input_color') }}</label>
                        <div class="col-sm-8">
                            <select id="color" name="color"
                                class="form-control form-control-sm @error('color') is-invalid @enderror">
                                <option value="primary">{{ __('repair-business.input_primary') }}</option>
                                <option value="secondary">{{ __('repair-business.input_secondary') }}</option>
                                <option value="success">{{ __('repair-business.input_success') }}</option>
                                <option value="danger">{{ __('repair-business.input_danger') }}</option>
                                <option value="warning">{{ __('repair-business.input_warning') }}</option>
                                <option value="info">{{ __('repair-business.input_info') }}</option>
                                <option value="light">{{ __('repair-business.input_light') }}</option>
                                <option value="white">{{ __('repair-business.input_white') }}</option>
                            </select>
                            @error('color')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="group"
                            class="col-sm-4 col-form-label">{{ __('repair-business.input_group') }}</label>
                        <div class="col-sm-8">

                            <select id="group" name="group"
                                class="form-control form-control-sm @error('group') is-invalid @enderror">
                                <option value="priority">{{ __('repair-business.input_priority') }}</option>
                                <option value="status">{{ __('repair-business.input_status') }}</option>
                            </select>
                            @error('group')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <div class="col-sm-4"> </div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn  btn-primary mt-3 mb-2 btn-sm"><i class="fa fa-save"></i>
                                {{ __('repair-business.button_create') }}</button>
                        </div>
                    </div>
                </form>
                @if (!$priority_status->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_name') }}</th>
                                    <th scope="col">{{ __('repair-business.table_group') }}</th>
                                    <th scope="col">{{ __('repair-business.table_color') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($priority_status as $setting)
                                    <tr>
                                        <form method="POST" action="{{ route('update-setting-repair', $setting) }}">
                                            @csrf
                                            @method('PUT')
                                            <td><input type="text" class="form-control form-control-sm" name="name"
                                                    value="{{ $setting->name }}"> </td>
                                            <td>

                                                <select id="group" name="group"
                                                    class="form-control form-control-sm @error('group') is-invalid @enderror">
                                                    <option @if ($setting->group == 'status') selected @endif
                                                        value="status">{{ __('repair-business.input_status') }}</option>
                                                    <option @if ($setting->group == 'priority') selected @endif
                                                        value="priority">{{ __('repair-business.input_priority') }}
                                                    </option>
                                                </select>

                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col ">
                                                        <select id="color" name="color"
                                                            class="form-control form-control-sm @error('color') is-invalid @enderror">
                                                            <option value="primary"
                                                                @if ($setting->color === 'primary') selected @endif>
                                                                {{ __('repair-business.input_primary') }}
                                                            </option>
                                                            <option value="secondary"
                                                                @if ($setting->color === 'secondary') selected @endif>
                                                                {{ __('repair-business.input_secondary') }}
                                                            </option>
                                                            <option value="success"
                                                                @if ($setting->color === 'success') selected @endif>
                                                                {{ __('repair-business.input_success') }}
                                                            </option>
                                                            <option value="danger"
                                                                @if ($setting->color === 'danger') selected @endif>
                                                                {{ __('repair-business.input_danger') }}
                                                            </option>
                                                            <option value="warning"
                                                                @if ($setting->color === 'warning') selected @endif>
                                                                {{ __('repair-business.input_warning') }}
                                                            </option>
                                                            <option value="info"
                                                                @if ($setting->color === 'info') selected @endif>
                                                                {{ __('repair-business.input_info') }}
                                                            </option>
                                                            <option value="light"
                                                                @if ($setting->color === 'light') selected @endif>
                                                                {{ __('repair-business.input_light') }}
                                                            </option>
                                                            <option value="white"
                                                                @if ($setting->color === 'white') selected @endif>
                                                                {{ __('repair-business.input_white') }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col col-auto">
                                                        <i class="fas fa-circle text-{{ $setting->color }} mr-2"></i>
                                                        </di>
                                                    </div>
                                            </td>
                                            <td class="text-right"><button type="submit" class="btn btn-warning  btn-sm"><i
                                                        class="fas fa-edit"></i>
                                                    {{ __('repair-business.button_update') }}</button></td>
                                        </form>
                                        <td class="text-right">
                                            <form method="POST" action="{{ route('delete-setting-repair', $setting) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger  btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    {{ __('repair-business.button_delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
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
