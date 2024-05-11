<nav class="navbar bg-body-tertiary px-4">
    <div class="container-fluid">
       <a href="{{ url('/') }}" class="position-relative">
        <h3 class="text-black">Ecommerce</h3>
       </a>

       <div class="align-items-center d-flex gap-3 justify-content-center">
       <a href="{{ url('cart') }}" class="position-relative">
            <i class='bx bx-cart fs-2 text-black'></i>
            <span class="badge bg-danger rounded-circle position-absolute translate-middle" id="header_cart_count">
                0
            </span>
        </a>
        <a href="{{ url('add-products') }}" class="bg-black border-0 btn btn-primary rounded-5 " >
            Add Product
        </a>
       </div>
    </div>
</nav>
