<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."libraries/awslib/aws-autoloader.php";
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Sync\UploadSyncBuilder;

class Aws {
    public function __construct() {
        // parent::__construct();
    }

    function synctoaws(){           
        if(IAMKEY != "" && IAMSECRETKEY != "" && REGION != "" && AWSLINK != "" && ALLOWS3!= ""){
            $client = S3Client::factory(
                array(
                    'credentials' => array(
                        'key' => IAMKEY,
                        'secret' => IAMSECRETKEY
                    ),
                    'version' => 'latest',
                    'region'  => REGION
                )
            );
        }else{
            echo 2;
            exit;
        }    
        $GLOBALS['s3'] = $client;
        //$dir = AWS_PATH;

        if(ALLOWS3 == '1') {
            $client->uploadDirectory(AWS_ASSETS_PATH, COMMONBUCKETLINK, CLIENTNAME, array(
                'concurrency' => 20,
                'before' => function (\Aws\Command $command) {
                    $command['ACL'] = 'public-read';
                },		
                //'debug' => true,
            ));
            $client->uploadDirectory(AWS_UPLOADED_PATH, BUCKETNAME, CLIENTNAME, array(
                'concurrency' => 20,
                'before' => function (\Aws\Command $command) {
                    $command['ACL'] = 'public-read';
                },		
                //'debug' => true,
            ));
        }else if(ALLOWS3 == '0') {
            $location = AWS_ASSETS_PATH; // here root folder contains only images under assets
            if(COMMONBUCKETLINK != ""){  
                $client->downloadBucket($location, COMMONBUCKETLINK);
            }
            $location = AWS_UPLOADED_PATH; // here root folder contains only images under assets
            if(BUCKETNAME != ""){  
                $client->downloadBucket($location, BUCKETNAME);
            }
        }
    }    

    function verifyawscredentials(){
        try {
            $client = S3Client::factory(
                array(
                    'credentials' => array(
                        'key' => IAMKEY,
                        'secret' => IAMSECRETKEY
                    ),
                    'version' => 'latest',
                    'region'  => REGION
                )
            );
            $GLOBALS['s3'] = $client;
            $GLOBALS['s3']->registerStreamWrapper();
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
    
    function get_aws_url($key, $time = '+1 day') {
        $this->verifyawscredentials(); 
        if(ALLOWS3 == '1') {
            $new_key = str_replace(' ', '+', $key);  
            $key_name = CLIENTNAME."/".$new_key;
            $key  = CLIENTNAME.'/'.$key;
      
            $response = $GLOBALS['s3']->doesObjectExist(BUCKETNAME, $key_name);
            $cmd = $GLOBALS['s3']->getCommand('GetObject', [
                'Bucket' => BUCKETNAME,
                'Key' => $key
            ]);
            
            $request = $GLOBALS['s3']->createPresignedRequest($cmd, $time);
            $aws_url = (string)$request->getUri();
        } else {
            $base_location = DOMAIN_URL;
            $loc = explode('/', $base_location);
            $aws_url = DOMAIN_URL.ROOT_FOLDER.$key;
        }
        echo $aws_url;
    }
    
}   