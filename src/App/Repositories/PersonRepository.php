<?php

namespace App\Repositories;

use App\Entities\Person;
use App\Exceptions\EntityNotFoundException;

class PersonRepository
{
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function find(int $id): Person
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email FROM persons WHERE id = :id'
        );

        if (! $stmt) {
            throw new \RuntimeException(
                "unable to prepare statement"
            );
        }

        if (! $stmt->execute(compact('id'))) {
            throw new \RuntimeException(
                "unable to execute statement: {$stmt->queryString}"
            );
        }

        $person = $stmt->fetchObject(Person::class);

        if (! $person) {
            throw new EntityNotFoundException(
                Person::class,
                compact('id')
            );
        }

        return $person;
    }
}
