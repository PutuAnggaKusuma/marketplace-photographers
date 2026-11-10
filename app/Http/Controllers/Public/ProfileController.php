<?php



namespace App\Http\Controllers\Public;



use App\Http\Controllers\Controller;

use App\Models\User;

use App\Models\RolePhotographer;

use App\Models\RoleClient;

use App\Models\Contract;

use App\Models\ForumPost;

use App\Models\Testimonial;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rules\Password;



class ProfileController extends Controller

{

    /**

     * Tampilkan Halaman Pengaturan Profil & Keamanan Akun.

     */

    public function index()

    {

        $user = Auth::user();

        if ($user->role === 'photographer') {
            $photographer = RolePhotographer::with(['province', 'city', 'categories'])->firstOrCreate(
                ['id_user' => $user->id],
                ['nama' => $user->nama]
            );
            $provinces = \Laravolt\Indonesia\Models\Province::orderBy('name', 'asc')->get();
            $categories = \App\Models\Category::all();
            $cities = $photographer->province_code 
                ? \Laravolt\Indonesia\Models\City::where('province_code', $photographer->province_code)->orderBy('name', 'asc')->get()
                : collect();

            return view('photographer.profile.index', compact('user', 'photographer', 'provinces', 'categories', 'cities'));
        }



        $photographer = null;

        $client = $user->role === 'client'

            ? RoleClient::where('id_user', $user->id)->first()

            : null;



        return view('public.profile.index', compact('user', 'photographer', 'client'));

    }



    /**

     * Perbarui Informasi Profil Biodata.

     */

    public function updateProfile(Request $request)

    {

        $user = Auth::user();



        $request->validate([

            'nama' => 'required|string|max:255',

            'email' => 'required|email|max:255|unique:users,email,' . $user->id,

            'foto' => 'nullable|url',

            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',

            'foto_sampul_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',

            'nomor_telepon' => 'nullable|string|max:20',

            'link_sosmed' => 'nullable|string|max:255',

            'province_code' => 'nullable|string|max:10',

            'city_code' => 'nullable|string|max:10',

            'alamat' => 'nullable|string|max:255',

            'categories' => 'nullable|array',

            'categories.*' => 'exists:categories,id',

            'deskripsi_bio' => 'nullable|string|max:2000',

            'nama_bank' => 'nullable|string|max:50',

            'nomor_rekening' => 'nullable|string|max:50',

            'atas_nama_rekening' => 'nullable|string|max:100',

        ], [

            'nama.required' => 'Nama lengkap wajib diisi.',

            'email.required' => 'Alamat email wajib diisi.',

            'email.unique' => 'Alamat email sudah digunakan oleh pengguna lain.',

            'foto.url' => 'Format URL foto avatar tidak valid.',

            'foto_file.image' => 'File foto profil harus berupa gambar.',

            'foto_file.mimes' => 'Format foto profil harus JPG, PNG, atau WEBP.',

            'foto_file.max' => 'Ukuran foto profil maksimal 10 MB.',

            'foto_sampul_file.image' => 'File foto sampul harus berupa gambar.',

            'foto_sampul_file.mimes' => 'Format foto sampul harus JPG, PNG, atau WEBP.',

            'foto_sampul_file.max' => 'Ukuran foto sampul maksimal 20 MB.',

        ]);



        $user->update([

            'nama' => trim($request->nama),

            'email' => trim($request->email),

        ]);



        if ($user->role === 'photographer') {

            $photographer = RolePhotographer::where('id_user', $user->id)->first();

            if ($photographer) {

                $photoData = [

                    'nama' => trim($request->nama ?? ''),

                    'nomor_telepon' => trim($request->nomor_telepon ?? ''),

                    'link_sosmed' => trim($request->link_sosmed ?? ''),

                    'province_code' => $request->province_code ?: null,

                    'city_code' => $request->city_code ?: null,

                    'alamat' => trim($request->alamat ?? ''),

                    'deskripsi_bio' => trim($request->deskripsi_bio ?? ''),

                    'nama_bank' => trim($request->nama_bank ?? ''),

                    'nomor_rekening' => trim($request->nomor_rekening ?? ''),

                    'atas_nama_rekening' => trim($request->atas_nama_rekening ?? ''),

                ];



                if ($request->hasFile('foto_file')) {

                    $path = $request->file('foto_file')->store('photographer_avatars', 'public');

                    $photoData['foto'] = asset('storage/' . $path);

                    
                } elseif ($request->filled('foto')) {

                    $photoData['foto'] = trim($request->foto);

                }



                if ($request->hasFile('foto_sampul_file')) {

                    $path = $request->file('foto_sampul_file')->store('photographer_covers', 'public');

                    $photoData['foto_sampul'] = asset('storage/' . $path);

                }



                $wasRejected = ($photographer->status_verifikasi === 'ditolak');
                $photographer->update($photoData);

                if ($request->has('categories')) {
                    $photographer->categories()->sync($request->categories);
                }

                $isComplete = $photographer->fresh(['categories'])->isProfileComplete();
                $updatePayload = ['is_onboarded' => $isComplete];

                if ($wasRejected) {
                    $updatePayload['status_verifikasi'] = 'menunggu';
                    $updatePayload['is_verified'] = false;
                }

                $photographer->update($updatePayload);

                if ($wasRejected) {
                    \App\Models\ActivityLog::record(
                        'photographer_reapplied',
                        "Mitra '{$photographer->nama}' memperbarui data profil setelah ditolak dan mengajukan verifikasi kembali.",
                        $photographer
                    );

                    $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
                    foreach ($admins as $admin) {
                        \App\Models\Notification::send(
                            $admin->id,
                            'Pengajuan Ulang Verifikasi Mitra',
                            "Mitra fotografer '{$photographer->nama}' telah memperbarui profil dan mengajukan verifikasi kembali.",
                            'photographer_verification',
                            route('admin.photographers.show', $photographer->id)
                        );
                    }

                    return redirect()->back()->with('success', 'Profil berhasil diperbarui dan diajukan kembali ke Admin untuk verifikasi!');
                }

            }

        } elseif ($user->role === 'client') {

            $client = RoleClient::where('id_user', $user->id)->first();

            if ($client) {

                $clientData = [

                    'nama' => trim($request->nama),

                    'nomor_telepon' => trim($request->nomor_telepon),

                ];



                if ($request->hasFile('foto_file')) {

                    $path = $request->file('foto_file')->store('client_avatars', 'public');

                    $clientData['foto'] = asset('storage/' . $path);

                    
                } elseif ($request->filled('foto')) {

                    $clientData['foto'] = trim($request->foto);

                    
                }



                $client->update($clientData);

            }

        } elseif (in_array($user->role, ['admin', 'super_admin'])) {

            $admin = \App\Models\RoleAdmin::where('id_user', $user->id)->first();

            if ($admin) {

                $adminData = [

                    'nama' => trim($request->nama),

                    'no_telepon' => trim($request->nomor_telepon),

                ];

                if ($request->hasFile('foto_file')) {

                    $path = $request->file('foto_file')->store('admin_avatars', 'public');

                    $adminData['foto'] = asset('storage/' . $path);

                    
                } elseif ($request->filled('foto')) {

                    $adminData['foto'] = trim($request->foto);

                    
                }

                $admin->update($adminData);

            }

        }



        return redirect()->back()->with('success', 'Profil akun Anda berhasil diperbarui!');

    }



