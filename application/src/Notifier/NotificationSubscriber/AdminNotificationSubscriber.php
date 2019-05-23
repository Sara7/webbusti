<?php

namespace App\Notifier\NotificationSubscriber;

use App\Events\OrderEvent;
use App\Events\OrderEvents;
use App\Events\UserEvent;
use App\Events\UserEvents;
use App\Notifier\AdminNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminNotificationSubscriber implements EventSubscriberInterface
{
    /**
     * @var AdminNotifier
     */
    private $notifier;

    /**
     * NotifyAdminsOnEvents constructor.
     *
     * @param AdminNotifier $notifier
     */
    public function __construct(AdminNotifier $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::CREATED => 'notifyOnUserCreated',
            OrderEvents::CREATED => 'notifyOnOrderCreated',
            OrderEvents::SHIPPED => 'notifyOnOrderShipped',
            OrderEvents::DELETED => 'notifyOnOrderCanceled',
        ];
    }

    /**
     * @param UserEvent $event
     */
    public function notifyOnUserCreated(UserEvent $event): void
    {
        $user = $event->getUser();

        $this->notifier->notify('admin_user_created', 'Nuovo utente registrato', [
            'user' => $user,
        ]);
    }

    /**
     * @param OrderEvent $event
     */
    public function notifyOnOrderCreated(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $title = sprintf('Nuovo ordine id %s', $order->getId());

        $this->notifier->notify('admin_order_confirmed', $title, [
            'order' => $order,
        ]);
    }

    /**
     * @param OrderEvent $event
     */
    public function notifyOnOrderShipped(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $title = sprintf('Spedizione ordine id %s', $order->getId());

        $this->notifier->notify('admin_order_shipped', $title, [
            'order' => $order,
        ]);
    }

    /**
     * @param OrderEvent $event
     */
    public function notifyOnOrderCanceled(OrderEvent $event): void
    {
        $order = $event->getOrder();

        $title = sprintf('Cancellazione ordine id %s', $order->getId());

        $this->notifier->notify('admin_order_canceled', $title, [
            'order' => $order,
        ]);
    }
}
