var page = 0;
var pageLoading = false;
function getproductList() {
    if (!pageLoading) {
        pageLoading = true;
        $.ajax({
            url: base_url + "get-products",
            type: "get",
            data: {
                page: page,
                limit: 8,
            },
            dataType: "JSON",
            success: function (response) {
                if (response.status) {
                    if (response.data.length > 0) {
                        if (page == 0) {
                            $(".catagery-list").empty();
                        }
                        response.data.forEach(function (product) {
                            var productDiv = $("<div>").addClass(
                                "content col p-4  shadow m-3 rounded-2"
                            );

                            var carouselId = "product-carousel-" + product.id;
                            var carousel = $("<div>")
                                .addClass("carousel slide")
                                .attr({
                                    id: carouselId,
                                    "data-bs-ride": "carousel",
                                });

                            var indicators = $("<div>").addClass(
                                "carousel-indicators"
                            );
                            var images = product.product_image;
                            for (var i = 0; i < images.length; i++) {
                                var indicator = $("<button>")
                                    .attr({
                                        type: "button",
                                        "data-bs-target": "#" + carouselId,
                                        "data-bs-slide-to": i,
                                        class: i === 0 ? "active" : "",
                                    })
                                    .attr(
                                        "aria-current",
                                        i === 0 ? "true" : "false"
                                    )
                                    .attr("aria-label", "Slide " + (i + 1));
                                indicators.append(indicator);
                            }

                            var carouselInner =
                                $("<div>").addClass("carousel-inner");
                            images.forEach(function (image, index) {
                                var carouselItem = $("<div>")
                                    .addClass(
                                        "carousel-item" +
                                            (index === 0 ? " active" : "")
                                    )
                                    .append(
                                        $("<img>")
                                            .addClass(
                                                "d-block h-250 product-image"
                                            )
                                            .attr("src", image)
                                    );
                                carouselInner.append(carouselItem);
                            });
                            carousel.append(indicators, carouselInner);
                            productDiv.append(carousel);
                            productDiv.append(
                                $(
                                    '<h4 class="pt-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="' +
                                        product.name +
                                        '">'
                                ).text(product.name)
                            );
                            productDiv.append(
                                $("<h6>").text("$" + product.price)
                            );
                            var link = $("<button>")
                                .addClass(
                                    "w-100 m-auto bg-black border-0 btn btn-primary rounded-5 buy-" +
                                        product.id
                                )
                                .text("Add To cart");
                            link.attr("href", "javascript:void(0);");
                            link.on("click", function () {
                                AddTocart(this, product.id);
                            });
                            productDiv.append(link);

                            $(".catagery-list").append(productDiv);

                            var bsCarousel = new bootstrap.Carousel(
                                document.getElementById(carouselId)
                            );
                        });
                        pageLoading = false;
                        page++;
                    }
                } else {
                    if (page == 0) {
                        $(".catagery-list").html("No results found");                        
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }
}

var page_cart = 0;
var pageLoading_cart = false;
function getcartproductList() {
    if (!pageLoading_cart) {
        pageLoading_cart = true;
        $.ajax({
            url: base_url + "get-cart-items",
            type: "get",
            data: {
                page: page_cart,
                limit: 10,
            },
            dataType: "JSON",
            success: function (response) {
                if (response.status) {
                    if (response.data.length > 0) {
                        if (page_cart == 0) {
                            $("#cart-items").empty();
                        }
                        response.data.forEach(function (product) {
                            html = getCartContent(product);
                            $("#cart-items").append(html);
                        });
                        pageLoading_cart = false;
                        page_cart++;
                    }
                } else {
                    if (page_cart == 0) {
                        $("#cart-items").html("No cart items");
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }
}
function AddTocart(event, product_id, quantity = 1) {
    $(event).attr("disabled", true).html("Loading...");
    $.ajax({
        url: base_url + "add-to-cart",
        type: "post",
        data: {
            product_id: product_id,
            user_id: 1,
            quantity: quantity,
            _token: $('input[name="_token"]').val(),
        },
        success: function (response) {
            $(event).attr("disabled", false).html("Add to cart");

            if (response.status) {
                toastr.success(response.message);
                $("#header_cart_count").html(response.cart_count);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}
function getCartContent(product_details) {
    htmlContent =
        '<div class="row gap-3 justify-content-center" id="cart_div_' +
        product_details.id +
        '"><div class="col-lg-8 shadow p-3"><div class="row "><div class="col-lg-4 p-3 cart-product"><img src="' +
        product_details.product_image +
        '"></div><div class="col-lg-8  p-3"><h5>' +
        product_details.name +
        '</h5><span class="font-monospace">Price :</span><span> $' +
        product_details.original_price +
        '</span><br><span class="font-monospace">quantity : </span><span>' +
        product_details.quantity +
        '</span><div class="d-flex gap-2 py-3 flex-column flex-md-row align-items-center"><span class="align-items-center border-end d-flex fs-6 gap-2 pe-2 without-spinner"><button class="align-items-center d-flex  decrement justify-content-center theme-button update_cart_btn bg-transparent border-0	p-0 rounded-circle" onclick="updateCart(this, ' +
        product_details.product_id +
        ')"><i class="bx bx-minus"></i></button><input type="number" data-pro="108" class="product_quantity text-center" value="' +
        product_details.quantity +
        '" min="1" maxlength="2" readonly oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); "><button class="align-items-center d-flex  increment justify-content-center theme-button update_cart_btn bg-transparent border-0 p-0 rounded-circle" onclick="updateCart(this, ' +
        product_details.product_id +
        ')"><i class="bx bx-plus"></i></button></span><span class="border-o pe-2 fs-6 "><a href="#" onclick="removeProductItem(event,' +
        product_details.id +
        ')" class="text-black removeItem" data-id="' +
        product_details.id +
        '"><i class="bx bx-trash"></i> Remove</a></span></div></div></div></div><div class="col-lg-3 shadow p-3"><div class=""><div class="d-flex justify-content-between"><h6 class="my-2">Price</h6><h6 class="my-2">$' +
        product_details.original_price +
        '</h6></div><div class="d-flex justify-content-between"><h6 class="my-2 ">Quantity</h6><h6 class="my-2 pro_quantity_' +
        product_details.product_id +
        '">' +
        product_details.quantity +
        ' </h6></div><hr class="my-2 my-lg-3"><div class="d-flex justify-content-between"><h6 class="">Total Price</h6><h6 class="  sub-total-pro-amount-' +
        product_details.product_id +
        '">$' +
        product_details.total_price +
        '</h6></div><hr class="my-2 my-lg-3"></div></div></div>';
    return htmlContent;
}
function updateCart(button, product_id) {
    var qtyI = $(button).siblings(".product_quantity");
    var count = qtyI.val();
    if ($(button).hasClass("increment")) {
        count++;
        qtyI.val(count);
    } else {
        if (count > 1) {
            count--;
            qtyI.val(count);
        }
    }
    var qtyInput = $(button).siblings(".product_quantity");
    var qty = qtyInput.val();
    if (qty <= 0) {
        toast.error("Invalid quantity");
        return false;
    }
    $.ajax({
        url: base_url + "update-cart",
        type: "post",
        data: {
            product_id: product_id,
            user_id: 1,
            quantity: qty,
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            $(event).attr("disabled", false).html("Add to cart");

            if (response.status) {
                $(".sub-total-pro-amount-" + product_id).html(
                    "$" + parseFloat(response.data.price).toFixed(2)
                );
                $(".pro_quantity_" + product_id).html(response.data.quantity);
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}
function removeProductItem(event, cart_id) {
    event.preventDefault();
    swal({
        title: "Are you sure?",
        text: "Do you want to remove this product from your cart?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function (result) {
        console.log(result);
        if (result) {
            $.ajax({
                url: base_url + "delete-cart",
                type: "post",
                data: {
                    user_id: 1,
                    id: cart_id,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.status) {
                        toastr.success(response.message);
                        if (response.isCartEmpty) {
                            setTimeout(function () {
                                window.location.href = base_url;
                            }, 2000);
                        } else {
                            $("#cart_div_" + cart_id).remove();
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                },
            });
        }
    });
}

$(document).ready(function () {
    if (current_page === "product-list") {
        getproductList();
        $(document).on("scroll", function () {
            var position = $(this).scrollTop();
            var documentHeight = $(document).height();
            var windowHeight = $(window).height();
            var scrollBottom = documentHeight - (position + windowHeight);

            if (scrollBottom < 100 && !pageLoading) {
                getproductList();
            }
        });
    }
    if (current_page === "cart-list") {
        getcartproductList();
        $(document).on("scroll", function () {
            var position = $(this).scrollTop();
            var documentHeight = $(document).height();
            var windowHeight = $(window).height();
            var scrollBottom = documentHeight - (position + windowHeight);

            if (scrollBottom < 100 && !pageLoading) {
                getcartproductList();
            }
        });
    }
    $.ajax({
        url: base_url + "get-cart-count",
        type: "get",
        data: {
            user_id: 1,
        },
        success: function (response) {
            $("#header_cart_count").text(response.cart_count);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
    $("#addProduct").submit(function (e) {
        e.preventDefault();
        var fileInput = document.getElementById("product_images");

        if (fileInput.files.length === 0) {
            alert("Please select at least one image.");
            return;
        }
        $("#btnSubmitProduct").attr("disabled", true).html("Loading...");

        var formData = new FormData($("#addProduct")[0]);
        $.ajax({
            url: base_url + "save-products",
            type: "post",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                $("#btnSubmitProduct").attr("disabled", false).html("Submit");

                if (response.status) {
                    toastr.success(response.message);
                    setTimeout(function () {
                        window.location.href = base_url;
                    }, 3000);
                } else {
                    if (response?.errors?.length > 0) {
                        toastr.error(response?.errors?.[0]);
                    } else {
                        toastr.error(response.message);
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });
});
