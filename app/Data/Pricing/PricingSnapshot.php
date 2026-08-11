<?php

namespace App\Data\Pricing;

use JsonSerializable;

final class PricingSnapshot implements JsonSerializable
{
    public function __construct(
        public readonly array $data,
        public readonly string $checksum,
    ) {
    }

    public static function fromQuote(
        PricingQuote $quote,
        int $sequence = 1,
        ?int $actorId = null,
        ?string $reason = null,
    ): self {
        $data = $quote->toArray();
        $data['snapshot_sequence'] = $sequence;
        $data['calculated_by'] = $actorId;
        $data['reason'] = $reason;
        $canonicalJson = json_encode(
            self::sortRecursively($data),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return new self($data, hash('sha256', $canonicalJson));
    }

    public function toArray(): array
    {
        return $this->data + ['snapshot_checksum' => $this->checksum];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private static function sortRecursively(array $value): array
    {
        if (!array_is_list($value)) {
            ksort($value);
        }

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = self::sortRecursively($item);
            }
        }

        return $value;
    }
}
