<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('filament.admin.resources.ghg-submissions.index'))
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_filament_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.ghg-submissions.index'))
            ->assertOk();
    }

    public function test_authenticated_non_admin_receives_403_for_filament_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('filament.admin.resources.ghg-submissions.index'))
            ->assertForbidden();
    }

    public function test_admin_create_command_creates_an_admin_with_a_hashed_password(): void
    {
        $this->artisan('admin:create', [
            'email' => 'new-admin@example.com',
            '--name' => 'Quản trị mới',
        ])->expectsQuestion(
            'Mật khẩu (ít nhất 12 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt)',
            'MatKhauAnToan123!'
        )->expectsQuestion(
            'Nhập lại mật khẩu',
            'MatKhauAnToan123!'
        )->expectsOutput('Đã tạo tài khoản quản trị: new-admin@example.com')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'new-admin@example.com')->sole();

        $this->assertTrue($admin->isAdmin());
        $this->assertNotSame('MatKhauAnToan123!', $admin->password);
    }
}
