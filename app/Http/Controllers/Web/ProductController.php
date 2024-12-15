<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ImportProductsRequest;
use App\Http\Requests\Api\v1\ProductImageUploadRequest;
use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Traits\FileManager;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

/**
 * ProductController handles various product-related operations.
 *
 * Methods:
 * - index: Displays the product list view with user-specific permissions.
 * - indexApi: Returns a paginated list of products with optional search and sorting.
 * - delete: Deletes a specified product and redirects back with a success message.
 * - importFromExcel: Imports products from an Excel file and redirects back with a success message.
 * - uploadImages: Uploads product images and associates them with a product, then redirects back with a success message.
 */
class ProductController extends Controller
{
    use FileManager;

    /**
     * Display the products page with user-specific permissions.
     *
     * Determines the user's role to set permissions for adding to cart
     * or deleting products. Returns a view with the products page title
     * and the user's permissions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $canAddToCart = false;
        $canDeleteProduct = false;

        if (Auth::check()) {
            $user = Auth::user();

            if ($user && $user->role === User::USER_ROLE) {
                $canAddToCart = true;
            } elseif ($user->role === User::ADMIN_ROLE) {
                $canDeleteProduct = true;
            }
        }

        return view('UI.products', [
            'page_title' => 'Products',
            'canAddToCart' => $canAddToCart,
            'canDeleteProduct' => $canDeleteProduct,
        ]);
    }

    /**
     * Handles the API request to retrieve a paginated list of products.
     *
     * This method supports optional search and sorting functionality.
     * If a 'search' parameter is provided, it filters products by name
     * or associated user's name and role. If 'sort_field' and 'sort_order'
     * parameters are provided, it sorts the results accordingly, ensuring
     * the sort field is valid. The response includes the paginated list
     * of products, total count, and a success message.
     *
     * @param Request $request The HTTP request instance containing optional
     *                         'search', 'sort_field', 'sort_order', and 'per_page' parameters.
     * @return \Illuminate\Http\JsonResponse JSON response with product data and status.
     */
    public function indexApi(Request $request)
    {
        $data = Product::with(['user', 'productImages']);

        if ($request->has('search')) {
            $search = '%' . $request->search . '%';
            $data = $data->where(function ($query) use ($search) {
                $query = $query->where('name', 'like', $search)
                    ->orWherehas('user', fn($q) => $q->where('name', 'like', $search)->orWhere('role', 'like', $search));
            });
        }

        if ($request->has('sort_field')) {
            $sort_field = $request->sort_field;
            $sort_order = $request->input('sort_order', 'asc');
            if (! in_array($sort_field, Schema::getColumnListing((new Product())->table))) {
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
            'message' => 'Products list returned.',
            'status' => '1',
        ]);
    }

    /**
     * Deletes a product based on the provided product ID in the request.
     *
     * @param Request $request The HTTP request containing the product ID to be deleted.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message upon successful deletion.
     */
    public function delete(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $product->delete();

        return redirect()->back()->with(['success' => 'Product deleted successfully.']);
    }

    /**
     * Imports products from an Excel file.
     *
     * @param ImportProductsRequest $request The request containing the Excel file to import.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message upon successful import.
     * @throws \Throwable If an error occurs during the import process.
     */
    public function importFromExcel(ImportProductsRequest $request)
    {
        try {
            Excel::import(new ProductImport(), $request->import_file);
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->back()->with(['success' => "Products imported successfully."]);
    }

    /**
     * Handles the upload of product images.
     *
     * @param ProductImageUploadRequest $request The request containing the images to be uploaded.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message upon successful upload.
     */
    public function uploadImages(ProductImageUploadRequest $request)
    {
        foreach ($request->image as $image) {
            ProductImage::create([
                'product_id' => $request->product_id,
                'image_path' => config('app.url') . '/' . $this->saveFile($image, 'product_images')
            ]);
        }

        return redirect()->back()->with(['success' => 'Images uploaded successfully.']);
    }
}
