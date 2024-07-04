@extends('layouts.admin')
@section('page')
{{ __('repair-business.users') }}
@endsection
@section('page-content')

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.users') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                @if (!$users->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_name') }}</th>
                                    <th scope="col">{{ __('repair-business.table_email') }}</th>
                                    <th scope="col">{{ __('repair-business.table_role') }}</th>
                                    <th scope="col">{{ __('repair-business.table_password') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($users as $user)
                                    <form method="POST" action="{{ route('update-user', $user) }}">

                                        @csrf
                                        @method('PUT')
                                        <tr>
                                            <td> <input type="text" name="name" class="form-control form-control-sm"
                                                    value="{{ $user->name }}" required> </td>
                                            <td> <input type="email" name="email" class="form-control form-control-sm"
                                                    value="{{ $user->email }}" required> </td>
                                            <td>

                                                <select name="role" class="form-control form-control-sm" required>
                                                    <option @if ($user->role == 'user') selected @endif
                                                        value="user">{{ __('repair-business.user') }}</option>
                                                    <option @if ($user->role == 'admin') selected @endif
                                                        value="admin">{{ __('repair-business.admin') }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="password" name="password" class="form-control form-control-sm"
                                                    placeholder="Password" required>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-warning   btn-sm"><i
                                                        class="fas fa-edit"></i> {{ __('repair-business.button_update') }}</button>
                                    </form>
                                    <form class="d-inline" method="POST" action="{{ route('delete-user', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger   btn-sm"><i class="fas fa-trash"></i>
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
