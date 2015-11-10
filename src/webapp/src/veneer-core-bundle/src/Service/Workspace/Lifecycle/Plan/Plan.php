<?php

namespace Veneer\CoreBundle\Service\Workspace\Lifecycle\Plan;

class Plan
{
    protected $details;

    public function addDetail($topic, $verb, $noun, $preposition = null)
    {
        $this->details[] = [
            'topic' => $topic,
            'verb' => $verb,
            'noun' => $noun,
            'preposition' => $preposition,
        ];

        return $this;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getTopicDetails($topic)
    {
        return array_filter(
            $this->details,
            function (array $detail) use ($topic) {
                return $topic == $detail['topic'];
            }
        );
    }
}
