<?php

require_once(__DIR__ . '/../functions.php');

class FunctionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Check if PDO_SQLITE is available.
     */
    public function testConnectDbSqlite()
    {
        define("DSN", "sqlite::memory:");
        $this->assertNotNull(connectDb());
    }

    /**
     * Check if PDO_MYSQL is available.
     */
    public function testConnectDbMysql()
    {
        define("DSN", "mysql:host=192.0.2.1;dbname=testConnectDbMysql");
        $this->assertNotNull(connectDb());
    }

    /**
     * Check if unsafe characters are sanitized
     */
    public function testEscape()
    {
        $this->assertNotEquals("\"", escape("'"));
        $this->assertNotEquals("'", escape("\""));
        $this->assertNotEquals("<", escape("<"));
        $this->assertNotEquals(">", escape(">"));
    }

    /**
     * Check if token is set
     */
    public function testSetToken()
    {
        setToken();

        $this->assertNotNull($_SESSION['token']);

        unset($_SESSION['token']);
    }

    /**
     * Check if token is treated correctly : empty session
     *
     * @expectedException Exception
     */
    public function testCheckTokenEmpty()
    {
        checkToken();
    }

    /**
     * Check if token is treated correctly : wrong token
     *
     * @expectedException Exception
     */
    public function testCheckTokenWrong()
    {
        $_SESSION['token'] = "hoge";
        $_POST['token'] = "huga";

        checkToken();

        unset($_SESSION['token']);
        unset($_POST['token']);
    }

    /**
     * Check if token is treated correctly
     */
    public function testCheckToken()
    {
        setToken();
        $_POST['token'] = $_SESSION['token'];

        checkToken();

        unset($_SESSION['token']);
        unset($_POST['token']);
    }
}
