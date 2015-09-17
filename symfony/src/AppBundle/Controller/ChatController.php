<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\ChatMessage;

use Predis\Client;

class ChatController extends Controller
{
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
      
      // TODO: publish message
      
      $response = new JsonResponse();
      $response->setData($message);
      return $response;
    }
}
