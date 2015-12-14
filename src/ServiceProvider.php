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

use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\Incident;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Mrbase\CachetSlackIntegration\Handlers\Events\Component\ComponentUpdated;
use Mrbase\CachetSlackIntegration\Handlers\Events\Incident\IncidentReported;
use Mrbase\CachetSlackIntegration\Handlers\Events\Incident\IncidentUpdated;

/**
 * Class ServiceProvider
 *
 * @package Mrbase\CachetSlackIntegration
 * @author  Ulrik Nielsen <me@ulrik.co>
 */
class ServiceProvider extends EventServiceProvider
{
    protected $listen = [
        'CachetHQ\Cachet\Events\Incident\IncidentWasReportedEvent'  => [IncidentReported ::class],
        'CachetHQ\Cachet\Events\Incident\IncidentWasUpdatedEvent'   => [IncidentUpdated::class],
        'CachetHQ\Cachet\Events\Component\ComponentWasUpdatedEvent' => [ComponentUpdated::class],
    ];

    /**
     * Setting up event listeners.
     *
     * @param DispatcherContract $events
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        Incident::updating(function(Incident $incident) {
            Utils::registerChanges('incident', $incident->getDirty());
        });

        Component::updating(function(Component $component) {
            Utils::registerChanges('component', $component->getDirty());
        });

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'slack');

        $this->publishes([
            __DIR__.'/resources/slack.php' => config_path('slack.php'),
            __DIR__.'/resources/lang'      => base_path('resources/lang/vendor/slack'),
        ], 'public');
    }

    /**
     * {@inheritdoc}
     */
    public function register(){}
}
