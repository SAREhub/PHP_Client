################
Wiadomości
################

Czym jest wiadomość?
=======================

Wiadomosć jest obiektem, który przechowuje dane przekazywane do różnych miejsc.
Może być przekazywana w różnych formatach, jest wrzucana do wymiany.

Tworzenie wiadomości
=======================

Kreacja wiadomości odbywa się poprzez stworzenie nowego obiektu klasy implementującej
interfejs wiadomości, przykładową klasą istniejącą już w kliencie jest klasa *BasicMessage*.

.. code-block:: PHP

  <?php
  BasicMessage::newInstance()
    ->setBody('Przykładowa wiadomość.');
   ?>

W taki sposób stworzyliśmy wiadomość z ciałem zawierającym tekst *Przykładowa wiadomość.*.
Teraz należy w odpowiednim momencie pobrać wiadomość z wymiany, aby ją wyświetlić bądź
przekazać dalej.    
