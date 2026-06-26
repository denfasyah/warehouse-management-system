<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required|numeric|min:0',
        ]);

        foreach ($request->settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        // Jalankan rekalkulasi otomatis setiap kali setting diupdate
        \App\Services\CBSService::recalculateAll();

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui dan klasifikasi CBS telah dihitung ulang.');
    }
}
