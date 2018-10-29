<?php


namespace src\Integration;


abstract class AbstractDataProvider
{
    private $dsn;
    private $host;
    private $user;
    private $password;

    /**
     * AbstractDataProvider constructor.
     * @param string $dsn Параметры подключения
     */
    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;

        $this->host = $this->makeFromDsn('host');
        $this->user = $this->makeFromDsn('user');
        $this->password = $this->makeFromDsn('password');
    }

    /**
     * @param string $param
     */
    private function makeFromDsn(string $param) {
        // определяем из строки соединения нужный параметр
    }

    /**
     * @param array $request
     * @return array
     * @throws DataProviderException
     */
    abstract public function get(array $request):array ;
}