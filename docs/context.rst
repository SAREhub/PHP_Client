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
