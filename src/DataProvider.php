<?php

namespace src\Integration;

class DataProvider extends AbstractDataProvider
{

    /**
     * @param array $request
     * @throws DataProviderException
     * @return array
     */
    public function get(array $request): array
    {
        // returns a response from external service
    }
}