<?php

namespace tests\unit\models;

use common\models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    // --- password helpers ---

    public function testSetPasswordAndValidatePassword(): void
    {
        $user = new User();
        $user->setPassword('secret123');
        $this->assertTrue($user->validatePassword('secret123'));
        $this->assertFalse($user->validatePassword('wrongpassword'));
    }

    // --- auth key ---

    public function testGenerateAuthKeyProducesNonEmptyString(): void
    {
        $user = new User();
        $user->generateAuthKey();
        $this->assertNotEmpty($user->auth_key);
        $this->assertIsString($user->auth_key);
    }

    public function testValidateAuthKeyMatchesGeneratedKey(): void
    {
        $user = new User();
        $user->generateAuthKey();
        $this->assertTrue($user->validateAuthKey($user->auth_key));
        $this->assertFalse($user->validateAuthKey('wrong-key'));
    }

    // --- status label ---

    public function testGetStatusLabelReturnsCorrectLabels(): void
    {
        $cases = [
            User::STATUS_ACTIVE   => 'Active',
            User::STATUS_INACTIVE => 'Inactive',
            User::STATUS_DELETED  => 'Deleted',
        ];

        foreach ($cases as $status => $expected) {
            $user = new User();
            $user->status = $status;
            $this->assertSame($expected, $user->getStatusLabel(), "Label mismatch for status: $status");
        }
    }

    public function testGetStatusLabelReturnsUnknownForUnrecognizedStatus(): void
    {
        $user = new User();
        $user->status = 99;
        $this->assertSame('Unknown', $user->getStatusLabel());
    }

    // --- password reset token ---

    public function testExpiredPasswordResetTokenIsInvalid(): void
    {
        $expiredToken = 'abc_' . (time() - User::PASSWORD_RESET_TOKEN_EXPIRE - 1);
        $this->assertFalse(User::isPasswordResetTokenValid($expiredToken));
    }

    public function testFreshPasswordResetTokenIsValid(): void
    {
        $freshToken = 'abc_' . time();
        $this->assertTrue(User::isPasswordResetTokenValid($freshToken));
    }

    public function testEmptyPasswordResetTokenIsInvalid(): void
    {
        $this->assertFalse(User::isPasswordResetTokenValid(''));
    }
}
