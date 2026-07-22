<?php

namespace Shivananddubeyji01\CiCdDemo;

class EmployeeController
{
    public function createEmployee(int $id, string $name, string $department): Employee
    {
        return new Employee($id, $name, $department);
    }
}