<?php

namespace Tests\Console;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Mockery;
use Tests\TestCase;

class GetBannedUsersTest extends TestCase
{
    public function testWithHeaders(): void
    {
        $userRepository = Mockery::mock(UserRepository::class);
        $user = new User();
        $user->id = 1;
        $user->email = 'user1@example.com';
        $user->banned_at = '2023-01-01 00:00:00';
        $bannedUsers = [
            $user
        ];
        $userRepository
            ->shouldReceive('getBannedUsers')
            ->with(
                false, // $activeUsersOnly
                false, // $withTrashed
                false, // $trashedOnly
                false, // $noAdmin
                false, // $adminOnly
                'email' // $sortBy
            )
            ->andReturn(new LazyCollection($bannedUsers));

        $this->app->instance(UserRepository::class, $userRepository);

        $this->artisan('banned-users:get', ['--with-headers' => true]);
        $output = Artisan::output();
        $this->assertStringContainsString('id', $output);
        $this->assertStringContainsString('email', $output);
        $this->assertStringContainsString('banned_at', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('user1@example.com', $output);
        $this->assertStringContainsString('2023-01-01 00:00:00', $output);
    }

    public function testWithoutHeaders(): void
    {
        $userRepository = Mockery::mock(UserRepository::class);
        $user = new User();
        $user->id = 1;
        $user->email = 'user1@example.com';
        $user->banned_at = '2023-01-01 00:00:00';
        $bannedUsers = [
            $user
        ];
        $userRepository
            ->shouldReceive('getBannedUsers')
            ->with(
                false, // $activeUsersOnly
                false, // $withTrashed
                false, // $trashedOnly
                false, // $noAdmin
                false, // $adminOnly
                'email' // $sortBy
            )
            ->andReturn(new LazyCollection($bannedUsers));

        $this->app->instance(UserRepository::class, $userRepository);

        $this->artisan('banned-users:get');
        $output = Artisan::output();
        $this->assertStringNotContainsString('id', $output);
        $this->assertStringNotContainsString('email', $output);
        $this->assertStringNotContainsString('banned_at', $output);
        $this->assertStringContainsString('1', $output);
        $this->assertStringContainsString('user1@example.com', $output);
        $this->assertStringContainsString('2023-01-01 00:00:00', $output);
    }

    public function testSaveToFile(): void
    {
        $userRepository = Mockery::mock(UserRepository::class);
        $user = new User();
        $user->id = 1;
        $user->email = 'user1@example.com';
        $user->banned_at = '2023-01-01 00:00:00';
        $bannedUsers = [
            $user
        ];
        $userRepository
            ->shouldReceive('getBannedUsers')
            ->with(
                false, // $activeUsersOnly
                false, // $withTrashed
                false, // $trashedOnly
                false, // $noAdmin
                false, // $adminOnly
                'email' // $sortBy
            )
            ->andReturn(new LazyCollection($bannedUsers));

        $this->app->instance(UserRepository::class, $userRepository);
        Storage::fake('local');
        $this->artisan('banned-users:get', ['save-to' =>'test.csv', '--with-headers' => true]);
        Storage::disk('local')->assertExists('test.csv');
        $content = Storage::disk('local')->get('test.csv');
        $this->assertStringContainsString('id', $content);
        $this->assertStringContainsString('email', $content);
        $this->assertStringContainsString('banned_at', $content);
        $this->assertStringContainsString('1', $content);
        $this->assertStringContainsString('user1@example.com', $content);
        $this->assertStringContainsString('2023-01-01 00:00:00', $content);
    }

    public function testFileAlreadyExists(): void
    {
        $userRepository = Mockery::mock(UserRepository::class);
        $user = new User();
        $user->id = 1;
        $user->email = 'user1@example.com';
        $user->banned_at = '2023-01-01 00:00:00';
        $bannedUsers = [
            $user
        ];
        $userRepository
            ->shouldReceive('getBannedUsers')
            ->with(
                false, // $activeUsersOnly
                false, // $withTrashed
                false, // $trashedOnly
                false, // $noAdmin
                false, // $adminOnly
                'email' // $sortBy
            )
            ->andReturn(new LazyCollection($bannedUsers));

        $this->app->instance(UserRepository::class, $userRepository);
        Storage::fake('local');
        Storage::disk('local')->put('test.csv', 'qwe');
        $result = $this->artisan('banned-users:get', ['save-to' =>'test.csv', '--with-headers' => true]);
        Storage::disk('local')->assertExists('test.csv');
        $output = Artisan::output();
        $this->assertStringContainsString('File test.csv already exists', $output);
        $this->assertEquals(0, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
