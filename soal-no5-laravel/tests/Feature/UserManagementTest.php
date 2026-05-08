<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_ajax_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'secret',
        ]);

        $response = $this->postJson(route('login.attempt'), [
            'email' => 'login@example.com',
            'password' => 'secret',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('redirect', route('users.index'));
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => 'secret',
        ]);

        $response = $this->postJson(route('login.attempt'), [
            'email' => 'login@example.com',
            'password' => 'wrong',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_authenticated_user_can_manage_users_with_ajax_requests(): void
    {
        $admin = User::factory()->create();

        $createResponse = $this->actingAs($admin)->post(route('users.store'), [
            'email' => 'arya@example.com',
            'name' => 'Arya Isnaidi',
            'password' => 'secret',
            'profile_image' => $this->fakePng('profile.png'),
        ], [
            'Accept' => 'application/json',
        ]);

        $createResponse->assertCreated();

        $user = User::query()->where('email', 'arya@example.com')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'email' => 'arya@example.com',
            'name' => 'Arya Isnaidi',
        ]);
        $this->assertFileExists(public_path($user->profile_image));

        $datatableResponse = $this->actingAs($admin)->getJson(route('users.datatable', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'arya'],
            'order' => [
                ['column' => 1, 'dir' => 'asc'],
            ],
        ]));

        $datatableResponse
            ->assertOk()
            ->assertJsonPath('draw', 1)
            ->assertJsonPath('recordsFiltered', 1)
            ->assertJsonPath('data.0.email', 'arya@example.com');

        $oldImage = $user->profile_image;

        $updateResponse = $this->actingAs($admin)->post(route('users.update', $user), [
            '_method' => 'PUT',
            'email' => 'arya@example.com',
            'name' => 'Arya Updated',
            'password' => 'new-secret',
            'profile_image' => $this->fakePng('new-profile.png'),
        ], [
            'Accept' => 'application/json',
        ]);

        $updateResponse->assertOk();

        $user->refresh();

        $this->assertSame('Arya Updated', $user->name);
        $this->assertTrue(Hash::check('new-secret', $user->password));
        $this->assertFileDoesNotExist(public_path($oldImage));
        $this->assertFileExists(public_path($user->profile_image));

        $this->actingAs($admin)
            ->deleteJson(route('users.destroy', $user))
            ->assertOk();

        $this->assertDatabaseMissing('users', [
            'email' => 'arya@example.com',
        ]);
        $this->assertFileDoesNotExist(public_path($user->profile_image));
    }

    private function fakePng(string $name): UploadedFile
    {
        $onePixelPng = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='
        );

        return UploadedFile::fake()->createWithContent($name, $onePixelPng);
    }
}
