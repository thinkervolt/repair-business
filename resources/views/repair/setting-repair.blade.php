@extends('layouts.admin')
@section('page')
    Settings Repair
@endsection

@section('page-content')


    <!-- Begin Page Content -->
    <div class="container-fluid">
        @if (session()->has('error'))
            <div class="alert {{ session()->get('alert') }} alert-dismissible fade show">
                <li>{{ session()->get('error') }}</li>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Settings Repair</h1>

        </div>



        <!-- Content Row -->
        <div class="row">

            <div class="col">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h5 class="h5 mb-0 text-gray-700">Priority & Status</h5>

                </div>

                <!-- FORM -->
                <form action="{{ route('create-setting-repair') }}" method="POST">
                    @csrf

                    <div class="form-group row m-0">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
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
                        <label for="color" class="col-sm-2 col-form-label">Color</label>
                        <div class="col-sm-10">
                            <select id="color" name="color"
                                class="form-control form-control-sm @error('color') is-invalid @enderror">
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                                <option value="success">Success</option>
                                <option value="danger">Danger</option>
                                <option value="warning">Warning</option>
                                <option value="info">Info</option>
                                <option value="light">Light</option>
                                <option value="white">White</option>
                            </select>
                            @error('color')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <label for="group" class="col-sm-2 col-form-label">Group</label>
                        <div class="col-sm-10">

                            <select id="group" name="group"
                                class="form-control form-control-sm @error('group') is-invalid @enderror">
                                <option value="priority">Priority</option>
                                <option value="status">Status</option>
                            </select>
                            @error('group')
                                <span class="invalid-feedback mb-1" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row m-0">
                        <div class="col-sm-2"> </div>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-block btn-primary mt-3 mb-2 btn-sm"><i
                                    class="fa fa-save"></i> Create</button>
                        </div>
                    </div>
                </form>
                <!-- END FORM -->


                <!-- INDEX -->


                @if (!$priority_status->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">NAME</th>
                                    <th scope="col">GROUP</th>
                                    <th scope="col">COLOR</th>
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
                                                        value="status">Status</option>
                                                    <option @if ($setting->group == 'priority') selected @endif
                                                        value="priority">Priority</option>
                                                </select>

                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col ">
                                                        <select id="color" name="color"
                                                            class="form-control form-control-sm @error('color') is-invalid @enderror">
                                                            <option value="primary"
                                                                @if ($setting->color === 'primary') selected @endif>Primary
                                                            </option>
                                                            <option value="secondary"
                                                                @if ($setting->color === 'secondary') selected @endif>Secondary
                                                            </option>
                                                            <option value="success"
                                                                @if ($setting->color === 'success') selected @endif>Success
                                                            </option>
                                                            <option value="danger"
                                                                @if ($setting->color === 'danger') selected @endif>Danger
                                                            </option>
                                                            <option value="warning"
                                                                @if ($setting->color === 'warning') selected @endif>Warning
                                                            </option>
                                                            <option value="info"
                                                                @if ($setting->color === 'info') selected @endif>Info
                                                            </option>
                                                            <option value="light"
                                                                @if ($setting->color === 'light') selected @endif>Light
                                                            </option>
                                                            <option value="white"
                                                                @if ($setting->color === 'white') selected @endif>White
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col col-auto">
                                                        <i class="fas fa-circle text-{{ $setting->color }} mr-2"></i>
                                                        </di>
                                                    </div>
                                            </td>
                                            <td><button type="submit" class="btn btn-warning btn-block btn-sm"><i
                                                        class="fas fa-edit"></i> Update</button></td>
                                        </form>

                                        <form method="POST" action="{{ route('delete-setting-repair', $setting) }}">
                                            @csrf
                                            @method('DELETE')
                                            <td><button type="submit" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i> Delete</button></td>
                                        </form>
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
