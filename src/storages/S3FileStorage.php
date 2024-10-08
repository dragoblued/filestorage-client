<?php

namespace Dragoblued\Filestorageclient\storages;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Gaufrette\Adapter\AwsS3 as AwsS3Adapter;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Dragoblued\Filestorageclient\interfaces\FileStorageInterface;
use Dragoblued\Filestorageclient\exceptions\S3StorageException;
use Dragoblued\Filestorageclient\File;
use Throwable;

/**
 * Class S3FileStorage
 */
class S3FileStorage implements FileStorageInterface
{
    private FilesystemInterface $filesystem;
    private AwsS3Adapter $awsS3Adapter;
    private S3Client $s3Client;
    private string $bucket;
    private string $rootDirectory;

    /**
     * @param array $config
     */
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
            throw new S3StorageException("Error connecting to S3: " . $e->getMessage());
        }
        $this->bucket = $config['S3_BUCKET'];
        $this->rootDirectory = $config['S3_ROOT_DIRECTORY'] ?: '';
        $this->awsS3Adapter = new AwsS3Adapter($this->s3Client, $this->bucket, [
            'directory' => $this->rootDirectory,
            'create' => true,
            'acl' => 'public-read'
        ]);
        $this->filesystem = new Filesystem($this->awsS3Adapter);
    }

    /**
     * @param string $name
     * @param string $tmpName
     *
     * @return void
     */
    public function upload(string $name, string $tmpName): void
    {
        try {
            $this->filesystem->write($name, file_get_contents($tmpName));
        } catch (Throwable $e) {
            throw new S3StorageException('Error uploading file: ' . $name . ' ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $name
     *
     * @return ?File
     */
    public function getFile(string $name): ?File
    {
        try {
            if ($this->filesystem->has($name)) {
                return (new File($name, $this->filesystem));
            }
            return null;
        } catch (Throwable $e) {
            throw new S3StorageException('Error getting file: ' . $name . ' ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getUrl(string $name): string
    {
        try {
            return $this->s3Client->getObjectUrl($this->bucket, $name);
        } catch (Throwable $e) {
            throw new S3StorageException('Error getting url: ' . $name . ' ' . $e->getMessage(), 0, $e);
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
            throw new S3StorageException('Error cleanup bucket: ' . $e->getMessage());
        }
    }
}
