<?php

namespace App\EventSubscriber;

use App\Exceptions\ConstraintViolationException;
use App\Http\ResponseFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

        $status = match (get_class($exception)) {
            NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
            AccessDeniedException::class => Response::HTTP_UNAUTHORIZED,
            default => Response::HTTP_BAD_REQUEST,
        };

        $response = ResponseFormatter::error($exception->getMessage(), $status);

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
