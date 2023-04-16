<?php
require_once 'Security.php';

$security = new Security();
function saveComment($nickname, $email, $comment, $security)
{
    $commentData = [
        'nickname' => $security->preventSsiAttack($nickname),
        'email' => $security->preventSsiAttack($email),
        'comment' => $security->preventSsiAttack($comment)
    ];

    $file = fopen("comments.txt", "a");
    fwrite($file, json_encode($commentData) . "\n");
    fclose($file);
}

function displayComments($security)
{
    $file = fopen("comments.txt", "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $commentData = json_decode($line, true);
            echo "<div class='comment'>";
            echo "<h3>" . $commentData['nickname'] . "</h3>";
            echo "<p>" . $commentData['comment'] . "</p>";
            echo "</div>";
            echo "<br>\n";
        }
        fclose($file);
    } else {
        echo "Error: Unable to open comments file.";
    }
}

// Save comments
saveComment("User1", "user1@example.com", "This is a normal comment.", $security);
saveComment("User2", "user2@example.com", "This is another normal comment.", $security);
saveComment("Attacker", "attacker@example.com", "<!--#include virtual=\"comments.txt\" -->", $security);

// If the web server supports SSI, disabling security will insert comment.txt to HTML display
displayComments($security);