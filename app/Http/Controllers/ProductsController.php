<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProduct;
use App\Models\Carts;
use App\Models\ProductImages;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function addProducts()
    {
        $data["title"] = "Add Product";
        $data["page"] = "add-product";
        return view('addproducts', $data);
    }
    public function getAllProducts()
    {
        $data["title"] = "Product List";
        $data["page"] = "product-list";
        return view('home', $data);
    }
    function createSlug($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
        $string = strtolower($string);
        $slug = str_replace(' ', '-', $string);
        return $slug;
    }
    public function SaveProducts(AddProduct $request)
    {
        DB::beginTransaction();
        try {
            $addProduct = new Products();
            $addProduct->name = $request->input('product_name');
            $addProduct->slug = $this->createSlug($request->input('product_name'));
            $addProduct->price = $request->input('product_price');
            $addProduct->save();

            $productId = $addProduct->id;

            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $image) {
                    $addProductImage = new ProductImages();
                    $addProductImage->product_id = $productId;
                    $imagePath = $image->store('public/product_images');

                    $imagePath = str_replace('public/', '', $imagePath);

                    $addProductImage->image_url = $imagePath;

                    $addProductImage->save();
                }
            }

            DB::commit();

            return response()->json(['type' => 'success', 'code' => 200, 'status' => true, 'message' => 'Product added successfully.', 'toast' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error product adding: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
    public function getProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $page = $request->page;
            $limit = $request->limit;
            $offset = $page * $limit;

            $product_query = Products::query();
            if ($request->filled('page')) {
                $product_query->skip($offset);
            }
            if ($request->filled('limit')) {
                $product_query->take($limit);
            }
            $product_query->orderBy("id", "desc");
            $Products = $product_query->get()->toArray();

            $result = array();
            if (!empty($Products)) {
                foreach ($Products as $key => $value) {
                    $product_image = ProductImages::where("product_id", $value["id"])
                        ->orderBy("id", "asc")->get();
                    $prod_img = array();
                    if (!empty($product_image)) {
                        foreach ($product_image as $key => $value1) {
                            $prod_img[] = config("app.url") . "storage/" . $value1->image_url;
                        }
                    }

                    $value["product_image"] = $prod_img;
                    $result[] = $value;
                }
            }
            if (!empty($result)) {
                return response()->json(['type' => 'success', 'code' => 200, 'status' => true, 'message' => 'Product data retrived successfully.', 'toast' => true, 'data' => $result]);
            } else {
                return response()->json(['type' => 'success', 'code' => 200, 'status' => false, 'message' => 'No product available.', 'toast' => true]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error product adding: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
}
