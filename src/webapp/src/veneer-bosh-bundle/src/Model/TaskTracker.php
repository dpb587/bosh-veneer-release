<?php

namespace Veneer\BoshBundle\Model;

class TaskTracker
{
    protected $state = [];
    protected $errors = [];

    public function __construct(array $events = [])
    {
        foreach ($events as $event) {
            $this->append($event);
        }
    }

    public function append(array $event)
    {
        if (isset($event['error'])) {
            $this->errors[] = $event;

            return;
        }

        if (!isset($this->state[$event['stage']])) {
            $this->state[$event['stage']] = [
                'id' => 'stage-'.substr(md5($event['stage']), 0, 12),
                'name' => $event['stage'],
                'time_begin' => $event['time'],
                'time_end' => null,
                'state' => $event['state'],
                'tasks_ended' => 0,
                'tasks_failed' => 0,
                'tasks' => [],
            ];
        }

        foreach ($this->state[$event['stage']]['tasks'] as &$existingTask) {
            if (($existingTask['name'] == $event['task']) && ($existingTask['index'] == $event['index'])) {
                $existingTask['state'] = $event['state'];
                $existingTask['progress'] = $event['progress'];

                if (in_array($event['state'], ['failed', 'finished'])) {
                    $existingTask['time_end'] = $event['time'];

                    $this->state[$event['stage']]['tasks_ended'] += 1;

                    if ('failed' == $event['state']) {
                        $this->state[$event['stage']]['tasks_failed'] += 1;
                    }

                    if (count($this->state[$event['stage']]['tasks']) == $this->state[$event['stage']]['tasks_ended']) {
                        $this->state[$event['stage']]['time_end'] = $event['time'];
                        $this->state[$event['stage']]['state'] = (0 == $this->state[$event['stage']]['tasks_failed']) ? 'finished' : 'failed';
                    }

                    if (isset($event['data'])) {
                        $existingTask['data'] = array_merge(
                            isset($existingTask['data']) ? $existingTask['data'] : [],
                            $event['data']
                        );
                    }
                }

                $existingTask['events'][] = $event;

                return;
            }
        }

        $this->state[$event['stage']]['tasks'][] = [
            'name' => $event['task'],
            'index' => $event['index'],
            'time_begin' => $event['time'],
            'time_end' => null,
            'state' => $event['state'],
            'progress' => $event['progress'],
            'events' => [
                $event,
            ],
        ];

        // new task, so we're apparently still in progress
        $this->state[$event['stage']]['time_end'] = null;
        $this->state[$event['stage']]['state'] = $event['state'];
    }

    public function getState()
    {
        return $this->state;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
