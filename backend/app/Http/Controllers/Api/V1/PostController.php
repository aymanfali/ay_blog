<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    use ApiResponseTrait;

    /**
     * List posts
     */
    public function index(Request $request)
    {
        $posts = Post::withTranslation()
            ->with([
                'category' => fn($q) => $q->withTranslation(),
                'tags.translations',
                'author',
            ])
            ->when(
                $request->filled('status'),
                fn($q) => $q->where('status', $request->status)
            )
            ->when(
                $request->filled('category_id'),
                fn($q) => $q->where('category_id', $request->category_id)
            )
            ->when(
                $request->filled('user_id'),
                fn($q) => $q->where('user_id', $request->user_id)
            )
            ->paginate($request->integer('per_page', 15));

        return $this->ok($posts, 'messages.success');
    }

    /**
     * Show single post
     */
    public function show(Post $post)
    {
        $post->load([
            'translation',
            'category' => fn($q) => $q->withTranslation(),
            'tags.translations',
            'author',
            'comments.user',
            'reactions.user',
        ]);

        $post->increment('views_count');

        return $this->ok($post, 'messages.success');
    }

    /**
     * Store post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title.en' => 'required|string|max:255',
            'title.ar' => 'required|string|max:255',
            'excerpt.en' => 'nullable|string|max:500',
            'excerpt.ar' => 'nullable|string|max:500',
            'content.en' => 'required|string',
            'content.ar' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'status' => 'required|in:pending,published,draft',
            'published_at' => 'nullable|date',
            'expected_time_to_read' => 'nullable|integer|min:1',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        try {
            DB::beginTransaction();

            $post = Post::create([
                'user_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'image' => $validated['image'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? null,
                'expected_time_to_read' => $validated['expected_time_to_read'] ?? 1,
            ]);

            foreach ($validated['title'] as $locale => $title) {
                $post->translations()->create([
                    'locale' => $locale,
                    'title' => $title,
                    'excerpt' => $validated['excerpt'][$locale] ?? null,
                    'content' => $validated['content'][$locale],
                ]);
            }

            if (!empty($validated['tag_ids'])) {
                $post->tags()->sync($validated['tag_ids']);
            }

            DB::commit();

            return $this->created(
                $post->load(['translations', 'category.translations', 'tags.translations', 'author']),
                'messages.post_created'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update post
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title.en' => 'sometimes|string|max:255',
            'title.ar' => 'sometimes|string|max:255',
            'excerpt.en' => 'nullable|string|max:500',
            'excerpt.ar' => 'nullable|string|max:500',
            'content.en' => 'sometimes|string',
            'content.ar' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|string|max:255',
            'status' => 'sometimes|in:pending,published,draft',
            'published_at' => 'nullable|date',
            'expected_time_to_read' => 'nullable|integer|min:1',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        try {
            DB::beginTransaction();

            $post->update($request->only([
                'category_id',
                'image',
                'status',
                'published_at',
                'expected_time_to_read',
            ]));

            foreach (['en', 'ar'] as $locale) {
                $data = [];

                if (isset($validated['title'][$locale])) {
                    $data['title'] = $validated['title'][$locale];
                }

                if (isset($validated['excerpt'][$locale])) {
                    $data['excerpt'] = $validated['excerpt'][$locale];
                }

                if (isset($validated['content'][$locale])) {
                    $data['content'] = $validated['content'][$locale];
                }

                if (!empty($data)) {
                    $post->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $data
                    );
                }
            }

            if (isset($validated['tag_ids'])) {
                $post->tags()->sync($validated['tag_ids']);
            }

            DB::commit();

            return $this->ok(
                $post->load(['translations', 'category.translations', 'tags.translations', 'author']),
                'messages.post_updated'
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete post
     */
    public function destroy(Post $post)
    {
        $deletedId = $post->id;
        $post->delete();

        return $this->deletedResponse(
            null,
            [
                'deleted_id' => $deletedId,
                'deleted_at' => now()->toISOString(),
            ],
            'messages.post_deleted'
        );
    }
}
