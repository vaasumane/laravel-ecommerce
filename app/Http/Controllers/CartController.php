<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTocartrequest;
use App\Models\Carts;
use App\Models\ProductImages;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function getCart()
    {
        $data["title"] = "Cart";
        $data["page"] = "cart-list";
        return view('cart', $data);
    }
    public function addTocart(AddTocartrequest $request)
    {
        try {
            DB::beginTransaction();
            $product_details = Products::where('id', $request->product_id)->first();
            $cartDetails = Carts::where("user_id", $request->user_id)->where("product_id", $request->product_id)->first();
            if (empty($cartDetails)) {
                $cartData = new Carts();
                $cartData->product_id = $request->product_id;
                $cartData->user_id = $request->user_id;
                $cartData->quantity = $request->quantity;
                $cartData->price = $product_details->price;
                $cartData->save();
            } else {
                $cartDetails->quantity += $request->input('quantity');
                $cartDetails->price += $product_details->price;
                $cartDetails->save();
            }
            DB::commit();
            $UserCart = Carts::where("user_id", $request->user_id)->count();


            return response()->json(['type' => 'success', 'code' => 200, 'status' => true, 'message' => 'Product added  to cart.', 'toast' => true, 'cart_count' => $UserCart]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error cart: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
    public function Updatecart(AddTocartrequest $request)
    {
        try {
            DB::beginTransaction();
            $product_details = Products::where('id', $request->product_id)->first();
            $cartDetails = Carts::where("user_id", $request->user_id)->where("product_id", $request->product_id)->first();

            $cartDetails->quantity = $request->input('quantity');
            $cartDetails->price = $request->input('quantity') * $product_details->price;
            $cartDetails->save();
            DB::commit();
            $UserCart = Carts::where("user_id", $request->user_id)->count();


            return response()->json(['type' => 'success', 'code' => 200, 'status' => true, 'message' => 'Cart updated.', 'toast' => true, 'data' => $cartDetails]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error update cart: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
    public function deletecart(Request $request)
    {
        try {
            if (!$request->id) {
                return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Cart Id not found', 'toast' => true]);
            }
            if (!$request->user_id) {
                return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'User Id not found', 'toast' => true]);
            }
            $UserCart = Carts::where("id", $request->id)->first();

            if ($UserCart) {
                $UserCart->delete();
                $UserCartDetails = Carts::where("user_id", $request->user_id)->count();
                $isCartEmpty = true;
                if($UserCartDetails > 0){
                    $isCartEmpty = false;
                }

                return response()->json(['success' => true, 'status'=>true, 'message' => 'Cart item deleted successfully',"isCartEmpty"=>$isCartEmpty]);
            } else {
                return response()->json(['success' => false,'status'=>false, 'message' => 'Cart item not found'], 404);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error delete cart: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
    public function getCartCount(Request $request)
    {
        try {
            if (!$request->user_id) {
                return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'User ID not found', 'toast' => true]);
            }
            $UserCart = Carts::where("user_id", $request->user_id)->count();
            return response()->json(['type' => 'success', 'code' => 200, 'status' => true, 'message' => '', 'toast' => true, 'cart_count' => $UserCart]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error product adding: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
    public function getCartItems(Request $request)
    {
        DB::beginTransaction();
        try {
            $page = $request->page;
            $limit = $request->limit;
            $offset = $page * $limit;

            $product_query = DB::table('tbl_carts')
                ->join('products', 'products.id', 'tbl_carts.product_id')
                ->select('tbl_carts.*', 'products.name', 'products.price as original_price');

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
                  
                    $product_image = ProductImages::where("product_id", $value->product_id)
                        ->orderBy("id", "asc")->limit(1)->first();
                    $prod_img =  config("app.url") . "storage/" . $product_image->image_url;
                    $value->product_image = $prod_img;
                    $value->total_price = $value->price;
                    $result[] = $value;
                }
            }
            if (!empty($result)) {
                return response()->json(['type' => 'success', 'code' => 200, 'status' => true, 'message' => 'Cart data retrived successfully.', 'toast' => true, 'data' => $result]);
            } else {
                return response()->json(['type' => 'success', 'code' => 200, 'status' => false, 'message' => 'Cart is empty', 'toast' => true]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error product adding: ' . $e->getMessage());
            return response()->json(['type' => 'error', 'code' => 500, 'status' => false, 'message' => 'Error while processing', 'toast' => true]);
        }
    }
}
