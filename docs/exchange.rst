#################################################
Wymiana
#################################################

Czym jest wymiana?
===================

Jest to miejsce, w którym aktualnie są przechowywane wiadomości przepływające
przez rurociąg. To właśnie wiadomości z wymiany są przetwarzane przez procesy z
procesorów.

Tworzenie wymiany
==================

Wymiane tworzymy poprzez stworzenie nowej instancji klasy *BasicExchange*.
Następnie ustawiamy różne wartości wejścia i wyjścia(metody **setIn()** oraz **setOut()**),
w których wrzucamy instancje klasy implementującej interfejs wiadomości, przykładowo
*BasicMessage*.

.. code-block:: PHP

  <?php
  BasicExchange::newInstance()
    ->setIn(BasicMessage::newInstance());
   ?>

W taki sposób wrzuciliśmy nową wiadomość do wymiany, która odbywa się w rurociągu.
Podczas wymiany wiadomość przejdzie przez różne procesy, które zostaną zaimplementowane,
oraz dojdzie do miejsca docelowego.
