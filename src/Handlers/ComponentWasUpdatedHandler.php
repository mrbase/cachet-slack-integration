<?php

namespace Mrbase\CachetSlackIntegration\Handlers;

/**
 * Class ComponentWasUpdatedHandler
 *
 * @package Mrbase\CachetSlackIntegration
 */
class ComponentWasUpdatedHandler extends BaseHandler
{
    /**
     * @var string
     */
    private $message;

    /**
     * ComponentWasUpdatedHandler constructor.
     *
     * @param int    $status
     * @param string $name
     */
    public function __construct($status, $name)
    {
        $this->message  = trans('slack::messages.component.status_update', [
            'name'   => $name,
            'status' => $this->translateComponentStatus($status),
        ]);
    }

    /**
     * Send the slack message.
     *
     * @return mixed
     */
    public function send()
    {
        $this->sendMessage($this->message);
    }
}
