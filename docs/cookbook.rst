#################################################
Cookbook
#################################################
Klient używany do tworzenia mikroserwisów implementuje kilka ich rodzajów.
Rozróżniamy aktualnie następujące typy:
- służy do przekazywania informacji w postaci wiadomości, przykładowo
tagów przez brokera RabbitMQ. Obsługuje głównie protokół AMQP. Ponadto ogólnie
wykonuje wszelakie logiczne procesy w tle wiadomości.

* **moduł** - służy do przekazywania informacji w postaci wiadomości, przykładowo tagów przez brokera RabbitMQ. Obsługuje głównie protokół AMQP. Ponadto ogólnie wykonuje wszelakie logiczne procesy w tle wiadomości.

* **API** - wykorzystywane jest do odbierania danych z aplikacji na Frontendzie. Posiada kontrolery oraz obsługuje żądania wysyłając odpowiednie informacje dla modułów.

* **filtr** - filtruje dane.

Tworzenie modułu
====================
Tworzony przez nas moduł obsługuje tzw. Pipeline'y, które zostały opisane w dokumentacji.
Przykładowa ich obsługa jest pokazana w tej fabryce:

.. code-block:: php

   {
    "from": "nadawca emaila",
    "to": "odbiorca emaila",
    "subject": "tytuł emaila",
    "body": {
     "txt": "string lub object z parametrem url",
     "html": "string lub object z parametrem url"
    }
   }
