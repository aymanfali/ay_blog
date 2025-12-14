<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = User::with(['posts', 'comments', 'reactions', 'bookmarks']);

        // Apply filters
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return $this->ok($users, 'messages.success');
    }

    public function show(User $user)
    {
        $user->load([
            'posts' => function ($q) {
                $q->withTranslation();
            },
            'comments.post',
            'reactions.post',
            'bookmarks' => function ($q) {
                $q->with(['post' => function ($pq) {
                    $pq->withTranslation();
                }]);
            }
        ]);

        return $this->ok($user, 'messages.success');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return $this->created($user, 'messages.user_created');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [];

        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }

        if (isset($validated['email'])) {
            $updateData['email'] = $validated['email'];
        }

        if (isset($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return $this->ok($user, 'messages.user_updated');
    }

    public function destroy(User $user)
    {
        $deletedId = $user->id;
        $user->delete();

        return $this->deletedResponse(
            null,
            ['deleted_id' => $deletedId, 'deleted_at' => now()->toISOString()],
            'messages.user_deleted'
        );
    }
}

