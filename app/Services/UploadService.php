<?php

namespace App\Services;
use Aws\S3\S3Client;

class UploadService {
    private $s3;
    private $bucket;

    public function __construct() {
        $this->s3 = new S3Client([
            'region'  => 'us-east-1',
            'endpoint' => 'https://ropeg.kemenag.go.id:9000/',
            'use_path_style_endpoint' => true,
            'version' => 'latest',
            'credentials' => [
                'key'    => "Uqq32YZ89Ikq5HYj4Upm",
                'secret' => "l3oFeNrd5WuDjY8iluui953vdGDq5IWsOEK3EIb8",
            ],
            'http'    => [
                'verify' => false
            ]
        ]);

        $this->bucket = 'layanan';
    }

    public function upload($file,$name,$location) {
        // dd($file['name']);
        if (!isset($file) || !isset($file)) {
            return ['status' => false, 'message' => 'File tidak ditemukan.'];
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = $name . '.japel.' . time() . '.' . $ext;
        $temp_file_location = $file;

        try {
            $result = $this->s3->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $location . '/' . $file_name,
                'SourceFile'  => $temp_file_location,
                'ContentType' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]);

            return [
                'status' => true,
                'url'    => $result->get('ObjectURL'),
                'file_name' => $file_name
            ];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}