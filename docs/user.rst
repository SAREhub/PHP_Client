#################################################
Użytkownik
#################################################

Jak działa użytkownik w kliencie?
==================================
Użytkownik w kliencie unifikuje przesyłane dane w wiadomościach,
pozwala ustawić w wiadomości identyfikatory typu:

* cookie - identyfikator przechowywany w danych przeglądarki.
* id - identyfikator numeryczny klienta.
* email - identyfikator zawierający adres e-mail.
* mobile - identyfikator zawierający numer telefonu.

Typy identyfikatorów są przechowywane w interfejsie UserKeys jako stałe.

Tworzenie użytkownika
======================
Do stworzenia użytkownika potrzebna będzie kreacja nowej instancji klasy User,
następnie należy przypisać instancji odpowiednie atrybuty. Wygląda to w nastepujący
sposób:

.. code-block:: php

  <?php
    $user = new User([
      UserKeys::ID => 123,
      UserKeys::COOKIE => 14321123416123413215,
      UserKeys::EMAIL =>
    ]);
   ?>
