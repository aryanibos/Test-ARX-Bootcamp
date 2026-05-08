<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $draw = (int) $request->input('draw', 1);
        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 10) : 10;
        $search = trim((string) $request->input('search.value', ''));

        $orderableColumns = [
            1 => 'email',
            2 => 'name',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 1);
        $orderColumn = $orderableColumns[$orderColumnIndex] ?? 'email';
        $orderDirection = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';

        $baseQuery = User::query();
        $recordsTotal = (clone $baseQuery)->count();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search): void {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $users = $baseQuery
            ->orderBy($orderColumn, $orderDirection)
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users->map(fn (User $user): array => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'profile_image_url' => $this->profileImageUrl($user),
            ]),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'profile_image_url' => $this->profileImageUrl($user),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $user = User::query()->create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'password' => $validated['password'],
            'profile_image' => $this->storeProfileImage($request),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'user' => $user,
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate($this->rules($user), $this->messages());

        $data = [
            'name' => $validated['name'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        if ($request->hasFile('profile_image')) {
            $this->deleteProfileImage($user);
            $data['profile_image'] = $this->storeProfileImage($request);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User berhasil diubah.',
            'user' => $user->fresh(),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $isDeletingCurrentUser = Auth::id() === $user->id;

        $this->deleteProfileImage($user);
        $user->delete();

        if ($isDeletingCurrentUser) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'User berhasil dihapus.',
                'redirect' => route('login'),
            ]);
        }

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(?User $user = null): array
    {
        $passwordRules = $user ? ['nullable', 'string'] : ['required', 'string'];

        return [
            'email' => [
                $user ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'name' => ['required', 'string', 'max:255'],
            'password' => $passwordRules,
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan user lain.',
            'name.required' => 'Nama wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'profile_image.image' => 'File profil harus berupa gambar.',
            'profile_image.mimes' => 'Gambar profil harus JPG, JPEG, PNG, GIF, atau WEBP.',
            'profile_image.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }

    private function storeProfileImage(Request $request): ?string
    {
        if (! $request->hasFile('profile_image')) {
            return null;
        }

        $file = $request->file('profile_image');
        $filename = uniqid('profile_', true).'.'.$file->getClientOriginalExtension();

        $file->move(public_path('profile-images'), $filename);

        return 'profile-images/'.$filename;
    }

    private function deleteProfileImage(User $user): void
    {
        if ($user->profile_image) {
            $path = public_path($user->profile_image);

            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    private function profileImageUrl(User $user): ?string
    {
        if (! $user->profile_image) {
            return null;
        }

        return asset($user->profile_image);
    }
}
