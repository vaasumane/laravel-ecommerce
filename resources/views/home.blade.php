@extends('layouts.Frontend')

@section('content')
<div class="row justify-content-center py-5">
    @csrf
    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 mt-1 catagery-list justify-content-center" id="load-product-list">
    </div>
</div>
@endsection