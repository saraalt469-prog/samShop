<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{

   public function index()
    {
        $items = auth()->user()->cartItems()->with('product')->get();
        
        $total = 0;
        foreach ($items as $item) {
            $total += $item->product->price * $item->quantity;
        }
        
        return response()->json([
            'items' => $items,
            'total_price' => $total
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);
        
        if ($product->stock < $request->quantity) {
            return response()->json(['message' => 'Не хватает товара'], 400);
        }

        $cart = CartItem::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->quantity += $request->quantity;
            $cart->save();
        } else {
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }
        return response()->json(['message' => 'Добавлено']);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $cartItem->delete();

        return response()->json(['message' => 'Товар удален']);
    }
    public function clear()
    {
        auth()->user()->cartItems()->delete();
        return response()->json(['message' => 'Корзина очищена']);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $product = Product::find($cartItem->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json(['message' => 'Не хватает товара'], 400);
        }
        
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        
        return response()->json(['message' => 'Обновлено']);
    }
}