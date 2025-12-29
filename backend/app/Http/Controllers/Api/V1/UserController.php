<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            // Required
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

            'status' => ['sometimes', Rule::in(['active', 'inactive'])],

            // Nullable fillables
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|string',
            'banner' => 'nullable|string',
            'oauth_provider' => 'nullable|string',
            'oauth_id' => 'nullable|string',
        ]);

        $user = User::create([
            ...collect($validated)->except('password')->toArray(),
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'] ?? 'active',
        ]);

        return $this->created($user, 'messages.user_created');
    }


    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();

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
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'bio' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|string',
            'banner' => 'nullable|string',
            'oauth_provider' => 'nullable|string',
            'oauth_id' => 'nullable|string',
        ]);

        if (isset($validated['status'])) {
            // Prevent admin from inactivating themselves
            if ($authUser->id === $user->id && $validated['status'] === 'inactive') {
                return $this->forbidden(__('messages.cannot_inactivate_self'));
            }
            $updateData['status'] = $validated['status'];
        }

        $updateData = collect($validated)->except('password')->toArray();

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return $this->ok($user, 'messages.user_updated');
    }

    public function destroy(User $user)
    {
        $authUser = Auth::user();

        if (! $authUser) {
            return $this->unauthorized(__('messages.invalid_credentials'));
        }

        // Prevent admin from deleting their own account
        if ($authUser->id === $user->id) {
            return $this->forbidden(__('messages.cannot_delete_self'));
        }

        $deletedId = $user->id;
        $user->delete();

        return $this->deletedResponse(
            null,
            ['deleted_id' => $deletedId, 'deleted_at' => now()->toISOString()],
            'messages.user_deleted'
        );
    }
}
