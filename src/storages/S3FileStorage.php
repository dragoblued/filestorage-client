<?php

namespace Dragoblued\Filestorageclient\storages;

use Aws\S3\S3Client;
use Gaufrette\Adapter\AwsS3 as AwsS3Adapter;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\Extras\Resolvable\ResolvableFilesystem;
use Gaufrette\Extras\Resolvable\Resolver\AwsS3PublicUrlResolver;
use Dragoblued\Filestorageclient\interfaces\FileStorageInterface;
use Throwable;


use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Exception;
use Gaufrette\Adapter\AwsS3 as AwsS3Adapter;
use Gaufrette\File;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\Extras\Resolvable\ResolvableFilesystem;
use Gaufrette\Extras\Resolvable\Resolver\AwsS3PublicUrlResolver;
use common\services\tabaeva\interfaces\FileStorageInterface;
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

    public function __construct(array $config = [])
    {
        try {
            $this->s3Client = new S3Client([
                'version' => 'latest',
                'region' => $config['S3_REGION'] ?: '',
                'credentials' => [
                    'key' => $config['S3_KEY'] ?: '',
                    'secret' => $config['S3_SECRET'] ?: '',
                ],
                'endpoint' => $config['S3_ENDPOINT'] ?: '',
            ]);
        } catch (AwsException $e) {
            throw new Exception("Error connecting to S3: " . $e->getMessage());
        }
        $this->bucket = $config['S3_BUCKET'];
        $this->rootDirectory = $config['S3_ROOT_DIRECTORY'] ?: '';
        $this->awsS3Adapter = new AwsS3Adapter($this->s3Client, $this->bucket, [
            'directory' => $this->rootDirectory,
            'create' => true,
            'acl' => 'public-read'
        ]);
        $this->filesystem = new Filesystem($this->awsS3Adapter);
        $this->resolver = new AwsS3PublicUrlResolver($this->s3Client, $this->bucket, $this->rootDirectory);
        $this->resolvableFilesystem = new ResolvableFilesystem($this->filesystem, $this->resolver);
    }

    /**
     * @param string $name
     * @param string $tmp
     * @param string $path
     *
     * @return void
     */
    public function upload(string $name, string $tmp, string $path = null): void
    {
        try {
            $this->filesystem->write($name, file_get_contents($tmp));
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function delete(string $name): void
    {
        try {
            $this->filesystem->delete($name);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     *
     * @return File
     */
    public function get(string $name): ?File
    {
        try {
            if ($this->filesystem->has($name)) {
                return $this->filesystem->get($name);
            }
            return null;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function size(string $name): int
    {
        try {
            return $this->filesystem->size($name);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function url(string $name): string
    {
        try {
            return $this->s3Client->getObjectUrl($this->bucket, $name);
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