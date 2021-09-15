<?php
namespace App\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class Maintenance
{
    private $container;
    private $twig;

    public function __construct(ContainerInterface $container, Environment $twig)
    {
        $this->container= $container;
        $this->twig = $twig;
    }

    public function onkernelRequest(RequestEvent $event)
    {
        //the pages which can be used in maintenance mode
        $routeavailabe=[
            'app_register',
            'app_login',
            'app_logout',
            "app_reset_password",
            "app_forgot_password_request"
            
        ];
        // get maintenance parameters
         $maintenance = $this->container->getParameter('maintenance') ? $this->container->getParameter('maintenance') : '';

         $debug = in_array($this->container->get('kernel')->getEnvironment(), array('test'));

         //condition to set maintenance

         //get the route

         $route_name= $event->getRequest()->attributes->get('_route');
         if($maintenance && !$debug)
         {      
             if(!in_array($route_name,$routeavailabe))
            {
                if(!$this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
                {
                    $content= $this->twig->render('maintenance/index.html.twig', []);
                    $event->setResponse(new Response($content, 503));
                    $event->stopPropagation();
                }
               
            }
         }
    }


}