<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DestroyManyUserRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\EditorUserResource;
use App\Http\Resources\User\UserResource;
use App\Models\Address;
use App\Models\BankConnection;
use App\Models\Date;
use App\Models\Email;
use App\Models\Link;
use App\Models\Phonenumber;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }



    public function index(Request $request)
    {
        // Base query
        $query = User::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('username', $request->filter_search)
                    ->orWhereFuzzy('email', $request->filter_search);
            });
        }

        // Filter
        if ($request->roles)
        {
            $query->whereHas('roles', function ($query) use ($request) {
                $query->whereIn('name', $request->roles);
            });
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return UserResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(User $user)
    {
        return response()->json(EditorUserResource::make($user));
    }

    
    
    public function store(CreateUserRequest $request)
    {
        // Update user model
        $user = User::create($request->model);

        // Update password if set
        if ($request->password) $user->updatePassword($request->password);

        // Update extended user information
        $user->user_name()->updateOrCreate([], $request->user_name);
        $user->user_company()->updateOrCreate([], $request->user_company);
        $user->syncMany(Address::class, $request->addresses);
        $user->syncMany(BankConnection::class, $request->bank_connections, 'bank_connections');
        $user->syncMany(Email::class, $request->emails);
        $user->syncMany(Phonenumber::class, $request->phonenumbers);
        $user->syncMany(Date::class, $request->dates);
        $user->syncMany(Link::class, $request->links);

        // Update roles
        // $user->syncRoles($request->roles);

        // Return updated user
        return response()->json(EditorUserResource::make($user));
    }

    
    
    public function update(UpdateUserRequest $request, User $user)
    {
        // Update user model
        $user->update($request->model);

        // Update password if set
        if ($request->password) $user->updatePassword($request->password);

        // Update extended user information
        $user->user_name()->updateOrCreate([], $request->user_name);
        $user->user_company()->updateOrCreate([], $request->user_company);
        $user->syncMany(Address::class, $request->addresses);
        $user->syncMany(BankConnection::class, $request->bank_connections, 'bank_connections');
        $user->syncMany(Email::class, $request->emails);
        $user->syncMany(Phonenumber::class, $request->phonenumbers);
        $user->syncMany(Date::class, $request->dates);
        $user->syncMany(Link::class, $request->links);

        // Update roles
        // $user->syncRoles($request->roles);

        // Return updated user
        return response()->json(EditorUserResource::make($user));
    }

    
    
    public function destroy(User $user)
    {
        // Delete resource
        $user->delete();
    }

    
    
    public function destroyMany(DestroyManyUserRequest $request)
    {
        // Authorize action
        $this->authorize('deleteMany', [User::class, $request->ids]);

        // Delete resources
        User::destroy($request->ids);
    }
}
