<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\ConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * @return RequestInterface[]
     *
     * @throws ConstraintViolationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dtoType = $argument->getType();
        if (null === $dtoType || !is_subclass_of($dtoType, RequestInterface::class)) {
            return [];
        }

        $dto = $this->serializer->deserialize($request->getContent(), $dtoType, JsonEncoder::FORMAT);

        $constraints = $this->validator->validate($dto);
        if (0 < count($constraints)) {
            throw new ConstraintViolationException($constraints);
        }

        return [$dto];
    }
}
