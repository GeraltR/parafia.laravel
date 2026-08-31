<?php

namespace App\Http\Controllers;

use App\Enums\PermissionLevel;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()
            ->orderByDesc('permission_level')
            ->orderBy('name')
            ->get();

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'permission_level' => $data['permissionLevel'],
        ]);

        return UserResource::make($user)->response()->setStatusCode(201);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();

        abort_if(
            $request->user()->id === $user->id && $data['permissionLevel'] !== PermissionLevel::Supervisor->value,
            422,
            'Nie możesz odebrać sobie uprawnień Supervisora.'
        );

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'permission_level' => $data['permissionLevel'],
        ]);

        return UserResource::make($user);
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user): Response
    {
        $actingUser = $request->user();

        abort_unless(
            $actingUser->id === $user->id || $actingUser->permission_level->canWriteManagement(),
            403,
            'Brak uprawnień do zmiany hasła tego użytkownika.'
        );

        $user->update(['password' => $request->validated('newPassword')]);

        return response()->noContent();
    }

    public function destroy(Request $request, User $user): Response
    {
        abort_if($request->user()->id === $user->id, 422, 'Nie możesz usunąć własnego konta.');

        $user->delete();

        return response()->noContent();
    }
}
