<?php

namespace App\Services;


use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ArrayNormalizer
{

    public function normalize($array)
    {
        $normalizer = new ObjectNormalizer();

        $normalizer->setCircularReferenceLimit(0);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $normalizers = array($normalizer);

        $serializer = new Serializer($normalizers);

        $normalized = $serializer->normalize($array);

        return $normalized;
    }
}