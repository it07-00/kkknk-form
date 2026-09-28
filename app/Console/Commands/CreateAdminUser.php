<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

#[Signature('admin:create
            {email : Địa chỉ email đăng nhập}
            {--name=Quản trị viên : Tên người quản trị}')]
#[Description('Tạo mới hoặc cấp quyền quản trị cho một tài khoản')]
class CreateAdminUser extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $name = (string) $this->option('name');
        $password = (string) $this->secret('Mật khẩu (ít nhất 12 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt)');
        $confirmation = (string) $this->secret('Nhập lại mật khẩu');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'email' => ['required', 'email:rfc', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password]
        );
        $user->forceFill(['is_admin' => true])->save();

        $this->info("Đã tạo tài khoản quản trị: {$user->email}");

        return self::SUCCESS;
    }
}
