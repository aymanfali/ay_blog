<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Bookmark::with([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        // Apply filters
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('post_id')) {
            $query->where('post_id', $request->post_id);
        }

        $bookmarks = $query->paginate($request->get('per_page', 15));

        return $this->ok($bookmarks, 'messages.success');
    }

    public function show(Bookmark $bookmark)
    {
        $bookmark->load([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        return $this->ok($bookmark, 'messages.success');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        // Check if bookmark already exists
        $existingBookmark = Bookmark::where('user_id', $validated['user_id'])
            ->where('post_id', $validated['post_id'])
            ->first();

        if ($existingBookmark) {
            return $this->validationErrorResponse([
                'bookmark' => ['This bookmark already exists for this user and post.']
            ]);
        }

        $bookmark = Bookmark::create($validated);

        $bookmark->load([
            'user',
            'post' => function ($q) {
                $q->withTranslation();
            }
        ]);

        return $this->created($bookmark, 'messages.bookmark_created');
    }

    public function destroy(Bookmark $bookmark)
    {
        $deletedId = $bookmark->id;
        $bookmark->delete();

        return $this->deletedResponse(
            null,
            ['deleted_id' => $deletedId, 'deleted_at' => now()->toISOString()],
            'messages.bookmark_deleted'
        );
    }
}

