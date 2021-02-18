<?php

namespace Sendios\Resources;

final class Goal extends Resource
{
    private const CREATE_GOAL_RESOURCE = 'goals/create';

    private $validGoals = [];
    private $invalidGoals = [];

    /**
     * @param array $data
     * @return array|int[]|string[]
     */
    public function createGoal(array $data): array
    {
        $resultArray = ['goals_added' => 0];

        foreach ($data as $dataItem) {
            $validData = $this->validateData($dataItem);
            if ($validData === false) {
                continue;
            }
            $this->validGoals[] = $validData;
        }

        if (count($this->validGoals) === 0) {
            return array_merge($resultArray, $this->invalidGoals);
        }

        $sendStatus = $this->request->sendToApi3(self::CREATE_GOAL_RESOURCE, "POST", $data);

        if ($sendStatus) {
            $resultArray['goals_added'] = count($this->validGoals);

            return array_merge($resultArray, $this->invalidGoals);
        }

        return array_merge($resultArray, ['errors' => 'Sending data error!']);
    }

    /**
     * @param array $data
     * @return array|false
     */
    private function validateData(array $data)
    {
        $invalidItem = ['error_messages' => []];

        $filterOptions = [
            'options' => [
                'default' => false,
                'min_range' => 1
            ]];

        if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $invalidItem['error_messages'][] = "Parameter email is invalid";
        }

        if (!isset($data['type']) || !filter_var($data['type'], FILTER_SANITIZE_STRING)) {
            $invalidItem['error_messages'][] = "Parameter type is invalid";
        }

        if (!isset($data['project_id']) || !filter_var($data['project_id'], FILTER_VALIDATE_INT, $filterOptions)) {
            $invalidItem['error_messages'][] = "Parameter project_id is invalid";
        }

        if (isset($data['mail_id']) && !filter_var($data['mail_id'], FILTER_VALIDATE_INT, $filterOptions)) {
            $invalidItem['error_messages'][] = "Parameter mail_id is invalid";
        }

        if (count($invalidItem['error_messages']) > 0) {
            $invalidItem['errorCode'] = 409;
            $invalidItem['message'] = "Validation error";
            $invalidItem['goal_data'] = implode(';', $data);
            $this->invalidGoals[] = $invalidItem;

            return false;
        }

        return $data;
    }
}
