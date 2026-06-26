<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Category;
use Illuminate\Support\Str;

class ItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('item') ? $this->route('item')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('items')->ignore($itemId)],
            'category_id' => ['required', 'exists:categories,id'],
            'location_ids' => ['required', 'array', 'min:1'],
            'location_ids.*' => ['exists:locations,id'],
            'sku' => [
                'nullable', 
                'string', 
                'max:50', 
                Rule::unique('items')->ignore($itemId)
            ],
            'barcode' => [
                'nullable', 
                'string', 
                'max:100', 
                Rule::unique('items')->ignore($itemId)
            ],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation()
    {
        $data = [];

        // Auto-generate slug if empty
        if (empty($this->slug) && $this->name) {
            $data['slug'] = Str::slug($this->name);
        }

        // Auto-generate SKU if empty
        if (empty($this->sku) && $this->category_id) {
            $category = Category::find($this->category_id);
            if ($category) {
                $data['sku'] = $category->code . '-' . strtoupper(Str::random(6));
            }
        }

        // Auto-generate barcode if empty
        if (empty($this->barcode)) {
            $data['barcode'] = 'BC-' . strtoupper(Str::random(12));
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}
