<?php
use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // recreate table
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }

    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Alice", "Hello world");
        $this->assertIsInt($id);
        $this->assertEquals(1, $id);
    }
    public function testGetMail() {
    $mail = new Mail($this->pdo);
    $id = $mail->createMail("S1", "B1");

    $row = $mail->getMail($id);
    $this->assertEquals("S1", $row['subject']);
    $this->assertEquals("B1", $row['body']);
}

public function testGetAllMail() {
    $mail = new Mail($this->pdo);
    $mail->createMail("S1", "B1");
    $mail->createMail("S2", "B2");

    $rows = $mail->getAllMail();
    $this->assertCount(2, $rows);
}

public function testUpdateMail() {
    $mail = new Mail($this->pdo);
    $id = $mail->createMail("Old", "OldBody");

    $ok = $mail->updateMail($id, "New", "NewBody");
    $this->assertTrue($ok);

    $row = $mail->getMail($id);
    $this->assertEquals("New", $row['subject']);
    $this->assertEquals("NewBody", $row['body']);
}

public function testDeleteMail() {
    $mail = new Mail($this->pdo);
    $id = $mail->createMail("S1", "B1");

    $ok = $mail->deleteMail($id);
    $this->assertTrue($ok);

    $row = $mail->getMail($id);
    $this->assertFalse($row);
}

}
