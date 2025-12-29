<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    use ApiResponseTrait;

    /**
     * List tags
     */
    public function index(Request $request)
    {
        $tags = Tag::withTranslation()
            ->withCount('posts')
            ->when(
                $request->filled('search'),
                fn($q) => $q->whereHas(
                    'translations',
                    fn($t) => $t->where('name', 'like', '%' . $request->search . '%')
                )
            )
            ->paginate($request->integer('per_page', 20));

        return $this->ok($tags, 'messages.success');
    }

    /**
     * Show single tag
     */
    public function show(Tag $tag)
    {
        $tag->load([
            'translations',
            'posts.translation'
        ]);

        return $this->ok($tag, 'messages.success');
    }

    /**
     * Store tag
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $tag = Tag::create();

            foreach ($validated['name'] as $locale => $name) {
                $tag->translations()->create([
                    'locale' => $locale,
                    'name' => $name,
                ]);
            }

            DB::commit();

            return $this->created(
                $tag->load('translations'),
                'messages.tag_created'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update tag
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name.en' => 'sometimes|string|max:255',
            'name.ar' => 'sometimes|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            if (isset($validated['name'])) {
                foreach ($validated['name'] as $locale => $name) {
                    $tag->translations()->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $name]
                    );
                }
            }

            DB::commit();

            return $this->ok(
                $tag->load('translations'),
                'messages.tag_updated'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete tag
     */
    public function destroy(Tag $tag)
    {
        $deletedId = $tag->id;
        $tag->delete();

        return $this->deletedResponse(
            null,
            [
                'deleted_id' => $deletedId,
                'deleted_at' => now()->toISOString(),
            ],
            'messages.tag_deleted'
        );
    }
}
