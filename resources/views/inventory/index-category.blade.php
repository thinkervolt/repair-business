@extends('layouts.admin')
@section('page')
{{ __('repair-business.categories') }}
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('repair-business.categories') }}</h1>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{ route('inventory-create-category') }}" method="POST">
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
                        <div class="col-sm-4"> </div>
                        <div class="col-sm-8">
                            <button type="submit" class="btn  btn-primary mt-3 mb-2 btn-sm"><i
                                    class="fa fa-save"></i> {{ __('repair-business.button_create') }} </button>
                        </div>
                    </div>
                </form>
                @if (!$inventory_categories->isEmpty())
                    <div class="table-responsive">

                        <table class="table table-sm mt-3 table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('repair-business.table_id') }}</th>
                                    <th scope="col">{{ __('repair-business.table_name') }}</th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($inventory_categories as $inventory_category)
                                    <tr>
                                        <form action="{{ route('inventory-update-category', $inventory_category) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <td >
                                                <p class="m-0 p-0">{{ $inventory_category->id }}</p>
                                            </td>
                                            <td><input type="text" name="name" class="form-control form-control-sm"
                                                    value= "{{ $inventory_category->name }}"></td>
                                            <td class="text-right"><button type="submit" class="btn btn-warning  btn-sm"><i
                                                        class="fas fa-edit"></i> {{ __('repair-business.button_update') }}</button></td>
                                        </form>
                                        <td class="text-right">
                                            <form action="{{ route('inventory-delete-category', $inventory_category) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger  btn-sm"><i
                                                        class="fas fa-trash"></i> {{ __('repair-business.button_delete') }}</button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $inventory_categories->links() }}
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
