<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function index()
    {
        $cart = auth()->user()->cartItems()->with('product')->get();
        return response()->json($cart);
    }

    // Добавление
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($validated['product_id']);
        
        if ($product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Недостаточно товара'], 400);
        }

        // Поиск существующего
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            // Обновл
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Создаем 
            $cartItem = CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json($cartItem->load('product'), 201);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $cartItem->delete();

        return response()->json(['message' => 'Товар удален']);
    }
}