<?php

namespace App\Http\Controllers\Api;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;



class ProductrController extends Controller
{
 
    public function show(Request $request)
    {
        $product = Product::all();
        $total = $product->count();
        $perPage = $request->per_page ? $request->per_page : $total;
        $page = $request->input('page', 1);

        $name = $request->name ? $request->name : null;
        $description = $request->description ? $request->description : null;

        $sort = $request->sort && in_array($request->sort, ['id', 'name', 'description']) ? $request->sort : 'id';
        $order = $request->order && in_array($request->order, ['asc', 'desc']) ? $request->order : 'asc';
        if ($name || $description || $sort || $order) {
            $product = Product::where('name', 'like', '%' . $name . '%')
                ->where('description', 'like', '%' . $description . '%')->
                orderby($sort, $order)->offset(($page - 1) * $perPage)->limit($perPage)->get();
        }

        $data = [
            'status' => 200,
            'product' => $product,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
        if ($page * $perPage > $total) {
            return response()->json($data, 422);
        }
        return response()->json($data, 200);
    }
    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|mimes:jpeg,jpg,png,gif|max:1024'
        ]);
        if ($validator->fails()) {
            $data = [
                'status' => 422,
                'errors' => $validator->messages()
            ];
            return response()->json($data, 422);
        } else {
            $image_name = time() . '.' . $request->image->extension();
            $request->image->move(public_path('product'), $image_name);
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'image' => $image_name,
            ]);
            if ($product) {
                $data = [
                    'status' => 200,
                    'message' => 'Product Added Sucessfully',
                ];
                return response()->json($data, 200);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Product Not Added',
                ];
                return response()->json($data, 500);
            }
        }
    }

    public function singel_data($id)
    {
        $product = Product::find($id);
        if ($product) {
            $data = [
                'status' => 200,
                'product' => $product,
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'status' => 404,
                'message' => 'Product Not Found',
            ];
            return response()->json($data, 404);
        }
    }


    public function update_data(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:1024'
        ]);
        if ($validator->fails()) {
            $data = [
                'status' => 422,
                'errors' => $validator->messages()
            ];
            return response()->json($data, 422);
        } else {
            $image_name = time() . '.' . $request->image->extension();
            $request->image->move(public_path('product'), $image_name);
            $product = Product::find($id);
            $image_path = public_path("product\\" . $product->image);
            if (file_exists($image_path) && $image_name) {
                unlink($image_path);
            }

            if ($product) {
                $product->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'image' => $image_name,
                ]);
                $data = [
                    'status' => 200,
                    'message' => 'Product Updated Sucessfully',
                ];
                return response()->json($data, 200);
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Product Not Updated',
                ];
                return response()->json($data, 500);
            }
        }
    }
    public function destroy($id)
    {
        $product = Product::find($id);
        $image_path = public_path("product\\" . $product->image);
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        if ($product) {
            $product->delete();
            $data = [
                'status' => 200,
                'message' => 'Product Deleted Sucessfully',
            ];
            return response()->json($data, 200);
        } else {
            $data = [
                'status' => 500,
                'message' => 'Product Not Deleted',
            ];
            return response()->json($data, 500);
        }
    }
}

