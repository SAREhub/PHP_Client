#################################################
Kontekst
#################################################

Czym jest kontekst?
===================
Kontekst jest zbiorem wartości i ustawień dla systemu. Możemy w nim przekazywać
usługi pomiędzy klasami za pomocą klasy typu *Provider*, bądź ustawić globalne zmienne.

Inicjalizacja kontekstu
========================
Kontekst tworzymy za pomocą instancji klasy implementującej interfejs *ClientContext*.
W kliencie gotowa jest klasa o nazwie *BasicClientContext*, która implementuje wyżej
wymieniony interfejs.

.. code-block:: PHP

  <?php
    BasicClientContext::newInstance()
      ->setProperty('mysqlHost', 'localhost');
    ?>

Tak utworzony kontekst możemy przekazywać pomiędzy klasami, dzięki temu w każdej z
klas będziemy mieli dostęp do danych MySQL.

Jak przekazać serwis do klasy?
==============================
Aby przekazać serwis, który odpowiada za różne działania żądane w stworzonej klasie,
należy w niej czytać kontekst. Przykładowo tworzymy właściwość przechowującą klasy
implementujące ClientContext oraz za pomocą DependencyInjection wstrzykujemy kontekst
do klasy.

.. code-block:: php

  <?php
    class TestClass {
      /**
      * @var ClientContext
      */
      private $context;

      public function __construct(ClientContext $c) {
        $this->context = $c;
      }
    }
   ?>

Tak przekazany kontekst możemy teraz odczytać za pomocą odwołania się do właściwości
*$context*. Przypuśćmy na ten moment, że posiadamy usługę, która posiada możliwość
dostępu do wszystkich obiektów w bazie danych oraz zarządzania nimi. Niech będzie to
klasa *DatabaseManagerService*. Klase tą razem z jej zapisanymi właściwościami możemy
przekazać do drugiej klasy za pomocą kontekstu, przypisując do niego serwis metodą
**addService()**.

.. code-block:: php

  <?php
    class AppContextProvider implements ContextProvider {

      public function register(ClientContext $c) {
        $c->addService(new DatabaseManagerService('localhost', 'root', 'testpasswd', 'db'));
      }
    }
   ?>


W taki sposób dodaliśmy do kontekstu serwis, aby go pobrać możemy użyć metody **getProperty()**.
