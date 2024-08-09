<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Izin;
use App\Models\Penyminyakbumi;
use App\Models\Penygasbumi;
use Illuminate\Support\Facades\Crypt;
use App\Imports\Importpenyimpananmb;
use App\Imports\Importpenyimpanangb;
use Maatwebsite\Excel\Facades\Excel;

class PenyMinyakbumiController extends Controller
{
    public function index()
    {
        // $pm = Penyminyakbumi::where('badan_usaha_id', Auth::user()->badan_usaha_id)
        //     ->groupBy('bulan')->get();

        $pm = DB::table('penyminyakbumis')
            ->select('*', DB::raw('MAX(status) as status_tertinggi'), DB::raw('MAX(catatan) as catatanx'))
            ->where('badan_usaha_id', Auth::user()->badan_usaha_id)
            ->groupBy('bulan')
            ->get();

        return view('badan_usaha.penyimpanan.minyak_bumi.index', compact('pm'));
    }
    public function index_pggb()
    {

        // $pm = Penygasbumi::where('badan_usaha_id', Auth::user()->badan_usaha_id)
        //     ->groupBy('bulan')->get();

        $pm = DB::table('penygasbumis')
            ->select('*', DB::raw('MAX(status) as status_tertinggi'), DB::raw('MAX(catatan) as catatanx'))
            ->where('badan_usaha_id', Auth::user()->badan_usaha_id)
            ->groupBy('bulan')
            ->get();

        return view('badan_usaha.penyimpanan.gas_bumi.index', compact('pm'));
    }
    public function show_pmbx($id)
    {
        $pecah = explode(',', Crypt::decryptString($id));
        $pggb = Penyminyakbumi::get();
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        // Mengambil bulan dari tabel penyminyakbumis sesuai ID badan usaha dan bulan yang ditemukan
        $bulan_ambil = DB::table('penyminyakbumis')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->orderBy('status', 'desc')
            ->where('bulan', $pecah[0])
            ->first();

        // Mengambil substring dari bulan
        $bulan_ambilx = $bulan_ambil ? substr($bulan_ambil->bulan, 0, 7) : '';
        $statusx = $bulan_ambil->status;


        $pmb = Penyminyakbumi::where([
            'bulan' => $pecah[0],
            'badan_usaha_id' => $pecah[1]
        ])->orderBy('status', 'desc')->get();

        return view('badan_usaha.penyimpanan.minyak_bumi.show', compact(
            'pmb',
            'pggb',
            'bulan_ambilx',
            'statusx'
        ));
    }
    public function show_pggbx($id)
    {

        $pecah = explode(',', Crypt::decryptString($id));
        $pggb = Penygasbumi::get();
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        // Mengambil bulan dari tabel Penygasbumi sesuai ID badan usaha dan bulan yang ditemukan
        $bulan_ambil = DB::table('penygasbumis')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $pecah[0])
            ->orderBy('status', 'desc')
            ->first();

        // Mengambil substring dari bulan
        $bulan_ambilx = $bulan_ambil ? substr($bulan_ambil->bulan, 0, 7) : '';
        $statusx = $bulan_ambil->status;


        $pggb = Penygasbumi::where([
            'bulan' => $pecah[0],
            'badan_usaha_id' => $pecah[1]
        ])->orderBy('status', 'desc')->get();