    /**

     * Perbarui Kata Sandi / Password Akun.

     */

    public function updatePassword(Request $request)

    {

        $user = Auth::user();



        $request->validate([

            'current_password' => 'required|string',

            'new_password' => ['required', 'string', 'min:8', 'confirmed'],

        ], [

            'current_password.required' => 'Masukkan kata sandi saat ini.',

            'new_password.required' => 'Masukkan kata sandi baru.',

            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',

            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',

        ]);



        if (!Hash::check($request->current_password, $user->password)) {

            return redirect()->back()
                ->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.'])
                ->with('active_tab', 'password')
                ->with('error', 'Kata sandi saat ini yang Anda masukkan salah.');

        }



        $user->update([

            'password' => Hash::make($request->new_password),

        ]);



        return redirect()->back()
            ->with('active_tab', 'password')
            ->with('success', 'Kata sandi akun Anda berhasil diperbarui!');

    }



    /**

     * Tampilkan Halaman Informasi Pengguna / Klien yang Dibagikan Publik.

     */

    public function publicShow($id)

    {

        // Cari pengguna berdasarkan ID User atau ID RoleClient

        $user = User::with(['roleClient', 'rolePhotographer'])->find($id);

        $client = null;



        if (!$user) {

            $client = RoleClient::with('user')->find($id);

            if ($client && $client->user) {

                $user = $client->user;

            } elseif ($client) {

                $clientName = $client->nama;

                $avatar = $client->foto ? (str_starts_with($client->foto, 'http') ? $client->foto : asset('storage/' . $client->foto)) : null;

                $initials = strtoupper(substr($clientName, 0, 2));

                return view('public.users.show', [

                    'user' => null,

                    'client' => $client,

                    'clientName' => $clientName,

                    'avatar' => $avatar,

                    'initials' => $initials,

                    'totalCompletedBookings' => 0,

                    'forumPostsCount' => 0,

                    'reviewsCount' => 0,

                    'joinedAt' => $client->created_at ? $client->created_at->locale('id')->translatedFormat('F Y') : '2026',

                ]);

            }

        }



        if (!$user) {

            abort(404, 'Profil pengguna tidak ditemukan.');

        }



        // Jika akun merupakan fotografer, alihkan langsung ke profil fotografer publik

        if ($user->role === 'photographer' && $user->rolePhotographer) {

            return redirect('/fotografer/' . $user->rolePhotographer->id);

        }



        $client = $user->roleClient;

        $clientName = $client?->nama ?? $user->nama;

        $avatar = $client?->foto ? (str_starts_with($client->foto, 'http') ? $client->foto : asset('storage/' . $client->foto)) : null;

        $initials = $user->initials;



        $totalCompletedBookings = 0;

        if ($client) {

            $totalCompletedBookings = Contract::where('id_client', $client->id)

                ->where('status_contract', 'selesai')

                ->count();

        }



        $forumPostsCount = ForumPost::where('id_user', $user->id)->count();

        $reviewsCount = $client ? Testimonial::where('id_client', $client->id)->count() : 0;

        $joinedAt = $user->created_at ? $user->created_at->locale('id')->translatedFormat('F Y') : '2026';



        return view('public.users.show', compact(

            'user',

            'client',

            'clientName',

            'avatar',

            'initials',

            'totalCompletedBookings',

            'forumPostsCount',

            'reviewsCount',

            'joinedAt'

        ));

    }

}