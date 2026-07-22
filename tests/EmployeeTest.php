<?php

use PHPUnit\Framework\TestCase;
use Shivananddubeyji01\CiCdDemo\EmployeeController;

require_once __DIR__ . '/../vendor/autoload.php';

class EmployeeTest extends TestCase
{
    public function testCreateEmployee()
    {
        $controller = new EmployeeController();

        $employee = $controller->createEmployee(
            101,
            "Shivanand",
            "DevOps"
        );

        $this->assertEquals(101, $employee->getId());
        $this->assertEquals("Shivanand", $employee->getName());
        $this->assertEquals("DevOps", $employee->getDepartment());
    }
}