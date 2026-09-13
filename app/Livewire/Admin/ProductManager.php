<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductManager extends Component
{
    use WithFileUploads;

    public ?int $editingId = null;

    #[Validate('required|string|max:180')]
    public string $nameEn = '';

    #[Validate('required|string|max:180')]
    public string $nameMs = '';

    #[Validate('required|string|max:2000')]
    public string $descriptionEn = '';

    #[Validate('required|string|max:2000')]
    public string $descriptionMs = '';

    #[Validate('required|exists:categories,id')]
    public ?int $categoryId = null;

    #[Validate('required|numeric|min:0|max:99999.99')]
    public string $price = '';

    #[Validate('required|in:draft,published')]
    public string $status = 'draft';

    #[Validate('required|alpha_dash|max:180')]
    public string $slug = '';

    #[Validate('nullable|image|max:2048')]
    public $cover = null;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorize('viewAny', Product::class);
    }

    public function create(): void
    {
        $this->authorize('create', Product::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Product $product): void
    {
        $this->authorize('update', $product);

        $this->editingId = $product->id;
        $this->nameEn = $product->translation('en')?->name ?? '';
        $this->nameMs = $product->translation('ms')?->name ?? '';
        $this->descriptionEn = $product->translation('en')?->description ?? '';
        $this->descriptionMs = $product->translation('ms')?->description ?? '';
        $this->categoryId = $product->category_id;
        $this->price = number_format($product->price_cents / 100, 2, '.', '');
        $this->status = $product->status;
        $this->slug = $product->slug;
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'category_id' => $this->categoryId,
            'slug' => $this->slug,
            'price_cents' => (int) round(((float) $this->price) * 100),
            'status' => $this->status,
        ];

        if ($this->cover) {
            $data['cover_path'] = $this->cover->store('products/covers', 'public');
        }

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            $this->authorize('update', $product);
            $product->update($data);
        } else {
            $this->authorize('create', Product::class);
            $product = Product::create($data);
        }

        $product->translations()->updateOrCreate(['locale' => 'en'], ['name' => $this->nameEn, 'description' => $this->descriptionEn]);
        $product->translations()->updateOrCreate(['locale' => 'ms'], ['name' => $this->nameMs, 'description' => $this->descriptionMs]);

        session()->flash('status', __('admin.products.saved'));
        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(Product $product): void
    {
        $this->authorize('delete', $product);

        if ($product->cover_path) {
            Storage::disk('public')->delete($product->cover_path);
        }

        $product->delete();
        session()->flash('status', __('admin.products.deleted'));
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'nameEn', 'nameMs', 'descriptionEn', 'descriptionMs', 'categoryId', 'price', 'status', 'slug', 'cover']);
        $this->status = 'draft';
        $this->resetValidation();
    }

    public function updatedNameEn(string $value): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function render(): View
    {
        return view('livewire.admin.product-manager', [
            'products' => Product::with(['category', 'translations'])->latest()->get(),
            'categories' => Category::orderBy('name')->get(),
        ])->layout('layouts.authenticated');
    }
}
