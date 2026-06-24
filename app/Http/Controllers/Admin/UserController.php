<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'petugas')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['role'] = 'petugas';
        $data['password'] = Hash::make($data['password']);

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'Akun petugas berhasil ditambahkan.');
    }

    public function update(UserRequest $request, User $user)
    {
        // Admin tidak boleh mengedit user role admin lain lewat rute ini untuk keamanan
        if ($user->role === 'admin') {
            return back()->with('error', 'Anda tidak dapat mengedit akun admin.');
        }

        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'Akun petugas berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Anda tidak dapat menghapus akun admin.');
        }

        // Opsional: cek apakah petugas ini punya riwayat barang masuk/keluar
        if ($user->incomingGoods()->count() > 0 || $user->requestedOutgoingGoods()->count() > 0) {
            // Bisa menggunakan soft deletes atau cukup me-nonaktifkan
            $user->update(['is_active' => false]);
            return redirect()->route('admin.users.index')->with('success', 'Akun petugas dinonaktifkan karena memiliki riwayat transaksi.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun petugas berhasil dihapus.');
    }
}
