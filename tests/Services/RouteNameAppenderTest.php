<?php

namespace App\Tests\Services;

use App\Entity\User;
use App\Services\RouteNameAppender;
use PHPUnit\Framework\TestCase;

class RouteNameAppenderTest extends TestCase
{
    /**
     * @dataProvider getData
     */
    public function testItGeneratesCorrectRoutes($data)
    {
        $user = new User();
        $user->setRole($data['role']);
        $nameAppender = new RouteNameAppender();
        $route = $nameAppender->appendRoleToBeginning($user, 'test_route');
        $this->assertEquals(0, strcmp($route, $data['expected']));
    }

    public static function getData()
    {
        yield [['role' => 'ROLE_USER', 'expected' => 'user_test_route']];
        yield [['role' => 'ROLE_EMPLOYEE', 'expected' => 'employee_test_route']];
        yield [['role' => 'ROLE_ADMIN', 'expected' => 'admin_test_route']];
    }
}