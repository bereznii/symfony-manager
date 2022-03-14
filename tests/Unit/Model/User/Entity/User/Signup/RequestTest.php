<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\Signup;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(
            $id = Id::next(),
            $date = new \DateTimeImmutable(),
            $email = new Email('test@app.test'),
            $password = 'hash',
            $token = 'token'
        );

        $this->assertTrue($user->isWait());
        $this->assertFalse($user->isActive());

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($date, $user->getCreatedAt());
        $this->assertEquals($password, $user->getPasswordHash());
        $this->assertEquals($token, $user->getConfirmToken());
    }
}