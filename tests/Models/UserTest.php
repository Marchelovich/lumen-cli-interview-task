<?php

namespace Tests\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Mockery;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testActivatedScope()
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereNotNull')->with('activated_at')->andReturn($builder);
        $user = new User();
        $result = $user->scopeActivated($builder);
        $this->assertSame($builder, $result);
    }

    public function testBannedScope()
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereNotNull')->with('banned_at')->andReturn($builder);
        $user = new User();
        $result = $user->scopeBanned($builder);
        $this->assertSame($builder, $result);
    }

    public function testNoAdminScope()
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('whereNot')->with('name', Role::ADMIN_ROLE_NAME)->andReturn($builder);
        $builder->shouldReceive('whereDoesntHave')->withSomeOfArgs('roles')->andReturn($builder);
        $user = new User();
        $result = $user->scopeNoAdmin($builder);
        $this->assertSame($builder, $result);
    }

    public function testAdminScope()
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('where')->with('name', Role::ADMIN_ROLE_NAME)->andReturn($builder);
        $builder->shouldReceive('whereHas')->withSomeOfArgs('roles')->andReturn($builder);
        $user = new User();
        $result = $user->scopeAdmin($builder);
        $this->assertSame($builder, $result);
    }
}
