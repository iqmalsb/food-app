@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Orders Listing</div>


                <div class="card-body">
                    <form action="" method="">
                        <div class="input-group">
                            {{-- <input type="text" class="form-control" id="searchInput" name="keyword" value="{{ request()->get('keyword') }}">
                            <button type="submit" class="btn btn-primary">Search</button> --}}
                        </div>
                    </form>
                    {{-- <a href="{{ route('food.create') }}"type="button" class="btn btn-dark mt-2">Add New Food</a> --}}
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Order ID No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Descrption</th>
                            <th scope="col">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                            {{-- @foreach ($orders as $order)
                                <tr>
                                    <th scope="row">{{$loop->iteration}}</th>
                                    <td>{{$order->name}}</td>
                                    <td>{{$order->description}}</td>
                                    <td><img src="{{ asset('/storage/'.$order->image) }}" class="img-thumbnail"></td>
                                    <td>
                                        <a href="{{ route('food.show', $food) }}" type="button" class="btn btn-info">Details</a>
                                        <a href="{{ route('food.delete', $food) }}" type="button" class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                            @endforeach --}}

                            {{-- 
                                1	id	bigint unsigned	NULL	NULL	NO	NULL	auto_increment		
                                2	total_price	varchar(255)	utf8mb4	utf8mb4_unicode_ci	NO	NULL			
                                3	is_paid	tinyint(1)	NULL	NULL	NO	0			
                                4	is_dine_in	tinyint(1)	NULL	NULL	NO	1			
                                5	has_cutlery	tinyint(1)	NULL	NULL	NO	0			
                                6	additional_request	varchar(255)	utf8mb4	utf8mb4_unicode_ci	NO	NULL			
                                7	user_id	bigint unsigned	NULL	NULL	NO	NULL		users(id)	
                                8	created_at	timestamp	NULL	NULL	YES	NULL			
                                9	updated_at	timestamp	NULL	NULL	YES	NULL			
                                --}}
                        </tbody>
                      </table>
                      {{-- {{ $foods->links() }} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
