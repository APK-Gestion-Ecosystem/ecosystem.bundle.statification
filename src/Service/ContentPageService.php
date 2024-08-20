<?php

namespace Ecosystem\StatificationBundle\Service;

use Aws\S3\S3Client;

class ContentPageService
{
    private const CONTENTPAGE_STATIFICATION_KEY = 'contentpage';

    private S3Client $client;

    public function __construct(private readonly string $bucket)
    {
        $config = [
            'region' => getenv('AWS_REGION'),
            'version' => '2006-03-01',
        ];

        if (getenv('LOCALSTACK')) {
            $config['endpoint'] = 'http://localstack:4566';
            $config['credentials'] = false;
            $config['use_path_style_endpoint'] = true;
        }

        $this->client = new S3Client($config);
    }

    public function getContentPage(string $contentPage): ?array
    {
        $key = sprintf(
            'statifications/%s/%s.json',
            self::CONTENTPAGE_STATIFICATION_KEY,
            $contentPage
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
            return json_decode($content->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
    }
}