        return view('badan_usaha.penyimpanan.gas_bumi.show', compact(
            'pggb',
            'bulan_ambilx',
            'statusx'
        ));
    }
    public function simpan_pmbx(Request $request)
    {
        $pesan = [
            'badan_usaha_id.required' => 'badan_usaha_id masih kosong',
            'bulan.required' => 'bulan masih kosong',
            'jenis_fasilitas.required' => 'jenis_fasilitas masih kosong',
            'no_tangki.required' => 'no_tangki masih kosong',
            'jenis_komoditas.required' => 'jenis komoditas masih kosong',
            'produk.required' => 'produk masih kosong',
            'provinsi.required' => 'provinsi masih kosong',
            'kab_kota.required' => 'kab kota masih kosong',
            'kategori_supplai.required' => 'kategori supplai masih kosong',
            'volume_stok_awal.required' => 'volume stok_awal masih kosong',
            'volume_supply.required' => 'volume supply masih kosong',
            'volume_output.required' => 'volume output masih kosong',
            'volume_stok_akhir.required' => 'volume stok akhir masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'utilasi_tangki.required' => 'utilasi tangki masih kosong',
            'pengguna.required' => 'pengguna masih kosong',
            'jangka_waktu_penggunaan.required' => 'jangka waktu penggunaan masih kosong',
            'tarif_penyimpanan.required' => 'tarif_penyimpanan masih kosong',
            'satuan_tarif.required' => 'satuan tarif masih kosong',
            'keterangan.required' => 'keterangan masih kosong',
        ];

        $validatedData = $request->validate([
            'badan_usaha_id' => 'required',
            'bulan' => 'required',
            'jenis_fasilitas' => 'required',
            'no_tangki' => 'required',
            'jenis_komoditas' => 'required',
            'produk' => 'required',
            'provinsi' => 'required',
            'kab_kota' => 'required',
            'kategori_supplai' => 'required',
            'volume_stok_awal' => 'required',
            'volume_supply' => 'required',
            'volume_output' => 'required',
            'volume_stok_akhir' => 'required',
            'satuan' => 'required',
            'utilasi_tangki' => 'required',
            'pengguna' => 'required',
            'jangka_waktu_penggunaan' => 'required',
            'tarif_penyimpanan' => 'required',
            'satuan_tarif' => 'required',
            'keterangan' => 'required',
            'jangka_waktu_penggunaan' => 'required',
        ], $pesan);

        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $cekdb = DB::table('penyminyakbumis')
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

        $validatedData = Penyminyakbumi::create([
            'badan_usaha_id' => $request->badan_usaha_id,
            'bulan' => $request->bulan . '-01',
            'jenis_fasilitas' => $request->jenis_fasilitas,
            'no_tangki' => $request->no_tangki,
            'jenis_komoditas' => $request->jenis_komoditas,
            'produk' => $request->produk,
            'provinsi' => $request->provinsi,
            'kab_kota' => $request->kab_kota,
            'kategori_supplai' => $request->kategori_supplai,
            'volume_stok_awal' => $request->volume_stok_awal,
            'volume_supply' => $request->volume_supply,
            'volume_output' => $request->volume_output,
            'volume_stok_akhir' => $request->volume_stok_akhir,
            'satuan' => $request->satuan,
            'utilasi_tangki' => $request->utilasi_tangki,
            'pengguna' => $request->pengguna,
            'jangka_waktu_penggunaan' => $request->jangka_waktu_penggunaan,
            'tarif_penyimpanan' => $request->tarif_penyimpanan,
            'satuan_tarif' => $request->satuan_tarif,
            'keterangan' => $request->keterangan,
            'jangka_waktu_penggunaan' => $request->jangka_waktu_penggunaan,

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
    public function simpan_pggbx(Request $request)
    {
        $pesan = [
            'badan_usaha_id.required' => 'badan_usaha_id masih kosong',
            'bulan.required' => 'bulan masih kosong',
            'no_tangki.required' => 'no_tangki masih kosong',
            'produk.required' => 'produk masih kosong',
            'kab_kota.required' => 'kab kota masih kosong',
            'volume_stok_awal.required' => 'volume stok_awal masih kosong',
            'volume_supply.required' => 'volume supply masih kosong',
            'volume_output.required' => 'volume output masih kosong',
            'volume_stok_akhir.required' => 'volume stok akhir masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'utilasi_tangki.required' => 'utilasi tangki masih kosong',
            'pengguna.required' => 'pengguna masih kosong',
            'jangka_waktu_penggunaan.required' => 'jangka waktu penggunaan masih kosong',
            'tarif_penyimpanan.required' => 'tarif_penyimpanan masih kosong',
            'satuan_tarif.required' => 'satuan tarif masih kosong',
        ];

        $validatedData = $request->validate([
            'badan_usaha_id' => 'required',
            'bulan' => 'required',
            'no_tangki' => 'required',
            'produk' => 'required',
            'kab_kota' => 'required',
            'volume_stok_awal' => 'required',
            'volume_supply' => 'required',
            'volume_output' => 'required',
            'volume_stok_akhir' => 'required',
            'satuan' => 'required',
            'utilasi_tangki' => 'required',
            'pengguna' => 'required',
            'jangka_waktu_penggunaan' => 'required',
            'tarif_penyimpanan' => 'required',
            'satuan_tarif' => 'required',
        ], $pesan);

        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $cekdb = DB::table('penygasbumis')
            ->where('badan_usaha_id', $badan_usaha_id)
            ->where('bulan', $request->bulan . '-01')
            ->orderBy('status', 'desc')
            ->first();

        // dd($cekdb);
        // die;

        if (isset($cekdb) == 1) {
            if ($cekdb->status == 1) {
                Alert::error('Error', 'Bulan yang anda pilih sedang status kirim / revisi');
                return back();
            }
        }

        $validatedData = Penygasbumi::create([
            'badan_usaha_id' => $request->badan_usaha_id,
            'bulan' => $request->bulan . '-01',
            'no_tangki' => $request->no_tangki,
            'produk' => $request->produk,
            'kab_kota' => $request->kab_kota,
            'volume_stok_awal' => $request->volume_stok_awal,
            'volume_supply' => $request->volume_supply,
            'volume_output' => $request->volume_output,
            'volume_stok_akhir' => $request->volume_stok_akhir,
            'satuan' => $request->satuan,
            'utilasi_tangki' => $request->utilasi_tangki,
            'pengguna' => $request->pengguna,
            'jangka_waktu_penggunaan' => $request->jangka_waktu_penggunaan,
            'tarif_penyimpanan' => $request->tarif_penyimpanan,
            'satuan_tarif' => $request->satuan_tarif,

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
    public function hapus_pmbx(Request $request, $id)
    {
        Penyminyakbumi::destroy($id);
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
    public function hapus_pggbx(Request $request, $id)
    {
        Penygasbumi::destroy($id);
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
    public function submit_pmbx(Request $request, $id)
    {
        $idx = $id;
        $validatedData = DB::update("update penyminyakbumis set status='1' where id='$idx'");

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
    public function submit_pggbx(Request $request, $id)
    {
        $idx = $id;
        $validatedData = DB::update("update penygasbumis set status='1' where id='$idx'");

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
    public function get_pmb($id)
    {
        $data['produk'] = DB::select("SELECT produks.name FROM produks GROUP BY produks.name");
        $data['provinsi'] = DB::select("SELECT provinces.id, provinces.name FROM provinces GROUP BY provinces.name");
        $data['find'] = Penyminyakbumi::find($id);
        return response()->json(['data' => $data]);
    }
    public function get_pggb($id)
    {
        $data['produk'] = DB::select("SELECT produks.name FROM produks GROUP BY produks.name");
        $data['provinsi'] = DB::select("SELECT provinces.id, provinces.name FROM provinces GROUP BY provinces.name");
        $data['find'] = Penygasbumi::find($id);
        return response()->json(['data' => $data]);
    }
    public function update_pmbx(Request $request, $id)
    {
        $pmb = $id;
        $pesan = [
            'jenis_fasilitas.required' => 'jenis_fasilitas masih kosong',
            'no_tangki.required' => 'no_tangki masih kosong',
            'jenis_komoditas.required' => 'jenis komoditas masih kosong',
            'produk.required' => 'produk masih kosong',
            'kab_kota.required' => 'kab kota masih kosong',
            'kategori_supplai.required' => 'kategori supplai masih kosong',
            'volume_stok_awal.required' => 'volume stok_awal masih kosong',
            'volume_supply.required' => 'volume supply masih kosong',
            'volume_output.required' => 'volume output masih kosong',
            'volume_stok_akhir.required' => 'volume stok akhir masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'utilasi_tangki.required' => 'utilasi tangki masih kosong',
            'pengguna.required' => 'pengguna masih kosong',
            'jangka_waktu_penggunaan.required' => 'jangka waktu penggunaan masih kosong',
            'tarif_penyimpanan.required' => 'tarif_penyimpanan masih kosong',
            'satuan_tarif.required' => 'satuan tarif masih kosong',
            'keterangan.required' => 'keterangan masih kosong',
        ];

        $rules = [
            'jenis_fasilitas' => 'required',
            'no_tangki' => 'required',
            'jenis_komoditas' => 'required',
            'produk' => 'required',
            'kab_kota' => 'required',
            'kategori_supplai' => 'required',
            'volume_stok_awal' => 'required',
            'volume_supply' => 'required',
            'volume_output' => 'required',
            'volume_stok_akhir' => 'required',
            'satuan' => 'required',
            'utilasi_tangki' => 'required',
            'pengguna' => 'required',
            'jangka_waktu_penggunaan' => 'required',
            'tarif_penyimpanan' => 'required',
            'satuan_tarif' => 'required',
            'keterangan' => 'required',
            'jangka_waktu_penggunaan' => 'required',
            'status' => 'required',
        ];

        $validatedData = $request->validate($rules, $pesan);

        Penyminyakbumi::where('id', $pmb)
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
    public function update_pggbx(Request $request, $id)
    {
        $pmb = $id;
        $pesan = [
            'no_tangki.required' => 'no_tangki masih kosong',
            'produk.required' => 'produk masih kosong',
            'kab_kota.required' => 'kab kota masih kosong',
            'volume_stok_awal.required' => 'volume stok_awal masih kosong',
            'volume_supply.required' => 'volume supply masih kosong',
            'volume_output.required' => 'volume output masih kosong',
            'volume_stok_akhir.required' => 'volume stok akhir masih kosong',
            'satuan.required' => 'satuan masih kosong',
            'utilasi_tangki.required' => 'utilasi tangki masih kosong',
            'pengguna.required' => 'pengguna masih kosong',
            'jangka_waktu_penggunaan.required' => 'jangka waktu penggunaan masih kosong',
            'tarif_penyimpanan.required' => 'tarif_penyimpanan masih kosong',
            'satuan_tarif.required' => 'satuan tarif masih kosong',
        ];

        $rules = [
            'no_tangki' => 'required',
            'produk' => 'required',
            'kab_kota' => 'required',
            'volume_stok_awal' => 'required',
            'volume_supply' => 'required',
            'volume_output' => 'required',
            'volume_stok_akhir' => 'required',
            'satuan' => 'required',
            'utilasi_tangki' => 'required',
            'pengguna' => 'required',
            'jangka_waktu_penggunaan' => 'required',
            'tarif_penyimpanan' => 'required',
            'satuan_tarif' => 'required',
        ];

        $validatedData = $request->validate($rules, $pesan);

        Penygasbumi::where('id', $pmb)
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
    public function hapus_bulan_pmbx(Request $request, $bulan)
    {
        // dd($bulan);
        // die;
        $bulanx = $bulan;
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("delete from penyminyakbumis where badan_usaha_id='$badan_usaha_id' and bulan='$bulanx'");
        // pengangkutan_minyakbumi::destroy($bulan);
        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dihapus');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dihapus');
            return back();
        }
    }
    public function submit_bulan_pmbx(Request $request, $bulan)
    {
        $bulanx = $bulan;
        // dd($bulanx);
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("update penyminyakbumis set status='1' where bulan='$bulanx' and badan_usaha_id='$badan_usaha_id'");

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
    public function import_pmbx(Request $request)
    {
        $bulan = $request->bulan . "-01";
        // dd($bulan);

        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $cekdb = DB::table('penyminyakbumis')
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

        $import = Excel::import(new Importpenyimpananmb($bulan), request()->file('file'));

        if ($import) {
            //redirect dengan pesan sukses
            Alert::success('Success', 'Data excel berhasil diupload');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('Error', 'Data excel gagal diupload');
            return back();
        }
    }
    public function import_pggbx(Request $request)
    {
        $bulan = $request->bulan . "-01";

        $badan_usaha_id = Auth::user()->badan_usaha_id;

        $cekdb = DB::table('penygasbumis')
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
        $import = Excel::import(new Importpenyimpanangb($bulan), request()->file('file'));

        if ($import) {
            //redirect dengan pesan sukses
            Alert::success('Success', 'Data excel berhasil diupload');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('Error', 'Data excel gagal diupload');
            return back();
        }
    }
    public function get_kab_kota()
    {

        $data = DB::select("SELECT kotas.nama_kota FROM kotas");
        // $data = Produk::get();
        return response()->json(['data' => $data]);
    }
    public function get_sektor()
    {

        $data = DB::select("SELECT sektors.nama_sektor FROM sektors");
        // $data = Produk::get();
        return response()->json(['data' => $data]);
    }
    public function get_kab_kota_mb($kabupaten_kota)
    {
        // $data = DB::select("SELECT kotas.nama_kota FROM kotas WHERE kotas.kabupaten_kota = '$kabupaten_kota'");
        $data = DB::select("SELECT kotas.`nama_kota` FROM  kotas WHERE kotas.`id_prov` = (SELECT kotas.`id_prov` FROM kotas WHERE kotas.`nama_kota` = '$kabupaten_kota')");
        // $data = Produk::get();
        return response()->json(['data' => $data]);
    }
    public function hapus_bulan_pggbx(Request $request, $bulan)
    {
        // dd($bulan);
        // die;
        $bulanx = $bulan;
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("delete from penygasbumis where badan_usaha_id='$badan_usaha_id' and bulan='$bulanx'");
        // pengangkutan_minyakbumi::destroy($bulan);
        if ($validatedData) {
            //redirect dengan pesan sukses
            Alert::success('success', 'Data berhasil dihapus');
            return back();
        } else {
            //redirect dengan pesan error
            Alert::error('error', 'Data gagal dihapus');
            return back();
        }
    }
    public function submit_bulan_pggbx(Request $request, $bulan)
    {
        $bulanx = $bulan;
        $badan_usaha_id = Auth::user()->badan_usaha_id;
        $validatedData = DB::update("update penygasbumis set status='1' where bulan='$bulanx' and badan_usaha_id='$badan_usaha_id'");

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
}
