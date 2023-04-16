<?php

class Security
{
    public function imitateSsiAttack($input) {
        return "<!--#include virtual=\"{$input}\" -->";
    }

    public function preventSsiAttack($input) {
        return str_replace(["<!--", "-->"], ["<!&ndash;", "&ndash;>"], $input);
    }
    public function imitateSqlInjection($search) {
        return "SELECT * FROM users WHERE name LIKE '%$search%'";
    }

    public function preventSqlInjection($search, $conn) {
        return $conn->real_escape_string($search);
    }

    public function imitateXssAttack($comment) {
        return "<div class='comment'>{$comment}</div>";
    }

    public function preventXssAttack($comment) {
        return htmlspecialchars($comment);
    }

    public function imitateCsrfAttack($token1, $token2) {
        return $token1 === $token2;
    }

    public function preventCsrfAttack() {
        return bin2hex(random_bytes(32));
    }
}