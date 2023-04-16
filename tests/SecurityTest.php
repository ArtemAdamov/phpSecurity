<?php

use PHPUnit\Framework\TestCase;

require_once 'Security.php';

class SecurityTest extends TestCase
{
    private $dbConnection;

    protected function setUp(): void
    {
        $this->dbConnection = $this->getMockBuilder(mysqli::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->security = new Security();
    }

    public function testSqlInjectionProtection()
    {
        $unsafeSearch = "'; DROP TABLE users; --";
        $safeSearch = $this->security->preventSqlInjection($unsafeSearch, $this->dbConnection);
        $sql = $this->security->imitateSqlInjection($safeSearch);
        $this->assertStringNotContainsString("DROP TABLE", $sql, "SQL Injection protection failed");
    }

    public function testXssProtection()
    {
        $unsafeComment = "<script>alert('XSS');</script>";
        $safeComment = $this->security->preventXssAttack($unsafeComment);
        $output = $this->security->imitateXssAttack($safeComment);
        $this->assertStringNotContainsString("<script>", $output, "XSS protection failed");
    }

    public function testCsrfProtection()
    {
        $token1 = $this->security->preventCsrfAttack();
        $token2 = $this->security->preventCsrfAttack();
        $this->assertNotEquals($token1, $token2, "CSRF tokens are not unique");
    }
    public function testSsiProtection()
    {
        $input = "file.txt";
        $outputWithSsiAtack = $this->security->imitateSsiAttack($input);
        $safeInput = $this->security->preventSsiAttack($outputWithSsiAtack);
        $this->assertStringNotContainsString("<!--#include", $safeInput, "SSI protection failed");
    }
}
