<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Twig\Environment;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $maintenanceMode;

    public function __construct(Environment $twig, $maintenanceMode)
    {
        $this->twig = $twig;
        $this->maintenanceMode = $maintenanceMode;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->maintenanceMode && 'book_index' === $event->getRequest()->attributes->get('_route')) {
            $content = $this->twig->render('maintenance/maintenance.html.twig');
            $response = new Response($content);

            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
           'kernel.request' => 'onKernelRequest',
        ];
    }
}
