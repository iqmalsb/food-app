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
                    @if(auth()->user()->role === 'superadmin' && !session('current_organisation_id'))
                        <div class="alert alert-warning mt-2 mb-0 py-2 px-3 d-flex align-items-center" style="font-size: 0.9rem;">
                            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                            <span>Please select an active tenant from the header dropdown to create new tables.</span>
                        </div>
                        <button class="btn btn-dark mt-2" disabled>Add New Table</button>
                    @else
                        <a href="{{ route('tables.create') }}" class="btn btn-dark mt-2">Add New Table</a>
                    @endif
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
                                @if(auth()->user()->role === 'superadmin')
                                    <th scope="col">Organisation</th>
                                @endif
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
                                        @if(auth()->user()->role === 'superadmin')
                                            <td>
                                                @if($table->organisation)
                                                    <span class="badge bg-secondary">{{ $table->organisation->name }}</span>
                                                @else
                                                    <span class="badge bg-dark">Global System</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <a href="{{ route('tables.show', $table) }}" type="button" class="btn btn-info btn-sm">Details</a>
                                            <a href="{{ route('tables.delete', $table) }}" type="button" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this table?')">Delete</a>
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
