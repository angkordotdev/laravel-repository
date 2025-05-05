<?php

namespace Torann\LaravelRepository\Test\Repositories;

use Illuminate\Support\Collection;
use Torann\LaravelRepository\Test\TestCase;

class RepositoryTest extends TestCase
{

    public function testPreventInvalidUserInput()
    {
        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('paginate')->once()
            ->with(15, ['*'], 'page', 0)
            ->andReturn(true);

        // Ensure the package casts the per page limit correctly and returns the default 15
        $this->assertEquals(true, $repo->paginate('select *'));
    }

    public function testShouldGetAll()
    {
        $return = new Collection('foo');

        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('get')->once()
            ->andReturn($return);

        $this->assertSame($return, $repo->all());
    }

    public function testPluck()
    {
        $expected_array = [
            [
                'title' => 'admin',
                'name' => 'Bill',
            ],
            [
                'title' => 'user',
                'name' => 'Kelly',
            ],
        ];

        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('pluck')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->pluck('title', 'name'));
    }

    public function testPaginate()
    {
        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('paginate')->once()
            ->andReturn(true);

        $this->assertEquals(true, $repo->paginate(11));
    }

    public function testSimplePaginate()
    {
        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('simplePaginate')->once()
            ->andReturn(true);

        $this->assertEquals(true, $repo->simplePaginate(11));
    }

    public function testFind()
    {
        $expected_array = [
            'id' => 123,
            'email' => 'admin@mail.com',
            'name' => 'Bill',
        ];

        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('find')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->find($expected_array['id']));
    }

    public function testFindBy()
    {
        $expected_array = [
            'id' => 123,
            'email' => 'admin@mail.com',
            'name' => 'Bill',
        ];

        $repo = $this->makeRepository();
        $query = $this->makeMockQuery();

        $repo->builderMock
            ->shouldReceive('where')->once()
            ->with('id', '=', $expected_array['id'])->once()
            ->andReturn($query);

        $query->shouldReceive('first')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->findBy('id', $expected_array['id']));
    }

    public function testFindAllBy()
    {
        $expected_array = [
            [
                'id' => 123,
                'email' => 'admin@mail.com',
                'name' => 'Bill',
            ],
            [
                'id' => 124,
                'email' => 'admin@mail.com',
                'name' => 'Todd',
            ],
        ];

        $repo = $this->makeRepository();
        $query = $this->makeMockQuery();

        $repo->builderMock
            ->shouldReceive('where')->once()
            ->with('email', '=', 'admin@mail.com')->once()
            ->andReturn($query);

        $query->shouldReceive('get')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->findAllBy('email', 'admin@mail.com'));
    }

    public function testFindAllByArray()
    {
        $ids = [1, 33];

        $expected_array = [
            [
                'id' => 1,
                'email' => 'admin@mail.com',
                'name' => 'Bill',
            ],
            [
                'id' => 33,
                'email' => 'admin@mail.com',
                'name' => 'Todd',
            ],
        ];

        $repo = $this->makeRepository();
        $query = $this->makeMockQuery();

        $repo->builderMock
            ->shouldReceive('whereIn')->once()
            ->with('id', $ids)->once()
            ->andReturn($query);

        $query->shouldReceive('get')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->findAllBy('id', $ids));
    }

    public function testFindWhere()
    {
        $expected_array = [
            [
                'id' => 123,
                'email' => 'admin@mail.com',
                'name' => 'Bill',
            ],
        ];

        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('where')->once()
            ->with('id', '=', 123)->once()
            ->shouldReceive('get')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->findWhere([
            'id' => 123,
        ]));
    }

    public function testFindWhereWithConditions()
    {
        $expected_array = [
            [
                'id' => 123,
                'email' => 'admin@mail.com',
                'name' => 'Bill',
            ],
        ];

        $repo = $this->makeRepository();

        $repo->builderMock
            ->shouldReceive('where')->once()
            ->with('id', '<', 123)->once()
            ->shouldReceive('get')->once()
            ->andReturn($expected_array);

        $this->assertEquals($expected_array, $repo->findWhere([
            ['id', '<', 123],
        ]));
    }

    public function testCacheCallbackWithCache()
    {
        $repo = $this->makeRepository();

        $repo->skipCacheCheck = true;

        $cache = app('cache');

        $cache->shouldReceive('tags')->once()
            ->with(['repositories', 'Torann\\LaravelRepository\\Test\\Stubs\\TestRepository'])->once()
            ->andReturnSelf();

        $cache->shouldReceive('remember')->once()
            ->andReturn('admin@mail.com');

        $repo::setCacheInstance($cache);

        $this->assertEquals('admin@mail.com', $repo->findByEmail('admin@mail.com'));
    }

    public function testFindUsingScope()
    {
        $expected = new Collection([
            [
                'id' => 123,
                'email' => 'admin@mail.com',
                'name' => 'Bill',
                'is_admin' => true,
            ],
            [
                'id' => 33,
                'email' => 'admin@mail.com',
                'name' => 'Todd',
                'is_admin' => true,
            ],
        ]);

        $repo = $this->makeRepository();
        $query = $this->makeMockQuery();

        $repo->builderMock
            ->shouldReceive('where')->once()
            ->with('is_admin', true)->once()
            ->andReturn($query);

        $query->shouldReceive('get')->once()
            ->andReturn($expected);

        $this->assertEquals($expected, $repo->adminOnlyScope()->all());
    }
}
