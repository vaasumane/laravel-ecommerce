@extends('layouts.Frontend')

@section('content')
<div class="row justify-content-center ">
    <h1 class="text-center py-3">Add Product</h1>
    <div class="card col-lg-6">
        <div class="card-body">

            <form id="addProduct" method="post" action="/">
                @csrf

                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label for="product_price" class="form-label">Product Price</label>
                    <input type="number" class="form-control" id="product_price" name="product_price" maxlength="10" required>
                </div>
                <div class="mb-3">
                    <label for="product_images" class="form-label">Product Media</label>
                    <input type="file" class="form-control" id="product_images" name="product_images[]" multiple accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary" id="btnSubmitProduct">Submit</button>
            </form>
         
        </div>

    </div>
</div>
@endsection
