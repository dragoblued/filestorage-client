<?php

namespace common\services\tabaeva\src\storages;

use Aws\S3\S3Client;
use Gaufrette\Adapter\AwsS3 as AwsS3Adapter;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\Extras\Resolvable\ResolvableFilesystem;
use Gaufrette\Extras\Resolvable\Resolver\AwsS3PublicUrlResolver;
use common\services\tabaeva\src\interfaces\FileStorageInterface;
use Throwable;

class S3FileStorage implements FileStorageInterface
{
    private FilesystemInterface $filesystem;
    private AwsS3Adapter $awsS3Adapter;
    private S3Client $s3Client;
    private AwsS3PublicUrlResolver $resolver;
    private ResolvableFilesystem $resolvableFilesystem;
    private string $bucket;
    private string $rootDirectory;

    public function __construct(
        array $config = []
    )
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => $config['S3_REGION'] ?: '',
            'credentials' => [
                'key'    => $config['S3_KEY'] ?: '',
                'secret' => $config['S3_SECRET'] ?: '',
            ],
            'endpoint' => $config['S3_ENDPOINT'] ?: '',
        ]);
        $this->bucket = $config['S3_BUCKET'];
        $this->rootDirectory = $config['S3_ROOT_DIRECTORY'] ?: '';
        $this->awsS3Adapter = new AwsS3Adapter($this->s3Client, $this->bucket, [
            'directory' => $this->rootDirectory,
            'create' => true,
            'acl' => 'public-read'
        ]);
        $this->filesystem = new Filesystem($this->awsS3Adapter);

        // Создаем разрешающий резолвер
        $this->resolver = new AwsS3PublicUrlResolver($this->s3Client, $this->bucket, $this->rootDirectory);

        // Создаем ResolvableFilesystem
        $this->resolvableFilesystem = new ResolvableFilesystem($this->filesystem, $this->resolver);
    }

    public function upload(string $name, string $fileContent): void
    {
        try {
            $this->filesystem->write($name, $fileContent);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function delete(string $name): void
    {
        try {
            $this->filesystem->delete($name);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function get(string $name)
    {
        try {
            return $this->filesystem->get($name);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function size(string $name): int
    {
        try {
            return $this->filesystem->size($name);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function has(string $name): bool
    {
        try {
            return $this->filesystem->has($name);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function resolve(string $key): string
    {
        try {
            return $this->filesystem->resolve($key);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @return void
     */
    public function clearBucket(): void
    {
        try {
            $result = $this->s3Client->listObjects(
                ['Bucket' => $this->bucket, 'Prefix' => $this->rootDirectory]
            );

            if (!$result->hasKey('Contents')) {
                return;
            }

            foreach ($result->get('Contents') as $staleObject) {
                $this->s3Client->deleteObject(['Bucket' => $this->bucket, 'Key' => $staleObject['Key']]);
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }
}