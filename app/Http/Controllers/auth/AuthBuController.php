<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\T_perusahaan;
use Illuminate\Support\Str;
use App\Mail\GenOTPMail;
use App\Models\Meping;
use App\Models\User;
use Mail;

class AuthBuController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     return redirect('/login');
    //     // $this->middleware(['permission:permissions.index']);
    // }

    // public function postloginIzin(Request $request)
	// {

	// 	$bu = $request->perusahaan;


	// 	$check = User::where('badan_usaha_id', $bu)->count();

	// 	if ($check == '0') {
	// 		// dd('hai');
	// 		//insert ke table user dulu
	// 		$perusahaan = DB::table('t_perusahaan')
	// 			->where('ID_PERUSAHAAN', $bu)
	// 			->first();

	// 		$storeUser = User::create([
	// 			'name' => $perusahaan->NAMA_PERUSAHAAN,
	// 			'email' => $perusahaan->EMAIL_PERUSAHAAN,
	// 			'npwp' => $perusahaan->NPWP,
	// 			'password' => bcrypt('-'),
	// 			'badan_usaha_id' => $perusahaan->ID_PERUSAHAAN,
	// 			'role' => 'BU',
	// 		]);
	// 	}

	// 	$user = User::where('badan_usaha_id', $bu)->first();
	// 	// dd($user);
	// 	$email = $user->email;
	// 	$password = '-';
	// 	$credentials = [
	// 		'email' => $email,
	// 		'password' => $password
	// 	];

	// 	$dologin = Auth::attempt($credentials);

	// 	if ($dologin) {
	// 		return redirect('/');
	// 	} else {
	// 		// dd('hai');
	// 		return redirect('/login')->with('statusLogin', 'Eror Autentikasi');
	// 	}
	// }

	public function postloginIzin(Request $request)
	{
		// Ambil data dari request
		$emailInput = $request->EMAIL_PERUSAHAAN;
		$passwordInput = $request->PASSWORD; // Password asli
	
		// Cek apakah email ada di tabel t_perusahaan
		$perusahaan = DB::table('t_perusahaan')
			->where('EMAIL_PERUSAHAAN', $emailInput)
			->first();
			// dd($perusahaan);
		// Jika data perusahaan ditemukan
		if ($perusahaan) {
	
			// Cek apakah pengguna dengan email tersebut sudah ada di tabel users
			$user = User::where('email', $emailInput)->first();
			
	
			// Jika pengguna belum ada, buat data pengguna baru
			if (!$user) {
				$user = User::create([
					'name' => $perusahaan->NAMA_PERUSAHAAN,
					'email' => $perusahaan->EMAIL_PERUSAHAAN,
					'npwp' => $perusahaan->NPWP,
					'password' => bcrypt('Esdm1234'), // Password default menggunakan bcrypt
					'badan_usaha_id' => $perusahaan->ID_PERUSAHAAN,
					'role' => 'BU',
				]);
			}
	
			// Siapkan kredensial untuk login
			$credentials = [
				'email' => $user->email,
				'password' => $passwordInput // Password asli yang akan di-verify oleh Auth
			];

			// dd($credentials);
	
			// Coba lakukan login
			if (Auth::attempt($credentials)) {
				return redirect('/')->with('statusLogin', 'Berhasil login');
			} else {
				return redirect('/login')->with('statusLogin', 'Password salah atau akun tidak ditemukan');
			}
		} else {
			// Jika perusahaan tidak ditemukan di tabel t_perusahaan
			return redirect('/login')->with('statusLogin', 'Akun tidak ditemukan');
		}
	}
	
    
    public function logoutBU()
	{

		Auth::logout();
		return redirect('/login')->with('statusLogin', 'Sukses Logout');
	}
}
