<?php

namespace SML\PlatformBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;


class MessagePostEvent extends Event
{
  protected $message;
  protected $user;

  /**
   * MessagePostEvent constructor.
   * @param $message
   * @param UserInterface $user
   */
  public function __construct($message, UserInterface $user)
  {
    $this->message = $message;
    $this->user = $user;
  }

  /**
   * @return mixed
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param mixed $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }

  /**
   * @return UserInterface
   */
  public function getUser()
  {
    return $this->user;
  }

}