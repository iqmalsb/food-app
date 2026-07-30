@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tables Index</div>

                @if (session()->has('alert-message'))
                    <div class="alert {{ session()->get('alert-type') }}">
                        {{ session()->get('alert-message') }}
                    </div>
                @endif
                
                <div class="card-body">
                    {{-- <form action="" method="">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" name="keyword" value="{{ request()->get('keyword') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form> --}}
                    <a href="{{ route('tables.create') }}"type="button" class="btn btn-dark mt-2">Add New Table</a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Table No</th>
                                <th scope="col">Max Pax</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach ($tables as $table)
                                    <tr>
                                        <th scope="row">{{$loop->iteration}}</th>
                                        <td>{{$table->table_no}}</td>
                                        <td>{{$table->max_pax}}</td>
                                        <td>{{$table->status}}</td>
                                        <td>
                                            <a href="{{ route('tables.show', $table) }}" type="button" class="btn btn-info">Details</a>
                                            <a href="{{ route('tables.delete', $table) }}" type="button" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- {{ $foods->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
