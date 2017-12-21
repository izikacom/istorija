<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Dayuse\Istorija\Serializer;

use Dayuse\Istorija\Utils\Ensure;

class JsonObjectSerializer
{
    private $jsonEncodeOptions;
    private $jsonDecodeOptions;
    private $maxDepth;

    public function __construct(
        int $jsonEncodeOptions = 0,
        ?int $jsonDecodeOptions = null,
                                int $maxDepth = 512
    ) {
        $this->jsonDecodeOptions = $jsonDecodeOptions ?? JSON_OBJECT_AS_ARRAY;
        $this->jsonEncodeOptions = $jsonEncodeOptions;
        $this->maxDepth          = $maxDepth;
    }

    public function getContentType()
    {
        return 'application/json';
    }

    public function assertContentType($mimeType)
    {
        Ensure::eq('application/json', $mimeType);
    }

    public function serialize($object): string
    {
        $normalizedMessage = $this->normalize($object);

        return json_encode($normalizedMessage, $this->jsonEncodeOptions, $this->maxDepth);
    }

    private function normalize($data)
    {
        if (is_object($data)) {
            $normalizedMessage = [];
            $refObject         = new \ReflectionObject($data);
            $properties        = $refObject->getProperties();

            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($data);
                $property->setAccessible(false);

                $normalizedMessage[$property->getName()] = $this->normalize($value);
            }

            return $normalizedMessage;
        }

        if (is_array($data)) {
            $normalizedData = [];
            foreach ($data as $key => $value) {
                $normalizedData[$key] = $this->normalize($value);
            }

            return $normalizedData;
        }

        return $data;
    }

    public function deserialize(string $serializedString, string $typeHint = null)
    {
        $rawData = @json_decode($serializedString, true, $this->maxDepth, $this->jsonDecodeOptions);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new CorruptedSerializedMessage(json_last_error_msg());
        }

        if (null === $typeHint) {
            return $rawData;
        }

        $messageClass = str_replace('.', '\\', $typeHint);
        if (!class_exists($messageClass)) {
            throw UnableToDeserializeMessage::invalidMessageContract($typeHint);
        }

        $refClass  = new \ReflectionClass($messageClass);
        $object    = $refClass->newInstanceWithoutConstructor();
        $refObject = new \ReflectionObject($object);

        foreach ($rawData as $key => $value) {
            $property = $refObject->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }

        return $object;
    }
}
