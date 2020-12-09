<?php

namespace Tests\App\Repositories;

use App\Entities\Person;
use App\Exceptions\EntityNotFoundException;
use App\Repositories\PersonRepository;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Repositories\PersonRepository
 */
class PersonRepositoryTest extends TestCase
{
    protected $pdo;

    public function setUp(): void
    {
        $this->pdo = new \PDO("sqlite::memory:");

        $this->pdo->exec(
            "CREATE TABLE persons (id INT PRIMARY KEY, name STRING, email STRING)"
        );

        $stmt = $this->pdo->prepare(
            "INSERT INTO `persons` VALUES (:id, :name, :email)"
        );

        $stmt->execute([
            'id' => 1,
            'name' => 'Nathalie PORTMAN',
            'email' => 'nathalie.portman@example.com'
        ]);

        $stmt->execute([
            'id' => 2,
            'name' => 'Jack BLACK',
            'email' => 'jack.black@example.com'
        ]);

        $stmt->execute([
            'id' => 3,
            'name' => 'Leonardo DICAPRIO',
            'email' => 'leonardo.dicaprio@oexample.com'
        ]);
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     * @dataProvider findProvider
     */
    public function testFind(int $id, array $data)
    {
        $repository = new PersonRepository($this->pdo);

        $person = $repository->find($id);

        $this->assertInstanceOf(
            Person::class,
            $person,
            "The return of 'App\Repositories\PersonRepository::find' should be an 'App\Entities\Person' instance"
        );

        $this->assertEquals(
            $data['name'],
            $person->name,
            "The name of the person with id '{$id}' should be '{$data['name']}'"
        );

        $this->assertEquals(
            $data['email'],
            $person->email,
            "The email of the person with id '{$id}' should be '{$data['email']}'"
        );
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     */
    public function testFindFailsWhenDatabaseIsNotReady()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("unable to prepare statement");

        // for this test we connect to an empty database
        // so we are sure the query will fail.
        $repository = new PersonRepository(new \PDO("sqlite::memory:"));
        $repository->find(1);
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     */
    public function testFindFailsWhenQueryFails()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("unable to execute statement");

        // let's create a PDO mock that returns statements
        // whose execute method will always return false
        $pdo = new class("sqlite::memory:") extends \PDO {
            public function prepare($statement, $options = null) {
                $stmt = new class {
                    public function execute() {
                        return false;
                    }
                };
                $stmt->queryString = $statement;
                return $stmt;
            }
        };

        $repository = new PersonRepository($pdo);
        $repository->find(1);
    }

    /**
     * @covers \App\Repositories\PersonRepository::find
     */
    public function testFindFailsWhenNoResultFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("unable to find entity");

        $repository = new PersonRepository($this->pdo);
        $repository->find(4);
    }

    public function findProvider(): array
    {
        return [
            "Scenario 1 : user with id '1' is 'Nathalie PORTMAN'" => [
                'id' =>  1,
                'data' => [
                    'name' => "Nathalie PORTMAN",
                    'email' => "nathalie.portman@example.com",
                ],
            ],

            "Scenario 2 : user with id '2' is 'Jack BLACK'" => [
                'id' =>  2,
                'data' => [
                    'name' => "Jack BLACK",
                    'email' => "jack.black@example.com",
                ],
            ],

            "Scenario 3 : user with id '3' is 'Leonardo DICAPRIO'" => [
                'id' =>  3,
                'data' => [
                    'name' => "Leonardo DICAPRIO",
                    'email' => "leonardo.dicaprio@oexample.com",
                ],
            ],
        ];
    }
}
