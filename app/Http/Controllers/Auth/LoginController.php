<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\MenuAccessService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    protected function authenticated($request, $user)
    {
       // cabang yang di-assign ke user, dipakai header & dashboard
       $cabangs = $user->cabangs()->where('is_active', 1)->get(['tb_master_cabang.cabang_id', 'cabang_name'])->toArray();
       session([
           'cabangs' => $cabangs,
           // Owner default semua cabang (null), Pegawai default cabang pertama
           'cabang_aktif' => $user->role === 'Owner' ? null : ($cabangs[0]['cabang_id'] ?? null),
       ]);

       app(MenuAccessService::class)->loadIntoSession($user);

       $tes = $user->role;
       if($tes == "Owner"){
            return redirect('/');
       }else{
            return redirect('/transaksi/create');
       }
    }

    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
