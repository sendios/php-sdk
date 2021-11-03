<?php

namespace Sendios\Resources;

final class Event extends BaseResource
{
    private const CREATE_EVENT_RESOURCE = 'product-event/create';

    /**
     * @param array $data
     * @return bool|mixed
     * @throws \Exception
     */
    public function send(array $data = [])
    {
        // simple validation
        if (!is_array($data[0])) {
            return false;
        }
        if (empty($data[0]['project_id']) || empty($data[0]['event_id']) || empty($data[0]['receiver_id'])) {
            return false;
        }

        return $this->request->create(self::CREATE_EVENT_RESOURCE, $data);
    }
}
