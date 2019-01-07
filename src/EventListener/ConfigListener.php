<?php
/**
 * Created by IntelliJ IDEA.
 * User: hp
 * Date: 10/11/2018
 * Time: 12:19 PM
 */

namespace App\EventListener;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use \Unirest;

/**
 */
class ConfigListener
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var object
     */
    private $route;

    /**
     * ConfigListener constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->route = $this->container->get('router');
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $res = new Response();
        $route = $event->getRequest()->attributes->get('_route');

        if ($event->isMasterRequest()){
            $session = new Session();
            $headers = array('Accept' => 'application/json');
            $template_name = $event->getRequest()->get('template_name');
            $api_key = $event->getRequest()->get('api_key');
//        api/vitrine/tdCqnoI-YIjoNXbTTDlKeqPnaMFbQDhpZLFcgprSrXm
            Unirest\Request::verifyPeer(false); // Disables SSL cert validation
            $response = Unirest\Request::get('https://api.jsonbin.io/b/5c249f7e3f8bd92e4cc3cbcd', $headers);


            $body = $response->body;
            $data = $body->data;
            $pages = $data->pages;
            $sections = $data->sections;
            $menu = $data->menus;
//            dd($response);
//            die();

            if ($response->code === 200 ){
                $response = json_decode($response->raw_body, true);

                    $session->set('data', $response['data']);
                    if ($body->success == false){

                    }else{
                        $session->set('data', $data);
                        //  je met toutes les pages dans un tableau "$pages_tb"
                        $pages_tb = [];
                        foreach($pages as   $v){
                            if ($v->status == "publish")
                            array_push($pages_tb, $v);
                        }
                        $session->set('pages', $pages_tb);

                        // je met tous mes menu dans un tableau "$menu_tb"
                        $menu_tb = [];
                        foreach ($menu as $k => $v ){
                            array_push($menu_tb, $v);

                        }

                        $session->set('menu', $menu_tb);


                    }

            }else{
                $session->set('error', ['success' => 'definitely success is false']);
//                $event->setResponse(new RedirectResponse($this->route->generate('error404', ['template_name' => $template_name, 'api_key' => $api_key]), 302));
            }

        }


    }

}
