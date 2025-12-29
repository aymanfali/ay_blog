<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * List categories
     */
    public function index(Request $request)
    {
        $categories = Category::withTranslation()
            ->with([
                'posts' => fn($q) => $q->published()
            ])
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->status)
            )
            ->paginate($request->integer('per_page', 15));

        return $this->ok($categories, 'messages.success');
    }

    /**
     * Show single category
     */
    public function show(Category $category)
    {
        $category->load([
            'translation',
            'posts' => fn($q) => $q->published()
        ]);

        return $this->ok($category, 'messages.success');
    }

    /**
     * Store category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'status' => 'required|in:pending,published,draft',
            'published_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $category = Category::create([
                'image' => $validated['image'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? null,
            ]);

            foreach ($validated['name'] as $locale => $name) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }

            DB::commit();

            return $this->created(
                $category->load('translations'),
                'messages.category_created'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name.en' => 'sometimes|string|max:255',
            'name.ar' => 'sometimes|string|max:255',
            'image' => 'nullable|string|max:255',
            'status' => 'sometimes|in:pending,published,draft',
            'published_at' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $category->update($request->only([
                'image',
                'status',
                'published_at'
            ]));

            if (isset($validated['name'])) {
                foreach ($validated['name'] as $locale => $name) {
                    $category->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $name]
                    );
                }
            }

            DB::commit();

            return $this->ok(
                $category->load('translations'),
                'messages.category_updated'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        $deletedId = $category->id;
        $category->delete();

        return $this->deletedResponse(
            null,
            [
                'deleted_id' => $deletedId,
                'deleted_at' => now()->toISOString(),
            ],
            'messages.category_deleted'
        );
    }
}
