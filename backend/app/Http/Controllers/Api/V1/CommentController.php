<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Comment::with([
            'post' => function ($q) {
                $q->withTranslation();
            },
            'user',
            'parent',
            'replies.user'
        ]);

        // Apply filters
        if ($request->has('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->has('is_approved')) {
            $query->where('is_approved', $request->boolean('is_approved'));
        }

        // Only root comments (no parent) by default
        if ($request->has('root_only') && $request->boolean('root_only')) {
            $query->whereNull('parent_id');
        }

        $comments = $query->paginate($request->get('per_page', 15));

        return $this->ok($comments, 'messages.success');
    }

    public function show(Comment $comment)
    {
        $comment->load([
            'post' => function ($q) {
                $q->withTranslation();
            },
            'user',
            'parent',
            'replies.user'
        ]);

        return $this->ok($comment, 'messages.success');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string|max:5000',
            'is_approved' => 'nullable|boolean',
        ]);

        $comment = Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $validated['user_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'is_approved' => $validated['is_approved'] ?? false,
        ]);

        $comment->load(['post' => function ($q) {
            $q->withTranslation();
        }, 'user', 'parent']);

        return $this->created($comment, 'messages.comment_created');
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'sometimes|string|max:5000',
            'is_approved' => 'nullable|boolean',
            'post_id' => 'sometimes|exists:posts,id',
        ]);

        $comment->update([
            'content' => $validated['content'] ?? $comment->content,
            'is_approved' => $validated['is_approved'] ?? $comment->is_approved,
            'post_id' => $validated['post_id'] ?? $comment->post_id,
        ]);

        $comment->load(['post' => function ($q) {
            $q->withTranslation();
        }, 'user', 'parent', 'replies.user']);

        return $this->ok($comment, 'messages.comment_updated');
    }

    public function destroy(Comment $comment)
    {
        $deletedId = $comment->id;
        $comment->delete();

        return $this->deletedResponse(
            null,
            ['deleted_id' => $deletedId, 'deleted_at' => now()->toISOString()],
            'messages.comment_deleted'
        );
    }
}

