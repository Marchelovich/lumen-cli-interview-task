<?php

namespace Tests\Repositories;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\LazyCollection;
use Mockery;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    public function testGetBannedUsersWithTrashed()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('withTrashed', 'banned');
        $user->shouldNotReceive('onlyTrashed', 'withoutTrashed');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('email');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers(
            false,
            true,
        );

        $this->assertSame($collection, $result);
    }

    public function testGetBannedUsersWithoutTrashed()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('withoutTrashed', 'banned');
        $user->shouldNotReceive('onlyTrashed', 'withTrashed');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('email');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers();

        $this->assertSame($collection, $result);
    }

    public function testGetBannedUsesTrashedOnly()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('onlyTrashed', 'banned');
        $user->shouldNotReceive('withoutTrashed', 'withTrashed');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('email');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers(false, false, true);

        $this->assertSame($collection, $result);
    }

    public function testGetBannedUsersActivated()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('withoutTrashed', 'banned', 'activated');
        $user->shouldNotReceive('onlyTrashed', 'withTrashed');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('email');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers(true);

        $this->assertSame($collection, $result);
    }

    public function testGetBannedUsersNoAdmin()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('withoutTrashed', 'banned', 'noAdmin');
        $user->shouldNotReceive('onlyTrashed', 'withTrashed', 'admin');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('email');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers(false, false, false, true);

        $this->assertSame($collection, $result);
    }

    public function testGetBannedUsersAdmin()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('withoutTrashed', 'banned', 'admin');
        $user->shouldNotReceive('onlyTrashed', 'withTrashed', 'noAdmin');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('email');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers(false, false, false, false, true);

        $this->assertSame($collection, $result);
    }

    public function testGetBannedUsersSortedBy()
    {
        $collection = new LazyCollection();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('query')->andReturn($user);
        $user->shouldReceive('withoutTrashed', 'banned');
        $user->shouldNotReceive('onlyTrashed', 'withTrashed');
        $user->shouldReceive('lazy')->andReturn($collection);
        $user->shouldReceive('orderBy')->with('id');


        $repository = new UserRepository($user);

        $result = $repository->getBannedUsers(false, false, false, false, false, 'id');

        $this->assertSame($collection, $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }
}
