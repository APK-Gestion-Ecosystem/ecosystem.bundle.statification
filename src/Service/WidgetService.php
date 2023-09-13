<?php

namespace Ecosystem\StatificationBundle\Service;

use Aws\S3\S3Client;

class WidgetService
{
    private const WIDGETS_STATIFICATION_KEY = 'widget';

    private S3Client $client;

    public function __construct(private readonly string $bucket)
    {
        $config = [
            'region' => getenv('AWS_REGION'),
            'version' => '2006-03-01',
        ];

        if (false !== getenv('AWS_ACCESS_KEY_ID') && false !== getenv('AWS_SECRET_ACCESS_KEY')) {
            $config['credentials'] = [
                'key' => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ];
        }

        $this->client = new S3Client($config);
    }

    public function getWidget(string $widget): ?string
    {
        $key = sprintf(
            'statifications/%s/%s.json',
            self::WIDGETS_STATIFICATION_KEY,
            $widget
        );

        if (!$this->client->doesObjectExist($this->bucket, $key)) {
            return null;
        }

        $content = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ])->get('Body');

        if (!$content) {
            return null;
        }

        try {
            $content = json_decode($content->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        return $content[$widget] ?? null;
    }
}
