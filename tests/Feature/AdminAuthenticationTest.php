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
        $this->get('/admin/submissions')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_log_in_and_log_out(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => 'MatKhauAnToan123!',
        ]);

        $this->post('/admin/login', [
            'email' => $admin->email,
            'password' => 'MatKhauAnToan123!',
        ])->assertRedirect('/admin/submissions');

        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_non_admin_account_cannot_log_in_to_admin_area(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'MatKhauAnToan123!',
        ]);

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'MatKhauAnToan123!',
        ])->assertRedirect('/admin/login')
            ->assertInvalid([
                'email' => 'Thông tin đăng nhập quản trị không chính xác.',
            ]);

        $this->assertGuest();
    }

    public function test_authenticated_non_admin_receives_403_for_admin_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/submissions')
            ->assertForbidden();
    }

    public function test_admin_login_is_locked_after_repeated_failures(): void
    {
        User::factory()->admin()->create([
            'email' => 'locked-admin@example.com',
            'password' => 'MatKhauAnToan123!',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', [
                'email' => 'locked-admin@example.com',
                'password' => 'sai-mat-khau',
            ])->assertInvalid(['email']);
        }

        $this->post('/admin/login', [
            'email' => 'locked-admin@example.com',
            'password' => 'MatKhauAnToan123!',
        ])->assertInvalid([
            'email' => 'Bạn đã đăng nhập sai quá nhiều lần.',
        ]);

        $this->assertGuest();
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
