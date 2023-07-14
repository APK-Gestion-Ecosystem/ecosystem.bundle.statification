<?php

namespace Ecosystem\StatificationBundle\Service;

use Aws\S3\S3Client;

class SettingService
{
    private const STATIFICATIONS_DIRECTORY = 'statifications';
    private const SETTINGS_STATIFICATION_KEY = 'setting';
    private const SETTINGS_STATIFICATION_FILENAME = 'settings';

    private S3Client $client;

    public function __construct(
        private readonly string $bucket,
        private readonly ?string $key,
        private readonly ?string $secret
    ) {
        $config = [
            'region' => getenv('AWS_REGION'),
            'version' => '2006-03-01',
        ];

        if ($this->key !== null && $this->bucket) {
            $config['credentials'] = [
                'key' => $this->key,
                'secret' => $this->secret,
            ];
        }
        $this->client = new S3Client($config);
    }

    public function getSetting(string $setting): ?string
    {
        $content = $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => sprintf(
                '%s/%s/%s.json',
                self::STATIFICATIONS_DIRECTORY,
                self::SETTINGS_STATIFICATION_KEY,
                self::SETTINGS_STATIFICATION_FILENAME
            )
        ])->get('Body');

        if (!$content) {
            return null;
        }

        try {
            $content = json_decode($content->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        return $content[$setting] ?? null;
    }
}