<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $response = new Response();
        $response->setContent('<h1>Hello from Kubernetes running on AWS!</h1>');

        return $response;
    }
}
