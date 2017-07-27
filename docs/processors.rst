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

Jak przetworzyć treść wiadomości do formatu JSON?
=================================================
Aby przetworzyć treść wiadomości najlepiej będzie skorzystać z gotowych procesorów
zaimplementowanych w kliencie PHP. W tym wypadku skorzystamy z procesora o nazwie
*MarshalProcessor*, który przetwarza wiadomości na różne formaty. Przypuśćmy, że
wrzuciliśmy już gotową wiadomość za pomocą procesora *SendMessageProcessor* do
rurociągu i mamy aktualnie następujący kod.

.. code-block:: PHP

  <?php
    Pipeline::start()
    ->add(new SendMessageProcessor())
    ->process();
   ?>

Teraz, by przekonwertować wiadomość do formatu JSON wystarczy dodać nową instancje
procesora MarshalProcessor ustawiając typ konwertowania na dany format i wiadomość,
która przechodzi przez rurociąg zostanie przetworzona na żądany typ. W praktyce
wygląda to mniejwięcej tak:

.. code-block:: PHP

  <?php
    Pipeline::start()
    ->add(new SendMessageProcessor())
    ->add(MarshalProcessor::withDataFormat(new JsonDataFormat()))
    ->process();
   ?>

Wiadomość wysyłana przez procesor *SendMessageProcessor* trafia do kolejnego
procesora, który formatuje ją na sprecyzowany klasą o nazwie *JsonDataFormat* format.
