<?php

namespace App\Http\Controllers\bu;

use RealRashid\SweetAlert\Facades\Alert;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Penjualan_lng;
use Illuminate\Http\Request;
use App\Models\Harga_bbm_jbu;
use App\Models\Pasokanlng;
use App\Models\Izin;
use App\Models\Ekspor;
use App\Models\Impor;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Importlngpenjualan;
use App\Imports\Importlngpasok;
use Illuminate\Support\Facades\Crypt;

class LngController extends Controller
{
    public function index()
    {
        // $pm = Penjualan_lng::where('badan_usaha_id', Auth::user()->badan_usaha_id)
        //     ->groupBy('bulan')->get();

        $pm = DB::table('penjualan_lngs')
            ->select('*', DB::raw('MAX(status) as status_tertinggi'), DB::raw('MAX(catatan) as catatanx'))
            ->where('badan_usaha_id', Auth::user()->badan_usaha_id)
            ->groupBy('bulan')
            ->get();

        $pasoklng = DB::table('pasokanlngs')
            ->select('*', DB::raw('MAX(status) as status_tertinggi'), DB::raw('MAX(catatan) as catatanx'))
            ->where('badan_usaha_id', Auth::user()->badan_usaha_id)
            ->groupBy('bulan')
            ->get();

        return view('badan_usaha.niaga.lng.index', compact(
            'pm',
            'pasoklng'
        ));
    }
    public function show_lngx($id, $lng)
    {
        $lngx = $lng;

        // dd($lngx);
        // die;
        $pecah = explode(',', Crypt::decryptString($id));
        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $bulan_ambil_penjualan_lng = DB::table('penjualan_lngs')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $pecah[0])
            ->orderBy('status', 'desc')
            ->first();

