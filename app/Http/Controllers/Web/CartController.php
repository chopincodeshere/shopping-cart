<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\AddToCartRequest;
use App\Http\Requests\Api\v1\DeleteFromCartRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * Handles cart-related operations for the web interface.
 *
 * Methods:
 * - addToCart: Adds a product to the user's cart or updates the quantity if it already exists.
 * - index: Displays the cart view page.
 * - indexApi: Returns a JSON response with the user's cart items, supporting search and sorting.
 * - delete: Removes an item from the user's cart.
 *
 * Utilizes Laravel's request validation, authentication, and database query capabilities.
 */
class CartController extends Controller
{
    /**
     * Adds an item to the user's cart or updates the quantity if it already exists.
     *
     * @param AddToCartRequest $request The request containing the product ID and quantity.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message.
     */
    public function addToCart(AddToCartRequest $request)
    {
        $user = Auth::user();

        $cart_item = CartItem::updateOrCreate([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
        ], [
            'quantity' => $request->quantity
        ]);

        return redirect()->back()->with(['success' => 'Item added to cart.']);
    }

    /**
     * Display the cart view.
     *
     * This method returns the cart view with a specified page title.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('UI.cart', [
            'page_title' => 'Cart',
        ]);
    }

    /**
     * Handles the API request to retrieve the user's cart items with optional search and sorting.
     *
     * This method fetches the cart items for the authenticated user, allowing optional filtering
     * by product name or price, and sorting by specified fields. It calculates the total price,
     * tax, and grand total of the cart items. The results are paginated and returned as a JSON response.
     *
     * @param Request $request The incoming request containing optional search, sort, and pagination parameters.
     * @return \Illuminate\Http\JsonResponse The JSON response containing cart items, total price, tax, and grand total.
     */
    public function indexApi(Request $request)
    {
        $user = Auth::user();
        $data = CartItem::with(['product.productImages'])->where('user_id', $user->id);

        if ($request->has('search')) {
            $search = '%' . $request->search . '%';
            $data = $data->where(function ($query) use ($search) {
                $query = $query->wherehas('product', fn($q) => $q->where('name', 'like', $search)->orWhere('price', 'like', $search));
            });
        }

        if ($request->has('sort_field')) {
            $sort_field = $request->sort_field;
            $sort_order = $request->input('sort_order', 'asc');
            if (! in_array($sort_field, Schema::getColumnListing((new CartItem())->table))) {
                return response()->json([
                    'message' => __('messages.invalid_field_for_sorting'),
                    'status' => '0',
                ]);
            }
            $data = $data->orderBy($sort_field, $sort_order);
        }

        $cartItems = $data->get(); // Use get() instead of paginate to process the data here

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->quantity * $item->product->price;
        }

        $taxRate = 0.18; // 18% tax rate
        $tax = $total * $taxRate;

        // Calculate grand total
        $grandTotal = $total + $tax;

        $data = $data->paginate($request->has('per_page') ? $request->per_page : 10);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'message' => 'Cart list returned.',
            'status' => '1',
            'total_price' => round($total, 2),
            'tax' => round($tax, 2),
            'grand_total' => round($grandTotal, 2)
        ]);
    }

    /**
     * Remove an item from the user's cart.
     *
     * This method deletes a cart item for the authenticated user based on the
     * provided cart item ID in the request. After deletion, it redirects back
     * with a success message indicating the item was removed.
     *
     * @param DeleteFromCartRequest $request The request containing the cart item ID to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message.
     */
    public function delete(DeleteFromCartRequest $request)
    {
        $user = Auth::user();

        CartItem::where('user_id', $user->id)
            ->where('cart_item_id', $request->cart_item_id)->delete();

        return redirect()->back()->with(['success' => 'Item removed from cart.']);
    }
}
