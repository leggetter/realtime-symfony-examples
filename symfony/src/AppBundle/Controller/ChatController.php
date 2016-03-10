<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\ChatMessage;
use AppBundle\Entity\Caller;

use Predis\Client;

class ChatController extends Controller
{
    /**
     * @Route("/chat-api/messages")
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
     * @Route("/chat-api/message")
     */
    public function postMessage(Request $request)
    {
      $message = new ChatMessage();
      $message
        ->setUsername($request->get('username'))
        ->setText($request->get('chat_text'))
        ->setTimestamp(new \DateTime());
        
      $em = $this->getDoctrine()->getManager();
      $em->persist($message);
      $em->flush();
      
      // Uncomment this to publish to redis and use Ratchet or Faye
      // $data = [
      //   'event' => 'new-message',
      //   'data' => $message
      // ];
      // $jsonContent = json_encode($data);
      // $redis = new Client('tcp://127.0.0.1:6379');
      // $redis->publish('chat', $jsonContent);

      // Uncomment this to use Pusher
      // $pusher = $this->container->get('lopi_pusher.pusher');
      // $pusher->trigger(
      //   'chat',
      //   'new-message',
      //   $message
      // );
      
      // Uncomment to sent an SMS to any registered numbers.
      $this->sendSmsToAll($message);
      
      $response = new JsonResponse();
      $response->setData($message);
      return $response;
    }
    
    // Utility
    
    private function sendSmsToAll($message) {
      
      $repository = $this->getDoctrine()
        ->getRepository('AppBundle:Caller');

      $query = $repository->createQueryBuilder('c')
        ->select('c')
        ->groupBy('c.phoneNumber')
        ->getQuery();

      $callers = $query->getResult();
      
      $sender = $this->container->get('jhg_nexmo_sms');
      foreach ($callers as $caller) {
        $sender->sendText($caller->getPhoneNumber(), $message->getText(), $message->getUsername());
      }
    }
    
    // ----------------------------------
    
    // View Routes
    
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('chat/chat.html.twig');
    }
    
    /**
     * @Route("/chat/{framework}")
     */
    public function chat($framework)
    {
        return $this->render('chat/chat.html.twig', 
                             ['framework' => $framework]);
    }
    
    /**
     * @Route("/faye", name="faye")
     */
    public function fayeChat(Request $request)
    {
        return $this->render('chat/faye.html.twig');
    }
    
    // ----------------------------------
    
    /**
     * @Route("/chat-api/incoming-call")
     */
    public function incomingCall(Request $request)
    {
      $caller = new Caller();
      $caller
        ->setPhoneNumber($request->get('nexmo_caller_id'))
        ->setCalledAt(new \DateTime());
        
      $em = $this->getDoctrine()->getManager();
      $em->persist($caller);
      $em->flush();
      
      $pusher = $this->container->get('lopi_pusher.pusher');
      $pusher->trigger(
        'chat',
        'incoming-call',
        array(
          'number' => '...' . substr($caller->getPhoneNumber(), -4),
          'called_at' => $caller->getCalledAt()
        )
      );
      
      $xml =  '<?xml version="1.0" encoding="UTF-8"?>' .
              '<vxml version="2.1">' .
                '<form>' .
                  '<block>' .
                    '<prompt>Hello Cloud Conf!</prompt>' .
                  '</block>' .
                '</form>' .
              '</vxml>';
              
      $response = new Response(
          $xml,
          Response::HTTP_OK,
          array('content-type' => 'xml')
      );
      
      return $response;
    }
    
    /**
     * @Route("/chat-api/callers")
     */
    public function listUniqueCallers() {
      $repository = $this->getDoctrine()
        ->getRepository('AppBundle:Caller');

      $query = $repository->createQueryBuilder('c')
        ->select('c')
        ->groupBy('c.phoneNumber')
        ->getQuery();

      $callers = $query->getResult();
      
      $allNumbers = [];
      foreach ($callers as $caller) {
        $allNumbers[] = $caller->getPhoneNumber();
      }
      
      $response = new JsonResponse();
      $response->setData($allNumbers);
      return $response;
    }
}
