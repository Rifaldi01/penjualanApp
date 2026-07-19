<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    public function authenticated(Request $request, $user)
    {
//        session()->flash('show_popup', true);
        if ($user->hasRole('superAdmin')) {
            return redirect('/superadmin/dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('manager')) {
            return redirect('/manager/dashboard');
        } else {
            return redirect('/gudang/dashboard');
        }
    }
    protected function attemptLogin(Request $request)
    {
        $user = User::with('divisi')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return false;
        }

        // Jika user memiliki divisi dan status divisinya non active
        if ($user->divisi_id !== null) {

            if (!$user->divisi || $user->divisi->status !== 'active') {
                return false;
            }

        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::with('divisi')
            ->where('email', $request->email)
            ->first();

        if (
            $user &&
            $user->divisi_id !== null &&
            (!$user->divisi || $user->divisi->status !== 'active')
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => ['Divisi Anda sedang Non Active. Silakan hubungi Administrator.'],
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}
