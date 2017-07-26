#################################################
Procesory
#################################################

Czym jest procesor?
===================
Procesor jest klasą, która implementuje metody wywołujące procesy zaimplementowane przez nas.
Przykładowo wysłanie wiadomości będzie rozdzielone na kilka procesów, takich jak: zbudowanie wiadomości,
definiowanie miejsca wysyłki, wysyłka wiadomości. Procesy te wywołane zostaną w metodzie **process()**, która
jest zaimplementowana w interfejsie procesora.

Tworzenie procesora
===================
Procesor tworzymy poprzez utworzenie nowej klasy z sufiksem Processor, przykładowo *SendMessageProcessor*.
Klasa ta implementuje interfejs o nazwie *Processor*.

.. code-block:: PHP

  <?php
    class SendMessageProcessor implements Processor {
        public function process(Exchange $exchange) {
          //...
        }
    }
   ?>

Powyższy kod pokazuje przykładową zawartość klasy procesora.

Uruchamianie procesora
======================
Stworzoną klase procesora wywołujemy poprzez wrzucenie jej do rurociągu za pomocą metody **add()**.

.. code-block:: PHP

  <?php
    Pipeline::start()
    ->add(new SendMessageProcessor())
    ->process();
   ?>
