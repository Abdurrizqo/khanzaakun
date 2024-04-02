<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\pegawai;
use App\Models\Petugas;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    public function allUser(Request $request)
    {

        $search = $request->query('search');
        if (empty($search)) {
            $allUser = DB::table('user')
                ->selectRaw("*, AES_DECRYPT(user.id_user, 'nur') AS id_user, AES_DECRYPT(user.password, 'windi') AS password, pegawai.nama AS nama_pegawai")
                ->leftJoin('pegawai', 'pegawai.nik', '=', DB::raw("AES_DECRYPT(user.id_user, 'nur')"))
                ->paginate(20);
        } else {
            $allUser = DB::table('user')
                ->selectRaw("*, AES_DECRYPT(user.id_user, 'nur') AS id_user, AES_DECRYPT(user.password, 'windi') AS password, pegawai.nama AS nama_pegawai")
                ->leftJoin('pegawai', 'pegawai.nik', '=', DB::raw("AES_DECRYPT(user.id_user, 'nur')"))
                ->whereRaw("AES_DECRYPT(user.id_user, 'nur') LIKE ?", ["%$search%"])
                ->orWhereRaw("pegawai.nama LIKE ?", ["%$search%"]) // Tambahkan baris ini
                ->paginate(20);
            $allUser->withQueryString();
        }
        $headers = [];
        if (!empty($allUser)) {
            $headers = array_keys((array) $allUser[0]);
        }


        return view('akun/listAkun', ['allUser' => $allUser, 'headData' => $headers]);
    }

    public function selectUser($idUser)
    {
        $selectedUser = DB::selectOne("
        SELECT *, 
               AES_DECRYPT(user.id_user, 'nur') AS id_user, 
               AES_DECRYPT(user.password, 'windi') AS password, 
               pegawai.nama
        FROM user
        LEFT JOIN pegawai ON pegawai.nik = AES_DECRYPT(user.id_user, 'nur')
        WHERE id_user = AES_ENCRYPT(?, 'nur') 
        LIMIT 1", [$idUser]);

        $headers = [];
        if (!empty($selectedUser)) {
            $headers = array_keys((array) $selectedUser);
        }

        return view('Akun/detailUser', ['user' => $selectedUser, 'headData' => $headers]);
    }

    public function editSatuUser(Request $request, $idUser)
    {
        if ($request['nilaiAwal'] === 'true') {
            $request['nilaiAwal'] = 'false';
        } else {
            $request['nilaiAwal'] = 'true';
        }

        $tes = DB::table('user')
            ->where('id_user', '=', DB::raw("AES_ENCRYPT('$idUser', 'nur')"))
            ->update([$request['namaKolom'] => $request['nilaiAwal']]);

        if ($tes) {
            return redirect()->refresh()->with('success', 'Berhasil Ubah Akses');
        } else {
            return redirect()->refresh()->with('success', 'Gagal Ubah Akses');
        }
    }

    public function copyAkses(Request $request)
    {
        DB::beginTransaction();

        try {
            $selectedUser = DB::selectOne("
                SELECT *
                FROM user
                WHERE id_user = AES_ENCRYPT(?, 'nur') 
                LIMIT 1
            ", [$request->userUtamaSalin]);

            if ($selectedUser) {
                $updateData = [];
                foreach ($selectedUser as $key => $value) {
                    if ($key != 'id_user' && $key != 'password') {
                        $updateData[$key] = $value;
                    }
                }

                $tes = DB::table('user')
                    ->whereRaw("id_user = AES_ENCRYPT(?, 'nur')", [$request->userGanti])
                    ->update($updateData);
            } else {
                DB::rollback();
                return redirect()->back()->withInput()->withErrors(['error' => 'Id User Akun Tidak Ditemukan']);
            }

            DB::commit();
            if ($tes) {
                return redirect('/')->with('success', 'Berhasil Copy Akses');
            } else {
                return redirect('/')->with('success', 'Tidak Ada Yang DiUbah dari aksesnya');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'SALAH']);
        }
    }

    public function deleteUser($user)
    {
        $tes = DB::table('user')->whereRaw("id_user = AES_ENCRYPT(?, 'nur')", [$user])->delete();

        if ($tes) {
            return redirect('/')->with('success', 'Berhasil Delete User');
        } else {
            return redirect()->refresh()->withInput()->withErrors(['error' => 'Gagal Delete User']);
        }
    }

    public function gantiPassword($user)
    {

        $selectedUser = DB::selectOne("
        SELECT id_user
        FROM user
        WHERE id_user = AES_ENCRYPT(?, 'nur') 
        LIMIT 1", [$user]);

        $tes = DB::update("
            UPDATE user 
            SET password = AES_ENCRYPT('1234', 'windi') 
            WHERE id_user = ?
        ", [$selectedUser->id_user]);

        if ($tes) {
            return redirect('/')->with('success', 'Berhasil Ganti Password');
        }
        return redirect()->refresh()->withInput()->withErrors(['error' => 'Gagal Ganti Password']);
    }

    public function buatUser(Request $request)
    {
        DB::beginTransaction();

        try {

            $selectedUser = DB::selectOne(
                "SELECT *
                    FROM user
                    WHERE id_user = AES_ENCRYPT(?, 'nur') 
                    LIMIT 1",
                [$request->userUtama]
            );

            if (empty($selectedUser)) {
                DB::rollback();
                return redirect()->back()->withInput()->withErrors(['error' => 'Id Pegawai Akun Salin Tidak Ditemukan']);
            }

            $updateData = [];
            foreach ($selectedUser as $key => $value) {
                $updateData[$key] = $value;
            }

            $updateData['id_user'] = DB::raw("AES_ENCRYPT('$request->userBaru', 'nur')");
            $updateData['password'] = DB::raw("AES_ENCRYPT('1234', 'windi')");

            $tes = DB::table('user')->insert($updateData);

            if ($tes) {
                return redirect('/')->with('success', 'Berhasil Tambah User');
            } else {
                return redirect()->back()->withInput()->withErrors(['error' => 'Gagal Tambah User']);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'SALAH']);
        }
    }

    public function copyBuatBanyak(Request $request)
    {
        DB::beginTransaction();

        try {
            $selectedUser = DB::selectOne(
                "SELECT *
                    FROM user
                    WHERE id_user = AES_ENCRYPT(?, 'nur') 
                    LIMIT 1",
                [$request->userUtama]
            );

            if (empty($selectedUser)) {
                DB::rollback();
                throw new Error('User Utama Tidak Ditemukan');
            } else {
                $properti = [];
                foreach ($selectedUser as $key => $value) {
                    $properti[$key] = $value;
                }

                $res = array();

                foreach ($request['userMasuk'] as $item) {
                    $checkUser = DB::selectOne(
                        "SELECT 
                        AES_DECRYPT(user.id_user, 'nur') AS id_user, 
                        AES_DECRYPT(user.password, 'windi') AS password
                            FROM user
                            WHERE id_user = AES_ENCRYPT(?, 'nur') 
                            LIMIT 1",
                        [$item]
                    );

                    if ($checkUser) {
                        $properti['id_user'] =  DB::raw("AES_ENCRYPT('$checkUser->id_user', 'nur')");
                        $properti['password'] = DB::raw("AES_ENCRYPT('$checkUser->password', 'windi')");

                        DB::table('user')
                            ->whereRaw("id_user = AES_ENCRYPT(?, 'nur')", [$item])
                            ->update($properti);

                        $res[] = "berhasil update, $checkUser->id_user";
                    } else {
                        $properti['id_user'] = DB::raw("AES_ENCRYPT('$item', 'nur')");
                        $properti['password'] = DB::raw("AES_ENCRYPT('1234', 'windi')");

                        DB::table('user')->insert($properti);
                        $res[] = "berhasil tambah baru, $item";
                    }
                }

                DB::commit();
                return response()->json(["data" => $res], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["error" => $th->getMessage()], 400);
        }
    }

    public function buatUserBanyak(Request $request)
    {
        DB::beginTransaction();

        try {
            $pegawai = pegawai::where('nik', $request->userBaru)->first();

            if (empty($pegawai)) {
                DB::rollback();
                return redirect()->back()->withInput()->withErrors(['error' => 'Id Pegawai AKun Baru Tidak Ditemukan']);
            } else {
                $selectedUser = DB::selectOne(
                    "SELECT *
                    FROM user
                    WHERE id_user = AES_ENCRYPT(?, 'nur') 
                    LIMIT 1",
                    [$request->userUtama]
                );

                if (empty($selectedUser)) {
                    DB::rollback();
                    return redirect()->back()->withInput()->withErrors(['error' => 'Id Pegawai Akun Salin Tidak Ditemukan']);
                }

                $updateData = [];
                foreach ($selectedUser as $key => $value) {
                    $updateData[$key] = $value;
                }

                $updateData['id_user'] = DB::raw("AES_ENCRYPT('$request->userBaru', 'nur')");
                $updateData['password'] = DB::raw("AES_ENCRYPT('1234', 'windi')");

                $tes = DB::table('user')->insert($updateData);

                if ($tes) {
                    return redirect('/')->with('success', 'Berhasil Tambah User');
                } else {
                    return redirect()->back()->withInput()->withErrors(['error' => 'Gagal Tambah User']);
                }
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->back()->withInput()->withErrors(['error' => 'SALAH']);
        }
    }

    public function listPetugas(Request $request)
    {
        $search = $request->query('search');
        if (empty($search)) {
            $allUser = Petugas::get();
        } else {

            $allUser = Petugas::where('nama', 'LIKE', "%$search%")->get();
        }


        return view('petugas/listPetugas', ['allUser' => $allUser]);
    }
}
