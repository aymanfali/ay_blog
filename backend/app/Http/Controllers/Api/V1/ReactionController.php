<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReactionController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Reaction::with([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        // Apply filters
        if ($request->has('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $reactions = $query->paginate($request->get('per_page', 15));

        return $this->ok($reactions, 'messages.success');
    }

    public function show(Reaction $reaction)
    {
        $reaction->load([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        return $this->ok($reaction, 'messages.success');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'type' => 'required|in:like,dislike,funny,helpful',
        ]);

        // Check if reaction already exists
        $existingReaction = Reaction::where('user_id', $validated['user_id'])
            ->where('post_id', $validated['post_id'])
            ->where('type', $validated['type'])
            ->first();

        if ($existingReaction) {
            return $this->validationErrorResponse([
                'reaction' => ['This reaction already exists for this user and post.']
            ]);
        }

        $reaction = Reaction::create($validated);

        // Update post reaction counts
        $post = $reaction->post;
        if ($validated['type'] === 'like') {
            $post->increment('likes_count');
        } elseif ($validated['type'] === 'dislike') {
            $post->increment('dislikes_count');
        }

        $reaction->load([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        return $this->created($reaction, 'messages.reaction_created');
    }

    public function update(Request $request, Reaction $reaction)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:like,dislike,funny,helpful',
        ]);

        // Update post reaction counts if type changes
        $oldType = $reaction->type;
        $newType = $validated['type'] ?? $oldType;

        if ($oldType !== $newType) {
            $post = $reaction->post;

            // Decrement old counts
            if ($oldType === 'like') {
                $post->decrement('likes_count');
            } elseif ($oldType === 'dislike') {
                $post->decrement('dislikes_count');
            }

            $reaction->update($validated);

            // Increment new counts
            if ($newType === 'like') {
                $post->increment('likes_count');
            } elseif ($newType === 'dislike') {
                $post->increment('dislikes_count');
            }
        } else {
            $reaction->update($validated);
        }

        $reaction->load([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        return $this->ok($reaction, 'messages.reaction_updated');
    }

    public function destroy(Reaction $reaction)
    {
        $deletedId = $reaction->id;
        $post = $reaction->post;

        // Update post reaction counts
        if ($reaction->type === 'like') {
            $post->decrement('likes_count');
        } elseif ($reaction->type === 'dislike') {
            $post->decrement('dislikes_count');
        }

        $reaction->delete();

        return $this->deletedResponse(
            null,
            ['deleted_id' => $deletedId, 'deleted_at' => now()->toISOString()],
            'messages.reaction_deleted'
        );
    }
}