        $bulan_ambil_pasok_lng = DB::table('pasokanlngs')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $pecah[0])
            ->orderBy('status', 'desc')
            ->first();

        // Mengambil substring dari bulan
        $bulan_ambil_penjualan_lngx = $bulan_ambil_penjualan_lng ? substr($bulan_ambil_penjualan_lng->bulan, 0, 7) : '';
        $statuspenjualan_lngx = $bulan_ambil_penjualan_lng->status ?? '';

        $bulan_ambil_pasok_lngx = $bulan_ambil_pasok_lng ? substr($bulan_ambil_pasok_lng->bulan, 0, 7) : '';
        $statuspasok_lngx = $bulan_ambil_pasok_lng->status ?? '';

        $lng = Penjualan_lng::where([
            'bulan' => $pecah[0],
            'badan_usaha_id' => $pecah[1]
        ])->orderBy('status', 'desc')->get();

        $pasok_lng = Pasokanlng::where([
            'bulan' => $pecah[0],
            'badan_usaha_id' => $pecah[1]
        ])->orderBy('status', 'desc')->get();


        return view('badan_usaha.niaga.lng.show', compact(
            'lng',
            'pasok_lng',
            'bulan_ambil_penjualan_lngx',
            'bulan_ambil_pasok_lngx',
            'statuspenjualan_lngx',
            'statuspasok_lngx',
            'lngx'
        ));
    }
    public function simpan_lngx(Request $request)
    {
        $pesan = [
            'badan_usaha_id.required' => 'badan_usaha_id masih kosong',
            'bulan.required' => 'bulan masih kosong',
            'provinsi.required' => 'provinsi masih kosong',
            'kabupaten_kota.required' => 'kabupaten_kota masih kosong',
            'produk.required' => 'produk masih kosong',
            'konsumen.required' => 'konsumen masih kosong',
            'sektor.required' => 'sektor masih kosong',
            'volume.required' => 'volume masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'biaya_kompresi.required' => 'biaya_kompresi masih kosong',
            'biaya_penyimpanan.required' => 'biaya_penyimpanan masih kosong',
            'biaya_pengangkutan.required' => 'biaya_pengangkutan masih kosong',
            'harga_jual.required' => 'harga_jual masih kosong',
            'biaya_niaga.required' => 'biaya_niaga masih kosong',
        ];

        $validatedData = $request->validate([

            'badan_usaha_id' => 'required',
            'bulan' => 'required',
            'provinsi' => 'required',
            'kabupaten_kota' => 'required',
            'produk' => 'required',
            'konsumen' => 'required',
            'sektor' => 'required',
            'volume' => 'required',
            'satuan' => 'required',
            'biaya_kompresi' => 'required',
            'biaya_penyimpanan' => 'required',
            'biaya_pengangkutan' => 'required',
            'harga_jual' => 'required',
            'biaya_niaga' => 'required',
        ], $pesan);

        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $cekdb = DB::table('penjualan_lngs')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $request->bulan . '-01')
            ->orderBy('status', 'desc')
            ->first();

        if (isset($cekdb) == 1) {
            if ($cekdb->status == 1) {
                Alert::error('Error', 'Bulan yang anda pilih sedang status kirim / revisi');
                return back();
            }
        }
        $validatedData = Penjualan_lng::create([
            'badan_usaha_id' =>  $request->badan_usaha_id,
            'bulan' => $request->bulan . '-01',
            'provinsi' => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'produk' => $request->produk,
            'konsumen' => $request->konsumen,
            'sektor' => $request->sektor,
            'volume' => $request->volume,
            'satuan' => $request->satuan,
            'biaya_kompresi' => $request->biaya_kompresi,
            'biaya_penyimpanan' => $request->biaya_penyimpanan,
            'biaya_pengangkutan' => $request->biaya_pengangkutan,
            'harga_jual' => $request->harga_jual,
            'biaya_niaga' => $request->biaya_niaga,

        ]);

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil ditambahkan');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal berhasil ditambahkan');
            return back();
        }
    }
    public function hapus_lngx(Request $request, $id)
    {
        Penjualan_lng::destroy($id);
        if ($id) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dihapus');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dihapus');
            return back();
        }
    }
    public function edit($id)
    {
        $show_lng = Penjualan_lng::find($id);
        return response()->json([
            'data' => $show_lng
        ]);
    }
    public function get_penjualan_lng($id)
    {
        $data['produk'] = DB::select("SELECT produks.name FROM produks GROUP BY produks.name");
        $data['provinsi'] = DB::select("SELECT provinces.id, provinces.name FROM provinces GROUP BY provinces.name");
        $data['sektor'] = DB::select("SELECT sektors.id, sektors.nama_sektor FROM sektors GROUP BY sektors.nama_sektor");
        $data['find'] = Penjualan_lng::find($id);
        return response()->json(['data' => $data]);

        // $data = Penjualan_lng::find($id);
        // return response()->json(['data' => $data]);
    }
    public function update_lngx(Request $request, $id)
    {
        // echo json_encode($request->all());exit;
        $penjualan_lng = $id;
        $pesan = [
            // 'badan_usaha_id.required' => 'badan_usaha_id masih kosong',
            // 'bulan.required' => 'bulan masih kosong',
            'provinsi.required' => 'provinsi masih kosong',
            'kabupaten_kota.required' => 'kabupaten_kota masih kosong',
            'produk.required' => 'produk masih kosong',
            'konsumen.required' => 'konsumen masih kosong',
            'sektor.required' => 'sektor masih kosong',
            'volume.required' => 'volume masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'biaya_kompresi.required' => 'biaya_kompresi masih kosong',
            'biaya_penyimpanan.required' => 'biaya_penyimpanan masih kosong',
            'biaya_pengangkutan.required' => 'biaya_pengangkutan masih kosong',
            'harga_jual.required' => 'harga_jual masih kosong',
            'biaya_niaga.required' => 'biaya_niaga masih kosong',
        ];

        $rules = [
            // 'badan_usaha_id' => 'required',
            // 'bulan' => 'required',
            'provinsi' => 'required',
            'kabupaten_kota' => 'required',
            'produk' => 'required',
            'konsumen' => 'required',
            'sektor' => 'required',
            'volume' => 'required',
            'satuan' => 'required',
            'biaya_kompresi' => 'required',
            'biaya_penyimpanan' => 'required',
            'biaya_pengangkutan' => 'required',
            'harga_jual' => 'required',
            'biaya_niaga' => 'required',
        ];

        $validatedData = $request->validate($rules, $pesan);

        Penjualan_lng::where('id', $penjualan_lng)
            ->update($validatedData);

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil diupdate');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal diupdate');
            return back();
        }
    }

    public function simpan_pasokan_lngx(Request $request)
    {
        // echo json_encode($request->all());exit;
        $pesan = [
            'badan_usaha_id.required' => 'badan_usaha_id masih kosong',
            'bulan.required' => 'bulan masih kosong',
            'produk.required' => 'produk masih kosong',
            'nama_pemasok.required' => 'nama_pemasok masih kosong',
            'kategori_pemasok.required' => 'kategori_pemasok masih kosong',
            'volume.required' => 'volume masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'harga_gas.required' => 'harga_gas masih kosong',
        ];

        $validatedData = $request->validate([
            'badan_usaha_id' => 'required',
            'bulan' => 'required',
            'produk' => 'required',
            'nama_pemasok' => 'required',
            'kategori_pemasok' => 'required',
            'volume' => 'required',
            'satuan' => 'required',
            'harga_gas' => 'required',
        ], $pesan);

        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $cekdb = DB::table('pasokanlngs')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $request->bulan . '-01')
            ->orderBy('status', 'desc')
            ->first();
        // echo json_encode($cekdb);
        // exit;

        if (isset($cekdb) == 1) {
            if ($cekdb->status == 1) {
                Alert::error('Error', 'Bulan yang anda pilih sedang status kirim / revisi');
                return back();
            }
        }


        $validatedData = Pasokanlng::create([
            'badan_usaha_id' =>  $request->badan_usaha_id,
            'bulan' => $request->bulan . '-01',
            'produk' => $request->produk,
            'nama_pemasok' => $request->nama_pemasok,
            'kategori_pemasok' => $request->kategori_pemasok,
            'volume' => $request->volume,
            'satuan' => $request->satuan,
            'harga_gas' => $request->harga_gas,

        ]);

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil ditambahkan');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal berhasil ditambahkan');
            return back();
        }
    }
    public function hapus_pasok_lngx(Request $request, $id)
    {
        Pasokanlng::destroy($id);
        if ($id) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dihapus');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dihapus');
            return back();
        }
    }
    public function get_pasok_lng($id)
    {
        $data['produk'] = DB::select("SELECT produks.name FROM produks GROUP BY produks.name");
        $data['find'] = Pasokanlng::find($id);
        return response()->json(['data' => $data]);
    }
    public function update_pasok_lngx(Request $request, $id)
    {
        // echo json_encode($request->all());exit;
        $penjualan_lng = $id;
        $pesan = [
            // 'badan_usaha_id.required' => 'badan_usaha_id masih kosong',
            // 'bulan.required' => 'bulan masih kosong',
            'produk.required' => 'produk masih kosong',
            'nama_pemasok.required' => 'nama_pemasok masih kosong',
            'kategori_pemasok.required' => 'kategori_pemasok masih kosong',
            'volume.required' => 'volume masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'harga_gas.required' => 'harga_gas masih kosong',
        ];

        $rules = [
            // 'badan_usaha_id' => 'required',
            // 'bulan' => 'required',
            'produk' => 'required',
            'nama_pemasok' => 'required',
            'kategori_pemasok' => 'required',
            'volume' => 'required',
            'satuan' => 'required',
            'harga_gas' => 'required',
        ];

        $validatedData = $request->validate($rules, $pesan);

        Pasokanlng::where('id', $penjualan_lng)
            ->update($validatedData);

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil diupdate');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal diupdate');
            return back();
        }
    }
    public function get_kota($kabupaten_kota)
    {
        // $data = DB::select("SELECT kotas.nama_kota FROM kotas WHERE kotas.kabupaten_kota = '$kabupaten_kota'");
        $data = DB::select("SELECT kotas.`nama_kota` FROM  kotas WHERE kotas.`id_prov` = (SELECT kotas.`id_prov` FROM kotas WHERE kotas.`nama_kota` = '$kabupaten_kota')");
        // $data = Produk::get();
        return response()->json(['data' => $data]);
    }
    public function submit_lngx(Request $request, $id)
    {
        $idx = $id;
        $validatedData = DB::update("update penjualan_lngs set status='1' where id='$idx'");

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dikirim');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dikirim');
            return back();
        }
    }
    public function submit_pasok_lngx(Request $request, $id)
    {
        $idx = $id;
        $validatedData = DB::update("update pasokanlngs set status='1' where id='$idx'");

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dikirim');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dikirim');
            return back();
        }
    }
    public function importlngpenx(Request $request)
    {
        $bulan = $request->bulan . "-01";
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $cekdb = DB::table('penjualan_lngs')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $bulan)
            ->orderBy('status', 'desc')
            ->first();

        if (isset($cekdb) == 1) {
            if ($cekdb->status == 1) {
                Alert::error('Error', 'Bulan yang anda pilih sedang status kirim / revisi');
                return back();
            }
        }
        $import = Excel::import(new Importlngpenjualan($bulan), request()->file('file'));

        if ($import) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data excel berhasil diupload');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data excel gagal diupload');
            return back();
        }
    }
    public function importlngpasokx(Request $request)
    {
        $bulan = $request->bulan . "-01";
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $cekdb = DB::table('pasokanlngs')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $bulan)
            ->orderBy('status', 'desc')
            ->first();

        if (isset($cekdb) == 1) {
            if ($cekdb->status == 1) {
                Alert::error('Error', 'Bulan yang anda pilih sedang status kirim / revisi');
                return back();
            }
        }
        $import = Excel::import(new Importlngpasok($bulan), request()->file('file'));

        if ($import) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data excel berhasil diupload');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data excel gagal diupload');
            return back();
        }
    }
    public function submit_bulan_lngx(Request $request, $bulan)
    {
        $bulanx = $bulan;
        // dd($bulanx);
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("update penjualan_lngs set status='1' where bulan='$bulanx' and badan_usaha_id='$badan_usaha_id'");

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dikirim');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dikirim');
            return back();
        }
    }
    public function submit_bulan_pasok_lngx(Request $request, $bulan)
    {
        $bulanx = $bulan;
        // dd($bulanx);
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("update pasokanlngs set status='1' where bulan='$bulanx' and badan_usaha_id='$badan_usaha_id'");

        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dikirim');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dikirim');
            return back();
        }
    }
    public function hapus_bulan_lngx(Request $request, $bulan)
    {
        $bulanx = $bulan;
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("DELETE FROM penjualan_lngs WHERE badan_usaha_id='$badan_usaha_id' AND bulan='$bulanx'");
        // pengangkutan_minyakbumi::destroy($bulan);
        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('Success', 'Data berhasil dihapus');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('Error', 'Data gagal dihapus');
            return back();
        }
    }
    public function hapus_bulan_pasok_lngx(Request $request, $bulan)
    {
        $bulanx = $bulan;
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("DELETE FROM pasokanlngs WHERE badan_usaha_id='$badan_usaha_id' AND bulan='$bulanx'");
        // pengangkutan_minyakbumi::destroy($bulan);
        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('Success', 'Data berhasil dihapus');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('Error', 'Data gagal dihapus');
            return back();
        }
    }
}
