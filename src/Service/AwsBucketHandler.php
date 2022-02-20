<?php

namespace App\Service;

use Aws\S3\S3Client;

class AwsBucketHandler
{
    private $s3Client;

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }

    public function putObjectS3($keyPath, $filePath)
    {
        $pathInfo = pathinfo($filePath);
        if (!isset($pathInfo['extension'])) {
            return null;
        }

        $fullKey = $keyPath . $pathInfo['filename'] . "." . $pathInfo['extension'];

        $this->s3Client->putObject([
            'Bucket' => $_ENV["AWS_BUCKET_NAME"],
            'Key' => $fullKey,
            'SourceFile' => $filePath,
            'ACL' => 'public-read']);

        return $this->s3Client->getObjectUrl($_ENV["AWS_BUCKET_NAME"], $fullKey);

    }



}
