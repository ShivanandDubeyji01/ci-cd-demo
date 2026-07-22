<?php

namespace Shivananddubeyji01\CiCdDemo;

class Employee
{
    private int $id;
    private string $name;
    private string $department;

    public function __construct(int $id, string $name, string $department)
    {
        $this->id = $id;
        $this->name = $name;
        $this->department = $department;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDepartment(): string
    {
        return $this->department;
    }
}