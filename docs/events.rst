##########
Zdarzenia
##########

Tworzenie zdarzenia
====================
Do stworzenia zdarzenia w kliencie należy skorzystać z klasy implementującej
interfejs *Event*. Zdarzenie posiada swoje właściwości, przypisanego użytkownika,
czas wykonania zdarzenia oraz jego nazwę. Działa to w sposób pokazany poniżej:

.. code-block:: php

  <?php
    BasicEvent::newInstance('message_send')
      ->withUser(new User([UserKeys::COOKIE => 134214123412341]))
      ->withTime(time())
      ->withProperties([
        'message_id' => 2
      ])
   ?>

Tak stworzone zdarzenie można posłać do brokera wiadomości, który przekaże informacje
o zdarzeniu dalej i zainicjuje wykonanie różnych procesów.
