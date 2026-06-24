<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Location;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locationId = $this->route('location') ? $this->route('location')->id : null;

        return [
            'zone' => ['required', 'string', 'max:50'],
            'rack' => ['required', 'string', 'max:10'],
            'bin'  => ['required', 'string', 'max:10'],
            'storage_class' => ['required', 'in:fast,medium,slow,general'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Cek apakah kombinasi zone, rack, bin sudah ada
            $locationId = $this->route('location') ? $this->route('location')->id : null;
            
            $exists = Location::where('zone', $this->zone)
                ->where('rack', str_pad($this->rack, 2, '0', STR_PAD_LEFT))
                ->where('bin', str_pad($this->bin, 2, '0', STR_PAD_LEFT));

            if ($locationId) {
                $exists->where('id', '!=', $locationId);
            }

            if ($exists->exists()) {
                $validator->errors()->add('code', 'Kombinasi Zone, Rack, dan Bin sudah terdaftar (Kode duplikat).');
            }
        });
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'zone' => strtoupper($this->zone),
        ]);
    }
}
