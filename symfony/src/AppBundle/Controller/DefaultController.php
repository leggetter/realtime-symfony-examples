<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\ChatMessage;

use Predis\Client;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }
    
    /**
     * @Route("/chat/messages")
     */
    public function getMessages() {
      $messages = $this->getDoctrine()
        ->getRepository('AppBundle:ChatMessage')
        ->findAll();

      $response = new JsonResponse();
      $response->setData($messages);
      return $response;
    }
    
    /**
     * @Route("/chat/message")
     */
    public function postMessage(Request $request)
    {
      // $pusher = $this->container->get('lopi_pusher.pusher');
      
      $message = new ChatMessage();
      $message
        ->setUsername($request->get('username'))
        ->setText($request->get('chat_text'))
        ->setTimestamp(new \DateTime());
        
      $em = $this->getDoctrine()->getManager();
      $em->persist($message);
      $em->flush();
      
      // $pusher->trigger(
      //   'chat',
      //   'new-message',
      //   $message
      // );
      
      $data = [
        'event' => 'new-message',
        'data' => $message
      ];
      $jsonContent = json_encode($data);
      
      $redis = new Client('tcp://127.0.0.1:6379');
      $redis->publish('chat', $jsonContent);
      
      $response = new JsonResponse();
      $response->setData($message);
      return $response;
    }
}
