<?php

namespace App\EventSubscriber;

use App\Exceptions\ConstraintViolationException;
use App\Http\ResponseFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ConstraintViolationException) {
            $response = ResponseFormatter::errorFromConstraintList($exception->getViolationList());

            $event->setResponse($response);

            return;
        }

        if ($exception instanceof NotFoundHttpException) {
        }

        $response = ResponseFormatter::errors([$exception->getMessage()]);

        $event->setResponse($response);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
