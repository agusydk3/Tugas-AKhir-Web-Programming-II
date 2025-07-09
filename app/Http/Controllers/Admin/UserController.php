<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,reseller,customer',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:admin,reseller,customer',
        ]);

        // Only update password if it's provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);

            $user->password = Hash::make($request->password);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Update user balance.
     */
    public function updateBalance(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'operation' => 'required|in:add,subtract,set',
        ]);
        
        $amount = $validated['amount'];
        $operation = $validated['operation'];
        
        // Process balance based on operation
        switch ($operation) {
            case 'add':
                $user->balance += $amount;
                $message = "Added Rp " . number_format($amount, 0, ',', '.') . " to user's balance";
                break;
            case 'subtract':
                // Check if user has enough balance
                if ($user->balance < $amount) {
                    return redirect()->route('admin.users.index')
                        ->with('error', 'User does not have enough balance.');
                }
                $user->balance -= $amount;
                $message = "Subtracted Rp " . number_format($amount, 0, ',', '.') . " from user's balance";
                break;
            case 'set':
                $user->balance = $amount;
                $message = "Set user's balance to Rp " . number_format($amount, 0, ',', '.');
                break;
        }
        
        $user->save();
        
        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }
}
