<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\PlaceOrderRequest;
use App\Http\Requests\Api\v1\UpdateOrderStatusRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Handles order-related operations for the web application.
 *
 * This controller provides methods to display orders, retrieve orders via API,
 * and place new orders. It supports searching, sorting, and pagination of orders
 * for the API endpoint. The placeOrder method processes cart items, creates orders,
 * and updates product stock accordingly.
 *
 * @package App\Http\Controllers\Web
 */
class OrderController extends Controller
{
    /**
     * Display the orders view.
     *
     * This method returns the view for displaying orders in the UI.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('UI.orders');
    }

    /**
     * Retrieve a paginated list of orders for the authenticated user.
     *
     * This method returns orders based on the user's role. Admin users
     * receive orders associated with their products, while regular users
     * receive their own orders. Supports optional search and sorting
     * functionality.
     *
     * @param Request $request The HTTP request instance containing optional
     *                         search, sort_field, sort_order, and per_page
     *                         parameters.
     * @return \Illuminate\Http\JsonResponse JSON response containing the
     *                                       paginated list of orders, total
     *                                       count, and a status message.
     */
    public function indexApi(Request $request)
    {
        $user = Auth::user();

        if ($user->role === User::ADMIN_ROLE) {
            $data = Order::with(['user', 'order_items'])->whereHas('order_items', function ($query) use ($user) {
                $query->whereHas('product', function ($query) use ($user) {
                    $query->where('admin_id', $user->id);
                });
            });
        } else {
            $data = Order::with(['user', 'order_items'])->where('user_id', $user->id);
        }

        if ($request->has('search')) {
            $search = '%' . $request->search . '%';
            $data = $data->where(function ($query) use ($search, $user) {
                $query = $query->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', $search);
                })->orWhereHas('order_items', function ($query) use ($search, $user) {
                    $query->whereHas('product', function ($query) use ($search, $user) {
                        $query->where('name', 'like', $search)->where('id', $user->id);
                    });
                });
            });
        }

        if ($request->has('sort_field')) {
            $sort_field = $request->sort_field;
            $sort_order = $request->input('sort_order', 'asc');
            if (! in_array($sort_field, Schema::getColumnListing((new Order())->table))) {
                return response()->json([
                    'message' => __('messages.invalid_field_for_sorting'),
                    'status' => '0',
                ]);
            }
            $data = $data->orderBy($sort_field, $sort_order);
        }

        $data = $data->paginate($request->has('per_page') ? $request->per_page : 10);

        return response()->json([
            'data' => $data->items(),
            'total' => $data->total(),
            'message' => __('Orders list returned.'),
            'status' => '1',
        ]);
    }

    /**
     * Places an order for the authenticated user by processing their cart items.
     * Groups cart items by store, creates orders and order items, updates product stock,
     * and clears the user's cart. Commits the transaction and redirects to the orders index.
     *
     * @param PlaceOrderRequest $request The request object containing order details.
     * @return \Illuminate\Http\RedirectResponse Redirects to the orders index page.
     */
    public function placeOrder(PlaceOrderRequest $request)
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $groupedByStore = $cartItems->groupBy(function ($item) {
            return $item->product->admin_id;
        });

        $orderIds = [];

        DB::beginTransaction();
        foreach ($groupedByStore as $adminId => $items) {
            $order = Order::create([
                'user_id' => $user->id,
                'admin_id' => $adminId,
                'total' => 0,
                'status' => Order::PENDING,
                'created_at' => now(),
            ]);

            $orderTotal = 0;

            foreach ($items as $cartItem) {
                $product = $cartItem->product;

                $totalPrice = $cartItem->quantity * $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                    'total' => $totalPrice,
                ]);

                $orderTotal += $totalPrice;

                $product->decrement('stock', $cartItem->quantity);
            }

            $order->update(['total' => $orderTotal]);

            $orderIds[] = $order->id;

            CartItem::where('user_id', $user->id)->delete();
            DB::commit();

            return redirect()->route('orders.index');
        }
    }

    /**
     * Update the status of an order.
     *
     * This method updates the status of an order based on the provided request data.
     * If the status is set to 'COMPLETED', the order is marked as paid.
     * Returns a JSON response indicating the success of the operation.
     *
     * @param UpdateOrderStatusRequest $request The request containing the order ID and new status.
     * @return \Illuminate\Http\JsonResponse JSON response with a success message and updated order details.
     */
    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        $order->status = $request->status;

        if ($request->status === Order::COMPLETED) {
            $order->is_paid = 1;
        }

        $order->save();

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order,
            'status' => '1'
        ]);
    }
}
