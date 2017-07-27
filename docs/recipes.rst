########
Przepisy
########

Jak wrzucić zdarzenie do wymiany?
==================================
Na początku aby wrzucić zdarzenie do wymiany musimy zdecydować,
co w zdarzeniu ma się znajdować. Załóżmy, że chcemy wysłać zdarzenie,
które poinformuje system o wysłaniu wiadomości, którą system zażądał wysłać.

.. code-block:: php

  <?php
    private function buildExchange(SystemMessage $message): Exchange {
    		return BasicExchange::newInstance()
    		  ->setIn(BasicMessage::newInstance()
    			->setBody(BasicEvent::newInstanceOf('message_sent')
    			  ->withProperties([
    				'id' => $message->getId()
    			  ])
    			  ->withUser(new User([UserKeys::ID => $message->getAuthor()->getId()]))
    			  ->withTime(time())));
    	}
   ?>

Mamy już zdeklarowaną wymianę razem ze zdarzeniem, teraz musimy pchnąć ją do
procesora, aby ten przesłał informację o tym do systemu.

.. code-block:: php

  <?php
      class SendEventToSystemProcessor implements Processor {
        public function process(Exchange $exchange) {
            $message = $exchange->getIn()->getBody();
            $this->sendMessage($message);
        }

        private function sendMessage(Message $message) {
          //Wysyłanie wiadomości do brokera.
        }
      }
   ?>

Ostatnim etapem jest utworzenie rurociągu, który przetworzy nasze działanie.

.. code-block:: php

  <?php
      class ModulePipelineFactory {
        /**
        * @var Exchange
        */
        private $exchange;

        public function create() {
          $this->exchange = $this->buildExchange($messageToSet);
          return $this->process();
        }

        private function process() {
          return Pipeline::start()
            ->add(new SendEventToSystemProcessor())
            ->process($this->exchange)
        }

        private function buildExchange(SystemMessage $message): Exchange {
      		return BasicExchange::newInstance()
      		  ->setIn(BasicMessage::newInstance()
      			->setBody(BasicEvent::newInstanceOf('message_sent')
      			  ->withProperties([
      				'id' => $message->getId()
      			  ])
      			  ->withUser(new User([UserKeys::ID => $message->getAuthor()->getId()]))
      			  ->withTime(time())));
      	}
      }
   ?>

W taki sposób można zaimplementować wysyłanie zdarzeń, do zmiennej **$messageToSet** należy
przypisać odpowiednie dane, które mogą być przekazane na przykład w klasie typu
**Provider**.
