<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\Adapter\Memcached;
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\AvailableSpaceCapableInterface;
use Zend\Cache\Storage\FlushableInterface;
use Zend\Cache\Storage\TotalSpaceCapableInterface;
/*
$this->params()->fromPost('paramname');   // From POST
$this->params()->fromQuery('paramname');  // From GET
$this->params()->fromRoute('paramname');  // From RouteMatch
$this->params()->fromHeader('paramname'); // From header
$this->params()->fromFiles('paramname');
*/
class SCGController extends AbstractActionController
{
    const BASE_URL = 'https://maps.googleapis.com/maps/api/place/';
    const TEXT_SEARCH_URL = 'textsearch/json';
    const API_KEY = 'AIzaSyC7RvHoeKui0o89w1ombfMAYpkxq3ti1FE';

    private $i = 0;
    private $output = 0;
    private $resp = '';
    private $results;

    public function indexAction() 
    {
        $view = new ViewModel();

        // 1
        $this->findVal(3);
        $view->ans1 = $this->resp;

        // 2
        $view->ans2 = $this->findRestaurantsbyArea();

        return $view;
    }

    public function findVal($init)
    {
        // recursive function for find value
        $this->output = $init + ( $this->i * 2 );
        $this->i += 1;
        $this->resp .= $this->output . ', ';
        if ($this->i < 7)
        {
            $this->findVal($this->output);
        }
    }

    public function findRestaurantsbyArea() 
    {
        // Create a new cURL resource
        $curl = curl_init(); 

        if ( ! $curl )
        {
            die("Couldn't initialize a cURL handle"); 
        }

        // Set the file URL to fetch through cURL
        curl_setopt( $curl , CURLOPT_URL, self::BASE_URL . self::TEXT_SEARCH_URL . '?query=restaurants+in+bangsue&key=' . self::API_KEY );

        curl_setopt( $curl , CURLOPT_RETURNTRANSFER, 1);

        // Do not check the SSL certificates
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 

        // Fetch the URL and save the content
        $data = curl_exec( $curl ); 

        // Check if any error has occurred 
        if ( curl_errno( $curl ) ) 
        {
            echo 'cURL error: ' . curl_error( $curl ); 
        }
        else
        {
            print_r( curl_getinfo( $curl ) ); 
        }
         
        // close cURL resource to free up system resources
        curl_close($curl);
        return json_decode( $data , true );
    }
}