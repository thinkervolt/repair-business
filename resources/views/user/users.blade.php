@extends('layouts.admin')
@section('page')
    Users
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
            <h1 class="h3 mb-0 text-gray-800">Users</h1>

        </div>



        <!-- Content Row -->
        <div class="row">

            <div class="col">

                <!-- INDEX -->

                @if (!$users->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">NAME</th>
                                    <th scope="col">E-MAIL</th>
                                    <th scope="col">ROLE</th>
                                    <th scope="col">PASSWORD</th>
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
                                                        value="user">user</option>
                                                    <option @if ($user->role == 'admin') selected @endif
                                                        value="admin">admin</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="password" name="password" class="form-control form-control-sm"
                                                    placeholder="Password" required>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-warning   btn-sm"><i
                                                        class="fas fa-edit"></i> Update</button>
                                    </form>
                                    <form class="d-inline" method="POST" action="{{ route('delete-user', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger   btn-sm"><i class="fas fa-trash"></i>
                                            Delete</button>
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
