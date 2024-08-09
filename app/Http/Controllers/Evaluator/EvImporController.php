<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Impor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EvImporController extends Controller
{

    public function index(){

        $perusahaan = DB::table('impors  as a')
            ->leftJoin('t_perusahaan as b', 'a.badan_usaha_id', '=', 'b.ID_PERUSAHAAN')
            ->select('b.id_perusahaan', 'b.NAMA_PERUSAHAAN')
            ->groupBy('a.badan_usaha_id')
            ->whereIn('a.status', [1, 2,3])
            ->get();
        $data = [
            'title'=>'Laporan Impor',
            'perusahaan' => $perusahaan,
        ];

        return view('evaluator.laporan_bu.exim.impor.index',$data);
    }

    public function cetakperiode(Request $request)
    {
        $perusahaan = $request->input('perusahaan');
        $t_awal = $request->input('t_awal');
        $t_akhir = $request->input('t_akhir');

        $result = DB::table('impors as a')
            ->leftJoin('t_perusahaan as b', 'a.badan_usaha_id', '=', 'b.ID_PERUSAHAAN')
            ->select('a.*', 'b.NAMA_PERUSAHAAN')
            ->where('badan_usaha_id', $perusahaan)
            ->whereIn('a.status', [1, 2, 3])
            ->whereBetween('bulan_pib', [$t_awal, $t_akhir])
            ->get();

        if ($result->isEmpty()) {
            return redirect()->back()->with('sweet_error', 'Data yang anda minta kosong.');
        } else {
            $data = [
                'title'=>'Laporan Impor',
                'result' => $result
            ];

            $view = view('evaluator.laporan_bu.exim.impor.cetak', $data);

            $view->with('reload', true);

            return response($view);
        }
    }

    public function periode($kode = '')
    {


        $p = !empty($kode) ? Crypt::decrypt($kode) : null;
        if ($p) {
            $query = DB::table('impors as a')
                ->leftJoin('t_perusahaan as b', 'a.badan_usaha_id', '=', 'b.ID_PERUSAHAAN')
                ->select('a.*', 'b.NAMA_PERUSAHAAN')
                ->where('a.badan_usaha_id', $p)
                ->whereIn('a.status', [1, 2,3])
                ->groupBy('a.bulan_pib')->get();


        } else {
            $query = '';

        }
        $data = [
            'title'=>'Laporan Impor',
            'p' => $p,
            'query' => $query,
            'per' => $query->first()
        ];
        return view('evaluator.laporan_bu.exim.impor.periode', $data);
    }

    public function show($kode = '')
    {

        $pecah = explode(',', Crypt::decryptString($kode));
        $query = DB::table('impors as a')
            ->leftJoin('t_perusahaan as b', 'a.badan_usaha_id', '=', 'b.ID_PERUSAHAAN')
            ->select('a.*', 'b.NAMA_PERUSAHAAN')
            ->where('a.badan_usaha_id', $pecah[1])
            ->where('a.bulan_pib', $pecah[0])
            ->whereIn('a.status', [1, 2,3])
            ->get();

//        var_dump($query);die();

        $data = [
            'title'=>'Laporan Impor',
            'query'=>$query,
            'per'=>$query->first()

        ];
        return view('evaluator.laporan_bu.exim.impor.pilihbulan', $data);

    }

    public function updateRevisionNotes(Request $request)
    {

        $request->validate([
            'catatan' => 'required',
        ]);

        $id = Crypt::decrypt($request->input('id'));


        $update = Impor::where('id', $id)
            ->update([
                'catatan' => $request->catatan,
                'status' => '2'
            ]);

        return redirect()->back()->with('sweet_success', 'Catatan revisi berhasil dikirim.');
    }

    public function updateRevisionNotesAll(Request $request)
    {

        $request->validate([
            'catatan' => 'required',
        ]);
        $badan_usaha_id = Crypt::decrypt($request->input('p')) ;
        $bulan = Crypt::decrypt($request->input('b')) ;



        $update = Impor::where('badan_usaha_id', $badan_usaha_id)->where('bulan_pib',$bulan)
            ->whereIn('status', [1, 2,3])
            ->update([
                'catatan' => $request->catatan,
                'status' => '2'
            ]);


        if ($update) {
            return redirect()->back()->with('sweet_success', 'Catatan revisi berhasil dikirim.');
        } else {
            return redirect()->back()->with('sweet_error', 'Catatan revisi gagal dikirim.');
        }
    }

    public function selesaiPeriodeAll(Request $request)
    {
        try {
            $badan_usaha_id = Crypt::decrypt($request->input('p'));
            $bulan = Crypt::decrypt($request->input('b'));

            // Pastikan bahwa badan_usaha_id dan bulan ada dalam kondisi where
            $update = Impor::where('badan_usaha_id', $badan_usaha_id)
                ->where('bulan_pib', $bulan)
                ->whereIn('status', [1, 2,3])
                ->update([
                    'status' => '3'
                ]);



            if ($update) {
                // Jika berhasil, kembalikan respons JSON
                return response()->json(['success' => 'Periode berhasil diselesaikan.']);
            } else {
                // Jika gagal, kembalikan respons JSON dengan status 500 (Internal Server Error)
                return response()->json(['error' => 'Gagal menyelesaikan periode.'], 500);
            }
        } catch (\Exception $e) {
            // Tangkap dan tangani exception
            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui status.'], 500);
        }
    }

    public function selesaiPeriode(Request $request)
    {
        try {
            $id = $request->input('id');

            // Pastikan bahwa badan_usaha_id dan bulan ada dalam kondisi where
            $update = Impor::where('id', $id)
                ->update([
                    'status' => '3'
                ]);



            if ($update) {
                // Jika berhasil, kembalikan respons JSON
                return response()->json(['success' => 'Periode berhasil diselesaikan.']);
            } else {
                // Jika gagal, kembalikan respons JSON dengan status 500 (Internal Server Error)
                return response()->json(['error' => 'Gagal menyelesaikan periode.'], 500);
            }
        } catch (\Exception $e) {
            // Tangkap dan tangani exception
            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui status.'], 500);
        }
    }

//
//    public function index(Request $request){
//		//dd($request->all());
//		$query = Impor::select('impors.*','t_perusahaan.NAMA_PERUSAHAAN','izins.key')
//			->leftJoin('t_perusahaan','impors.badan_usaha_id','t_perusahaan.ID_PERUSAHAAN')
//			->leftJoin('izins','impors.izin_id','izins.id')
//			->whereIn('status',['2','1']);
//
//		$bu = Impor::select('t_perusahaan.NAMA_PERUSAHAAN')
//			->leftJoin('t_perusahaan','impors.badan_usaha_id','t_perusahaan.ID_PERUSAHAAN')
//			->groupBy('t_perusahaan.NAMA_PERUSAHAAN')
//			->orderBy('t_perusahaan.NAMA_PERUSAHAAN','asc')
//			->get();
//
//		$produk = Impor::select('produk')
//			->groupBy('produk')
//			->orderBy('produk','asc')
//			->get();
//
//		if ($request->bulan1 == '' || $request->bulan2 == '') {
//			// dd('hai');
//            $bulan1 = Carbon::now()->format('Y-m');
//            // $bulan2 = Carbon::now()->subMonth(1);
//            $bulan2 = Carbon::now()->format('Y-m');
//			// dd($bulan1,$bulan2);
//			$query = $query->whereBetween(DB::raw("(date_format(bulan_pib,'%Y-%m'))"), [$bulan1, $bulan2]);
//        } else {
//            $request->validate([
//                'bulan1' => 'required|date',
//                'bulan2' => 'required|date|after_or_equal:date_start'
//            ]);
//
//            $bulan1 = $request->bulan1;
//            $bulan2 = $request->bulan2;
//			$query = $query->whereBetween(DB::raw("(date_format(bulan_pib,'%Y-%m'))"), [$bulan1, $bulan2]);
//        }
//		// dd($bulan1,$bulan2);
//
//		if($request->badan_usaha != ''){
//			$query = $query->where('t_perusahaan.NAMA_PERUSAHAAN',$request->badan_usaha);
//		}
//
//		if($request->produk != ''){
//			$query = $query->where('impors.produk',$request->produk);
//		}
//
//		if($request->kab_kota != ''){
//			$query = $query->where('impors.kabupaten_kota',$request->kab_kota);
//		}
//
//		$query = $query->orderBy('id','asc')->get();
//
//        // $query = [];
//        // $bu = [];
//        // $produk = [];
//        // $kota = [];
//
//		return view('evaluator.laporan_bu.exim.impor.index',compact('query','bu','produk'));
//	}
//
//	public function updateRevisionNotes(Request $request,$id){
//		// dd('hai');
//		// Validasi input jika diperlukan
//        $request->validate([
//            'catatan' => 'required',
//        ]);
//
//        // Proses untuk mengupdate catatan revisi
//        // Misalnya, Anda dapat menyimpan catatan revisi dalam database atau melakukan tindakan lainnya
//        // Contoh:
//		$update = Impor::where('id',$id)
//			->update([
//				'catatan' => $request->catatan,
//				'status' => '2'
//			]);
//
//        return redirect()->back()->with('success', 'Catatan revisi berhasil dikirim.');
//	}
}
