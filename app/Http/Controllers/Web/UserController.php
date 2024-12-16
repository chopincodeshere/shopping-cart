<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\ForgotPasswordRequest;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * UserController handles user-related operations such as login, logout, 
 * and retrieving user lists. It provides methods for authenticating users, 
 * displaying user views, and returning user data via API with optional 
 * search and sorting capabilities.
 *
 * Methods:
 * - login(Request $request): Authenticates a user based on email and password.
 * - index(Request $request): Returns the user view with a page title.
 * - indexApi(Request $request): Returns a paginated list of users in JSON format, 
 *   with optional search and sorting.
 * - logout(Request $request): Logs out the user and invalidates the session.
 */
class UserController extends Controller
{
    /**
     * Handles user login by validating email and password.
     *
     * @param Request $request The HTTP request object containing user credentials.
     * @return \Illuminate\Http\RedirectResponse Redirects to the dashboard on success or back with errors on failure.
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Email not found']);
        }
        if (! password_verify($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Wrong password']);
        }
        Auth::login($user);

        $request->session()->put('cart', '{}');

        return redirect('/dashboard');
    }

    /**
     * Display the users view.
     *
     * This method returns the 'UI.users' view with a page title of 'Users'.
     *
     * @param Request $request The HTTP request instance.
     * @return \Illuminate\View\View The view for displaying users.
     */
    public function index(Request $request)
    {
        return view('UI.users', [
            'page_title' => 'Users',
        ]);
    }

    /**
     * Handles the API request to retrieve a paginated list of users with optional search and sorting.
     *
     * @param Request $request The HTTP request instance containing optional 'search', 'sort_field', 'sort_order', and 'per_page' parameters.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the paginated list of users, total count, and status message.
     */
    public function indexApi(Request $request)
    {
        $data = User::whereIn('role', [User::USER_ROLE, User::ADMIN_ROLE, User::SUPER_ADMIN_ROLE]);

        if ($request->has('search')) {
            $search = '%' . $request->search . '%';
            $data = $data->where(function ($query) use ($search) {
                $query = $query->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('role', 'like', $search);
            });
        }

        if ($request->has('sort_field')) {
            $sort_field = $request->sort_field;
            $sort_order = $request->input('sort_order', 'asc');
            if (! in_array($sort_field, Schema::getColumnListing((new User())->table))) {
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
            'message' => __('Users list returned.'),
            'status' => '1',
        ]);
    }

    /**
     * Log out the currently authenticated user and invalidate the session.
     *
     * This method logs out the user, invalidates the current session, regenerates
     * the session token to prevent session fixation attacks, and redirects the
     * user to the login page.
     *
     * @param Request $request The HTTP request instance.
     * @return \Illuminate\Http\RedirectResponse Redirects to the login page.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function forgotPassword(Request $request) {
        return view('UI.auth.forgot');
    }

    public function forgotSendEmail(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $new_password = Str::random(11);
        $user->password = bcrypt($new_password);
        $user->save();

        Mail::to($user->email)->send(new ForgotPasswordMail($new_password));

        return redirect()->route('forgot-password', ['success' => true]);
    }
}
