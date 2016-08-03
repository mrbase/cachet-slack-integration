<?php
/**
 * This file is part of the CachetSlackIntegration package.
 *
 * (c) Ulrik Nielsen <me@ulrik.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mrbase\CachetSlackIntegration;

use CachetHQ\Cachet\Bus\Events\Component\ComponentWasUpdatedEvent;
use CachetHQ\Cachet\Bus\Events\Incident\IncidentWasReportedEvent;
use CachetHQ\Cachet\Bus\Events\Incident\IncidentWasUpdatedEvent;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\Incident;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Mrbase\CachetSlackIntegration\Handlers\ComponentWasUpdatedHandler;
use Mrbase\CachetSlackIntegration\Handlers\IncidentWasReportedHandler;
use Mrbase\CachetSlackIntegration\Handlers\IncidentWasUpdatedHandler;

/**
 * Class ServiceProvider
 *
 * @package Mrbase\CachetSlackIntegration
 * @author  Ulrik Nielsen <me@ulrik.co>
 */
class ServiceProvider extends EventServiceProvider
{
    /**
     * @var array
     */
    private static $changes = [];

    /**
     * {@inheritdoc}
     */
    public function register(){}

    /**
     * @param DispatcherContract $events
     */
    public function boot(DispatcherContract $events)
    {
        if (false === config('slack.endpoint', false)) {
            return;
        }

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'slack');

        /**
         * To get the actual changes we need to record the
         * changes before the Cachet event is fired.
         */
        Incident::updating(function(Incident $incident) {
            $dirty = [];
            foreach ($incident->getDirty() as $key => $v) {
                $dirty[$key] = $incident->getOriginal($key);
            }
            self::registerChanges('incident', $dirty);
        });

        Component::updating(function(Component $component) {
            $dirty = [];
            foreach ($component->getDirty() as $key => $v) {
                $dirty[$key] = $component->getOriginal($key);
            }
            self::registerChanges('component', $dirty);
        });


        /**
         * Send Slack notification on new incidents.
         */
        $events->listen('CachetHQ\Cachet\Bus\Events\Incident\IncidentWasReportedEvent', function (IncidentWasReportedEvent $event) {
            $handler = new IncidentWasReportedHandler(
                $event->incident->status,
                $event->incident->name,
                $event->incident->message,
                $event->incident['component_id']
            );

            return $handler->send();
        });


        /**
         * Send Slack notification on incident updates.
         */
        $events->listen('CachetHQ\Cachet\Bus\Events\Incident\IncidentWasUpdatedEvent', function (IncidentWasUpdatedEvent $event) {
            $handler = new IncidentWasUpdatedHandler(
                $event->incident->id,
                $event->incident->status,
                $event->incident->name,
                $event->incident->message,
                $event->incident['component_id'],
                self::getChanges('incident')
            );

            return $handler->send();
        });


        /**
         * Send Slack notification on component updates.
         * Note these are not send when a component is updated as part of an incident update.
         */
        $events->listen('CachetHQ\Cachet\Bus\Events\Component\ComponentWasUpdatedEvent', function (ComponentWasUpdatedEvent $event) {
            $handler = new ComponentWasUpdatedHandler(
                $event->component->status,
                $event->component->name
            );

            return $handler->send();
        });
    }

    /**
     * Register model object changes.
     *
     * @param string $model
     * @param array  $data
     */
    private static function registerChanges($model, array $data)
    {
        // For some reason, the changed/saving event is called twice on the model, we only need the first.
        if (!empty(self::$changes[$model])) {
            return;
        }

        self::$changes[$model] = $data;
    }

    /**
     * Return any changes to a model object.
     *
     * @param string $model
     *
     * @return array
     */
    private static function getChanges($model)
    {
        if (isset(self::$changes[$model])) {
            return self::$changes[$model];
        }

        return [];
    }
}
