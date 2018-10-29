<?php

namespace src\Decorator;

use DateTime;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;
use src\Integration\DataProviderException;

/**
 * Класс для получения данных от провайдеров
 *
 */
class DecoratorManager
{
    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var AbstractDataProvider
     */
    protected $dataProvider;

    /**
     * @param string $dsn
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(string $dsn, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $this->dataProvider = new DataProvider($dsn); // @todo В дальнейшем можно вынести определение провайдера из dsn
        $this->cache = $cache;
        $this->logger = $logger;
    }


    /**
     * Получаем ответ от сервиса
     * @param array $input
     * @return array
     */
    public function getResponse(array $input): array
    {
        try {
            $cacheItem = $this->cache->getItem( $this->getCacheKey($input) );

            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->dataProvider->get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day') // @todo Вынести в настройки приложения
                );

            return $result;
        } catch (DataProviderException $e) {
            $this->logger->warning($e->getMessage());
        } finally { // @todo Возможно все же в этом месте нужно падать, а не просто залогировать ошибку?
            $this->logger->warning('Undefined Error');
        }

        return [];
    }

    /**
     * Получаем ключ для запроса
     * @param array $input
     * @return string
     */
    protected function getCacheKey(array $input): string
    {
        // для выбора оптимального ключа нам нужно больше знаний о системе:
        // - возможно, нужно добавить имя провайдера?
        // - возможно, генерацию ключа через json_encode можно заменить на обычный md5?
    }
}