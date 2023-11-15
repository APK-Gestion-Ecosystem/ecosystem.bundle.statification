<?php

namespace Ecosystem\StatificationBundle\Service;

use Aws\S3\S3Client;

class SettingService
{
    private const SETTINGS_STATIFICATION_KEY = 'setting';
    private const SETTINGS_STATIFICATION_FILENAME = 'settings';

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

    public function getSettings(string $locale = 'es'): ?array
    {
        $key = sprintf(
            'statifications/%s/%s.json',
            self::SETTINGS_STATIFICATION_KEY,
            self::SETTINGS_STATIFICATION_FILENAME
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
            return $content[$locale] ?? null;
        } catch (\JsonException) {
            return null;
        }
    }

    public function getSetting(string $setting, string $locale = 'es'): ?string
    {
        $key = sprintf(
            'statifications/%s/%s.json',
            self::SETTINGS_STATIFICATION_KEY,
            self::SETTINGS_STATIFICATION_FILENAME
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

        return $content[$locale][$setting] ?? null;
    }
}
